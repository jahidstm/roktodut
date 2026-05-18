from __future__ import annotations

import argparse
from pathlib import Path

import numpy as np
import pandas as pd


def _clamp(value: float, low: float = 0.0, high: float = 1.0) -> float:
    return max(low, min(high, value))


def _acceptance_probability(
    distance_km: float,
    days_since_last_donation: int,
    temporal_hour: int,
    is_weekend: bool,
    historical_response_rate: float,
    donor_gender: str,
) -> float:
    # Hard eligibility rule from blueprint:
    # - Male needs >= 90 days
    # - Female needs >= 120 days
    min_days = 120 if donor_gender == "female" else 90
    if days_since_last_donation < min_days:
        return 0.0

    # Base response score driven by historical behavior + distance + time context.
    distance_score = _clamp(1 - (distance_km / 20.0))
    hour_score = 0.0
    if 7 <= temporal_hour <= 10:
        hour_score = 0.05
    elif 18 <= temporal_hour <= 22:
        hour_score = 0.08

    probability = (
        0.03
        + (0.52 * historical_response_rate)
        + (0.30 * distance_score)
        + hour_score
        + (0.02 if is_weekend else 0.0)
    )

    # Blueprint rule: heavy drop from 2-5 AM.
    if 2 <= temporal_hour <= 5:
        probability *= 0.18

    if distance_km > 20:
        probability *= 0.35

    return _clamp(probability, 0.0, 0.98)


def generate_rows(n_rows: int, seed: int = 42) -> pd.DataFrame:
    rng = np.random.default_rng(seed)

    now = pd.Timestamp.now(tz="UTC").floor("min")
    notified_offsets_min = rng.integers(0, 365 * 24 * 60, size=n_rows)
    notified_at = now - pd.to_timedelta(notified_offsets_min, unit="m")
    temporal_hour = notified_at.hour.astype(int)
    is_weekend = notified_at.dayofweek >= 5

    donor_gender = rng.choice(["male", "female"], size=n_rows, p=[0.68, 0.32])
    days_since_last_donation = np.clip(
        rng.normal(loc=170, scale=95, size=n_rows).round().astype(int), 0, 540
    )

    distance_km = np.round(
        np.clip(rng.gamma(shape=2.2, scale=3.4, size=n_rows), 0.2, 35.0), 2
    )
    historical_response_rate = np.round(rng.beta(a=2.2, b=3.8, size=n_rows), 4)

    probs = np.array(
        [
            _acceptance_probability(
                float(distance_km[i]),
                int(days_since_last_donation[i]),
                int(temporal_hour[i]),
                bool(is_weekend[i]),
                float(historical_response_rate[i]),
                str(donor_gender[i]),
            )
            for i in range(n_rows)
        ]
    )

    accepted = rng.random(n_rows) < probs

    status: list[str] = []
    responded_at: list[pd.Timestamp | pd.NaT] = []
    response_time_minutes: list[int | None] = []

    for i in range(n_rows):
        p = float(probs[i])
        is_accepted = bool(accepted[i])
        notified = notified_at[i]

        if is_accepted:
            status.append("accepted")
            # Faster responders when closer and historically reliable.
            base_minutes = int(
                12
                + (distance_km[i] * 2.1)
                + ((1.0 - historical_response_rate[i]) * 65)
                + rng.integers(0, 40)
            )
            mins = max(2, min(base_minutes, 240))
            response_time_minutes.append(mins)
            responded_at.append(notified + pd.Timedelta(minutes=mins))
            continue

        # Non-accepted statuses.
        if p == 0:
            # Cooldown-ineligible donors are likely to decline/ignore.
            s = "declined" if rng.random() < 0.75 else "ignored"
        else:
            roll = rng.random()
            if roll < 0.48:
                s = "ignored"
            elif roll < 0.83:
                s = "declined"
            else:
                s = "pending"

        status.append(s)

        if s == "declined":
            mins = int(
                max(5, min(360, 20 + rng.integers(0, 180) + (distance_km[i] * 1.5)))
            )
            response_time_minutes.append(mins)
            responded_at.append(notified + pd.Timedelta(minutes=mins))
        elif s == "ignored":
            response_time_minutes.append(None)
            responded_at.append(pd.NaT)
        else:  # pending
            response_time_minutes.append(None)
            responded_at.append(pd.NaT)

    request_id = rng.integers(1, 2200, size=n_rows)
    donor_id = rng.integers(1, 10000, size=n_rows)

    df = pd.DataFrame(
        {
            "request_id": request_id,
            "donor_id": donor_id,
            "notified_at": pd.to_datetime(notified_at).tz_convert(None),
            "responded_at": pd.to_datetime(responded_at),
            "status": status,
            "response_time_minutes": response_time_minutes,
            "distance_km": distance_km,
            "days_since_last_donation": days_since_last_donation,
            "temporal_hour": temporal_hour,
            "is_weekend": is_weekend.astype(int),
            "historical_response_rate": historical_response_rate,
        }
    )

    return df.sort_values("notified_at").reset_index(drop=True)


def main() -> None:
    parser = argparse.ArgumentParser(
        description="Generate synthetic donor_response_logs for cold-start model training."
    )
    parser.add_argument(
        "--rows",
        type=int,
        default=8000,
        help="Number of rows to generate (must be >= 5000).",
    )
    parser.add_argument(
        "--seed",
        type=int,
        default=42,
        help="Random seed for reproducibility.",
    )
    parser.add_argument(
        "--output",
        type=str,
        default=str(
            Path(__file__).resolve().parents[1]
            / "data"
            / "donor_response_logs_synthetic.csv"
        ),
        help="Output CSV path.",
    )
    args = parser.parse_args()

    if args.rows < 5000:
        raise ValueError("rows must be >= 5000 for blueprint compliance.")

    output_path = Path(args.output)
    output_path.parent.mkdir(parents=True, exist_ok=True)

    df = generate_rows(n_rows=args.rows, seed=args.seed)
    df.to_csv(output_path, index=False)

    accepted_rate = (df["status"] == "accepted").mean()
    print(f"Generated {len(df)} rows -> {output_path}")
    print("Status distribution:")
    print(df["status"].value_counts(normalize=True).round(4))
    print(f"Accepted rate: {accepted_rate:.4f}")


if __name__ == "__main__":
    main()
