#!/bin/bash
set -e

echo "Installing npm dependencies..."
npm ci --prefer-offline --no-audit

echo "Building assets..."
npm run build

echo "Assets built successfully!"
