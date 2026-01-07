# 🧪 How to Test Campaign Broadcasting

## Quick Test Steps

### Step 1: Ensure Queue Worker is Running

**Open a terminal** and run:
```bash
php artisan queue:listen --tries=3 --timeout=300
```

**Keep this terminal open** - it must be running for messages to send!

---

### Step 2: Create a Test Campaign

1. **Open Browser**: http://localhost:8000
2. **Login** with your admin credentials
3. **Navigate to**: Campaign Management → Create Campaign
4. **Fill the form**:
   - **Name**: "Test Campaign - [Your Name]"
   - **Subject**: "Test Message"
   - **Message**: "This is a test message from Paridharan CRM"
   - **Type**: Select "WhatsApp"
   - **Image**: Leave empty (will use test image automatically) OR paste: `https://d8iqbmvu05s9c.cloudfront.net/ajprhqgqg1otf7d5sm7u3brf27gv`
5. **Click Save**

---

### Step 3: Add Test Recipients

**Important**: Start with **1 recipient** for testing!

1. **After saving**, you'll see the campaign details
2. **Select Recipients**:
   - Choose **Exhibitors** or **Visitors** tab
   - Select **1 contact** (use a test phone number you can check)
   - Click to add recipient
3. **Verify**: Check that recipient is added

---

### Step 4: Send the Campaign

1. **Go to Campaigns List**: Campaign Management → Campaigns
2. **Find your test campaign**
3. **Click the "Send" button** (green paper plane icon)
4. **Confirm** if prompted

---

### Step 5: Monitor Progress

#### A. Watch Queue Worker Terminal

You should see:
```
Processing: App\Jobs\SendCampaignJob
App\Jobs\SendCampaignJob ...... DONE
```

#### B. Check Campaign Progress Logs

**Open another terminal** and run:
```bash
Get-Content storage\logs\campaign_progress.log -Wait -Tail 20
```

**Look for**:
- ✅ `"Message sent successfully"` - Success!
- ✅ `"using default test image for local development"` - Test image working
- ❌ `"WhatsApp API error"` - Check error details
- ❌ `"error_code": 132012` - Template/image issue

#### C. Check WhatsApp API Errors

```bash
Get-Content storage\logs\whatsapp_api_errors.log -Tail 20
```

---

### Step 6: Verify Results

#### Check Campaign Status

1. **Refresh** campaigns list page
2. **Check status** - Should show "Sent"
3. **View campaign** - Click eye icon
4. **Check recipient status**:
   - ✅ **Sent** = Successfully sent to WhatsApp API
   - ❌ **Failed** = Check logs for error
   - ⏳ **Pending** = Still processing

#### Check WhatsApp Message

- **Check the recipient's phone** for WhatsApp message
- **Verify image** appears in message header
- **Verify message** content is correct

---

## 🔍 Detailed Monitoring

### Real-time Log Monitoring

**Terminal 1** (Queue Worker):
```bash
php artisan queue:listen --tries=3 --timeout=300
```

**Terminal 2** (Campaign Logs):
```bash
Get-Content storage\logs\campaign_progress.log -Wait -Tail 50
```

**Terminal 3** (API Errors):
```bash
Get-Content storage\logs\whatsapp_api_errors.log -Wait -Tail 50
```

### Check Campaign Progress via API

You can check progress programmatically:
```
GET http://localhost:8000/admin/campaigns/{campaign_id}/progress
```

Response:
```json
{
    "total": 1,
    "sent": 1,
    "failed": 0,
    "pending": 0,
    "percent": 100
}
```

---

## 🐛 Troubleshooting

### Problem: Queue Worker Not Processing

**Check**:
```bash
# Check if queue worker is running
Get-Process | Where-Object {$_.ProcessName -like "*php*"}
```

**Solution**: Restart queue worker

### Problem: Messages Not Sending

**Check Logs**:
```bash
Get-Content storage\logs\campaign_progress.log -Tail 50
```

**Common Issues**:
1. **API Key not configured** → Check `.env` file
2. **Template not found** → Verify template name in `.env`
3. **Error 132012** → Image issue (should be fixed now)
4. **Error 131049** → Meta policy violation

### Problem: Test Image Not Working

**Verify**:
1. Check environment is "local":
   ```bash
   php artisan tinker
   >>> app()->environment()
   ```

2. Check logs for:
   ```
   "using default test image for local development"
   ```

3. Verify image URL is accessible:
   - Open: https://d8iqbmvu05s9c.cloudfront.net/ajprhqgqg1otf7d5sm7u3brf27gv
   - Should display image

### Problem: Recipient Status Stuck on "Pending"

**Causes**:
- Queue worker not running
- Job failed silently
- Database connection issue

**Solution**:
1. Check queue worker is running
2. Check logs for errors
3. Restart queue worker if needed

---

## ✅ Success Indicators

You'll know it's working when you see:

1. **Queue Worker**:
   ```
   App\Jobs\SendCampaignJob ...... DONE
   ```

2. **Logs**:
   ```json
   {
     "message": "Message sent successfully",
     "campaign_id": "...",
     "recipient_id": "...",
     "phone": "91xxxxxxxxxx"
   }
   ```

3. **Campaign Status**:
   - Status: "Sent"
   - Recipient Status: "Sent"

4. **WhatsApp Message**:
   - Message received on phone
   - Image appears in header
   - Message content correct

---

## 📝 Testing Checklist

Before testing:
- [ ] Queue worker is running
- [ ] WhatsApp API credentials configured in `.env`
- [ ] Template name matches in `.env`
- [ ] At least 1 test contact added
- [ ] Test phone number is valid WhatsApp number

During testing:
- [ ] Campaign created successfully
- [ ] Recipient added to campaign
- [ ] Campaign sent (status changed to "Sent")
- [ ] Queue worker processed the job
- [ ] Logs show "Message sent successfully"
- [ ] No error 132012 in logs

After testing:
- [ ] WhatsApp message received
- [ ] Image appears in message
- [ ] Message content is correct
- [ ] Recipient status is "Sent"

---

## 🚀 Quick Test Command

For a quick test, you can also use Tinker:

```bash
php artisan tinker
```

```php
use App\Services\CampaignService;
use App\Models\Campaign;

$service = app(CampaignService::class);

// Get a test campaign
$campaign = Campaign::where('status', 'draft')->first();

if ($campaign) {
    // Send it
    $service->sendCampaign($campaign->id);
    echo "Campaign sent! Check logs and queue worker.\n";
} else {
    echo "No draft campaigns found. Create one first.\n";
}
```

---

## 📊 Expected Timeline

- **Campaign Creation**: Instant
- **Adding Recipients**: Instant
- **Sending Campaign**: 1-2 seconds (queues the job)
- **Queue Processing**: 1-5 seconds per recipient
- **WhatsApp Delivery**: 5-30 seconds (depends on WhatsApp API)

---

**Last Updated**: January 7, 2026  
**Status**: Ready for Testing ✅

