<?php

require_once __DIR__ . '/BaseModel.php';

class PrescriptionModel extends BaseModel
{
    /**
     * Find prescription by appointment ID
     */
    public function findByAppointmentId(int $apptId): ?array
    {
        $result = $this->execute(
            'SELECT * FROM prescriptions WHERE appointment_id = ? LIMIT 1',
            'i',
            [$apptId]
        );
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Create a new prescription
     * Returns the new prescription ID
     */
    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO prescriptions (appointment_id, diagnosis, medications, notes, file_path)
             VALUES (?, ?, ?, ?, ?)',
            'issss',
            [
                $data['appointment_id'],
                $data['diagnosis'],
                $data['medications'],
                $data['notes']      ?? null,
                $data['file_path']  ?? null,
            ]
        );
        return $this->db->lastInsertId();
    }

    /**
     * Update an existing prescription
     */
    public function update(int $id, array $data): bool
    {
        $result = $this->execute(
            'UPDATE prescriptions
             SET diagnosis = ?, medications = ?, notes = ?, file_path = ?
             WHERE id = ?',
            'ssssi',
            [
                $data['diagnosis'],
                $data['medications'],
                $data['notes']     ?? null,
                $data['file_path'] ?? null,
                $id,
            ]
        );
        return (bool) $result;
    }

    /**
     * Get all prescriptions for a patient — verify ownership via appointments JOIN
     */
    public function getByPatient(int $patientId): array
    {
        $result = $this->execute(
            'SELECT pr.*,
                    a.appt_date,
                    a.appt_time,
                    u.name AS doctor_name,
                    s.name AS specialization_name
             FROM prescriptions pr
             JOIN appointments a    ON a.id  = pr.appointment_id
             JOIN doctors d         ON d.id  = a.doctor_id
             JOIN users u           ON u.id  = d.user_id
             JOIN specializations s ON s.id  = d.specialization_id
             WHERE a.patient_id = ?
             ORDER BY pr.created_at DESC',
            'i',
            [$patientId]
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    /**
     * Get prescriptions for a doctor's appointments
     */
    public function getByDoctor(int $doctorId): array
    {
        $result = $this->execute(
            'SELECT pr.*,
                    a.appt_date,
                    a.appt_time,
                    p.name AS patient_name
             FROM prescriptions pr
             JOIN appointments a ON a.id  = pr.appointment_id
             JOIN users p        ON p.id  = a.patient_id
             WHERE a.doctor_id = ?
             ORDER BY pr.created_at DESC',
            'i',
            [$doctorId]
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    }