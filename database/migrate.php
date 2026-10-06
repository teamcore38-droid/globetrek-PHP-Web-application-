<?php
// Database Migration Runner
try {
    $pdo = new PDO('mysql:host=127.0.0.1;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($sql);
    echo "SUCCESS: Database schema and seed data imported!\n";
    
    $tables = $pdo->query('SHOW TABLES FROM globetrek_db')->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in globetrek_db: " . implode(', ', $tables) . "\n";
    
    $users = $pdo->query('SELECT COUNT(*) FROM globetrek_db.users')->fetchColumn();
    $packages = $pdo->query('SELECT COUNT(*) FROM globetrek_db.packages')->fetchColumn();
    $bookings = $pdo->query('SELECT COUNT(*) FROM globetrek_db.bookings')->fetchColumn();
    echo "Stats -> Users: $users | Packages: $packages | Bookings: $bookings\n";
} catch (Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}
