<?php

require_once __DIR__ . '/BaseModel.php';

class AppointmentModel extends BaseModel
{
    /**
     * Book a new appointment
     * Returns false if double-booking UNIQUE constraint fails
     */
    public function book(array $data): bool
    {
        $result = $this->execute(
            'INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, reason)
             VALUES (?, ?, ?, ?, ?)',
            'iisss',
            [
                $data['patient_id'],
                $data['doctor_id'],
                $data['appt_date'],
                $data['appt_time'],
                $data['reason'] ?? null,
            ]
        );
        return (bool) $result;
    }

    /**
     * Check if a time slot is already taken
     */
    public function hasConflict(int $doctorId, string $date, string $time): bool
    {
        $result = $this->execute(
            'SELECT COUNT(*) as total FROM appointments
             WHERE doctor_id = ? AND appt_date = ? AND appt_time = ?
             AND status != "cancelled"',
            'iss',
            [$doctorId, $date, $time]
        );
        $row = $result->fetch_assoc();
        return (int) $row['total'] > 0;
    }

    /**
     * Get appointments for a specific patient — with filters + pagination
     */
    public function getByPatient(int $patientId, int $page, array $filters = []): array
    {
        [$where, $types, $params] = $this->buildFilters('patient', $patientId, $filters);
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $limit  = ITEMS_PER_PAGE;

        $sql = "SELECT a.*,
                       u.name  AS doctor_name,
                       s.name  AS specialization_name
                FROM appointments a
                JOIN doctors d ON d.id = a.doctor_id
                JOIN users u   ON u.id = d.user_id
                JOIN specializations s ON s.id = d.specialization_id
                WHERE a.patient_id = ? $where
                ORDER BY a.appt_date DESC, a.appt_time DESC
                LIMIT ? OFFSET ?";

        $result = $this->execute($sql, 'i' . $types . 'ii', array_merge([$patientId], $params, [$limit, $offset]));
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get appointments for a specific doctor — with filters + pagination
     */
    public function getByDoctor(int $doctorId, int $page, array $filters = []): array
    {
        [$where, $types, $params] = $this->buildFilters('doctor', $doctorId, $filters);
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $limit  = ITEMS_PER_PAGE;

        $sql = "SELECT a.*,
                       u.name AS patient_name
                FROM appointments a
                JOIN users u ON u.id = a.patient_id
                WHERE a.doctor_id = ? $where
                ORDER BY a.appt_date ASC, a.appt_time ASC
                LIMIT ? OFFSET ?";

        $result = $this->execute($sql, 'i' . $types . 'ii', array_merge([$doctorId], $params, [$limit, $offset]));
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get ALL appointments — admin only, with filters + pagination
     */
    public function getAll(int $page, array $filters = []): array
    {
        [$where, $types, $params] = $this->buildFilters('admin', 0, $filters);
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $limit  = ITEMS_PER_PAGE;

        $whereClause = $where !== '' ? 'WHERE ' . ltrim($where, ' AND ') : '';

        $sql = "SELECT a.*,
                       p.name  AS patient_name,
                       u.name  AS doctor_name,
                       s.name  AS specialization_name
                FROM appointments a
                JOIN users p       ON p.id = a.patient_id
                JOIN doctors d     ON d.id = a.doctor_id
                JOIN users u       ON u.id = d.user_id
                JOIN specializations s ON s.id = d.specialization_id
                $whereClause
                ORDER BY a.appt_date DESC, a.appt_time DESC
                LIMIT ? OFFSET ?";

        $result = $this->execute($sql, $types . 'ii', array_merge($params, [$limit, $offset]));
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Count appointments for Paginator
     * scope: 'patient' | 'doctor' | 'admin'
     */
    public function countFiltered(string $scope, int $scopeId, array $filters = []): int
    {
        [$where, $types, $params] = $this->buildFilters($scope, $scopeId, $filters);

        if ($scope === 'patient') {
            $sql = "SELECT COUNT(*) as total FROM appointments a WHERE a.patient_id = ? $where";
            $result = $this->execute($sql, 'i' . $types, array_merge([$scopeId], $params));
        } elseif ($scope === 'doctor') {
            $sql = "SELECT COUNT(*) as total FROM appointments a WHERE a.doctor_id = ? $where";
            $result = $this->execute($sql, 'i' . $types, array_merge([$scopeId], $params));
        } else {
            $whereClause = $where !== '' ? 'WHERE ' . ltrim($where, ' AND ') : '';
            $sql = "SELECT COUNT(*) as total FROM appointments a
                    JOIN doctors d ON d.id = a.doctor_id
                    JOIN users u   ON u.id = d.user_id
                    $whereClause";
            $result = $this->execute($sql, $types, $params);
        }

        $row = $result->fetch_assoc();
        return (int) $row['total'];
    }

    /**
     * Update appointment status and optional doctor notes
     */
    public function updateStatus(int $id, string $status, string $notes = ''): bool
    {
        $result = $this->execute(
            'UPDATE appointments SET status = ?, doctor_notes = ? WHERE id = ?',
            'ssi',
            [$status, $notes ?: null, $id]
        );
        return (bool) $result;
    }

    /**
     * Find a single appointment by ID — includes patient + doctor info
     */
    public function findById(int $id): ?array
    {
        $result = $this->execute(
            'SELECT a.*,
                    p.name  AS patient_name,
                    p.phone AS patient_phone,
                    u.name  AS doctor_name,
                    s.name  AS specialization_name,
                    d.id    AS doctor_record_id,
                    d.consultation_fee
             FROM appointments a
             JOIN users p       ON p.id = a.patient_id
             JOIN doctors d     ON d.id = a.doctor_id
             JOIN users u       ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE a.id = ?
             LIMIT 1',
            'i',
            [$id]
        );
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Build dynamic WHERE clause from filters
     * Returns [whereString, typesString, paramsArray]
     */
    private function buildFilters(string $scope, int $scopeId, array $filters): array
    {
        $conditions = [];
        $types      = '';
        $params     = [];

        if (!empty($filters['status'])) {
            $conditions[] = 'a.status = ?';
            $types .= 's';
            $params[] = $filters['status'];
        }

        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $types .= 's';
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $types .= 's';
            $params[] = $filters['end_date'];
        }

        if (!empty($filters['doctor_id']) && $scope !== 'doctor') {
            $conditions[] = 'a.doctor_id = ?';
            $types .= 'i';
            $params[] = (int) $filters['doctor_id'];
        }

        if (!empty($filters['patient_name']) && $scope === 'admin') {
            $conditions[] = 'p.name LIKE ?';
            $types .= 's';
            $params[] = '%' . $filters['patient_name'] . '%';
        }

        $where = count($conditions) > 0 ? ' AND ' . implode(' AND ', $conditions) : '';
        return [$where, $types, $params];
    }
}