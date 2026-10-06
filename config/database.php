<?php
/**
 * Database Connection using PDO
 * GlobeTrek Adventures Web Application
 */

class Database {
    private static ?PDO $instance = null;

    private const DB_HOST = '127.0.0.1';
    private const DB_NAME = 'globetrek_db';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    private const DB_CHARSET = 'utf8mb4';

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=" . self::DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
            } catch (PDOException $e) {
                // In production, log error instead of exposing database details
                die("Critical Database Connection Error: " . htmlspecialchars($e->getMessage()));
            }
        }
        return self::$instance;
    }
}
