#!/bin/bash

# Test Telegram Webhook Endpoint
# This script simulates a Telegram webhook request for testing purposes

echo "🧪 Testing Telegram Webhook Endpoint"
echo "====================================="
echo ""

# Configuration
WEBHOOK_URL="http://localhost:8000/api/telegram/webhook"
WEBHOOK_TOKEN="test-webhook-token"

# Test 1: /start command
echo "Test 1: Testing /start command"
echo "-------------------------------"
curl -X POST "$WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -H "X-Telegram-Bot-Api-Secret-Token: $WEBHOOK_TOKEN" \
  -d '{
    "message": {
      "message_id": 1,
      "from": {
        "id": 123456789,
        "first_name": "Test User"
      },
      "chat": {
        "id": 123456789,
        "type": "private"
      },
      "date": 1704960000,
      "text": "/start"
    }
  }' | python3 -m json.tool 2>/dev/null || echo "Failed to parse JSON"

echo ""
echo ""

# Test 2: /help command
echo "Test 2: Testing /help command"
echo "------------------------------"
curl -X POST "$WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -H "X-Telegram-Bot-Api-Secret-Token: $WEBHOOK_TOKEN" \
  -d '{
    "message": {
      "message_id": 2,
      "from": {
        "id": 123456789,
        "first_name": "Test User"
      },
      "chat": {
        "id": 123456789,
        "type": "private"
      },
      "date": 1704960000,
      "text": "/help"
    }
  }' | python3 -m json.tool 2>/dev/null || echo "Failed to parse JSON"

echo ""
echo ""

# Test 3: Data input
echo "Test 3: Testing data input"
echo "--------------------------"
curl -X POST "$WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -H "X-Telegram-Bot-Api-Secret-Token: $WEBHOOK_TOKEN" \
  -d '{
    "message": {
      "message_id": 3,
      "from": {
        "id": 123456789,
        "first_name": "Test User"
      },
      "chat": {
        "id": 123456789,
        "type": "private"
      },
      "date": 1704960000,
      "text": "Andi|2026-01-12|3.5|15000000|120|5000|1200"
    }
  }' | python3 -m json.tool 2>/dev/null || echo "Failed to parse JSON"

echo ""
echo ""

# Test 4: Invalid format
echo "Test 4: Testing invalid format"
echo "-------------------------------"
curl -X POST "$WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -H "X-Telegram-Bot-Api-Secret-Token: $WEBHOOK_TOKEN" \
  -d '{
    "message": {
      "message_id": 4,
      "from": {
        "id": 123456789,
        "first_name": "Test User"
      },
      "chat": {
        "id": 123456789,
        "type": "private"
      },
      "date": 1704960000,
      "text": "invalid data"
    }
  }' | python3 -m json.tool 2>/dev/null || echo "Failed to parse JSON"

echo ""
echo ""

# Test 5: Invalid webhook token
echo "Test 5: Testing invalid webhook token"
echo "--------------------------------------"
curl -X POST "$WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -H "X-Telegram-Bot-Api-Secret-Token: wrong-token" \
  -d '{
    "message": {
      "message_id": 5,
      "from": {
        "id": 123456789,
        "first_name": "Test User"
      },
      "chat": {
        "id": 123456789,
        "type": "private"
      },
      "date": 1704960000,
      "text": "/start"
    }
  }' | python3 -m json.tool 2>/dev/null || echo "Failed to parse JSON"

echo ""
echo ""
echo "✅ All tests completed!"
echo ""
echo "📝 Notes:"
echo "   - Make sure the server is running (php artisan serve)"
echo "   - Check storage/logs/laravel.log for detailed logs"
echo "   - Update WEBHOOK_TOKEN in this script to match your .env"
