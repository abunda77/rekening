<?php

use App\Models\Customer;

// Check character set configuration
echo 'Database Charset: '.config('database.connections.mysql.charset')."\n";
echo 'Database Collation: '.config('database.connections.mysql.collation')."\n";

// Fetch all customers and check for encoding issues
$customers = Customer::all();
$headerPrinted = false;

foreach ($customers as $customer) {
    foreach ($customer->getAttributes() as $key => $value) {
        if (is_string($value) && ! mb_check_encoding($value, 'UTF-8')) {
            echo "Found invalid UTF-8 in Customer ID {$customer->id}, Field {$key}\n";
            // echo "Value (hex): " . bin2hex($value) . "\n";
        }
    }
}

echo "Check complete.\n";
