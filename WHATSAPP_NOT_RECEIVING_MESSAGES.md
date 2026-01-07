# 🔍 WhatsApp Messages Not Being Received - Troubleshooting Guide

## ✅ What I've Fixed

### Phone Number Formatting Issue
**Problem**: Phone numbers were missing country code (e.g., `9454614110` instead of `919454614110`)

**Fix Applied**: 
- ✅ Automatically adds country code `91` (India) if phone number is 10 digits
- ✅ Removes spaces, dashes, and special characters
- ✅ Logs the formatted phone number for verification

---

## 🔍 Common Reasons Messages Aren't Received

### 1. Phone Number Format Issues ✅ FIXED

**Before**: `9454614110` (missing country code)  
**After**: `919454614110` (with country code)

**Check**: Look in logs for "Added country code to phone number"

---

### 2. Recipient Hasn't Opted In

**WhatsApp Business Policy**: Recipients must have:
- ✅ Previously messaged your WhatsApp Business number, OR
- ✅ Opted in to receive messages from you

**Solution**:
- Ask recipient to send a message to your WhatsApp Business number first
- Or ensure they've opted in through your opt-in process

---

### 3. WhatsApp Business Account Restrictions

**Check**:
1. Login to **Meta Business Suite**: https://business.facebook.com
2. Go to **WhatsApp Manager**
3. Check for any warnings or restrictions
4. Check **Account Quality** rating

**Common Issues**:
- Account quality rating too low
- Policy violations
- Rate limiting

---

### 4. Template Not Approved or Incorrect

**Verify**:
1. Login to **WebPay Services Dashboard**: http://meta.webpayservices.in
2. Go to **WhatsApp Campaign → Config → Manage Template**
3. Check template `campaign_message_v2` status is **APPROVED**
4. Verify template name matches `.env` file

---

### 5. API Response Shows Success But No Delivery

**Check Logs**:
```bash
Get-Content storage\logs\campaign_progress.log -Tail 50
```

**Look for**:
- `"message_id"` - If present, message was accepted by WhatsApp
- `"wa_id"` - WhatsApp ID of recipient (confirms number is valid)
- Check if `message_id` starts with `wamid.` (WhatsApp Message ID)

**If message_id exists but no delivery**:
- Message was accepted by WhatsApp API
- Delivery issue is on WhatsApp's side
- Check recipient's phone/WhatsApp status
- May take a few minutes to deliver

---

### 6. Phone Number Not on WhatsApp

**Verify**:
- Recipient has WhatsApp installed
- Phone number is correct
- Phone number is active

**Test**: Try sending a regular WhatsApp message to that number manually

---

### 7. Rate Limiting / Throttling

**WhatsApp Limits**:
- 1,000 conversations per 24 hours (for new accounts)
- 80 messages per second (API limit)
- May be lower if account quality is poor

**Check**: Look for rate limit errors in logs

---

## 🧪 How to Debug

### Step 1: Check Recent Logs

```bash
Get-Content storage\logs\campaign_progress.log -Tail 50
```

**Look for**:
```json
{
  "message": "Message sent successfully",
  "phone": "919454614110",  // Should have country code
  "wa_id": "919454614110",  // WhatsApp ID (confirms number valid)
  "message_id": "wamid.xxx"  // WhatsApp message ID
}
```

### Step 2: Verify Phone Number Format

**In logs, check**:
- `"original_phone"` - What was stored in database
- `"formatted_phone"` - What was sent to API
- Should see: `"Added country code to phone number"` if formatting was applied

### Step 3: Check API Response

**Look for**:
```json
{
  "api_response": {
    "messaging_product": "whatsapp",
    "contacts": [{
      "input": "919454614110",
      "wa_id": "919454614110"  // If this matches, number is valid
    }],
    "messages": [{
      "id": "wamid.xxx"  // Message ID means accepted by WhatsApp
    }]
  }
}
```

### Step 4: Check WebPay Services Dashboard

1. Login: http://meta.webpayservices.in
2. Go to: **WhatsApp Campaign → Report → API Report**
3. Check latest entries:
   - **Status**: DELIVERED / FAILED / PENDING
   - **Error**: If any errors shown

---

## 🔧 Quick Fixes

### Fix 1: Update Phone Numbers in Database

If your contacts have phone numbers without country code:

```sql
-- Update phone numbers to include country code
UPDATE contacts 
SET phone = CONCAT('91', phone) 
WHERE LENGTH(phone) = 10 AND phone NOT LIKE '91%';
```

**Or via Tinker**:
```bash
php artisan tinker
```

```php
use App\Models\Contacts;

// Update contacts with 10-digit numbers
Contacts::whereRaw('LENGTH(phone) = 10')
    ->where('phone', 'NOT LIKE', '91%')
    ->get()
    ->each(function($contact) {
        $contact->phone = '91' . $contact->phone;
        $contact->save();
    });
```

### Fix 2: Test with Your Own Number

1. Add your WhatsApp number to contacts (with country code: `91xxxxxxxxxx`)
2. Create a test campaign
3. Send to your number
4. Check if you receive it

### Fix 3: Verify Template

```bash
php artisan tinker
```

```php
config('services.whatsapp.template_name');
config('services.whatsapp.endpoint');
```

---

## 📊 Expected Log Output (Success)

When everything works, you should see:

```json
{
  "message": "Message sent successfully",
  "campaign_id": "01kafsanh0hjz0t543hwe32jc7",
  "recipient_id": "01KECV4KBPQ2S8B9Y6V2JKAFGN",
  "phone": "919454614110",
  "wa_id": "919454614110",
  "message_id": "wamid.HBgNOTE5NDU0NjE0MTEwFQIAERgSQjY4Q0U1QzY3QzY3QzY3QzY=",
  "api_response": {
    "messaging_product": "whatsapp",
    "contacts": [{
      "input": "919454614110",
      "wa_id": "919454614110"
    }],
    "messages": [{
      "id": "wamid.HBgN..."
    }]
  }
}
```

---

## ✅ Verification Checklist

- [ ] Phone numbers have country code (91 for India)
- [ ] Logs show `"message_id"` (message accepted by WhatsApp)
- [ ] Logs show `"wa_id"` (number is valid WhatsApp number)
- [ ] Template is APPROVED in Meta dashboard
- [ ] Recipient has opted in or messaged you before
- [ ] WhatsApp Business Account has no restrictions
- [ ] API response shows success
- [ ] Check WebPay Services dashboard for delivery status

---

## 🚨 If Still Not Working

### Check WebPay Services Dashboard

1. **Login**: http://meta.webpayservices.in
2. **Go to**: WhatsApp Campaign → Report → API Report
3. **Check**:
   - Message status (DELIVERED/FAILED/PENDING)
   - Error messages
   - Delivery timestamps

### Contact Support

- **WebPay Services**: Contact through their dashboard
- **Meta Business Support**: https://business.facebook.com/help

---

## 📝 Next Steps

1. **Test Again**: Create a new campaign with properly formatted phone number
2. **Check Logs**: Verify phone number formatting and API response
3. **Verify Delivery**: Check WebPay Services dashboard
4. **Test with Your Number**: Send to your own WhatsApp number first

---

**Last Updated**: January 7, 2026  
**Status**: Phone Number Formatting Fixed ✅

