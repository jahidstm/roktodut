from __future__ import annotations

import argparse
from datetime import datetime, timezone
from pathlib import Path

import joblib
import pandas as pd
from lightgbm import LGBMClassifier
from sklearn.metrics import accuracy_score, average_precision_score, roc_auc_score
from sklearn.model_selection import train_test_split

FEATURE_COLUMNS = [
    "distance_km",
    "days_since_last_donation",
    "temporal_hour",
    "is_weekend",
    "historical_response_rate",
]


def load_dataset(csv_path: Path) -> pd.DataFrame:
    df = pd.read_csv(csv_path)
    missing = [c for c in FEATURE_COLUMNS + ["status"] if c not in df.columns]
    if missing:
        raise ValueError(f"Missing required columns: {missing}")
    return df


def train(df: pd.DataFrame, random_state: int = 42):
    X = df[FEATURE_COLUMNS].copy()
    X["is_weekend"] = X["is_weekend"].astype(int)
    y = (df["status"] == "accepted").astype(int)

    X_train, X_test, y_train, y_test = train_test_split(
        X,
        y,
        test_size=0.2,
        random_state=random_state,
        stratify=y,
    )

    model = LGBMClassifier(
        objective="binary",
        n_estimators=320,
        learning_rate=0.05,
        num_leaves=31,
        subsample=0.9,
        colsample_bytree=0.9,
        reg_alpha=0.1,
        reg_lambda=0.1,
        random_state=random_state,
        n_jobs=-1,
    )
    model.fit(X_train, y_train)

    probs = model.predict_proba(X_test)[:, 1]
    preds = (probs >= 0.5).astype(int)

    metrics = {
        "roc_auc": float(roc_auc_score(y_test, probs)),
        "average_precision": float(average_precision_score(y_test, probs)),
        "accuracy_at_0.5": float(accuracy_score(y_test, preds)),
        "positive_rate_test": float(y_test.mean()),
    }

    return model, metrics


def main() -> None:
    parser = argparse.ArgumentParser(
        description="Train donor ranking model from synthetic donor_response_logs."
    )
    parser.add_argument(
        "--input",
        type=str,
        default=str(
            Path(__file__).resolve().parents[1]
            / "data"
            / "donor_response_logs_synthetic.csv"
        ),
        help="Input CSV path.",
    )
    parser.add_argument(
        "--output",
        type=str,
        default=str(
            Path(__file__).resolve().parents[1] / "models" / "donor_ranker.joblib"
        ),
        help="Output model path.",
    )
    parser.add_argument(
        "--random-state",
        type=int,
        default=42,
        help="Random seed.",
    )
    args = parser.parse_args()

    input_path = Path(args.input)
    output_path = Path(args.output)
    output_path.parent.mkdir(parents=True, exist_ok=True)

    if not input_path.exists():
        raise FileNotFoundError(f"Input CSV not found: {input_path}")

    df = load_dataset(input_path)
    model, metrics = train(df, random_state=args.random_state)

    artifact = {
        "model": model,
        "feature_columns": FEATURE_COLUMNS,
        "trained_at_utc": datetime.now(timezone.utc).isoformat(),
        "training_rows": int(len(df)),
        "metrics": metrics,
    }
    joblib.dump(artifact, output_path)

    print(f"Model saved: {output_path}")
    print(f"Training rows: {len(df)}")
    print("Metrics:")
    for key, value in metrics.items():
        print(f"  - {key}: {value:.4f}")


if __name__ == "__main__":
    main()
