from __future__ import annotations

import os
from contextlib import asynccontextmanager
from pathlib import Path
from typing import Any

import joblib
import pandas as pd
from fastapi import Depends, FastAPI, Header, HTTPException, status
from pydantic import BaseModel, ConfigDict, Field

DEFAULT_API_KEY = "ROKTODUT_AI_SECRET"
MODEL_PATH = Path(__file__).resolve().parent / "models" / "donor_ranker.joblib"
DEFAULT_FEATURE_COLUMNS = [
    "distance_km",
    "days_since_last_donation",
    "temporal_hour",
    "is_weekend",
    "historical_response_rate",
]


class CandidateDonor(BaseModel):
    donor_id: int = Field(..., gt=0)
    distance_km: float = Field(..., ge=0)
    days_since_last_donation: int = Field(..., ge=0)
    temporal_hour: int = Field(..., ge=0, le=23)
    is_weekend: bool
    historical_response_rate: float = Field(..., ge=0.0, le=1.0)


class RankingRequest(BaseModel):
    model_config = ConfigDict(extra="forbid")

    request_details: dict[str, Any] | None = None
    candidate_donors: list[CandidateDonor] = Field(..., min_length=1)


class RankedDonor(BaseModel):
    donor_id: int
    probability_score: float
    rank: int


def _resolve_expected_api_key() -> str:
    return os.getenv("ROKTODUT_API_KEY", DEFAULT_API_KEY)


def _load_model_artifact() -> tuple[Any, list[str]]:
    if not MODEL_PATH.exists():
        raise RuntimeError(f"Model not found: {MODEL_PATH}")

    artifact = joblib.load(MODEL_PATH)

    if isinstance(artifact, dict):
        model = artifact.get("model")
        feature_columns = artifact.get("feature_columns", DEFAULT_FEATURE_COLUMNS)
    else:
        model = artifact
        feature_columns = DEFAULT_FEATURE_COLUMNS

    if model is None:
        raise RuntimeError("Invalid model artifact: model is missing.")

    return model, list(feature_columns)


@asynccontextmanager
async def lifespan(app: FastAPI):
    model, feature_columns = _load_model_artifact()
    app.state.model = model
    app.state.feature_columns = feature_columns
    app.state.api_key = _resolve_expected_api_key()
    yield


app = FastAPI(
    title="RoktoDut ML Service",
    version="1.0.0",
    lifespan=lifespan,
)


def require_api_key(x_api_key: str = Header(..., alias="X-API-Key")) -> None:
    expected = app.state.api_key
    if x_api_key != expected:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid API key",
        )


@app.post("/api/v1/rank-donors", response_model=list[RankedDonor])
async def rank_donors(
    payload: RankingRequest,
    _: None = Depends(require_api_key),
) -> list[RankedDonor]:
    model = app.state.model
    feature_columns: list[str] = app.state.feature_columns

    rows = [
        {
            "distance_km": donor.distance_km,
            "days_since_last_donation": donor.days_since_last_donation,
            "temporal_hour": donor.temporal_hour,
            "is_weekend": int(donor.is_weekend),
            "historical_response_rate": donor.historical_response_rate,
        }
        for donor in payload.candidate_donors
    ]

    features_df = pd.DataFrame(rows, columns=feature_columns)
    probabilities = model.predict_proba(features_df)[:, 1]

    ranked = sorted(
        zip(payload.candidate_donors, probabilities, strict=True),
        key=lambda item: float(item[1]),
        reverse=True,
    )

    return [
        RankedDonor(
            donor_id=item[0].donor_id,
            probability_score=round(float(item[1]), 6),
            rank=idx + 1,
        )
        for idx, item in enumerate(ranked)
    ]
