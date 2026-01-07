# 📱 How to Broadcast Messages - Step by Step Guide

This guide explains how to broadcast WhatsApp messages using the Paridharan CRM system.

## 🎯 Overview

The broadcasting system allows you to:
- Create campaigns with messages
- Select recipients (Exhibitors or Visitors)
- Send WhatsApp messages to multiple recipients at once
- Track sending progress and status

---

## 📋 Prerequisites

Before broadcasting messages, ensure you have:

1. ✅ **WhatsApp API Configuration** - API key and credentials
2. ✅ **Queue Worker Running** - Required to process message sending
3. ✅ **Contacts Added** - Exhibitors or Visitors in the system
4. ✅ **WhatsApp Template Approved** - Template must be approved in Meta dashboard

---

## 🚀 Step-by-Step Process

### Step 1: Configure WhatsApp API (First Time Only)

#### 1.1 Get Your API Credentials

1. Login to **WebPay Services Dashboard**: http://meta.webpayservices.in
2. Go to **My Profile** → Copy your **API Key**
3. Go to **WABA Channels** → Copy your **Phone Number ID**
4. Note your **WABA Number** (WhatsApp Business Account number)

#### 1.2 Update Environment Variables

Edit your `.env` file and add:

```env
# WhatsApp API Configuration
WHATSAPP_API_KEY=your-api-key-here
WHATSAPP_BEARER_TOKEN=your-api-key-here
WHATSAPP_API_ENDPOINT=http://meta.webpayservices.in/v23.0/{phoneNumberId}/messages
WHATSAPP_PHONE_NUMBER_ID=920609244473081
WHATSAPP_WABA_NUMBER=919137315850
WHATSAPP_API_VERSION=v23.0
WHATSAPP_TEMPLATE_NAME=campaign_message_v2
WHATSAPP_BASE_URL=http://meta.webpayservices.in
```

**Important**: Replace `{phoneNumberId}` in the endpoint with your actual Phone Number ID.

#### 1.3 Clear Configuration Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

### Step 2: Start Queue Worker (Required!)

**The queue worker MUST be running to send messages!**

Open a new terminal and run:

```bash
php artisan queue:listen --tries=3 --timeout=300
```

**Keep this terminal open** - it processes all campaign jobs in the background.

**Alternative**: Use Laravel Horizon (if installed):
```bash
php artisan horizon
```

---

### Step 3: Create a Campaign

#### 3.1 Access Campaign Management

1. Open your browser: **http://localhost:8000** (or your server URL)
2. Login with admin credentials
3. Navigate to: **Campaign Management** → **Create Campaign**

#### 3.2 Fill Campaign Details

- **Name**: Campaign name (e.g., "New Product Launch")
- **Subject**: Brief subject line
- **Message**: Your message content (will be sent as {{2}} in template)
- **Type**: Select "WhatsApp"
- **Image** (Optional): Upload an image (must be HTTPS URL in production)

#### 3.3 Select Recipients

- **Exhibitors**: Select exhibitor contacts
- **Visitors**: Select visitor contacts
- You can select multiple recipients

#### 3.4 Save Campaign

Click **Save** - Campaign will be created with status "Draft"

---

### Step 4: Send/Broadcast the Campaign

#### 4.1 Go to Campaigns List

Navigate to: **Campaign Management** → **Campaigns List**

#### 4.2 Send Campaign

1. Find your campaign in the list
2. Click **Send** button (or use the send action)
3. Confirm the action

#### 4.3 What Happens Next

- Campaign status changes from "Draft" to "Sent"
- `SendCampaignJob` is dispatched to the queue
- Queue worker processes the job
- Messages are sent to all recipients one by one
- Each recipient status is updated (sent/failed/pending)

---

### Step 5: Monitor Progress

#### 5.1 Check Campaign Progress

The system provides a progress endpoint:
- **URL**: `/admin/campaigns/{id}/progress`
- Returns: `total`, `sent`, `failed`, `pending`, `percent`

#### 5.2 View Logs

**Campaign Progress Log**:
```bash
# Windows PowerShell
Get-Content storage\logs\campaign_progress.log -Wait -Tail 50

# Linux/Mac
tail -f storage/logs/campaign_progress.log
```

**WhatsApp API Errors Log**:
```bash
Get-Content storage\logs\whatsapp_api_errors.log -Wait -Tail 50
```

#### 5.3 Check Queue Worker

Look at your queue worker terminal for:
- `Processing: App\Jobs\SendCampaignJob`
- Success/error messages

---

## 🔍 Understanding the Broadcasting Flow

