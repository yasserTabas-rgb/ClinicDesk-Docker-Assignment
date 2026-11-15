<?php

require_once __DIR__ . '/../core/Database.php';

abstract class BaseModel
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Central execute method — all child models use this
     * Never call $this->db->query() directly in child models
     */
    protected function execute(string $sql, string $types = '', array $params = []): mixed
    {
        return $this->db->query($sql, $types, $params);
    }
}