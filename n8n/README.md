# WaliKelas Pro - n8n Integration

## Quick Start

### 1. Start n8n with Docker

```bash
cd n8n
cp .env.example .env
# Edit .env with your credentials
docker-compose up -d
```

### 2. Access n8n Dashboard

Open http://localhost:5678 and login with credentials from .env

### 3. Import Workflows

1. Go to **Settings** → **Import from File**
2. Select each JSON file from `n8n/workflows/` directory
3. Activate each workflow

## Workflows Overview

| Workflow | File | Description |
|----------|------|-------------|
| WhatsApp Sender | `01-whatsapp-sender.json` | Send WhatsApp messages via webhook |
| Attendance Magic Link | `02-attendance-magic-link.json` | Create session & send link |
| Bulk Attendance Notify | `03-bulk-attendance-notify.json` | Send bulk parent notifications |
| Violation Warning | `04-violation-warning.json` | Send violation alerts |
| Scheduled Attendance | `05-scheduled-attendance.json` | Auto-generate daily links |
| WhatsApp Incoming | `06-whatsapp-incoming.json` | Handle incoming WA commands |
| Exam Monitor | `07-exam-monitor.json` | CBT/ExamBrowser integration |

## Webhook Endpoints

After activating workflows, your endpoints will be:

```
POST http://localhost:5678/webhook/whatsapp-send
POST http://localhost:5678/webhook/attendance-magic-link
POST http://localhost:5678/webhook/attendance-bulk-notify
POST http://localhost:5678/webhook/violation-warning
POST http://localhost:5678/webhook/whatsapp-incoming
POST http://localhost:5678/webhook/exam-start
```

## API Endpoints

### Send WhatsApp Message

```bash
curl -X POST http://localhost:5678/webhook/whatsapp-send \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "6281234567890",
    "message": "Test message",
    "token": "your-n8n-secret"
  }'
```

### Generate Attendance Link

```bash
curl -X POST http://localhost:5678/webhook/attendance-magic-link \
  -H "Content-Type: application/json" \
  -d '{
    "class_id": 1,
    "phone": "6281234567890",
    "recipient_name": "Seksi Kehadiran"
  }'
```

## Rate Limits

- **Fonnte**: Max 100 messages/minute
- **n8n Delay**: Set 1.5-2 second delay between bulk sends
- **Queue Processing**: Use background jobs for large batches

## Troubleshooting

### Webhook not working
- Check if workflow is **activated**
- Verify webhook URL in `.env`
- Check n8n logs: `docker logs walas-n8n`

### WhatsApp not sending
- Verify Fonnte API key is valid
- Check phone number format (628...)
- Ensure sufficient Fonnte credits

### Attendance link not generating
- Verify WALAS_API_KEY matches Laravel config
- Check if class_id exists in database
