<?php
/**
 * إعدادات الاتصال بقاعدة البيانات والبيئة العامة
 * متوافق مع XAMPP الافتراضي
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// كشف البيئة تلقائياً: هل نحن على السيرفر السحابي (Hostinger) أم بيئة التطوير المحلية (Localhost / XAMPP)
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', '::1']) || php_sapi_name() === 'cli';

if ($isLocal) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'qima_db');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // إعدادات استضافة هوستنجر (Hostinger Production)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u460026118_u12345678qima');
    define('DB_USER', 'u460026118_u12345678admin');
    define('DB_PASS', 'Ziyad-_-518iqima');
}
define('DB_CHARSET', 'utf8mb4');

// تحديد المسار الأساسي بدقة (يعمل تلقائياً سواء في مجلد فرعي أو كنطاق رئيسي)
$host = $_SERVER['HTTP_HOST'] ?? '';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($isLocal) {
    define('BASE_URL', '/qima-php');
} else {
    // في الاستضافة السحابية إذا كان في الـ Root
    define('BASE_URL', ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '') ? '' : rtrim($scriptDir, '/'));
}

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $hostsToTry = [DB_HOST];
        if (DB_HOST === 'localhost') {
            $hostsToTry[] = '127.0.0.1';
            $hostsToTry[] = 'srv971.hstgr.io';
        }

        $lastException = null;
        foreach ($hostsToTry as $hostCandidate) {
            try {
                $dsn = "mysql:host={$hostCandidate};dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                break;
            } catch (PDOException $e) {
                $lastException = $e;
            }
        }

        if ($pdo === null && $lastException) {
            die("خطأ في الاتصال بقاعدة البيانات: " . htmlspecialchars($lastException->getMessage()) . "<br>يرجى التأكد من اسم المستخدم وقاعدة البيانات وكلمة المرور في ملف <code>config.php</code>.");
        }
    }
    return $pdo;
}

// دالة لتنظيف وتأمين المخرجات في HTML
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// دالة فحص تسجيل الدخول
function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}

function isAdmin(): bool {
    return isset($_SESSION['user']) && in_array($_SESSION['user']['role'], ['admin', 'owner']);
}
