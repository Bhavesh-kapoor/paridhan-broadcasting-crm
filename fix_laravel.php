<?php

echo "Fixing Laravel Configuration Issues...\n\n";

// Step 1: Check if .env file exists
if (!file_exists('.env')) {
    echo "❌ .env file not found!\n";
    echo "📝 Creating .env file...\n";

    $envContent = 'APP_NAME="Paridharan CRM"
APP_ENV=local
APP_KEY=base64:your-app-key-here
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paridharan_crm
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"';

    if (file_put_contents('.env', $envContent)) {
        echo " .env file created successfully!\n";
    } else {
        echo "❌ Failed to create .env file!\n";
        echo "Please create it manually with the content from env_setup.txt\n";
    }
} else {
    echo " .env file exists\n";
}

// Step 2: Check if storage directory is writable
if (!is_writable('storage')) {
    echo "❌ Storage directory is not writable!\n";
    echo "Please make sure the storage directory has write permissions\n";
} else {
    echo " Storage directory is writable\n";
}

// Step 3: Check if bootstrap/cache directory is writable
if (!is_writable('bootstrap/cache')) {
    echo "❌ Bootstrap cache directory is not writable!\n";
    echo "Please make sure the bootstrap/cache directory has write permissions\n";
} else {
    echo " Bootstrap cache directory is writable\n";
}

// Step 4: Generate a simple APP_KEY
echo "\n🔑 Generating APP_KEY...\n";
$appKey = 'base64:' . base64_encode(random_bytes(32));
echo "Generated APP_KEY: " . $appKey . "\n";

// Step 5: Update .env file with the generated key
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    $envContent = str_replace('APP_KEY=base64:your-app-key-here', 'APP_KEY=' . $appKey, $envContent);

    if (file_put_contents('.env', $envContent)) {
        echo " APP_KEY updated in .env file\n";
    } else {
        echo "❌ Failed to update APP_KEY in .env file\n";
        echo "Please manually update APP_KEY in your .env file to: " . $appKey . "\n";
    }
}

echo "\n🎯 Next Steps:\n";
echo "1. Make sure your database is running and accessible\n";
echo "2. Update database credentials in .env file if needed\n";
echo "3. Try running: php artisan config:clear\n";
echo "4. Try running: php artisan migrate\n";
echo "5. If successful, your employee management system should work!\n";

echo "\n📋 If you still get errors, try:\n";
echo "- php artisan config:cache\n";
echo "- php artisan route:cache\n";
echo "- php artisan view:cache\n";
echo "- php artisan optimize:clear\n";
