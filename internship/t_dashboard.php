<?php
session_start();
require_once 'db_connect.php';

// [จำลองการ Login อาจารย์]
$_SESSION['logged_in_teacher_id'] = 'T001'; 
$teacher_id = $_SESSION['logged_in_teacher_id'];

$message = "";

// อนุมัติ
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve'])) {
    $id = $_POST['request_id'];
    try {
        $stmt = $conn->prepare("UPDATE internship_requests SET status = 2 WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='fas fa-check-circle me-2'></i>อนุมัติคำขอเรียบร้อยแล้ว
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
    } catch(PDOException $e) {
        $message = "<div class='alert alert-danger'><i class='fas fa-times-circle me-2'></i>เกิดข้อผิดพลาด: " . $e->getMessage() . "</div>";
    }
}

// ไม่อนุมัติ
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reject'])) {
    $id = $_POST['request_id'];
    
    try {
        $stmt = $conn->prepare("UPDATE internship_requests SET status = 9 WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $message = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                        <i class='fas fa-times-circle me-2'></i>ไม่อนุมัติคำขอเรียบร้อยแล้ว
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
    } catch(PDOException $e) {
        $message = "<div class='alert alert-danger'><i class='fas fa-times-circle me-2'></i>เกิดข้อผิดพลาด: " . $e->getMessage() . "</div>";
    }
}

// ดึงข้อมูล
try {
    $stmt = $conn->query("SELECT * FROM internship_requests ORDER BY created_at DESC");
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("เกิดข้อผิดพลาด: " . $e->getMessage());
}

