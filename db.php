<?php
// 1. ตั้งค่าเวลาของ PHP ให้เป็นไทย (กันเหนียว)
date_default_timezone_set('Asia/Bangkok');

$host = 'db';  // ชื่อ Host ใน Docker
$db   = 'project_archive';
$user = 'root';
$pass = 'root'; // รหัสผ่าน Docker

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