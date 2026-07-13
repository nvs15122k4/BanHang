#!/bin/bash
set -e

echo "📦 Installing npm dependencies..."
npm install

echo "🎨 Building frontend assets with vite..."
./node_modules/.bin/vite build

echo "✅ Frontend build complete!"
