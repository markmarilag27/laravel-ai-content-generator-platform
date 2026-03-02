#!/usr/bin/env bash

# 1. Clean up any stale logs
touch storage/logs/reverb.log storage/logs/worker.log

echo "⏳ Starting Reverb..."
# We use (command &) and disown to completely detach it from the current shell
(php artisan reverb:start --host=0.0.0.0 --port=6001 >> storage/logs/reverb.log 2>&1 &)
disown -a

echo "⏳ Starting Queue Worker..."
(php artisan queue:work --tries=3 --backoff=3 >> storage/logs/worker.log 2>&1 &)
disown -a

echo "✅ Services started in the background."
echo "View Reverb logs: tail -f storage/logs/reverb.log"
echo "View Worker logs: tail -f storage/logs/worker.log"
