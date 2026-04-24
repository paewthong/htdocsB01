<?php
session_start();
require_once 'db_connect.php'; 

// [จำลองการ Login]
$_SESSION['logged_in_student_id'] = '64000001'; 
$student_id = $_SESSION['logged_in_student_id'];

try {
    $sql = "SELECT * FROM internship_requests WHERE student_id = :student_id ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("เกิดข้อผิดพลาด: " . $e->getMessage());
}

// ฟังก์ชันแปลงสถานะเป็น Badge สไตล์ Modern พร้อม Icon
function getStatusBadge($status_code) {
    switch($status_code) {
        case 1: return '<span class="status-badge status-1"><i class="fas fa-paper-plane me-1"></i>รับเรื่องเข้าระบบ</span>';
        case 2: return '<span class="status-badge status-2"><i class="fas fa-user-check me-1"></i>อาจารย์อนุมัติ</span>';
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
    <title>Dashboard | ประวัติการฝึกงาน</title>
    
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #d80d0d;
            --primary-hover: #ff0000;
            --bg-color: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        body { 
            font-family: 'Prompt', 'Inter', sans-serif; 
            background-color: var(--bg-color); 
            color: var(--text-main);
            /* Soft mesh gradient background */
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,0.05) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,0.03) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.05) 0, transparent 50%);
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
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .student-badge {
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

        .student-badge i {
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

        .date-text {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
        }

        .time-text {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-top: 0.2rem;
            font-family: 'Inter', sans-serif;
        }

        /* Empty State */
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
        }
        
        .empty-icon {
            width: 80px;
            height: 80px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: #94a3b8;
            font-size: 2rem;
        }

        .btn-modern {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
            text-decoration: none;
            display: inline-block;
        }

        .btn-modern:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px -1px rgb(202, 187, 187);
            color: white;
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
    </style>
</head>
<body>

    <div class="page-header">
        <div class="container text-center">
            <h1 class="page-title stagger-1">
                ประวัติคำขอฝึกงาน
            </h1>
            <div class="mt-3 stagger-2">
                <span class="student-badge">
                    <i class="fas fa-user-graduate"></i> 
                    รหัสนิสิต: <?php echo htmlspecialchars($student_id); ?>
                </span>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="modern-card">
                    <div class="table-responsive" style="overflow-x: visible;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>วันที่ยื่นเรื่อง</th>
                                    <th>บริษัท/สถานที่ฝึกงาน</th>
                                    <th>เริ่มฝึกงาน</th>
                                    <th class="text-center">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($requests) > 0): ?>
                                    <?php foreach ($requests as $row): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-light p-2 rounded-3 text-muted">
                                                    <i class="far fa-calendar-alt"></i>
                                                </div>
                                                <div>
                                                    <div class="date-text"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></div>
                                                    <div class="time-text"><?php echo date('H:i', strtotime($row['created_at'])); ?> น.</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="company-name"><?php echo htmlspecialchars($row['company_name']); ?></div>
                                        </td>
                                        <td>
                                            <span class="date-text text-dark">
                                                <i class="fas fa-flag-checkered text-muted me-2 small"></i>
                                                <?php echo date('d/m/Y', strtotime($row['start_date'])); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php echo getStatusBadge($row['status']); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="p-0">
                                            <div class="empty-state">
                                                <div class="empty-icon">
                                                    <i class="fas fa-folder-open"></i>
                                                </div>
                                                <h5 class="fw-bold mb-2">ยังไม่มีประวัติการยื่นขอฝึกงาน</h5>
                                                <p class="text-muted mb-4">เริ่มต้นการยื่นขอฝึกงานครั้งแรกของคุณได้ที่นี่</p>
                                                <a href="form_request.php" class="btn-modern">
                                                    <i class="fas fa-plus me-2"></i>สร้างคำขอใหม่
                                                </a>
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
