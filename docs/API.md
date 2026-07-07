# WaliKelas Pro - API Documentation

## Overview

WaliKelas Pro provides a REST API for external integrations including CBT systems, ExamBrowser, and school management software.

## Authentication

All API requests require a Bearer token in the Authorization header:

```
Authorization: Bearer your_api_token_here
```

API tokens can be created in the WaliKelas Pro dashboard under **Settings > API Tokens**.

## Base URL

```
Production: https://api.walaskelas.pro/v1
Staging: https://staging-api.walaskelas.pro/v1
Local: http://localhost:8000/api/v1
```

## Rate Limiting

- Default: 60 requests per minute
- Rate limit headers are included in every response:
  - `X-RateLimit-Limit`: Maximum requests per window
  - `X-RateLimit-Remaining`: Remaining requests
  - `X-RateLimit-Reset`: Reset timestamp

## Response Format

All responses follow a consistent JSON format:

### Success Response
```json
{
  "success": true,
  "data": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "error": "Error message"
}
```

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Rate Limited |
| 500 | Server Error |

---

## Endpoints

### Attendance

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/attendance` | List attendances |
| POST | `/attendance` | Submit attendance |
| GET | `/attendance/summary` | Get monthly summary |

### Students

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/students/class/{id}` | List students by class |
| GET | `/students/{id}` | Get student details |
| GET | `/students/{id}/photo` | Get student photo |

### Exam Monitoring

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/exam/start` | Start exam session |
| POST | `/exam/log` | Log exam event |
| POST | `/exam/end` | End exam session |
| GET | `/exam/status/{id}` | Get exam status |

### Reports

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/reports/student/{id}/attendance` | Student attendance report |
| GET | `/reports/class/{id}/attendance` | Class attendance report |
| GET | `/reports/student/{id}/violations` | Student violation report |

---

## Example Requests

### List Students
```bash
curl -X GET "https://api.walaskelas.pro/v1/students/class/1" \
  -H "Authorization: Bearer your_token_here"
```

### Submit Attendance
```bash
curl -X POST "https://api.walaskelas.pro/v1/attendance" \
  -H "Authorization: Bearer your_token_here" \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 1,
    "date": "2024-01-15",
    "status": "hadir"
  }'
```

### Start Exam Session
```bash
curl -X POST "https://api.walaskelas.pro/v1/exam/start" \
  -H "Authorization: Bearer your_token_here" \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": "EXAM-2024-001",
    "exam_name": "UAS Matematika",
    "class_id": 1,
    "start_time": "2024-01-15T08:00:00Z"
  }'
```

---

## Webhooks

WaliKelas Pro can also receive webhooks for integrations:

### WhatsApp Incoming
```
POST /webhook/whatsapp/incoming
Content-Type: application/json

{
  "from": "628123456789",
  "body": "MENU",
  "id": "message_id"
}
```

---

## Support

- Email: api@walaskelas.pro
- Docs: https://docs.walaskelas.pro/api
