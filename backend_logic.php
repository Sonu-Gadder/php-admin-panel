<?php
require 'db.php';

// Start session to check for admin login (Assuming you have a login system)
session_start();

$action = $_POST['action'] ?? '';

// --- 1. FRONTEND APPLICATION SUBMISSION (Public) ---
if ($action === 'frontend_apply') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $passport_no = trim($_POST['passport_no'] ?? '');
    $experience = $_POST['experience'] ?? '';
    $domain = trim($_POST['domain'] ?? '');
    $job_title = $_POST['job_title'] ?? 'General Pool';
    
    $pcc = isset($_POST['pcc']) ? 1 : 0;
    $medical = isset($_POST['medical']) ? 1 : 0;
    $consent = isset($_POST['consent']) ? 1 : 0;

    // Basic Validation
    if (empty($fullname) || empty($email) || empty($passport_no)) {
        header("Location: index.php?status=error&msg=missing_fields");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO inquiries (fullname, email, passport_no, experience, domain, job_title, compliance_pcc, compliance_medical, compliance_consent, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$fullname, $email, $passport_no, $experience, $domain, $job_title, $pcc, $medical, $consent]);
        header("Location: index.php?status=success");
    } catch (Exception $e) {
        // Log error internally: error_log($e->getMessage());
        header("Location: index.php?status=error");
    }
    exit();
}

// --- PROTECT ADMIN ACTIONS ---
// If you have a login system, uncomment the lines below:
/*
if (!isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}
*/

// --- 2. ADMIN AJAX ACTIONS (JSON) ---
if (in_array($action, ['update_status', 'delete_job', 'delete_applicant'])) {
    header('Content-Type: application/json');
    
    try {
        if ($action === 'update_status') {
            $stmt = $pdo->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
            $success = $stmt->execute([$_POST['status'], $_POST['id']]);
            echo json_encode(['success' => $success]);
        }

        if ($action === 'delete_job') {
            $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
            $success = $stmt->execute([$_POST['id']]);
            echo json_encode(['success' => $success]);
        }

        // Added missing action for admin.php
        if ($action === 'delete_applicant') {
            $stmt = $pdo->prepare("DELETE FROM inquiries WHERE id = ?");
            $success = $stmt->execute([$_POST['id']]);
            echo json_encode(['success' => $success]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}

// --- 3. ADMIN FORM SUBMISSIONS (Redirects) ---
if ($action === 'add_job') {
    try {
        $stmt = $pdo->prepare("INSERT INTO jobs (title, location, salary, tags, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([
            trim($_POST['title']), 
            trim($_POST['location']), 
            trim($_POST['salary']), 
            trim($_POST['tags'])
        ]);
        header("Location: admin.php?status=job_added");
    } catch (Exception $e) {
        header("Location: admin.php?status=error");
    }
    exit();
}

if ($action === 'update_job') {
    try {
        $stmt = $pdo->prepare("UPDATE jobs SET title = ?, location = ?, salary = ?, tags = ? WHERE id = ?");
        $stmt->execute([
            trim($_POST['title']), 
            trim($_POST['location']), 
            trim($_POST['salary']), 
            trim($_POST['tags']), 
            $_POST['id']
        ]);
        header("Location: admin.php?status=job_updated");
    } catch (Exception $e) {
        header("Location: admin.php?status=error");
    }
    exit();
}

// If no action matched, redirect home
header("Location: index.php");
exit();