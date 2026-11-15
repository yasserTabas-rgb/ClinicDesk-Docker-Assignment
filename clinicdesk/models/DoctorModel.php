<?php

require_once __DIR__ . '/BaseModel.php';

class DoctorModel extends BaseModel
{
    /**
     * Find doctor record by user_id — JOIN with users + specializations
     */
    public function findByUserId(int $userId): ?array
    {
        $result = $this->execute(
            'SELECT d.*, u.name, u.email, u.phone, u.avatar, u.is_active,
                    s.name AS specialization_name
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE d.user_id = ?
             LIMIT 1',
            'i',
            [$userId]
        );
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Find doctor record by doctor ID
     */
    public function findById(int $doctorId): ?array
    {
        $result = $this->execute(
            'SELECT d.*, u.name, u.email, u.phone, u.avatar,
                    s.name AS specialization_name
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE d.id = ?
             LIMIT 1',
            'i',
            [$doctorId]
        );
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Get all doctors — for dropdowns (book appointment form)
     */
    public function getAll(): array
    {
        $result = $this->execute(
            'SELECT d.*, u.name, s.name AS specialization_name
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE u.is_active = 1
             ORDER BY u.name ASC'
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get paginated doctors — for admin list
     */
    public function getAllPaginated(int $page): array
    {
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $limit  = ITEMS_PER_PAGE;

        $result = $this->execute(
            'SELECT d.*, u.name, u.email, u.is_active,
                    s.name AS specialization_name
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             ORDER BY u.name ASC
             LIMIT ? OFFSET ?',
            'ii',
            [$limit, $offset]
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Count all doctors — for Paginator
     */
    public function countAll(): int
    {
        $result = $this->execute('SELECT COUNT(*) as total FROM doctors');
        $row    = $result->fetch_assoc();
        return (int) $row['total'];
    }

    /**
     * Create a new doctor record
     */
    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days)
             VALUES (?, ?, ?, ?, ?)',
            'iisds',
            [
                $data['user_id'],
                $data['specialization_id'],
                $data['bio']              ?? null,
                $data['consultation_fee'] ?? 0.00,
                $data['available_days']   ?? 'Sun,Mon,Tue,Wed,Thu',
            ]
        );
        return $this->db->lastInsertId();
    }

    /**
     * Update doctor record
     */
    public function update(int $doctorId, array $data): bool
    {
        $result = $this->execute(
            'UPDATE doctors
             SET specialization_id = ?, bio = ?, consultation_fee = ?, available_days = ?
             WHERE id = ?',
            'iisdi',
            [
                $data['specialization_id'],
                $data['bio']              ?? null,
                $data['consultation_fee'] ?? 0.00,
                $data['available_days']   ?? 'Sun,Mon,Tue,Wed,Thu',
                $doctorId,
            ]
        );
        return (bool) $result;
    }

    /**
     * Get available days as an array
     * e.g. "Sun,Mon,Wed" → ['Sun','Mon','Wed']
     */
    public function getAvailableDays(int $doctorId): array
    {
        $result = $this->execute(
            'SELECT available_days FROM doctors WHERE id = ? LIMIT 1',
            'i',
            [$doctorId]
        );
        $row = $result->fetch_assoc();
        if (!$row) return [];
        return explode(',', $row['available_days']);
    }
}