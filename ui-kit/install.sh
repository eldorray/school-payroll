#!/bin/bash

# Laravel Shadcn UI Kit - Installation Script
# Usage: ./install.sh /path/to/your/laravel/project

set -e

TARGET_DIR="${1:-.}"

echo "🎨 Installing Laravel Shadcn UI Kit..."
echo ""

# Check if target is a Laravel project
if [ ! -f "$TARGET_DIR/artisan" ]; then
    echo "❌ Error: Not a Laravel project directory."
    echo "Usage: ./install.sh /path/to/your/laravel/project"
    exit 1
fi

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Create directories
echo "📁 Creating directories..."
mkdir -p "$TARGET_DIR/resources/views/components/ui"
mkdir -p "$TARGET_DIR/resources/views/components/layouts"

# Copy CSS
echo "🎨 Copying CSS..."
if [ -f "$TARGET_DIR/resources/css/app.css" ]; then
    echo "   ℹ️  Backing up existing app.css to app.css.backup"
    cp "$TARGET_DIR/resources/css/app.css" "$TARGET_DIR/resources/css/app.css.backup"
fi
cp "$SCRIPT_DIR/css/app.css" "$TARGET_DIR/resources/css/app.css"

# Copy Components
echo "📦 Copying Blade components..."
cp -r "$SCRIPT_DIR/components/ui/"* "$TARGET_DIR/resources/views/components/ui/"

# Copy Layouts
echo "🖼️  Copying layouts..."
cp -r "$SCRIPT_DIR/layouts/"* "$TARGET_DIR/resources/views/components/layouts/"

# Copy JS
echo "⚡ Copying JavaScript..."
if [ -f "$TARGET_DIR/resources/js/app.js" ]; then
    echo "   ℹ️  Backing up existing app.js to app.js.backup"
    cp "$TARGET_DIR/resources/js/app.js" "$TARGET_DIR/resources/js/app.js.backup"
fi
cp "$SCRIPT_DIR/js/app.js" "$TARGET_DIR/resources/js/app.js"

echo ""
echo "✅ Installation complete!"
echo ""
echo "📋 Next steps:"
echo "   1. Run: npm install alpinejs @tailwindcss/forms tailwindcss"
echo "   2. Run: npm run build"
echo "   3. Check README.md for usage examples"
echo ""
echo "🎉 Happy coding!"
