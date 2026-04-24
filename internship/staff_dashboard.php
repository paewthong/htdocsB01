<?php
session_start();
require_once 'db_connect.php';

$message = "";

// ตรวจสอบว่ามีการส่งฟอร์มเพื่อ "อัปเดตสถานะ" หรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['status'];

    try {
        $update_sql = "UPDATE internship_requests SET status = :status WHERE id = :id";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bindParam(':status', $new_status);
        $stmt_update->bindParam(':id', $request_id);
        
        if ($stmt_update->execute()) {
            $message = "<div class='message success mb-4'><i class='fas fa-check-circle fs-5'></i> อัปเดตสถานะคำขอ ID: " . htmlspecialchars($request_id) . " สำเร็จ!</div>";
        }
    } catch(PDOException $e) {
        $message = "<div class='message error mb-4'><i class='fas fa-exclamation-circle fs-5'></i> เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// ดึงข้อมูลคำขอทั้งหมดจากฐานข้อมูล (แสดงข้อมูลล่าสุดก่อน)
try {
    $stmt = $conn->query("SELECT * FROM internship_requests ORDER BY created_at DESC");
    $all_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("เกิดข้อผิดพลาด: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการฝึกงาน (Staff Dashboard)</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    
    <style>
        :root {
            --swu-red: #c8102e; 
            --swu-dark-red: #9e0b23;
            --swu-light-red: #fde8eb;
            --bg-color: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.98);
            --text-main: #1e293b; 
            --text-muted: #64748b;
        }

        body { 
            font-family: 'Prompt', 'Inter', sans-serif; 
            background-color: var(--bg-color); 
            color: var(--text-main);
            background-image: radial-gradient(at 0% 0%, rgba(200, 16, 46, 0.03) 0, transparent 40%), 
                              radial-gradient(at 100% 100%, rgba(200, 16, 46, 0.03) 0, transparent 40%);
            background-attachment: fixed;
            min-height: 100vh;
            line-height: 1.6;
        }

    
        /* Header Section */
        .admin-header {
            background: linear-gradient(135deg, var(--swu-dark-red) 0%, var(--swu-red) 100%);
            color: white;

            padding: 3rem 0 5rem 0; 
            border-radius: 0 0 2rem 2rem;
            box-shadow: 0 20px 40px -10px rgba(200, 16, 46, 0.2);
            margin-bottom: -3rem; 
            position: relative;
            z-index: 1;
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            letter-spacing: 0.5px;
        }

        .header-icon {
            background: rgba(255, 255, 255, 0.15); /
            padding: 1rem;
            border-radius: 1rem;
            backdrop-filter: blur(5px);
        }

        /* Glassmorphism Card Container */
        .modern-container {
            max-width: 1320px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            padding: 0 1.5rem;
        }

        .modern-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05); 
            padding: 3rem; 
            animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Alerts */
        .message { 
            padding: 1.25rem 1.5rem; 
            border-radius: 1rem; 
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: fadeIn 0.4s ease;
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

        /* Table Styling */
        .table {
            border-collapse: separate;
            border-spacing: 0 16px; 
            margin-top: -1rem; 
            margin-bottom: 50;
            width: 100%;
        }

        .table thead th {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1.5rem 1.5rem 1rem; 
            border-bottom: 2px solid #f1f5f9;
            text-align: center;
            vertical-align: middle;
        }

        .table tbody tr {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02); 
            transition: all 0.2s ease;
            border: 1px solid #f8fafc;
        }

        .table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.06);
            z-index: 10;
            position: relative;
            background-color: #ffffff;
        }

        .table tbody td {
            border: none;
            padding: 2rem 1.25rem; 
            vertical-align: middle;
            text-align: center;
        }

        .table tbody td:first-child {
            border-top-left-radius: 1rem;
            border-bottom-left-radius: 1rem;
        }

        .table tbody td:last-child {
            border-top-right-radius: 1rem;
            border-bottom-right-radius: 1rem;
        }

        .text-start-td {
            text-align: left;
        }

        /* Form Elements */
        .status-form {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem; 
            margin: 0;
        }

        .custom-select {
            padding: 0.6rem 2.5rem 0.6rem 1.25rem; 
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            color: var(--text-main);
            font-family: 'Prompt', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1rem;
            min-width: 200px; 
        }

        .custom-select:focus {
            outline: none;
            border-color: var(--swu-red);
            box-shadow: 0 0 0 3px var(--swu-light-red);
            background-color: white;
        }

        .btn-save {
            padding: 0.6rem 1.5rem; 
            background: var(--swu-red);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-family: 'Prompt', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-save:hover {
            background: var(--swu-dark-red);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(200, 16, 46, 0.25);
        }

        .btn-save:active {
            transform: translateY(0);
        }

        /* Typography Utilities */
        .student-name { font-weight: 500; color: var(--text-main); font-size: 1.05rem; }
        .company-name { font-weight: 400; color: #475569; font-size: 0.95rem; margin-top: 0.25rem; }
        .student-id { font-family: 'Inter', sans-serif; font-weight: 600; color: var(--swu-red); background: var(--swu-light-red); padding: 0.3rem 0.75rem; border-radius: 0.5rem; font-size: 0.9rem; letter-spacing: 0.5px; }
        .req-id { font-family: 'Inter', sans-serif; color: var(--text-muted); font-size: 0.9rem; font-weight: 600; background: #f1f5f9; padding: 0.3rem 0.6rem; border-radius: 0.4rem; }

        /* Empty State */
        .empty-state {
            padding: 5rem 2rem;
            text-align: center;
        }
        
        .empty-icon {
            font-size: 3.5rem;
            color: #cbd5e1;
            margin-bottom: 1.5rem;
        }

        /* Animations */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .modern-card { padding: 2rem; }
            .status-form { flex-direction: column; width: 100%; gap: 0.5rem; }
            .custom-select { width: 100%; }
            .btn-save { width: 100%; justify-content: center; }
            .table tbody td { padding: 1.25rem 1rem; }
        }
        @media (max-width: 768px) {
            .modern-card { padding: 1.5rem; }
        }
    </style>
</head>
<body>

    <div class="admin-header">
        <div class="container text-center">
            <h1 class="header-title">
                <span class="header-icon"><i class="fa-solid fa-users-gear"></i></span>
                ระบบจัดการฝึกงาน
            </h1>
        </div>
    </div>

    <div class="modern-container mb-5">
        <div class="modern-card">
            
            <?php echo $message; ?>

            <div class="table-responsive" style="overflow-x: visible;">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="8%">ID</th>
                            <th width="15%">รหัสนิสิต</th>
                            <th width="22%" class="text-start-td">ชื่อ-นามสกุล</th>
                            <th width="25%" class="text-start-td">หน่วยงาน/บริษัท</th>
                            <th width="30%">จัดการสถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($all_requests) && count($all_requests) > 0): ?>
                            <?php foreach ($all_requests as $row): ?>
                            <tr>
                                <td>
                                    <span class="req-id">#<?php echo htmlspecialchars($row['id'] ?? ''); ?></span>
                                </td>
                                <td>
                                    <span class="student-id"><?php echo htmlspecialchars($row['student_id'] ?? ''); ?></span>
                                </td>
                                <td class="text-start-td">
                                    <div class="student-name"><?php echo htmlspecialchars($row['student_name'] ?? ''); ?></div>
                                </td>
                                <td class="text-start-td">
                                    <div class="company-name">
                                        <i class="far fa-building me-2 text-muted"></i>
                                        <?php echo htmlspecialchars($row['company_name'] ?? ''); ?>
                                    </div>
                                </td>
                                <td>
                                    <form action="" method="POST" class="status-form">
                                        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($row['id'] ?? ''); ?>">
                                        <select name="status" class="custom-select">
                                            <option value="1" <?php if(isset($row['status']) && $row['status']==1) echo 'selected'; ?>>1: รับเรื่องเข้าระบบ</option>
                                            <option value="2" <?php if(isset($row['status']) && $row['status']==2) echo 'selected'; ?>>2: อาจารย์ที่ปรึกษาอนุมัติ</option>
                                            <option value="3" <?php if(isset($row['status']) && $row['status']==3) echo 'selected'; ?>>3: ออกใบส่งตัวแล้ว</option>
                                            <option value="4" <?php if(isset($row['status']) && $row['status']==4) echo 'selected'; ?>>4: ฝึกงานเสร็จสิ้น</option>
                                            <option value="9" <?php if(isset($row['status']) && $row['status']==9) echo 'selected'; ?>>9: ยกเลิก</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn-save">
                                            <i class="fas fa-save"></i> บันทึก
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-0">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox empty-icon"></i>
                                        <h5 class="fw-bold text-muted mt-3">ยังไม่มีข้อมูลคำขอฝึกงานในระบบ</h5>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>