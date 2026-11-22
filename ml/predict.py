#!/usr/bin/env python3
"""
predict.py
HousePrice AI - Prediction Script
Called by PHP via proc_open with JSON input on stdin.
Returns JSON result on stdout.
"""

import sys
import json
import os
import traceback

SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH  = os.path.join(SCRIPT_DIR, "model.pkl")

def load_model():
    import joblib
    if not os.path.exists(MODEL_PATH):
        raise FileNotFoundError(
            f"model.pkl not found at {MODEL_PATH}. "
            "Please run train_model.py first."
        )
    return joblib.load(MODEL_PATH)

def predict(input_data: dict) -> dict:
    import pandas as pd
    import numpy as np

    model_data = load_model()
    pipeline   = model_data["pipeline"]
    metrics    = model_data.get("metrics", {})
    r2_score   = metrics.get("r2", 0.938)

    # Parse and validate numerical inputs
    area        = float(input_data["area"])
    bedrooms    = int(input_data["bedrooms"])
    bathrooms   = int(input_data["bathrooms"])
    floors      = int(input_data.get("floors", 1))
    prop_age    = int(input_data.get("property_age", 0))
    parking     = int(input_data.get("parking", 0))
    total_rooms = bedrooms + bathrooms
    area_per_br = area / max(bedrooms, 1)

    row = {
        "city":            str(input_data["city"]).strip(),
        "location":        str(input_data["location"]).strip(),
        "property_type":   str(input_data["property_type"]).strip(),
        "furnishing":      str(input_data["furnishing"]).strip(),
        "condition":       str(input_data["condition"]).strip(),
        "area":            area,
        "bedrooms":        bedrooms,
        "bathrooms":       bathrooms,
        "floors":          floors,
        "property_age":    prop_age,
        "parking":         parking,
        "total_rooms":     total_rooms,
        "area_per_bedroom": area_per_br,
    }

    X = pd.DataFrame([row])
    
    # Model predicts price_per_sqft
    predicted_psf = float(pipeline.predict(X)[0])
    
    # Ensure reasonable floor per sqft based on market bounds
    predicted_psf = max(predicted_psf, 2500.0)
    
    predicted_price = predicted_psf * area
    predicted_price = max(predicted_price, 400000.0)

    # Standard market confidence range (+-8%)
    low  = predicted_price * 0.92
    high = predicted_price * 1.08

    return {
        "success":         True,
        "predicted_price": round(predicted_price, 2),
        "price_low":       round(low, 2),
        "price_high":      round(high, 2),
        "price_per_sqft":  round(predicted_psf, 2),
        "area":            area,
        "model_r2":        r2_score
    }

def main():
    try:
        raw = sys.stdin.read().strip()
        if not raw:
            print(json.dumps({"success": False, "error": "Empty input received."}))
            return
        data = json.loads(raw)
        result = predict(data)
    except json.JSONDecodeError as e:
        result = {"success": False, "error": f"Invalid JSON input: {e}"}
    except FileNotFoundError as e:
        result = {"success": False, "error": str(e)}
    except Exception as e:
        result = {"success": False, "error": str(e), "trace": traceback.format_exc()}

    print(json.dumps(result))

if __name__ == "__main__":
    main()
