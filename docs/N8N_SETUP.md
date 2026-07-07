# Setup n8n WhatsApp Bot

## Prerequisites

1. n8n installed (Docker or local)
2. Fonnte account OR WhatsApp Business API
3. Walas project deployed

---

## 1. Setup n8n

### Option A: Docker
```bash
docker run -d \
  --name n8n \
  -p 5678:5678 \
  -v n8n_data:/home/node/.n8n \
  n8nio/n8n
```

### Option B: Local
```bash
npm install -g n8n
n8n start
```

---

## 2. Import Workflow

1. Buka n8n: http://localhost:5678
2. Click **+** → **Import from File**
3. Pilih file: `n8n/workflows/01-whatsapp-sender.json`
4. Aktifkan workflow dengan klik **Activate**

---

## 3. Setup Environment Variables di n8n

1. Settings → Variables
2. Tambah variabel:
   - `FONNTE_API_KEY` = your-fonnte-api-key

---

## 4. Setup Fonnte (WhatsApp Gateway)

1. Daftar di https://fonnte.com
2. Dapatkan API Key
3. Scan QR Code untuk connect WhatsApp

---

## 5. Konfigurasi Laravel

Update `.env`:

```env
# n8n Webhook URL
WA_GATEWAY_URL=http://localhost:5678
WA_GATEWAY_TOKEN=your-n8n-webhook-token

# Fonnte (didalam n8n)
WA_API_URL=https://mu.fonnte.com/api/send
WA_API_TOKEN=your-fonnte-api-key
```

Untuk production:
```env
WA_GATEWAY_URL=https://n8n.your-domain.com
WA_GATEWAY_TOKEN=your-secure-token
```

---

## 6. Test WhatsApp

```bash
# Via Laravel Tinker
php artisan tinker

# Test kirim pesan
app(\App\Services\WhatsAppService::class)->send(
    '6281234567890',
    'Test pesan dari Walas Pro! 👋',
    ['user_id' => 1, 'recipient_name' => 'Admin']
);
```

---

## Webhook Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `http://localhost:5678/webhook/whatsapp-send` | POST | Kirim pesan WA |
| `http://localhost:5678/webhook/whatsapp-incoming` | POST | Terima pesan WA masuk |

---

## Troubleshooting

### Pesan tidak terkirim?
1. Cek n8n workflow aktif
2. Cek Fonnte API key valid
3. Cek WhatsApp terhubung ke Fonnte
4. Cek logs di n8n

### Error 401?
1. Pastikan `WA_GATEWAY_TOKEN` cocok dengan n8n webhook settings
2. Cek Authorization header

---

## API Format

### Send Message Request
```json
POST /webhook/whatsapp-send
{
  "phone": "6281234567890",
  "message": "Isi pesan di sini",
  "token": "your-n8n-webhook-token"
}
```

### Response
```json
{
  "success": true,
  "message": "Message sent successfully"
}
```
