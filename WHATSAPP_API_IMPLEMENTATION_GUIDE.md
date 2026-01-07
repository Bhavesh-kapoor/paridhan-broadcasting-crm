# WhatsApp API Implementation & Testing Guide

## 📋 Table of Contents
1. [Prerequisites](#prerequisites)
2. [Implementation Steps](#implementation-steps)
3. [Configuration](#configuration)
4. [Testing Guide](#testing-guide)
5. [Troubleshooting](#troubleshooting)
6. [API Reference](#api-reference)

---

## Prerequisites

### Required Information
Before starting, gather the following from your WebPay Services dashboard (http://meta.webpayservices.in):

- ✅ **API Key**: Found in My Profile section
- ✅ **Phone Number ID**: From WABA Channels section
- ✅ **WABA Number**: Your WhatsApp Business Account number
- ✅ **Template Name**: Approved message template name
- ✅ **WABA ID**: For template management (optional)

### System Requirements
- PHP 8.1+
- Laravel 12.x
- Composer
- MySQL/PostgreSQL
- Queue worker (Redis/Database)

---

## Implementation Steps

### Step 1: Install Dependencies

No additional packages needed - Laravel HTTP client is built-in.

### Step 2: Configure Services

**File**: `config/services.php`

Add WhatsApp configuration:

```php
'whatsapp' => [
    // API Key for authentication
    'api_key' => env('WHATSAPP_API_KEY'),

    // Bearer token (fallback to API key)
    'bearer_token' => env('WHATSAPP_BEARER_TOKEN', env('WHATSAPP_API_KEY')),

    // Full API endpoint URL
    'endpoint' => env('WHATSAPP_API_ENDPOINT'),

    // Phone Number ID from WABA Channels
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', '920609244473081'),

    // WABA Number
    'waba_number' => env('WHATSAPP_WABA_NUMBER', '919137315850'),

    // API Version (v23.0 per documentation)
    'api_version' => env('WHATSAPP_API_VERSION', 'v23.0'),

    // Default template name
    'template_name' => env('WHATSAPP_TEMPLATE_NAME', 'campaign_message_v2'),

    // Base URL
    'base_url' => env('WHATSAPP_BASE_URL', 'http://meta.webpayservices.in'),

    // WABA ID for template management
    'waba_id' => env('WHATSAPP_WABA_ID'),
],
```

### Step 3: Configure Logging

**File**: `config/logging.php`

Add campaign logging channels:

```php
'campaign_progress' => [
    'driver' => 'daily',
    'path' => storage_path('logs/campaign_progress.log'),
    'level' => 'debug',
    'days' => 30,
    'permission' => 0777,
    'replace_placeholders' => true,
],

'whatsapp_api_errors' => [
    'driver' => 'daily',
    'path' => storage_path('logs/whatsapp_api_errors.log'),
    'level' => 'error',
    'days' => 60,
    'permission' => 0777,
    'replace_placeholders' => true,
],
```

### Step 4: Update Environment Variables

**File**: `.env`

```env
# WhatsApp API Configuration
WHATSAPP_API_KEY=your-api-key-here
WHATSAPP_API_ENDPOINT=http://meta.webpayservices.in/v23.0/{phoneNumberId}/messages
WHATSAPP_PHONE_NUMBER_ID=920609244473081
WHATSAPP_WABA_NUMBER=919137315850
WHATSAPP_API_VERSION=v23.0
WHATSAPP_TEMPLATE_NAME=campaign_message_v2
WHATSAPP_BASE_URL=http://meta.webpayservices.in
```

**Replace**:
- `your-api-key-here` with your actual API key
- `{phoneNumberId}` with your actual phone number ID

### Step 5: Implement SendCampaignJob

**File**: `app/Jobs/SendCampaignJob.php`

Key features implemented:
- ✅ Dual authentication (Bearer token + X-API-KEY)
- ✅ HTTPS image validation
- ✅ Detailed error handling
- ✅ Meta policy violation detection (error 131049)
- ✅ Dual logging (campaign_progress + whatsapp_api_errors)
- ✅ Retry logic (3 attempts)
- ✅ Rate limiting (250ms delay between messages)
- ✅ Timeout (300 seconds)

### Step 6: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## Configuration

### Getting Your API Key

1. Login to http://meta.webpayservices.in
2. Navigate to **My Profile**
3. Find **API Key** section
4. Copy the API key
5. Update `.env` file

### Getting Phone Number ID

1. Login to http://meta.webpayservices.in
2. Navigate to **WABA Channels**
3. Find your WhatsApp Business Account
4. Copy the **PhoneNumber ID**
5. Update `.env` file

### Creating/Verifying Templates

1. Login to http://meta.webpayservices.in
2. Navigate to **WhatsApp Campaign → Config → Manage Template**
3. Create or verify your template:
   - **Name**: `campaign_message_v2` (or your custom name)
   - **Language**: English (en)
   - **Category**: Marketing/Utility
   - **Components**:
     - Header: Optional image
     - Body: `{{1}}` and `{{2}}` variables
     - Footer: Optional

4. Wait for **APPROVED** status
5. Update `WHATSAPP_TEMPLATE_NAME` in `.env`

---

## Testing Guide

### Test 1: Configuration Verification

```bash
php artisan tinker
```

```php
// Check configuration
config('services.whatsapp');

// Should output all WhatsApp settings
```

### Test 2: Start Queue Worker

**Terminal 1**:
```bash
php artisan queue:listen --tries=3 --timeout=300
```

Keep this running - it processes campaign jobs.

### Test 3: Start Development Server

**Terminal 2**:
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

### Test 4: Monitor Logs

**Terminal 3**:
```bash
# Watch campaign progress
Get-Content storage\logs\campaign_progress.log -Wait -Tail 50

# Or on Linux/Mac:
tail -f storage/logs/campaign_progress.log
```

### Test 5: Create Test Campaign

1. **Open Browser**: http://127.0.0.1:8000/sign-in
2. **Login** with your credentials
3. **Navigate**: Campaign Management → Create Campaign
4. **Fill Form**:
   - Name: "Test Campaign"
   - Subject: "Test Message"
   - Message: "This is a test"
   - Type: WhatsApp
   - Recipients: **Add ONLY 1 recipient** (important!)
5. **Save** campaign
6. **Send** campaign

### Test 6: Verify Results

#### Check Queue Worker (Terminal 1)
Look for:
```
Processing: App\Jobs\SendCampaignJob
```

#### Check Logs (Terminal 3)
**Success**:
```json
{
  "message": "Message sent successfully",
  "campaign_id": "xxx",
  "recipient_id": "xxx",
  "phone": "91xxxxxxxxxx"
}
```

**Error**:
```json
{
  "message": "WhatsApp API error",
  "error_code": "131049",
  "error_message": "..."
}
```

#### Check Dashboard
1. Login to http://meta.webpayservices.in
2. Go to: **WhatsApp Campaign → Report → API Report**
3. Check latest entry
4. Verify status (DELIVERED/FAILED)

---

## Troubleshooting

### Error: "WhatsApp API Key is not configured"

**Solution**:
```bash
# Check .env file
cat .env | grep WHATSAPP

# Ensure WHATSAPP_API_KEY is set
# Clear cache
php artisan config:clear
```

### Error: "Template not found"

**Solution**:
1. Verify template exists in dashboard
2. Check template name matches `.env`
3. Ensure template status is APPROVED
4. Update `WHATSAPP_TEMPLATE_NAME` in `.env`

### Error 131049: "Healthy ecosystem engagement"

**Cause**: Meta account quality issue

**Solution**:
1. Check Meta Business Suite for warnings
2. Review account quality rating
3. Contact WebPay Services support
4. Avoid sending spam messages
5. Wait for quality rating to improve

### Error: "Image URL is not HTTPS"

**Cause**: Campaign images must use HTTPS

**Solution**:
1. Upload images to CDN (Cloudinary, AWS S3)
2. Enable HTTPS on your server
3. Or skip images for now

### Queue Not Processing

**Solution**:
```bash
# Check if queue worker is running
Get-Process | Where-Object {$_.ProcessName -like "*php*"}

# Restart queue worker
php artisan queue:listen --tries=3 --timeout=300
```

### Messages Stuck in "Pending"

**Solution**:
1. Ensure queue worker is running
2. Check logs for errors
3. Verify API endpoint is correct
4. Test API credentials

---

## API Reference

### Authentication

**Method 1: Bearer Token** (Recommended)
```http
Authorization: Bearer your-api-key-here
Content-Type: application/json
```

**Method 2: X-API-KEY**
```http
X-API-KEY: your-api-key-here
Content-Type: application/json
```

### Send Message Endpoint

```http
POST http://meta.webpayservices.in/v23.0/{phoneNumberId}/messages
```

### Request Payload

```json
{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "919137315850",
  "type": "template",
  "template": {
    "name": "campaign_message_v2",
    "language": {
      "code": "en"
    },
    "components": [
      {
        "type": "header",
        "parameters": [
          {
            "type": "image",
            "image": {
              "link": "https://example.com/image.jpg"
            }
          }
        ]
      },
      {
        "type": "body",
        "parameters": [
          {
            "type": "text",
            "text": "Campaign Name"
          },
          {
            "type": "text",
            "text": "Campaign Message"
          }
        ]
      }
    ]
  }
}
```

### Success Response

```json
{
  "messaging_product": "whatsapp",
  "contacts": [
    {
      "input": "919137315850",
      "wa_id": "919137315850"
    }
  ],
  "messages": [
    {
      "id": "wamid.xxx"
    }
  ]
}
```

### Error Response

```json
{
  "error": {
    "message": "Error message",
    "type": "ErrorType",
    "code": 131049,
    "error_subcode": 33,
    "fbtrace_id": "xxx"
  }
}
```

### Common Error Codes

| Code | Meaning | Solution |
|------|---------|----------|
| 131049 | Meta policy violation | Contact support, check account quality |
| 100 | Invalid parameter | Verify template variables |
| 132000 | Template not found | Check template name and approval |
| 133016 | Message undeliverable | Verify phone number format |
| 135 | Rate limit exceeded | Add delays between messages |

---

## Testing Checklist

- [ ] API key configured in `.env`
- [ ] Phone number ID configured
- [ ] Template created and APPROVED
- [ ] Config cache cleared
- [ ] Queue worker running
- [ ] Development server running
- [ ] Test campaign created with 1 recipient
- [ ] Campaign sent successfully
- [ ] Logs show "Message sent successfully"
- [ ] Dashboard shows DELIVERED status
- [ ] No error 131049 in logs

---

## Production Deployment

### Before Going Live

1. **Verify Template**: Ensure template is approved
2. **Test Thoroughly**: Send to 5-10 test numbers
3. **Monitor Logs**: Check for any errors
4. **Account Quality**: Ensure no Meta warnings
5. **Rate Limiting**: Keep delays between messages
6. **HTTPS Images**: Use CDN for images
7. **Queue Worker**: Use supervisor/systemd for production
8. **Monitoring**: Set up log monitoring/alerts

### Production Queue Worker

Use Supervisor (Linux) or Task Scheduler (Windows):

```ini
[program:paridhan-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --tries=3 --timeout=300
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/logs/worker.log
```

### Recommended: Laravel Horizon

For better queue monitoring:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

---

## Support

### WebPay Services
- Dashboard: http://meta.webpayservices.in
- Support: Contact through dashboard

### Meta/WhatsApp
- Business Suite: https://business.facebook.com
- Documentation: https://developers.facebook.com/docs/whatsapp

### Laravel
- Documentation: https://laravel.com/docs
- Queue: https://laravel.com/docs/queues

---

**Last Updated**: January 7, 2026
**Version**: 1.0
**Status**: Production Ready ✅
