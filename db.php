<?php
// 0. ตั้งค่าเวลาของ PHP ให้เป็นไทย (กันเหนียว)
date_default_timezone_set('Asia/Bangkok');
// 1. กำหนด Path (ปรับเลข 1 หรือลบ dirname ตามโครงสร้างโฟลเดอร์จริงของคุณ)
// สมมติว่า db.php อยู่ในโฟลเดอร์ root เดียวกับ .env ให้ใช้บรรทัดนี้:
$envPath = __DIR__ . '/.env';
// แต่ถ้า db.php อยู่ในโฟลเดอร์ย่อย (เช่น config/) ให้ใช้บรรทัดนี้แทน:
// $envPath = dirname(__DIR__, 1) . '/.env';
$env = [];
// 2. พยายามอ่านไฟล์ .env ถ้ามี
if (file_exists($envPath)) {
    // ใช้ parse_ini_file และปิด warning หากไฟล์ format ไม่เป๊ะ
    $env = @parse_ini_file($envPath);
}
// 3. ฟังก์ชันดึงค่า (Priority: เอาจาก Environment ของ Docker ก่อน -> ถ้าไม่มีค่อยเอาจากไฟล์ .env -> ถ้าไม่มีเอาค่า Default)
function getEnvValue($key, $default, $fileEnv) {
    // ลองดึงจาก Server Environment (Docker)
    $val = getenv($key);
    if ($val !== false) return $val;
    // ลองดึงจากไฟล์ .env ที่อ่านมา
    if (isset($fileEnv[$key])) return $fileEnv[$key];
    // ถ้าไม่มีเลย ใช้ค่า default
    return $default;
}
// 4. กำหนดค่าตัวแปร
$host     = getEnvValue('DB_HOST', 'localhost', $env); // Default เดิมของคุณ
$db   = getEnvValue('DB_NAME', 'namedb', $env);
$user = getEnvValue('DB_USER', 'usernamedb', $env);
$pass = getEnvValue('DB_PASS', 'passworddb', $env);
$charset  = 'utf8mb4';
// 5. สร้างการเชื่อมต่อฐานข้อมูลด้วย PDO

try {
    // เชื่อมต่อฐานข้อมูล
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // 2. บังคับให้ MySQL ใช้เวลาไทย (+07:00) 
    // อันนี้แหละที่จะแก้ปัญหา "7 ชั่วโมงที่แล้ว"
    $conn->exec("SET time_zone = '+07:00';");

} catch (PDOException $e) {
    // ส่ง Error เป็น JSON
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}
?>