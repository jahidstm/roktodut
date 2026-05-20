from __future__ import annotations

import asyncio
import json
import os
from contextlib import asynccontextmanager
from pathlib import Path
from typing import Any, Literal

from dotenv import load_dotenv

# Load the parent Laravel .env file automatically
env_path = Path(__file__).resolve().parent.parent / '.env'
load_dotenv(dotenv_path=env_path)

import joblib
import pandas as pd
from fastapi import Depends, FastAPI, Header, HTTPException, status
from groq import Groq
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
DEFAULT_GROQ_MODEL = "llama3-8b-8192"

NER_SYSTEM_PROMPT = """You are a strict medical NER extractor for blood request triage.
Your task: extract structured fields from Bengali/English free-text blood request messages.

Return ONLY valid JSON object with this exact schema:
{
  "blood_group": "A+|A-|B+|B-|AB+|AB-|O+|O-|unknown",
  "urgency": "emergency|high|medium",
  "location_text": "string",
  "units_needed": integer >= 1,
  "confidence_score": float between 0.0 and 1.0
}

Rules:
1) If blood group is NOT explicitly stated, you MUST set `blood_group` to "unknown" and heavily penalize `confidence_score` (e.g., 0.1). DO NOT guess or hallucinate a blood group.
2) urgency mapping:
   - emergency for phrases like: emergency, urgent now, immediately, tonight critical, এক্ষুনি, ইমারজেন্সি, অতি জরুরি
   - high for: urgent, today, within hours, জরুরি
   - medium for all other or unclear cases.
3) location_text should be the most specific place string available in input.
4) units_needed must be integer; if missing assume 1.
5) confidence_score reflects extraction confidence based on clarity/completeness. High confidence (>0.8) ONLY if blood group and location are both clearly stated.
6) NEVER output markdown, explanations, comments, or additional keys.
"""


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


class ParseRequest(BaseModel):
    model_config = ConfigDict(extra="forbid")
    text: str = Field(..., min_length=3)


class ParsedBloodRequest(BaseModel):
    blood_group: Literal["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-", "unknown"]
    urgency: Literal["emergency", "high", "medium"]
    location_text: str = Field(..., min_length=2, max_length=255)
    units_needed: int = Field(..., ge=1, le=20)
    confidence_score: float = Field(..., ge=0.0, le=1.0)


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


def _resolve_groq_client() -> Groq | None:
    api_key = os.getenv("GROQ_API_KEY", "").strip()
    if api_key == "":
        return None
    return Groq(api_key=api_key)


@asynccontextmanager
async def lifespan(app: FastAPI):
    model, feature_columns = _load_model_artifact()
    app.state.model = model
    app.state.feature_columns = feature_columns
    app.state.api_key = _resolve_expected_api_key()
    app.state.groq_client = _resolve_groq_client()
    app.state.groq_model = os.getenv("GROQ_MODEL", DEFAULT_GROQ_MODEL).strip() or DEFAULT_GROQ_MODEL
    yield


app = FastAPI(
    title="RoktoDut ML Service",
    version="1.1.0",
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


def _call_groq_parse(client: Groq, model_name: str, text: str) -> ParsedBloodRequest:
    completion = client.chat.completions.create(
        model=model_name,
        temperature=0,
        response_format={"type": "json_object"},
        messages=[
            {"role": "system", "content": NER_SYSTEM_PROMPT},
            {"role": "user", "content": text},
        ],
    )

    content = completion.choices[0].message.content if completion.choices else None
    if not content:
        raise RuntimeError("Groq returned empty content.")

    parsed_raw = json.loads(content)
    return ParsedBloodRequest.model_validate(parsed_raw)


@app.post("/api/v1/parse-request", response_model=ParsedBloodRequest)
async def parse_request(
    payload: ParseRequest,
    _: None = Depends(require_api_key),
) -> ParsedBloodRequest:
    client: Groq | None = app.state.groq_client
    if client is None:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="GROQ_API_KEY is not configured.",
        )

    model_name: str = app.state.groq_model
    try:
        return await asyncio.to_thread(_call_groq_parse, client, model_name, payload.text)
    except Exception as exc:
        raise HTTPException(
            status_code=status.HTTP_502_BAD_GATEWAY,
            detail=f"NLP parsing failed: {exc}",
        ) from exc

