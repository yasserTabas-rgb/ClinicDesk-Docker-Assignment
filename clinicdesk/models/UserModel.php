<?php

require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel
{
    /**
     * Find a user by their ID
     */
    public function findById(int $id): ?array
    {
        $result = $this->execute(
            'SELECT * FROM users WHERE id = ? LIMIT 1',
            'i',
            [$id]
        );
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Find a user by email — used during login
     */
    public function findByEmail(string $email): ?array
    {
        $result = $this->execute(
            'SELECT * FROM users WHERE email = ? LIMIT 1',
            's',
            [$email]
        );
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Create a new user — password must be hashed before calling
     * Returns the new user ID
     */
    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)',
            'sssss',
            [
                $data['name'],
                $data['email'],
                $data['password'],
                $data['role'],
                $data['phone'] ?? null,
            ]
        );
        return $this->db->lastInsertId();
    }

    /**
     * Update user profile (name, phone, avatar)
     */
    public function update(int $id, array $data): bool
    {
        $result = $this->execute(
            'UPDATE users SET name = ?, phone = ?, avatar = ? WHERE id = ?',
            'sssi',
            [
                $data['name'],
                $data['phone'] ?? null,
                $data['avatar'] ?? null,
                $id,
            ]
        );
        return (bool) $result;
    }

    /**
     * Update only the password hash
     */
    public function updatePassword(int $id, string $newHash): bool
    {
        $result = $this->execute(
            'UPDATE users SET password = ? WHERE id = ?',
            'si',
            [$newHash, $id]
        );
        return (bool) $result;
    }

    /**
     * Get paginated list of users, optionally filtered by role
     */
    public function getAllPaginated(int $page, string $role = ''): array
    {
        $offset  = ($page - 1) * ITEMS_PER_PAGE;
        $limit   = ITEMS_PER_PAGE;

        if ($role !== '') {
            $result = $this->execute(
                'SELECT * FROM users WHERE role = ? ORDER BY created_at DESC LIMIT ? OFFSET ?',
                'sii',
                [$role, $limit, $offset]
            );
        } else {
            $result = $this->execute(
                'SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?',
                'ii',
                [$limit, $offset]
            );
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Count all users, optionally filtered by role
     */
    public function countAll(string $role = ''): int
    {
        if ($role !== '') {
            $result = $this->execute(
                'SELECT COUNT(*) as total FROM users WHERE role = ?',
                's',
                [$role]
            );
        } else {
            $result = $this->execute(
                'SELECT COUNT(*) as total FROM users'
            );
        }

        $row = $result->fetch_assoc();
        return (int) $row['total'];
    }

    /**
     * Flip is_active between 0 and 1
     */
    public function toggleActive(int $id): bool
    {
        $result = $this->execute(
            'UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?',
            'i',
            [$id]
        );
        return (bool) $result;
    }
}