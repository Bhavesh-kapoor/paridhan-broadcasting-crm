# WhatsApp API Testing Walkthrough

## 🎯 Quick Start Testing Guide

This walkthrough will guide you through testing the WhatsApp API integration in 15 minutes.

---

## ✅ Pre-Test Checklist

Before starting, ensure:
- [x] `.env` file has `WHATSAPP_API_KEY` configured
- [x] `.env` file has `WHATSAPP_API_ENDPOINT` configured
- [x] Template `campaign_message_v2` exists in WebPay dashboard
- [x] Template status is **APPROVED**
- [x] You have at least 1 test contact in the system

---

## 🚀 Step-by-Step Testing

### Step 1: Clear Cache (30 seconds)

Open terminal and run:

```bash
cd c:\xampp8\htdocs\paridhan
php artisan config:clear
php artisan cache:clear
```

**Expected Output**:
```
✓ Configuration cache cleared successfully.
✓ Application cache cleared successfully.
```

---

### Step 2: Start Queue Worker (Terminal 1)

```bash
php artisan queue:listen --tries=3 --timeout=300
```

**Expected Output**:
```
INFO  Processing jobs from the [default] queue.
```

**Keep this terminal open!**

---

### Step 3: Start Development Server (Terminal 2)

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

**Expected Output**:
```
INFO  Server running on [http://127.0.0.1:8000].
Press Ctrl+C to stop the server
```

**Keep this terminal open!**

---

### Step 4: Monitor Logs (Terminal 3 - Optional)

```bash
Get-Content storage\logs\campaign_progress.log -Wait -Tail 50
```

**This will show real-time logs as campaigns are sent.**

---

### Step 5: Create Test Campaign (2 minutes)

1. **Open Browser**: http://127.0.0.1:8000/sign-in

2. **Login**:
   - Email: paridhan@gmail.com
   - Password: admin@admin

3. **Navigate**: Campaign Management → Create Campaign

4. **Fill Form**:
   ```
   Name: WhatsApp API Test
   Subject: Test Message
   Message: Testing WhatsApp integration
   Type: WhatsApp
   Recipients: [Select 1 recipient]
   Image: [Leave empty for now]
   ```

5. **Click**: Create Campaign

6. **Expected**: Success message appears

---

### Step 6: Send Campaign (1 minute)

1. **Find**: Your campaign in the list
2. **Click**: Send button (paper plane icon)
3. **Confirm**: Click "Yes, Send Now"
4. **Expected**: Confirmation message

---

### Step 7: Monitor Queue Processing (30 seconds)

**Check Terminal 1 (Queue Worker)**:

You should see:
```
Processing: App\Jobs\SendCampaignJob
```

Then either:
```
✓ Processed App\Jobs\SendCampaignJob (528ms)
```

Or if there's an error:
```
✗ Failed App\Jobs\SendCampaignJob
```

---

### Step 8: Check Campaign Status (30 seconds)

1. **Refresh** the campaigns page
2. **Check Status** column

**Possible Statuses**:
- ✅ **sent** = Campaign processed successfully
- ❌ **failed** = Campaign processing failed
- ⏳ **pending** = Still processing (wait a bit)

---

### Step 9: Check Logs (1 minute)

**Option A: View in Terminal 3**

Look for:

**Success**:
```json
[2026-01-07 10:40:00] local.INFO: Message sent successfully
{
  "campaign_id": "xxx",
  "recipient_id": "xxx",
  "phone": "91xxxxxxxxxx"
}
```

**Error**:
```json
[2026-01-07 10:40:00] local.ERROR: WhatsApp API error
{
  "error_code": "131049",
  "error_message": "This message was not delivered..."
}
```

**Option B: View Log Files**

```bash
# Campaign progress
notepad storage\logs\campaign_progress.log

# API errors
notepad storage\logs\whatsapp_api_errors-2026-01-07.log
```

---

### Step 10: Verify in WebPay Dashboard (1 minute)

1. **Login**: http://meta.webpayservices.in
2. **Navigate**: WhatsApp Campaign → Report → API Report
3. **Check**: Latest entry (should be within last few minutes)
4. **Verify**:
   - Request time matches
   - Phone number matches
   - Status (DELIVERED/FAILED)
   - Error code if failed

---

## 📊 Interpreting Results

### ✅ Success Scenario

**Indicators**:
- Campaign status: **sent**
- Queue worker: `Processed App\Jobs\SendCampaignJob`
- Log: `Message sent successfully`
- Dashboard: Status = **DELIVERED**

**What This Means**:
- ✅ Code integration working
- ✅ Authentication working
- ✅ API endpoint correct
- ✅ Template valid
- ✅ Message delivered to recipient

**Next Steps**:
- Test with more recipients
- Test with images (HTTPS URLs)
- Deploy to production

---

### ⚠️ Partial Success Scenario

**Indicators**:
- Campaign status: **sent**
- Queue worker: `Processed App\Jobs\SendCampaignJob`
- Log: `WhatsApp API error`
- Dashboard: Status = **FAILED**

**What This Means**:
- ✅ Code integration working
- ✅ Queue processing working
- ❌ API returned error
- ❌ Message NOT delivered

