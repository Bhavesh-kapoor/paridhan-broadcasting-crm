<?php

echo "Adding Employee Fields to Users Table...\n\n";

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
    
    echo "✅ .env file loaded\n";
} else {
    echo "❌ .env file not found\n";
    exit(1);
}

// Connect to database
try {
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $database = $env['DB_DATABASE'] ?? 'paridharan_crm';
    $username = $env['DB_USERNAME'] ?? 'root';
    $password = $env['DB_PASSWORD'] ?? '';
    
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$database}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful!\n\n";
    
    // Check current table structure
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $existingColumns = [];
    foreach ($columns as $column) {
        $existingColumns[] = $column['Field'];
    }
    
    echo "📋 Current columns: " . implode(', ', $existingColumns) . "\n\n";
    
    // Add missing columns
    $queries = [
        "ALTER TABLE users ADD COLUMN date_of_birth DATE NULL AFTER address",
        "ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER date_of_birth",
        "ALTER TABLE users ADD COLUMN position VARCHAR(255) NULL AFTER status",
        "ALTER TABLE users ADD COLUMN salary DECIMAL(10,2) NULL AFTER position",
        "ALTER TABLE users ADD COLUMN hire_date DATE NULL AFTER salary"
    ];
    
    foreach ($queries as $query) {
        try {
            $pdo->exec($query);
            echo "✅ " . $query . "\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "⚠️  Column already exists: " . $query . "\n";
            } else {
                echo "❌ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Verify the changes
    echo "\n🔍 Verifying table structure...\n";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📋 Updated table structure:\n";
    foreach ($columns as $column) {
        echo "  - {$column['Field']} ({$column['Type']})";
        if ($column['Default'] !== null) {
            echo " DEFAULT {$column['Default']}";
        }
        if ($column['Null'] === 'NO') {
            echo " NOT NULL";
        }
        echo "\n";
    }
    
    // Check if all required fields are present
    $requiredFields = ['date_of_birth', 'status', 'position', 'salary', 'hire_date'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!in_array($field, array_column($columns, 'Field'))) {
            $missingFields[] = $field;
        }
    }
    
    if (empty($missingFields)) {
        echo "\n✅ All required employee fields have been added successfully!\n";
        echo "🎉 Your employee management system should now work!\n";
    } else {
        echo "\n❌ Missing fields: " . implode(', ', $missingFields) . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n🎯 Next Steps:\n";
echo "1. Try running: php artisan config:clear\n";
echo "2. Try running: php artisan migrate:status\n";
echo "3. If successful, test your employee management system\n";

