<?php
// student_form.php
require_once 'db_connect.php'; // ดึงไฟล์เชื่อมต่อฐานข้อมูลมาใช้

$message = "";

// ตรวจสอบว่ามีการกดปุ่ม Submit (POST Method) หรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจาก Form ทั้งหมด
    $student_id = $_POST['student_id'] ?? '';
    $student_name = $_POST['student_name'] ?? '';
    $student_year = $_POST['student_year'] ?? '';
    $student_major = $_POST['student_major'] ?? '';
    $student_tel = $_POST['student_tel'] ?? '';
    $student_email = $_POST['student_email'] ?? '';
    $internship_type = $_POST['internship_type'] ?? '';
    
    $company_name = $_POST['company_name'] ?? '';
    $company_position = $_POST['company_position'] ?? '';
    $coordinator_name = $_POST['coordinator_name'] ?? '';
    $company_tel = $_POST['company_tel'] ?? '';
    $company_email = $_POST['company_email'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    
    $status = 1; 

    if(empty($student_id) || empty($student_name) || empty($company_name) || empty($start_date)) {
        $message = "<div class='message error'>กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน</div>";
    } else {
        try {
            // คำสั่ง SQL (ใช้ Prepared Statement ป้องกัน SQL Injection)
            $sql = "INSERT INTO internship_requests (
                        student_id, student_name, student_year, student_major, student_tel, student_email, internship_type, 
                        company_name, company_position, coordinator_name, company_tel, company_email, start_date, end_date, status
                    ) VALUES (
                        :student_id, :student_name, :student_year, :student_major, :student_tel, :student_email, :internship_type, 
                        :company_name, :company_position, :coordinator_name, :company_tel, :company_email, :start_date, :end_date, :status
                    )";
            
            $stmt = $conn->prepare($sql);
            
            // ผูกค่าตัวแปร
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':student_name', $student_name);
            $stmt->bindParam(':student_year', $student_year);
            $stmt->bindParam(':student_major', $student_major);
            $stmt->bindParam(':student_tel', $student_tel);
            $stmt->bindParam(':student_email', $student_email);
            $stmt->bindParam(':internship_type', $internship_type);
            
            $stmt->bindParam(':company_name', $company_name);
            $stmt->bindParam(':company_position', $company_position);
            $stmt->bindParam(':coordinator_name', $coordinator_name);
            $stmt->bindParam(':company_tel', $company_tel);
            $stmt->bindParam(':company_email', $company_email);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
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
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --swu-red: #c8102e; 
            --swu-dark-red: #9e0b23;
            --swu-gray: #63666a; 
            --bg-color: #f4f5f7;
            --white: #ffffff;
            --border-color: #d1d5db;
        }

        body { 
            font-family: 'Prompt', sans-serif; 
            background-color: var(--bg-color); 
            color: #333;
            margin: 0;
            padding: 40px 20px;
        }

        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: var(--white);
            padding: 40px; 
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border-top: 6px solid var(--swu-red);
        }

        h2 {
            color: var(--swu-red);
            text-align: center;
            font-size: 28px;
            margin-top: 0;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .section-title { 
            margin-top: 40px; 
            border-bottom: 2px solid #e5e7eb; 
            padding-bottom: 10px;
            color: var(--swu-gray);
            font-size: 20px;
            font-weight: 500;
        }

        .form-group { 
            margin-bottom: 20px; 
        }

        .form-group label { 
            display: block; 
            font-weight: 500; 
            margin-bottom: 8px;
            color: #4b5563;
            font-size: 15px;
        }

        .form-group input, 
        .form-group select { 
            width: 100%; 
            padding: 12px 15px; 
            box-sizing: border-box;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: 'Prompt', sans-serif;
            font-size: 15px;
            background-color: #fafafa;
            transition: all 0.3s ease;
        }

        .form-group input:focus, 
        .form-group select:focus {
            outline: none;
            border-color: var(--swu-red);
            background-color: var(--white);
            box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.15);
        }

        .message { 
            padding: 15px; 
            margin-bottom: 25px; 
            border-radius: 8px; 
            font-weight: 500;
            text-align: center;
        }
        .message.success { 
            background-color: #ecfdf5; 
            color: #065f46; 
            border: 1px solid #a7f3d0; 
        }
        .message.error { 
            background-color: #fef2f2; 
            color: #991b1b; 
            border: 1px solid #fecaca; 
        }

        button[type="submit"] { 
            width: 100%;
            padding: 14px 20px; 
            background: linear-gradient(135deg, var(--swu-red), var(--swu-dark-red)); 
            color: white; 
            border: none; 
            border-radius: 8px;
            cursor: pointer; 
            font-size: 18px; 
            font-family: 'Prompt', sans-serif;
            font-weight: 600;
            margin-top: 20px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        button[type="submit"]:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(200, 16, 46, 0.3);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        /* Responsive สำหรับมือถือ */
        @media (max-width: 600px) {
            .container { padding: 25px; }
            h2 { font-size: 24px; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>แบบฟอร์มขอฝึกงาน / สหกิจศึกษา</h2>
    
    <?php echo $message; ?>

    <form action="student_form.php" method="POST">
        <h3 class="section-title">ข้อมูลนิสิต</h3>
        
        <div class="form-group">
            <label for="student_id">รหัสนิสิต:</label>
            <input type="text" id="student_id" name="student_id" placeholder="เช่น 64xxxxxxxx" required>
        </div>
        
        <div class="form-group">
            <label for="student_name">ชื่อ - นามสกุล:</label>
            <input type="text" id="student_name" name="student_name" placeholder="ชื่อ นามสกุล" required>
        </div>

        <div class="form-group">
            <label for="student_year">ชั้นปี:</label>
            <input type="text" id="student_year" name="student_year" placeholder="ระบุชั้นปี" required>
        </div>

        <div class="form-group">
            <label for="student_major">สาขาวิชา:</label>      
            <select id="student_major" name="student_major" required>
                <option value="">-- กรุณาเลือกสาขาวิชา --</option>
                <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาจิตวิทยา">สาขาวิชาจิตวิทยา</option>
                <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาฝรั่งเศส">สาขาวิชาภาษาฝรั่งเศส</option>
                <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาอังกฤษ">สาขาวิชาภาษาอังกฤษ</option>
                <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาไทย">สาขาวิชาภาษาไทย</option>
                <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาวรรณกรรมสำหรับเด็ก">สาขาวิชาวรรณกรรมสำหรับเด็ก</option>
                <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาปรัชญาและศาสนา">สาขาวิชาปรัชญาและศาสนา</option>
                <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาเพื่ออาชีพ (หลักสูตรนานาชาติ)">สาขาวิชาภาษาเพื่ออาชีพ (หลักสูตรนานาชาติ)</option>
                <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาไทย (5 ปี)">สาขาวิชาภาษาไทย (5 ปี)</option>
                <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาอังกฤษ (5 ปี)">สาขาวิชาภาษาอังกฤษ (5 ปี)</option>
                <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา">สาขาวิชาสารสนเทศศึกษา</option>
                <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาตะวันออก">สาขาวิชาภาษาตะวันออก</option>
                <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาเพื่อการสื่อสาร (หลักสูตรนานาชาติ)">สาขาวิชาภาษาเพื่อการสื่อสาร (หลักสูตรนานาชาติ)</option>  
                <option value="หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาภาษาและวัฒนธรรมอาเซียน">สาขาวิชาภาษาและวัฒนธรรมอาเซียน</option> 
            </select>
        </div>

        <div class="form-group">
            <label for="student_tel">หมายเลขโทรศัพท์ที่สามารถติดต่อนิสิตได้:</label>
            <input type="tel" id="student_tel" name="student_tel" placeholder="หมายเลขโทรศัพท์" required>
        </div>
         
        <div class="form-group">
            <label for="student_email">อีเมลที่สามารถติดต่อนิสิตได้:</label>
            <input type="email" id="student_email" name="student_email" placeholder="example@g.swu.ac.th" required>
        </div>

        <div class="form-group">
            <label for="internship_type">รูปแบบการขอฝึกประสบการณ์:</label>
            <select id="internship_type" name="internship_type" required>
                <option value="">-- กรุณาเลือกรูปแบบ --</option>
                <option value="ฝึกงานรายวิชา">ฝึกงานรายวิชา</option>
                <option value="สหกิจศึกษา">สหกิจศึกษา</option>
                <option value="ฝึกตามความต้องการนอกหลักสูตร">ฝึกตามความต้องการนอกหลักสูตร</option>
            </select>
        </div>

        <h3 class="section-title">ข้อมูลของหน่วยงานที่ขอฝึกงาน</h3>

        <div class="form-group">
            <label for="company_name">ชื่อหน่วยงาน:</label>
            <input type="text" id="company_name" name="company_name" placeholder="ชื่อบริษัท หรือ องค์กร" required>
        </div>

        <div class="form-group">
            <label for="company_position">ตำแหน่งฝึกงาน:</label>
            <input type="text" id="company_position" name="company_position" placeholder="เช่น ผู้ช่วยนักการตลาด, นักพัฒนาซอฟต์แวร์" required>
        </div>

        <div class="form-group">
            <label for="coordinator_name">ชื่อผู้ประสานงาน (ฝ่าย HR หรือบุคคลที่ติดต่อด้วย):</label>
            <input type="text" id="coordinator_name" name="coordinator_name" placeholder="ชื่อผู้ประสานงาน" required>
        </div>

        <div class="form-group">
            <label for="company_tel">หมายเลขโทรศัพท์ (หน่วยงาน):</label>
            <input type="tel" id="company_tel" name="company_tel" placeholder="เบอร์โทรศัพท์ติดต่อหน่วยงาน" required>
        </div>

        <div class="form-group">
            <label for="company_email">อีเมลที่สามารถติดต่อได้ (หน่วยงาน):</label>
            <input type="email" id="company_email" name="company_email" placeholder="อีเมลหน่วยงาน" required>
        </div>

        <div class="form-group">
            <label for="start_date">วันที่เริ่มฝึกงาน:</label>
            <input type="date" id="start_date" name="start_date" required>
        </div>
       
        <div class="form-group">
            <label for="end_date">วันที่สิ้นสุดของการฝึกงาน:</label>
            <input type="date" id="end_date" name="end_date" required>
        </div>

        <button type="submit">ส่งคำขอฝึกงาน</button>
    </form>
</div>

</body>
</html>