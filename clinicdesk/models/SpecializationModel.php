<?php

require_once __DIR__ . '/BaseModel.php';

class SpecializationModel extends BaseModel
{
    /**
     * Get all specializations — used in dropdowns
     */
    public function getAll(): array
    {
        $result = $this->execute('SELECT * FROM specializations ORDER BY name ASC');
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Find by ID
     */
    public function findById(int $id): ?array
    {
        $result = $this->execute(
            'SELECT * FROM specializations WHERE id = ? LIMIT 1',
            'i',
            [$id]
        );
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Create a new specialization
     */
    public function create(string $name): int
    {
        $this->execute(
            'INSERT INTO specializations (name) VALUES (?)',
            's',
            [$name]
        );
        return $this->db->lastInsertId();
    }

    /**
     * Delete a specialization
     */
    public function delete(int $id): bool
    {
        $result = $this->execute(
            'DELETE FROM specializations WHERE id = ?',
            'i',
            [$id]
        );
        return (bool) $result;
    }

    /**
     * Check if any doctors use this specialization before deleting
     */
    public function isSafeToDelete(int $id): bool
    {
        $result = $this->execute(
            'SELECT COUNT(*) as total FROM doctors WHERE specialization_id = ?',
            'i',
            [$id]
        );
        $row = $result->fetch_assoc();
        return (int) $row['total'] === 0;
    }
}