```
1. User creates campaign → Status: "Draft"
2. User clicks "Send" → CampaignService::sendCampaign()
3. Campaign status → "Sent"
4. SendCampaignJob dispatched → Queue
5. Queue Worker picks up job → SendCampaignJob::handle()
6. For each recipient:
   - Build WhatsApp API payload
   - Send HTTP POST to WhatsApp API
   - Update recipient status (sent/failed)
   - Add 250ms delay (rate limiting)
7. Log results → campaign_progress.log
```

---

## 📊 Campaign Statuses

- **Draft**: Campaign created but not sent
- **Sent**: Campaign has been sent (messages may still be processing)
- **Completed**: All messages processed (sent or failed)

---

## 📱 Recipient Statuses

- **Pending**: Not yet sent
- **Sent**: Successfully sent to WhatsApp API
- **Failed**: Failed to send (check logs for reason)

---

## ⚙️ API Configuration Details

### Message Template Structure

The system uses WhatsApp template messages with:
- **Header** (Optional): Image
- **Body**: 
  - `{{1}}` = Campaign Name
  - `{{2}}` = Campaign Message

### Rate Limiting

- **Delay**: 250ms between messages (4 messages/second)
- **Reason**: Avoid Meta spam detection
- **Meta Limit**: Up to 80 messages/second (but we're conservative)

---

## 🛠️ Troubleshooting

### Problem: Messages Not Sending

**Check 1**: Is queue worker running?
```bash
# Check if queue worker is running
Get-Process | Where-Object {$_.ProcessName -like "*php*"}
```

**Check 2**: Check logs for errors
```bash
Get-Content storage\logs\campaign_progress.log -Tail 100
```

**Check 3**: Verify API configuration
```bash
php artisan tinker
>>> config('services.whatsapp');
```

### Problem: "WhatsApp API Key is not configured"

**Solution**:
1. Check `.env` file has `WHATSAPP_API_KEY`
2. Run: `php artisan config:clear`
3. Restart queue worker

### Problem: Error 131049 (Meta Policy Violation)

**Cause**: Account quality issue with Meta

**Solution**:
1. Check Meta Business Suite for warnings
2. Review account quality rating
3. Contact WebPay Services support
4. Wait for quality rating to improve

### Problem: Image URL Not HTTPS

**Solution**:
- Use HTTPS URLs for images
- Upload to CDN (Cloudinary, AWS S3)
- Or skip images for now

### Problem: Template Not Found

**Solution**:
1. Verify template exists in Meta dashboard
2. Check template name matches `WHATSAPP_TEMPLATE_NAME` in `.env`
3. Ensure template status is **APPROVED**

---

## 📝 Quick Reference

### Create and Send Campaign (CLI Alternative)

If you want to test via code:

```php
// In tinker or controller
use App\Services\CampaignService;
use App\Models\Campaign;

$campaignService = app(CampaignService::class);

// Create campaign
$campaign = $campaignService->createCampaign([
    'name' => 'Test Campaign',
    'subject' => 'Test Subject',
    'message' => 'Test message content',
    'type' => 'whatsapp',
]);

// Add recipients (contact IDs)
$campaignService->addRecipientsToCampaign($campaign->id, [1, 2, 3]);

// Send campaign
$campaignService->sendCampaign($campaign->id);
```

### Check Campaign Progress

```php
// Via API endpoint
GET /admin/campaigns/{id}/progress

// Response:
{
    "total": 100,
    "sent": 85,
    "failed": 5,
    "pending": 10,
    "percent": 90
}
```

---

## ✅ Checklist Before Broadcasting

- [ ] WhatsApp API key configured in `.env`
- [ ] Phone Number ID configured
- [ ] Template created and APPROVED in Meta dashboard
- [ ] Config cache cleared (`php artisan config:clear`)
- [ ] Queue worker running (`php artisan queue:listen`)
- [ ] Contacts (Exhibitors/Visitors) added to system
- [ ] Campaign created with recipients
- [ ] Ready to send!

---

## 🎯 Best Practices

1. **Test First**: Always test with 1-2 recipients before bulk sending
2. **Monitor Logs**: Watch logs during first broadcast
3. **Rate Limiting**: Don't modify the 250ms delay unless necessary
4. **HTTPS Images**: Always use HTTPS for campaign images
5. **Template Approval**: Ensure template is approved before sending
6. **Account Quality**: Maintain good Meta account quality rating
7. **Queue Worker**: Always keep queue worker running in production

---

## 📞 Support

- **WebPay Services Dashboard**: http://meta.webpayservices.in
- **Meta Business Suite**: https://business.facebook.com
- **Laravel Documentation**: https://laravel.com/docs

---

**Last Updated**: January 2026
**Version**: 1.0

