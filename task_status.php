<?php
require 'includes/auth_check.php';
csrf_verify();
require 'includes/db_koneksi.php';

header('Content-Type: application/json; charset=utf-8');

$idInput = $_POST['id'] ?? null;
$statusInput = $_POST['status'] ?? null;
$id = is_string($idInput)
    ? filter_var($idInput, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
    : false;
$status = is_string($statusInput) ? $statusInput : '';

if ($id === false || !in_array($status, ['belum', 'progres', 'selesai'], true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'ID atau status tugas tidak valid.']);
    exit;
}

try {
    $stmt = mysqli_prepare($koneksi, 'UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?');
    mysqli_stmt_bind_param($stmt, 'sii', $status, $id, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) === 0) {
        $check = mysqli_prepare($koneksi, 'SELECT id FROM tasks WHERE id = ? AND user_id = ?');
        mysqli_stmt_bind_param($check, 'ii', $id, $_SESSION['user_id']);
        mysqli_stmt_execute($check);
        if (!mysqli_fetch_assoc(mysqli_stmt_get_result($check))) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Tugas tidak ditemukan atau tidak dapat diubah.']);
            exit;
        }
    }

    $_SESSION['task_notice'] = ['type' => 'success', 'message' => 'Status tugas berhasil diperbarui.'];
    echo json_encode(['success' => true, 'status' => $status]);
} catch (mysqli_sql_exception $exception) {
    error_log('Task status update failed: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Status tugas tidak dapat diperbarui saat ini.']);
}
