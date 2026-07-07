# Google OAuth Setup Guide

## Prerequisites

1. Google Cloud Console account
2. A project created in Google Cloud Console

## Steps to Get Google OAuth Credentials

### 1. Create OAuth Credentials

1. Go to [Google Cloud Console](https://console.cloud.google.com/apis/credentials)
2. Select your project
3. Click "Create Credentials" → "OAuth client ID"
4. Application type: "Web application"
5. Name: "Walas App"
6. Authorized redirect URIs: `https://your-domain.com/auth/google/callback`
7. Click "Create"
8. Copy the **Client ID** and **Client Secret**

### 2. Configure Environment Variables

Add these to your `.env` file:

```env
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
```

### 3. Enable Google+ API

1. Go to [Google Cloud Console API Library](https://console.cloud.google.com/apis/library)
2. Search for "Google+ API"
3. Click on it and click "Enable"

## System Architecture

### Data Flow

```
┌─────────────────────────────────────────────────────────────┐
│                      USER REGISTRATION                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  User clicks "Login dengan Google"                         │
│           │                                                 │
│           ▼                                                 │
│  ┌─────────────────┐                                       │
│  │ Google OAuth    │◄─────── User grants permission         │
│  │ Callback        │                                       │
│  └────────┬────────┘                                       │
│           │                                                 │
│           ▼                                                 │
│  ┌─────────────────────────────────────────────────┐       │
│  │ Check: User exists with Google ID?               │       │
│  └─────────────────────────────────────────────────┘       │
│           │                    │                            │
│      YES  │                    │  NO                        │
│           ▼                    ▼                            │
│  ┌─────────────────┐  ┌─────────────────┐                  │
│  │ Login directly  │  │ Check email exists?│                 │
│  │ Redirect to     │  └────────┬────────┘                  │
│  │ dashboard       │           │                           │
│  └─────────────────┘      YES  │                    NO     │
│                               ▼                            │
│                    ┌─────────────────┐                      │
│                    │ Link Google ID  │                      │
│                    │ Redirect to     │                      │
│                    │ dashboard       │                      │
│                    └─────────────────┘                      │
│                               │                           │
│                               ▼                           │
│                    ┌─────────────────┐                      │
│                    │ Show Setup Page │◄── User selects     │
│                    │ (Organization)  │    or creates      │
│                    └────────┬────────┘    school          │
│                             │                             │
│                             ▼                             │
│                    ┌─────────────────┐                      │
│                    │ Create User +   │                      │
│                    │ Organization   │                      │
│                    │ Redirect to     │                      │
│                    │ dashboard       │                      │
│                    └─────────────────┘                      │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Database Schema

#### Organizations Table

| Field       | Type      | Description                    |
|-------------|-----------|--------------------------------|
| id          | bigint    | Primary key                    |
| name        | string    | School/Organization name       |
| slug        | string    | URL-friendly identifier (unique)|
| type        | enum      | sd, smp, sma, smk, others      |
| city        | string    | City location                   |
| address     | string    | Full address (nullable)         |
| phone       | string    | Contact phone (nullable)        |
| email       | string    | Contact email (nullable)        |
| logo        | string    | Logo URL (nullable)             |
| status      | enum      | active, inactive, pending, suspended |
| created_at  | timestamp | Registration date               |
| updated_at  | timestamp | Last update                     |

#### Users Table (Updated)

| Field            | Type       | Description                         |
|------------------|------------|-------------------------------------|
| id               | bigint     | Primary key                         |
| name             | string     | Full name                           |
| email            | string     | Email (unique)                      |
| google_id        | string     | Google OAuth ID (nullable)          |
| avatar           | string     | Profile picture URL (nullable)       |
| organization_id  | bigint FK  | Reference to organizations table     |
| role             | string     | super_admin, admin, walas           |
| phone            | string     | Phone number (nullable)             |
| email_verified_at| timestamp  | Email verification date             |
| is_active        | boolean    | Account active status               |
| ...              | ...        | Other existing fields               |

### User Roles

| Role        | Description                              |
|-------------|-----------------------------------------|
| super_admin | Walas Platform administrator            |
| admin       | School-level administrator              |
| walas       | Homeroom teacher (default)              |

### Super Admin Dashboard

Access: `/admin/dashboard`

Features:
- View all registered organizations
- Filter by status, type, search
- View organization details
- Manage organization status
- View all users per organization

## Testing Locally

1. Add to `/etc/hosts` (for local testing):
   ```
   127.0.0.1  walas.test
   ```

2. Use ngrok for callback URL:
   ```
   ngrok http 8000
   ```

3. Set in `.env`:
   ```
   APP_URL=http://walas.test:8000
   GOOGLE_CLIENT_ID=your-ngrok-client-id
   GOOGLE_CLIENT_SECRET=your-ngrok-secret
   ```

## Production Checklist

- [ ] Set correct `APP_URL` (HTTPS required)
- [ ] Add callback URL to Google Console
- [ ] Use environment variables for credentials
- [ ] Enable Google+ API
- [ ] Test with test user first
- [ ] Set up logging for debugging
