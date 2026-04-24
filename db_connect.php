<?php
// db_connect.php
$host = 'localhost';
$dbname = 'internship_db'; // ชื่อฐานข้อมูลของคุณ
$username = 'root';        // Username ฐานข้อมูล
$password = '';            // Password ฐานข้อมูล

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // ตั้งค่า Error Mode ให้แจ้งเตือนเมื่อมีข้อผิดพลาด
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>