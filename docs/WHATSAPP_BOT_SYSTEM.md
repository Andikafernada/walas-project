# WhatsApp Bot System - Walas Pro

## Konsep

```
┌─────────────────────────────────────────────────────────────────┐
│           WALI KELAS WhatsApp Bot System                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  🎯 TUJUAN:                                                     │
│  Setiap Wali Kelas punya WhatsApp Bot PERSONAL untuk auto-kirim    │
│  magic link absensi ke Seksi Absensi berdasarkan JADWAL          │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘

ALUR LENGKAP:

┌──────────┐    ┌──────────────┐    ┌──────────────┐
│  WALAS   │───►│  SCAN QR    │───►│ WhatsApp    │
│  LOGIN   │    │  WhatsApp   │    │  TERHUBUNG │
└──────────┘    └──────────────┘    └──────────────┘
                                            │
                                            ▼
┌──────────────────────────────────────────────────────────┐
│              SETUP JADWAL                                │
│                                                          │
│  Senin 07:00 ──► Mata Pelajaran: Matematika             │
│  Selasa 07:00 ──► Mata Pelajaran: Bahasa Indonesia      │
│  dll...                                                │
└──────────────────────────────────────────────────┬─────┘
                                                   │
                                                   ▼
┌──────────────────────────────────────────────────────────┐
│              SCHEDULE AUTOMATION                       │
│                                                          │
│  Setiap 1 menit, sistem cek:                           │
│  "Apakah sekarang ada jadwal?"                          │
│                                                          │
│  Kalau ADA:                                            │
│  1. Create attendance session                         │
│  2. Kirim magic link ke Seksi Absensi via WhatsApp    │
│  3. Done! ✨                                        │
└──────────────────────────────────────────────────────────┘
```

---

## Setup

### 1. Koneksikan WhatsApp

Buka: `/whatsapp-bot`

1. Klik **Generate QR**
2. Scan QR dengan WhatsApp personal Anda
3. Status berubah menjadi **Connected** ✅

### 2. Setup Jadwal

Setup jadwal di menu **Jadwal** untuk setiap kelas.

Format jadwal:
```
Hari: Senin
Jam: 07:00 - 08:30
Mapel: Matematika
```

### 3. Sistem Auto-Run

Jalankan scheduler:
```bash
php artisan schedule:work
```

Atau via cron:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Cara Kerja

### Automasi:

1. Scheduler jalan setiap 1 menit
2. Cek: "Apakah sekarang ada jadwal?"
3. Jika ADA:
   - Create attendance session
   - Get Seksi Absensi dari struktur organisasi
   - Kirim magic link via WhatsApp user
4. Seksi Absensi terima link
5. Seksi Absensi klik link & isi absensi

### Kirim Manual:

Dashboard → Pilih Kelas → Attendance → Generate Magic Link

---

## Struktur Organisasi (Seksi Absensi)

Pastikan ada Seksi Absensi di struktur organisasi kelas:

```
Kelas: X IPA 1
├── Ketua Kelas: Budi
├── Seksi Absensi: Siti ←Ini yang terima magic link
├── Seksi Ketertiban: Andi
└── Bendahara: Rina
```

### Setup Seksi Absensi:

1. Buka menu **Organisasi Kelas**
2. Tambah struktur dengan:
   - Position: `seksi_absensi` atau `seksi_kehadiran`
   - Student: Pilih siswa yang jadi Seksi Absensi
   - Pastikan siswa punya **No. WhatsApp orang tua**

---

## WhatsApp Session

Setiap user punya WhatsApp session sendiri:

| Field | Keterangan |
|-------|-------------|
| `user_id` | Owner WhatsApp ini |
| `phone` | Nomor WhatsApp |
| `status` | disconnected, connecting, connected |
| `session_data` | Data session terenkripsi |

---

## Error Handling

| Error | Solusi |
|-------|--------|
| WhatsApp disconnected | Scan QR lagi |
| No Seksi Absensi | Setup struktur organisasi |
| No schedule | Setup jadwal kelas |
| Message failed | Cek WhatsApp gateway |

---

## WhatsApp Gateway

Sistem mengirim pesan via:

1. **n8n** (dikonfigurasi di `.env`)
2. **Fonnte API** (fallback)

---

## Command

```bash
# Auto send attendance link
php artisan walas:auto-attendance

# Process WhatsApp queue
php artisan wa:process --limit=50
```
