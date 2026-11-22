#!/usr/bin/env python3
"""
train_model.py
HousePrice AI - Random Forest Regression Model Training
Predicts price per square foot with market-aware feature engineering.
"""

import os
import sys
import json
import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import OneHotEncoder, StandardScaler
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
import joblib

# ── Paths ──────────────────────────────────────────────────────────────────────
SCRIPT_DIR   = os.path.dirname(os.path.abspath(__file__))
DATASET_PATH = os.path.join(SCRIPT_DIR, "dataset.csv")
MODEL_PATH   = os.path.join(SCRIPT_DIR, "model.pkl")

# ── Load dataset ───────────────────────────────────────────────────────────────
print("[1/5] Loading dataset ...")
df = pd.read_csv(DATASET_PATH)
print(f"      Loaded {len(df)} rows, {len(df.columns)} columns.")

# ── Clean data ─────────────────────────────────────────────────────────────────
print("[2/5] Cleaning data ...")
df = df.dropna().drop_duplicates()
print(f"      Valid records: {len(df)}.")

# ── Feature engineering ───────────────────────────────────────────────────────
print("[3/5] Engineering features ...")
df["price_per_sqft"]    = df["price"] / df["area"]
df["total_rooms"]       = df["bedrooms"] + df["bathrooms"]
df["area_per_bedroom"]  = df["area"] / (df["bedrooms"].replace(0, 1))

CATEGORICAL_FEATURES = ["city", "location", "property_type", "furnishing", "condition"]
NUMERICAL_FEATURES   = [
    "area", "bedrooms", "bathrooms", "floors",
    "property_age", "parking", "total_rooms", "area_per_bedroom"
]

X = df[CATEGORICAL_FEATURES + NUMERICAL_FEATURES]
y = df["price_per_sqft"]
prices = df["price"]

# ── Train / test split ────────────────────────────────────────────────────────
X_train, X_test, y_train, y_test, p_train, p_test = train_test_split(
    X, y, prices, test_size=0.15, random_state=42
)
print(f"      Train: {len(X_train)} | Test: {len(X_test)}")

# ── Preprocessing & Model Pipeline ────────────────────────────────────────────
print("[4/5] Training Random Forest Regressor Pipeline (300 estimators) ...")
preprocessor = ColumnTransformer(
    transformers=[
        ("cat", OneHotEncoder(handle_unknown="ignore", sparse_output=False), CATEGORICAL_FEATURES),
        ("num", StandardScaler(), NUMERICAL_FEATURES),
    ]
)

pipeline = Pipeline([
    ("preprocessor", preprocessor),
    ("model", RandomForestRegressor(
        n_estimators=300,
        max_depth=None,
        min_samples_split=2,
        min_samples_leaf=1,
        random_state=42,
        n_jobs=-1
    ))
])

pipeline.fit(X_train, y_train)

# ── Evaluate ──────────────────────────────────────────────────────────────────
y_pred_psf = pipeline.predict(X_test)
y_pred_price = y_pred_psf * X_test["area"]

r2_psf   = r2_score(y_test, y_pred_psf)
r2_price = r2_score(p_test, y_pred_price)
mae_price = mean_absolute_error(p_test, y_pred_price)
rmse_price = np.sqrt(mean_squared_error(p_test, y_pred_price))

print("\n" + "=" * 60)
print("  MODEL EVALUATION RESULTS")
print("=" * 60)
print(f"  R2 Score (Price/sqft) : {r2_psf:.4f}")
print(f"  R2 Score (Total Price): {r2_price:.4f}")
print(f"  MAE (Total Price)     : INR {mae_price:,.2f}")
print(f"  RMSE (Total Price)    : INR {rmse_price:,.2f}")
print("=" * 60)

# ── Save model ────────────────────────────────────────────────────────────────
print(f"\n[5/5] Saving model to {MODEL_PATH} ...")
model_data = {
    "pipeline":             pipeline,
    "target":               "price_per_sqft",
    "categorical_features": CATEGORICAL_FEATURES,
    "numerical_features":   NUMERICAL_FEATURES,
    "metrics": {
        "mae":      round(mae_price, 2),
        "rmse":     round(rmse_price, 2),
        "r2":       round(r2_price, 4),
        "r2_psf":   round(r2_psf, 4),
    }
}
joblib.dump(model_data, MODEL_PATH)

print("      Model saved successfully!\n")
print("  Training complete. Ready for real-time predictions.")
