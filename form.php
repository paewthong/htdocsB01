<?php
// student_form.php
require_once 'db_connect.php'; // ดึงไฟล์เชื่อมต่อฐานข้อมูลมาใช้

$message = "";

// ตรวจสอบว่ามีการกดปุ่ม Submit (POST Method) หรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจาก Form
    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];
    $company_name = $_POST['company_name'];
    $start_date = $_POST['start_date'];
    $status = 1; // ตาม Requirement: กำหนดสถานะเริ่มต้น = 1 (รับเรื่องเข้าระบบ)

    // ป้องกันข้อมูลว่าง
    if(empty($student_id) || empty($student_name) || empty($company_name)) {
        $message = "<div class='message error'>กรุณากรอกข้อมูลให้ครบถ้วน</div>";
    } else {
        try {
            // คำสั่ง SQL (ใช้ Prepared Statement ป้องกัน SQL Injection)
            $sql = "INSERT INTO internship_requests (student_id, student_name, company_name, start_date, status) 
                    VALUES (:student_id, :student_name, :company_name, :start_date, :status)";
            
            $stmt = $conn->prepare($sql);
            // ผูกค่าตัวแปร
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':student_name', $student_name);
            $stmt->bindParam(':company_name', $company_name);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':status', $status);
            
            // สั่ง Execute
            if($stmt->execute()) {
                $message = "<div class='message success'>บันทึกคำขอฝึกงานสำเร็จ! (สถานะ: รับเรื่องเข้าระบบ)</div>";
            }
        } catch(PDOException $e) {
            $message = "<div class='message error'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบขอฝึกงาน (สำหรับนิสิต)</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>

<div class="container">
    <h2>แบบฟอร์มขอฝึกงาน / สหกิจศึกษา</h2>
    
    <?php echo $message; ?>

    <form action="student_form.php" method="POST">
        <div class="form-group">
            <label for="student_id">รหัสนิสิต:</label>
            <input type="text" id="student_id" name="student_id" placeholder="เช่น 64xxxxxxxx" required>
        </div>
        
        <div class="form-group">
            <label for="student_name">ชื่อ-นามสกุล:</label>
            <input type="text" id="student_name" name="student_name" placeholder="ชื่อ นามสกุล" required>
        </div>

        <div class="form-group">
            <label for="company_name">สถานที่ฝึกงาน / บริษัท:</label>
            <input type="text" id="company_name" name="company_name" placeholder="ชื่อบริษัท" required>
        </div>

        <div class="form-group">
            <label for="start_date">วันที่เริ่มฝึกงาน:</label>
            <input type="date" id="start_date" name="start_date" required>
        </div>

        <button type="submit">บันทึกข้อมูลขอฝึกงาน</button>
    </form>
</div>

</body>
</html>