**Common Causes**:
1. **Error 131049**: Meta account quality issue
2. **Template not found**: Template name mismatch
3. **Invalid phone number**: Wrong format
4. **Account restricted**: Meta suspended account

**Next Steps**:
- Check error code in logs
- See troubleshooting section below

---

### ❌ Failure Scenario

**Indicators**:
- Campaign status: **pending** or **failed**
- Queue worker: `Failed App\Jobs\SendCampaignJob`
- Log: Exception or error
- Dashboard: No entry

**What This Means**:
- ❌ Code error or configuration issue
- ❌ API not being called

**Common Causes**:
1. API key not configured
2. Endpoint URL wrong
3. Code exception
4. Queue worker not running

**Next Steps**:
- Check configuration
- Review exception in logs
- Verify queue worker is running

---

## 🔍 Common Issues & Solutions

### Issue 1: Error 131049 - "Healthy Ecosystem"

**Log Entry**:
```json
{
  "error_code": "131049",
  "error_message": "This message was not delivered to maintain healthy ecosystem engagement"
}
```

**Cause**: Meta account quality rating dropped

**Solutions**:
1. Check Meta Business Suite for warnings
2. Contact WebPay Services support
3. Review message content (avoid spam)
4. Wait 24-48 hours for quality to improve
5. Send to opted-in users only

---

### Issue 2: Template Not Found

**Log Entry**:
```json
{
  "error_code": "132000",
  "error_message": "Template not found"
}
```

**Solutions**:
1. Login to WebPay dashboard
2. Check template name: `campaign_message_v2`
3. Verify status is **APPROVED**
4. Update `WHATSAPP_TEMPLATE_NAME` in `.env`
5. Clear cache: `php artisan config:clear`

---

### Issue 3: Image Not HTTPS

**Log Entry**:
```
Image URL is not HTTPS, skipping header
```

**Solutions**:
1. Upload image to CDN (Cloudinary, AWS S3)
2. Enable HTTPS on your server
3. Use external HTTPS image URL
4. Or skip images for now

---

### Issue 4: Queue Not Processing

**Symptom**: Campaign stays "pending"

**Solutions**:
1. Check queue worker is running:
   ```bash
   Get-Process | Where-Object {$_.ProcessName -like "*php*"}
   ```

2. Restart queue worker:
   ```bash
   php artisan queue:listen --tries=3 --timeout=300
   ```

3. Check for failed jobs:
   ```bash
   php artisan queue:failed
   ```

4. Retry failed jobs:
   ```bash
   php artisan queue:retry all
   ```

---

### Issue 5: API Key Not Configured

**Log Entry**:
```
WhatsApp API Key is not configured
```

**Solutions**:
1. Check `.env` file:
   ```bash
   cat .env | grep WHATSAPP
   ```

2. Ensure `WHATSAPP_API_KEY` is set

3. Clear cache:
   ```bash
   php artisan config:clear
   ```

4. Restart queue worker

---

## 📸 Expected Screenshots

### 1. Campaign Creation
![Campaign creation form with all fields filled]

### 2. Campaign List
![Campaign list showing "sent" status]

### 3. Queue Worker
```
INFO  Processing jobs from the [default] queue.
Processing: App\Jobs\SendCampaignJob
✓ Processed App\Jobs\SendCampaignJob (528ms)
```

### 4. Success Log
```json
[2026-01-07 10:40:00] local.INFO: Message sent successfully
{
  "campaign_id": "01JKABCD1234567890",
  "recipient_id": "123",
  "phone": "919137315850"
}
```

### 5. WebPay Dashboard
![API Report showing DELIVERED status]

---

## ✅ Test Completion Checklist

After testing, verify:

- [ ] Campaign created successfully
- [ ] Campaign sent without errors
- [ ] Queue worker processed job
- [ ] Campaign status shows "sent"
- [ ] Logs show "Message sent successfully"
- [ ] WebPay dashboard shows DELIVERED
- [ ] No error 131049 in logs
- [ ] Recipient received message (if account quality OK)

---

## 🎯 Next Steps

### If All Tests Pass ✅

1. **Test with Multiple Recipients**:
   - Create campaign with 5-10 recipients
   - Monitor rate limiting (250ms delay)
   - Check all messages delivered

2. **Test with Images**:
   - Upload image to HTTPS CDN
   - Create campaign with image
   - Verify image displays in WhatsApp

3. **Production Deployment**:
   - Set up supervisor for queue worker
   - Configure log rotation
   - Set up monitoring/alerts
   - Test in production environment

### If Tests Fail ❌

1. **Review Logs**: Check exact error message
2. **Check Configuration**: Verify all `.env` values
3. **Contact Support**: WebPay Services for account issues
4. **Consult Guide**: See WHATSAPP_API_IMPLEMENTATION_GUIDE.md

---

## 📞 Support Resources

- **Implementation Guide**: `WHATSAPP_API_IMPLEMENTATION_GUIDE.md`
- **Test Results**: `TEST_RESULTS.md`
- **Configuration**: `WHATSAPP_API_CORRECTED.md`
- **WebPay Dashboard**: http://meta.webpayservices.in
- **Meta Business Suite**: https://business.facebook.com

---

**Testing Time**: ~15 minutes
**Last Updated**: January 7, 2026
**Status**: Ready for Testing ✅
