<?php

class Database
{
    private static ?Database $instance = null;
    private mysqli $conn;

    private function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            // Never expose real error to user
            error_log('DB Connection failed: ' . $this->conn->connect_error);
            throw new RuntimeException('Database connection failed. Please try again later.');
        }

        $this->conn->set_charset(DB_CHARSET);
    }

    /**
     * Get the single instance of Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Execute a prepared statement
     * Returns result object for SELECT, true/false for INSERT/UPDATE/DELETE
     */
    public function query(string $sql, string $types = '', array $params = []): mixed
    {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log('Prepare failed: ' . $this->conn->error . ' | SQL: ' . $sql);
            throw new RuntimeException('Query preparation failed.');
        }

        if ($types !== '' && count($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        // SELECT queries → return result set
        $result = $stmt->get_result();
        if ($result !== false) {
            return $result;
        }

        // INSERT / UPDATE / DELETE → return true/false
        return $stmt->affected_rows >= 0;
    }

    /**
     * Get the last inserted row ID
     */
    public function lastInsertId(): int
    {
        return (int) $this->conn->insert_id;
    }

    // Prevent cloning and unserialization (Singleton rules)
    private function __clone() {}
    public function __wakeup() {}
}