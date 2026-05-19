import joblib
import pandas as pd
import shap
import matplotlib.pyplot as plt
from pathlib import Path
import json
import os

# Create directory for artifacts
os.makedirs('artifacts', exist_ok=True)

print("Loading model...")
MODEL_PATH = Path('models/donor_ranker.joblib')
artifact = joblib.load(MODEL_PATH)

if isinstance(artifact, dict):
    model = artifact.get("model")
    feature_columns = artifact.get("feature_columns", ["distance_km", "days_since_last_donation", "temporal_hour", "is_weekend", "historical_response_rate"])
else:
    model = artifact
    feature_columns = ["distance_km", "days_since_last_donation", "temporal_hour", "is_weekend", "historical_response_rate"]

print(f"Model type: {type(model)}")

# Let's create two dummy donors for testing SHAP
# Donor A: Very close, hasn't donated in a while, good history (High Probability)
# Donor B: Far away, donated recently, bad history (Low Probability)

dummy_data = [
    {
        "distance_km": 2.5,
        "days_since_last_donation": 180,
        "temporal_hour": 14,
        "is_weekend": 0,
        "historical_response_rate": 0.8
    },
    {
        "distance_km": 18.0,
        "days_since_last_donation": 95,
        "temporal_hour": 3,
        "is_weekend": 1,
        "historical_response_rate": 0.1
    }
]

df = pd.DataFrame(dummy_data, columns=feature_columns)

print("Predicting probabilities...")
probs = model.predict_proba(df)[:, 1]

print(f"Donor A Probability: {probs[0]:.4f}")
print(f"Donor B Probability: {probs[1]:.4f}")

print("Computing SHAP values...")
# Initialize explainer depending on model type
# We know it's likely a Tree Explainer if it's XGBoost/LightGBM/HistGradientBoosting
try:
    explainer = shap.TreeExplainer(model)
    shap_values = explainer(df)
    
    # Extract the values for the positive class (if binary classification)
    if len(shap_values.values.shape) == 3:  # (samples, features, classes)
        shap_values_pos = shap_values[:, :, 1]
    else:
        shap_values_pos = shap_values
        
    # Create the summary plot
    plt.figure(figsize=(10, 6))
    shap.waterfall_plot(shap_values_pos[0], show=False)
    plt.title('SHAP Waterfall Plot for Donor A (High Probability)')
    plt.tight_layout()
    plt.savefig('artifacts/shap_donor_a.png', dpi=300, bbox_inches='tight')
    plt.close()
    
    plt.figure(figsize=(10, 6))
    shap.waterfall_plot(shap_values_pos[1], show=False)
    plt.title('SHAP Waterfall Plot for Donor B (Low Probability)')
    plt.tight_layout()
    plt.savefig('artifacts/shap_donor_b.png', dpi=300, bbox_inches='tight')
    plt.close()
    
    # Output raw SHAP values for markdown
    results = {
        "Donor A": {
            "Base Value": float(shap_values_pos[0].base_values),
            "Final Output": float(shap_values_pos[0].values.sum() + shap_values_pos[0].base_values),
            "Features": {col: float(val) for col, val in zip(feature_columns, shap_values_pos[0].values)}
        },
        "Donor B": {
            "Base Value": float(shap_values_pos[1].base_values),
            "Final Output": float(shap_values_pos[1].values.sum() + shap_values_pos[1].base_values),
            "Features": {col: float(val) for col, val in zip(feature_columns, shap_values_pos[1].values)}
        }
    }
    
    with open('artifacts/shap_results.json', 'w') as f:
        json.dump(results, f, indent=4)
        
    print("SHAP analysis complete. Images saved to artifacts/.")
    
except Exception as e:
    print(f"Error computing SHAP values using TreeExplainer: {e}")
    print("Attempting KernelExplainer (Model agnostic)...")
    
    # Kernel explainer as fallback
    explainer = shap.KernelExplainer(model.predict_proba, df.iloc[:1])
    shap_values = explainer.shap_values(df)
    
    pos_shap_values = shap_values[1] if isinstance(shap_values, list) else shap_values
    
    results = {
        "Donor A": {
            "Features": {col: float(val) for col, val in zip(feature_columns, pos_shap_values[0])}
        },
        "Donor B": {
            "Features": {col: float(val) for col, val in zip(feature_columns, pos_shap_values[1])}
        }
    }
    with open('artifacts/shap_results.json', 'w') as f:
        json.dump(results, f, indent=4)
    print("Kernel Explainer SHAP analysis complete.")
