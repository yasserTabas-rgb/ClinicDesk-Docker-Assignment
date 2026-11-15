<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';

class DashboardController
{
    public function index(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');

        $role = Auth::role();
        $user = Auth::currentUser();
        $db   = Database::getInstance();

        if ($role === 'admin') {
            $this->adminDashboard($db);
        } elseif ($role === 'doctor') {
            $this->doctorDashboard($db, $user);
        } else {
            $this->patientDashboard($db, $user);
        }
    }

    // ── Admin ─────────────────────────────────────────────
    private function adminDashboard($db): void
    {
        // Users by role
        $result     = $db->query('SELECT role, COUNT(*) as total FROM users GROUP BY role');
        $userCounts = [];
        while ($row = $result->fetch_assoc()) {
            $userCounts[$row['role']] = $row['total'];
        }

        // Appointments today
        $result      = $db->query('SELECT COUNT(*) as total FROM appointments WHERE appt_date = CURDATE()');
        $todayCount  = (int)$result->fetch_assoc()['total'];

        // Appointments this week by status
        $result      = $db->query(
            'SELECT status, COUNT(*) as total FROM appointments
             WHERE WEEK(appt_date) = WEEK(NOW()) AND YEAR(appt_date) = YEAR(NOW())
             GROUP BY status'
        );
        $weekStats   = [];
        while ($row = $result->fetch_assoc()) {
            $weekStats[$row['status']] = $row['total'];
        }

        // Recent 5 appointments
        $result   = $db->query(
            'SELECT a.*, p.name AS patient_name, u.name AS doctor_name
             FROM appointments a
             JOIN users p   ON p.id = a.patient_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users u   ON u.id = d.user_id
             ORDER BY a.created_at DESC LIMIT 5'
        );
        $recentAppts = $result->fetch_all(MYSQLI_ASSOC);

        $pageTitle = 'Admin Dashboard';
        require_once __DIR__ . '/../views/dashboard/admin.php';
    }

    // ── Doctor ────────────────────────────────────────────
    private function doctorDashboard($db, array $user): void
    {
        $doctorModel = new DoctorModel();
        $doctor      = $doctorModel->findByUserId((int)$user['id']);

        if (!$doctor) {
            flashMessage('warning', 'Doctor profile not set up yet.');
            $pageTitle = 'Doctor Dashboard';
            require_once __DIR__ . '/../views/dashboard/doctor.php';
            return;
        }

        $dId = (int)$doctor['id'];

        // Today's appointments
        $result     = $db->query(
            'SELECT a.*, u.name AS patient_name
             FROM appointments a
             JOIN users u ON u.id = a.patient_id
             WHERE a.doctor_id = ? AND a.appt_date = CURDATE()
             ORDER BY a.appt_time ASC',
            'i', [$dId]
        );
        $todayAppts = $result->fetch_all(MYSQLI_ASSOC);

        // This month counts
        $result     = $db->query(
            'SELECT
               COUNT(*) as total,
               SUM(status="pending")   as pending,
               SUM(status="completed") as completed
             FROM appointments
             WHERE doctor_id = ?
             AND MONTH(appt_date) = MONTH(NOW())
             AND YEAR(appt_date)  = YEAR(NOW())',
            'i', [$dId]
        );
        $monthStats = $result->fetch_assoc();

        // Next 5 upcoming
        $result      = $db->query(
            'SELECT a.*, u.name AS patient_name
             FROM appointments a
             JOIN users u ON u.id = a.patient_id
             WHERE a.doctor_id = ?
             AND a.appt_date >= CURDATE()
             AND a.status NOT IN ("cancelled","completed")
             ORDER BY a.appt_date ASC, a.appt_time ASC
             LIMIT 5',
            'i', [$dId]
        );
        $upcomingAppts = $result->fetch_all(MYSQLI_ASSOC);

        $pageTitle = 'Doctor Dashboard';
        require_once __DIR__ . '/../views/dashboard/doctor.php';
    }

    // ── Patient ───────────────────────────────────────────
    private function patientDashboard($db, array $user): void
    {
        $pId = (int)$user['id'];

        // Active appointments (pending + confirmed)
        $result      = $db->query(
            'SELECT COUNT(*) as total FROM appointments
             WHERE patient_id = ? AND status IN ("pending","confirmed")',
            'i', [$pId]
        );
        $activeCount = (int)$result->fetch_assoc()['total'];

        // Completed count
        $result         = $db->query(
            'SELECT COUNT(*) as total FROM appointments
             WHERE patient_id = ? AND status = "completed"',
            'i', [$pId]
        );
        $completedCount = (int)$result->fetch_assoc()['total'];

        // Prescriptions count
        $result     = $db->query(
            'SELECT COUNT(*) as total FROM prescriptions pr
             JOIN appointments a ON a.id = pr.appointment_id
             WHERE a.patient_id = ?',
            'i', [$pId]
        );
        $prescCount = (int)$result->fetch_assoc()['total'];

        // Next upcoming appointment
        $result     = $db->query(
            'SELECT a.*, u.name AS doctor_name, s.name AS specialization_name
             FROM appointments a
             JOIN doctors d     ON d.id = a.doctor_id
             JOIN users u       ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE a.patient_id = ?
             AND a.appt_date >= CURDATE()
             AND a.status IN ("pending","confirmed")
             ORDER BY a.appt_date ASC, a.appt_time ASC
             LIMIT 1',
            'i', [$pId]
        );
        $nextAppt   = $result->fetch_assoc();

        // Active appointments list
        $result      = $db->query(
            'SELECT a.*, u.name AS doctor_name, s.name AS specialization_name
             FROM appointments a
             JOIN doctors d     ON d.id = a.doctor_id
             JOIN users u       ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE a.patient_id = ?
             AND a.status IN ("pending","confirmed")
             ORDER BY a.appt_date ASC',
            'i', [$pId]
        );
        $activeAppts = $result->fetch_all(MYSQLI_ASSOC);

        $pageTitle = 'My Dashboard';
        require_once __DIR__ . '/../views/dashboard/patient.php';
    }
}