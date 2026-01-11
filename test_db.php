<?php

echo "Testing Database Connection...\n\n";

// Load environment variables
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    $lines = explode("\n", $envContent);
    $env = [];

    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && !strpos($line, '#') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
    }

    echo " .env file loaded\n";
} else {
    echo "❌ .env file not found\n";
    exit(1);
}

// Test database connection
try {
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $database = $env['DB_DATABASE'] ?? 'paridharan_crm';
    $username = $env['DB_USERNAME'] ?? 'root';
    $password = $env['DB_PASSWORD'] ?? '';

    echo "🔌 Testing connection to: {$host}:{$port}\n";
    echo "📊 Database: {$database}\n";
    echo "👤 Username: {$username}\n";

    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$database}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo " Database connection successful!\n\n";

    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo " Users table exists\n";

        // Check table structure
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "📋 Users table structure:\n";
        foreach ($columns as $column) {
            echo "  - {$column['Field']} ({$column['Type']})\n";
        }

        // Check if we need to add employee fields
        $hasStatus = false;
        $hasDateOfBirth = false;
        $hasPosition = false;
        $hasSalary = false;
        $hasHireDate = false;

        foreach ($columns as $column) {
            if ($column['Field'] === 'status') $hasStatus = true;
            if ($column['Field'] === 'date_of_birth') $hasDateOfBirth = true;
            if ($column['Field'] === 'position') $hasPosition = true;
            if ($column['Field'] === 'salary') $hasSalary = true;
            if ($column['Field'] === 'hire_date') $hasHireDate = true;
        }

        echo "\n🔍 Missing employee fields:\n";
        if (!$hasStatus) echo "  ❌ status\n";
        if (!$hasDateOfBirth) echo "  ❌ date_of_birth\n";
        if (!$hasPosition) echo "  ❌ position\n";
        if (!$hasSalary) echo "  ❌ salary\n";
        if (!$hasHireDate) echo "  ❌ hire_date\n";

        if (!$hasStatus || !$hasDateOfBirth || !$hasPosition || !$hasSalary || !$hasHireDate) {
            echo "\n📝 You need to add these fields to the users table.\n";
            echo "Run the SQL script: add_employee_fields.sql\n";
        } else {
            echo "\n All required employee fields are present!\n";
        }
    } else {
        echo "❌ Users table does not exist\n";
    }
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "\n🔧 Please check:\n";
    echo "1. MySQL service is running\n";
    echo "2. Database credentials in .env file\n";
    echo "3. Database '{$database}' exists\n";
}

echo "\n🎯 Next Steps:\n";
echo "1. If database connection works, the Laravel issue is elsewhere\n";
echo "2. If database connection fails, fix that first\n";
echo "3. Add missing employee fields to users table\n";
echo "4. Try Laravel commands again\n";
