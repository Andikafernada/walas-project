# N8N WhatsApp Automation Workflow

## Overview

Workflows ini menghubungkan WaliKelas Pro dengan WhatsApp untuk otomatisasi absensi dan notifikasi.

## Prerequisites

1. **N8N Instance** - Self-hosted atau cloud.n8n.io
2. **WhatsApp Gateway** - Fonnte/Wablas/Wartel/meWhatsAPI
3. **Webhook URL** - Dari WaliKelas Pro

## WhatsApp Gateway Setup

### Option 1: Fonnte (Recommended)
```bash
# Get your API key from fonnte.com
FOXNTE_API_KEY=your_fonnte_key
FOXNTE_URL=https://mu.fonnte.com/api/send
```

### Option 2: Wablas
```bash
WABLAS_URL=https://node099.web walas.com/send
WABLAS_TOKEN=your_token
```

## N8N Webhooks

### 1. WhatsApp Send Webhook
```
POST {{base_url}}/webhook/whatsapp/send
Content-Type: application/json

{
  "phone": "628123456789",
  "message": "Isi pesan di sini"
}
```

### 2. Reply Webhook (Incoming Messages)
```
POST {{base_url}}/webhook/whatsapp/incoming
Content-Type: application/json

{
  "from": "628123456789",
  "body": "ABSENSI",
  "id": "message_id"
}
```

## Automation Workflows

### Workflow 1: Magic Link Sender
```
Trigger: Webhook (POST /webhook/walas/magic-link)
    │
    ├─→ HTTP Request (GET students by class)
    ├─→ Set node (format message)
    ├─→ HTTP Request (Send WhatsApp to Seksi Kehadiran)
    └─→ Response (200 OK)
```

### Workflow 2: Attendance Report Sender
```
Trigger: Webhook (POST /webhook/walas/attendance-report)
    │
    ├─→ Loop Over Students
    │   ├─→ HTTP Request (Get student data)
    ├─→ Set (Format message per student)
    ├─→ Delay (rate limit 1s)
    ├─→ HTTP Request (Send WhatsApp)
    └─→ Response (Done)
```

### Workflow 3: Incoming Commands
```
Trigger: Webhook (WhatsApp incoming)
    │
    ├─→ Switch (parse command)
    │   ├─ "MENU" → Reply menu
    │   ├─ "ABSENSI" → Reply attendance status
    │   ├─ "HELP" → Reply help
    │   └─ Default → Reply unknown command
    │
    └─→ WhatsApp Reply
```

## API Integration

### WaliKelas Pro → N8N Webhook

```php
// In WaliKelas Pro Controller
public function sendWhatsApp(string $phone, string $message): bool
{
    $webhookUrl = config('services.n8n.webhook_url') . '/whatsapp/send';

    $response = Http::timeout(10)->post($webhookUrl, [
        'phone' => $phone,
        'message' => $message,
        'token' => config('services.n8n.secret_token'),
    ]);

    return $response->successful();
}
```

## Environment Variables

```env
# N8N Configuration
N8N_WEBHOOK_URL=https://n8n.your-domain.com/webhook
N8N_SECRET_TOKEN=your-secret-token
N8N_WALAS_KEY=your-walas-api-key
```

## Testing

```bash
# Test webhook locally
ngrok http 5678

# Test with curl
curl -X POST http://localhost:5678/webhook/walas/magic-link \
  -H "Content-Type: application/json" \
  -d '{"class_id": 1, "token": "your-token"}'
```

## Rate Limiting

- Max 100 messages per minute per device
- Use delay nodes in N8N for bulk sending
- Implement exponential backoff for failures
