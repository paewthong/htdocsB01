-- สร้างตาราง internship_requests
CREATE TABLE internship_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(15) NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    status INT NOT NULL DEFAULT 1 COMMENT '1=รับเรื่อง, 2=อ.อนุมัติ, 3=ออกใบส่งตัว, 4=เสร็จสิ้น, 9=ยกเลิก',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;