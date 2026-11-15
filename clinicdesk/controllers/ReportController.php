<?php

class ReportController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Show report form + results
     */
    public function index(): void
    {
        Auth::requireRole('admin');

        $startDate   = trim($_GET['start_date'] ?? '');
        $endDate     = trim($_GET['end_date']   ?? '');
        $doctorId    = (int)($_GET['doctor_id'] ?? 0);
        $status      = trim($_GET['status']     ?? '');
        $appointments = [];
        $summary      = [];
        $errors       = [];

        // Load doctors for dropdown
        $result  = $this->db->query(
            'SELECT d.id, u.name FROM doctors d
             JOIN users u ON u.id = d.user_id
             ORDER BY u.name ASC'
        );
        $doctors = $result->fetch_all(MYSQLI_ASSOC);

        // Only run query if dates provided
        if ($startDate || $endDate) {

            // Validate dates
            if (!$startDate || !$endDate) {
                $errors[] = 'Both start date and end date are required.';
            } elseif ($startDate > $endDate) {
                $errors[] = 'Start date must be before or equal to end date.';
            }

            if (empty($errors)) {

                // Build dynamic WHERE
                $conditions = [
                    'a.appt_date >= ?',
                    'a.appt_date <= ?',
                ];
                $types  = 'ss';
                $params = [$startDate, $endDate];

                if ($doctorId > 0) {
                    $conditions[] = 'a.doctor_id = ?';
                    $types  .= 'i';
                    $params[] = $doctorId;
                }

                if ($status !== '') {
                    $conditions[] = 'a.status = ?';
                    $types  .= 's';
                    $params[] = $status;
                }

                $where = 'WHERE ' . implode(' AND ', $conditions);

                $sql = "SELECT
                            a.id,
                            p.name  AS patient_name,
                            u.name  AS doctor_name,
                            s.name  AS specialization_name,
                            a.appt_date,
                            a.appt_time,
                            a.status,
                            a.reason
                        FROM appointments a
                        JOIN users p           ON p.id = a.patient_id
                        JOIN doctors d         ON d.id = a.doctor_id
                        JOIN users u           ON u.id = d.user_id
                        JOIN specializations s ON s.id = d.specialization_id
                        $where
                        ORDER BY a.appt_date ASC, a.appt_time ASC";

                $result       = $this->db->query($sql, $types, $params);
                $appointments = $result->fetch_all(MYSQLI_ASSOC);

                // Summary counts per status
                foreach ($appointments as $a) {
                    $summary[$a['status']] = ($summary[$a['status']] ?? 0) + 1;
                }

                // CSV Export
                if (isset($_GET['export']) && $_GET['export'] === 'csv') {
                    $this->exportCSV($appointments, $startDate, $endDate);
                }
            }
        }

        $pageTitle = 'Appointment Reports';
        require_once __DIR__ . '/../views/reports/index.php';
    }

    /**
     * Stream CSV to browser
     */
    private function exportCSV(array $data, string $from, string $to): void
    {
        $filename = 'report_' . $from . '_to_' . $to . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM for Excel UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header row
        fputcsv($output, [
            'ID',
            'Patient Name',
            'Doctor Name',
            'Specialization',
            'Date',
            'Time',
            'Status',
            'Reason',
        ]);

        // Data rows
        foreach ($data as $row) {
            fputcsv($output, [
                $row['id'],
                $row['patient_name'],
                $row['doctor_name'],
                $row['specialization_name'],
                $row['appt_date'],
                $row['appt_time'],
                $row['status'],
                $row['reason'] ?? '',
            ]);
        }

        fclose($output);
        exit;
    }
}