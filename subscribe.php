<?php
require_once __DIR__ . '/config.php';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $stmt = $db->prepare("INSERT IGNORE INTO newsletter_subscribers (email) VALUES (?)");
            $stmt->execute([$email]);
        } catch (PDOException $e) {
            // تجاهل التكرار
        }
    }
}

// العودة للصفحة السابقة
$referer = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/');
header("Location: $referer");
exit;
