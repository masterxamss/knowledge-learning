#!/bin/bash

# Create the database, run migrations, and load fixtures
echo "[INFO] Preparing database..."
php bin/console app:setup-database

# Run tests
echo "[INFO] Running tests with Composer..."
composer test

# Start Symfony server in background
echo "[INFO] Starting Symfony server on port 8000..."
(symfony server:start --port=8000 --no-tls > /dev/null 2>&1 &)

# Give the server a few seconds to boot
sleep 3

# Check if the Symfony server is running
if ! lsof -i :8000 >/dev/null; then
  echo "[ERROR] Symfony server failed to start on port 8000."
  exit 1
fi

# Start Messenger consumer in background
echo "[INFO] Starting Messenger consumer..."
nohup php bin/console messenger:consume -vv > /dev/null 2>&1 &

# Function to open browser based on OS
open_browser() {
  URL="http://localhost:8000"
  OS_TYPE="$(uname -s)"

  case "${OS_TYPE}" in
    Darwin*)
      # macOS
      echo "[INFO] Opening browser (macOS)..."
      open "$URL"
      ;;
    Linux*)
      # Detect if running under WSL
      if grep -qi microsoft /proc/version; then
        # WSL (Windows Subsystem for Linux)
        if command -v wslview > /dev/null; then
          echo "[INFO] Opening browser (WSL)..."
          wslview "$URL"
        else
          echo "[WARNING] 'wslview' not found. Install it with: sudo apt install wslu"
        fi
      else
        # Regular Linux
        if command -v xdg-open > /dev/null; then
          echo "[INFO] Opening browser (Linux)..."
          xdg-open "$URL"
        else
          echo "[WARNING] 'xdg-open' not found. Cannot open browser automatically."
        fi
      fi
      ;;
    *)
      echo "[WARNING] Unsupported OS. Please open $URL manually."
      ;;
  esac
}

open_browser