// ฟังก์ชันแปลงสถานะเป็น Badge
function getStatusBadge($status_code) {
    switch($status_code) {
        case 1: return '<span class="status-badge status-1"><i class="fas fa-paper-plane me-1"></i>รอรับรองเข้าระบบ</span>';
        case 2: return '<span class="status-badge status-2"><i class="fas fa-user-check me-1"></i>อาจารย์ที่ปรึกษาอนุมัติ</span>';
        case 3: return '<span class="status-badge status-3"><i class="fas fa-envelope-open-text me-1"></i>ออกใบส่งตัวแล้ว</span>';
        case 4: return '<span class="status-badge status-4"><i class="fas fa-check-circle me-1"></i>ฝึกงานเสร็จสิ้น</span>';
        case 9: return '<span class="status-badge status-9"><i class="fas fa-times-circle me-1"></i>ยกเลิก</span>';
        default: return '<span class="status-badge bg-secondary text-white"><i class="fas fa-question-circle me-1"></i>ไม่ทราบสถานะ</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบอาจารย์ที่ปรึกษา | จัดการข้อมูลฝึกงาน</title>
    
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #c8102e;
            --primary-hover: #9e0b23;
            --bg-color: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-main: #0f172a;
            --text-muted: #63666a;
        }

        body { 
            font-family: 'Prompt', 'Inter', sans-serif; 
            background-color: var(--bg-color); 
            color: var(--text-main);
            background-image: 
                radial-gradient(at 0% 0%, hsla(350, 84%, 42%, 0.08) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(210, 5%, 40%, 0.05) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(350, 90%, 30%, 0.05) 0, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* Modern Header */
        .page-header {
            padding: 3.5rem 0 4rem;
            position: relative;
            overflow: hidden;
        }

        .page-title {
            font-weight: 700;
            font-size: 2.2rem;
            background: linear-gradient(135deg, #9e0b23 0%, #c8102e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .teacher-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-muted);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .teacher-badge i {
            color: var(--primary);
        }

        /* Glassmorphism Card */
        .modern-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Table Styling */
        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .table thead th {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0 1.5rem 0.5rem;
        }

        .table tbody tr {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            transform: translateY(-2px) scale(1.005);
            box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
            z-index: 10;
            position: relative;
        }

        .table tbody td {
            border: none;
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
        }

        .table tbody td:first-child {
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
        }

        .table tbody td:last-child {
            border-top-right-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        /* Modern Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .status-1 { background: #eef2ff; color: #4338ca; border: 1px solid #e0e7ff; }
        .status-2 { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .status-3 { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
        .status-4 { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
        .status-9 { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

        .company-name {
            font-weight: 600;
            color: var(--text-main);
            font-size: 1.05rem;
        }
        
        .student-name {
            font-weight: 600;
            color: var(--primary);
        }

        .date-text {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
        }

        .btn-modern {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(200, 16, 46, 0.2);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-modern:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px -1px rgba(200, 16, 46, 0.3);
            color: white;
        }
        
        .btn-outline-modern {
            background: transparent;
            color: var(--text-main);
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-outline-modern:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        
        .btn-success-modern {
            background: #16a34a;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-success-modern:hover {
            background: #15803d;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px -1px rgba(22, 163, 74, 0.3);
        }

        /* Modal Customization */
        .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }
        
        .modal-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.5rem;
        }
        
        .modal-title {
            font-weight: 600;
            color: var(--text-main);
        }
        
        .modal-body {
            padding: 2rem;
        }
        
        .info-group {
            margin-bottom: 1.5rem;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
            font-weight: 600;
        }
        
        .info-value {
            font-size: 1rem;
            color: var(--text-main);
            font-weight: 500;
        }

        /* Animations */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stagger-1 { animation-delay: 0.1s; animation-fill-mode: both; }
        .stagger-2 { animation-delay: 0.2s; animation-fill-mode: both; }

        .footer-text {
            color: #94a3b8;
            font-size: 0.85rem;
            margin-top: 2rem;
        }
        
        .btn-danger-modern {
            background: #dc2626;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-danger-modern:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px -1px rgba(220, 38, 38, 0.3);
            color: white;
        }
    </style>
</head>
<body>

    <div class="page-header">
        <div class="container text-center">
            <h1 class="page-title stagger-1">
                ระบบจัดการคำขอฝึกงาน
            </h1>
            <div class="mt-3 stagger-2">
                <span class="teacher-badge">
                    <i class="fas fa-chalkboard-teacher"></i> 
                    อาจารย์ที่ปรึกษา
                </span>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <?php if($message != "") echo $message; ?>
        
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="modern-card">
                    <div class="table-responsive" style="overflow-x: visible;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ข้อมูลนิสิต</th>
                                    <th>ข้อมูลการฝึกงาน</th>
                                    <th class="text-center">สถานะ</th>
                                    <th class="text-center" style="width: 200px;">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($requests) > 0): ?>
                                    <?php foreach ($requests as $row): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-light p-3 rounded-circle text-primary">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div>
                                                    <div class="student-name"><?php echo htmlspecialchars($row['student_name']); ?></div>
                                                    <div class="date-text">รหัส: <?php echo htmlspecialchars($row['student_id']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="company-name"><?php echo htmlspecialchars($row['company_name']); ?></div>
                                            <div class="date-text text-dark mt-1">
                                                <i class="far fa-calendar-alt text-muted me-1 small"></i>
                                                <?php echo date('d/m/Y', strtotime($row['start_date'])); ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php echo getStatusBadge($row['status']); ?>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-2 align-items-center">
                                                <!-- ปุ่มดูรายละเอียด -->
                                                <button type="button" class="btn-outline-modern w-100 justify-content-center" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $row['id']; ?>">
                                                    <i class="fas fa-search"></i> รายละเอียด
                                                </button>
                                                
                                                <!-- ปุ่มอนุมัติ / ไม่อนุมัติ (เฉพาะสถานะ 1) -->
                                                <?php if($row['status'] == 1): ?>
                                                <form method="POST" class="w-100 d-flex flex-column gap-2">
                                                    <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                                    <button type="submit" name="approve" class="btn-success-modern w-100 justify-content-center">
                                                        <i class="fas fa-check"></i> อนุมัติ
                                                    </button>
                                                    <button type="submit" name="reject" class="btn-danger-modern w-100 justify-content-center" onclick="return confirm('ยืนยันที่จะไม่อนุมัติคำขอนี้ใช่หรือไม่?');">
                                                        <i class="fas fa-times"></i> ไม่อนุมัติ
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal View Details -->
                                    <div class="modal fade" id="detailModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"><i class="fas fa-info-circle text-primary me-2"></i>รายละเอียดคำขอฝึกงาน</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-user-graduate me-2"></i>ข้อมูลนิสิต</h6>
                                                            <div class="info-group">
                                                                <div class="info-label">รหัสนิสิต</div>
                                                                <div class="info-value"><?php echo htmlspecialchars($row['student_id']); ?></div>
                                                            </div>
                                                            <div class="info-group">
                                                                <div class="info-label">ชื่อ-นามสกุล</div>
                                                                <div class="info-value"><?php echo htmlspecialchars($row['student_name']); ?></div>
                                                            </div>
                                                            <div class="info-group">
                                                                <div class="info-label">ชั้นปี / สาขาวิชา</div>
                                                                <div class="info-value">ปี <?php echo htmlspecialchars($row['student_year']); ?> - <?php echo htmlspecialchars($row['student_major']); ?></div>
                                                            </div>
                                                            <div class="info-group">
                                                                <div class="info-label">ติดต่อ</div>
                                                                <div class="info-value">
                                                                    <i class="fas fa-phone-alt text-muted me-1"></i> <?php echo htmlspecialchars($row['student_tel']); ?><br>
                                                                    <i class="fas fa-envelope text-muted me-1"></i> <?php echo htmlspecialchars($row['student_email']); ?>
                                                                </div>
                                                            </div>
                                                            <div class="info-group">
                                                                <div class="info-label">รูปแบบ</div>
                                                                <div class="info-value"><?php echo htmlspecialchars($row['internship_type']); ?></div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-md-6">
                                                            <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-building me-2"></i>ข้อมูลสถานที่ฝึกงาน</h6>
                                                            <div class="info-group">
                                                                <div class="info-label">ชื่อหน่วยงาน</div>
                                                                <div class="info-value"><?php echo htmlspecialchars($row['company_name']); ?></div>
                                                            </div>
                                                            <div class="info-group">
                                                                <div class="info-label">ตำแหน่ง</div>
                                                                <div class="info-value"><?php echo htmlspecialchars($row['company_position']); ?></div>
                                                            </div>
                                                            <div class="info-group">
                                                                <div class="info-label">ผู้ประสานงาน</div>
                                                                <div class="info-value"><?php echo htmlspecialchars($row['coordinator_name']); ?></div>
                                                            </div>
                                                            <div class="info-group">
                                                                <div class="info-label">ติดต่อหน่วยงาน</div>
                                                                <div class="info-value">
                                                                    <i class="fas fa-phone-alt text-muted me-1"></i> <?php echo htmlspecialchars($row['company_tel']); ?><br>
                                                                    <i class="fas fa-envelope text-muted me-1"></i> <?php echo htmlspecialchars($row['company_email']); ?>
                                                                </div>
                                                            </div>
                                                            <div class="info-group">
                                                                <div class="info-label">ระยะเวลาฝึกงาน</div>
                                                                <div class="info-value">
                                                                    <?php echo date('d/m/Y', strtotime($row['start_date'])); ?> 
                                                                    ถึง 
                                                                    <?php echo date('d/m/Y', strtotime($row['end_date'])); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 bg-light">
                                                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">ปิด</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Modal -->
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="p-0 text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-folder-open mb-3" style="font-size: 3rem; color: #cbd5e1;"></i>
                                                <h5>ไม่มีข้อมูลคำขอฝึกงาน</h5>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <p class="text-center footer-text">
                    © 2026 ระบบจัดการนิสิตฝึกงาน &middot; มหาวิทยาลัยศรีนครินทรวิโรฒ
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>