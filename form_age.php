<?php
// ================================
// ค่าระบบ
// ================================
$currentYear = 2569;

// ================================
// เตรียมตัวแปรเก็บข้อมูลผู้ใช้ (จำลองข้อมูล)
// ================================+
$users = [];

// ================================
// Function 1: คำนวณอายุ
// ================================
function calculateAge($birthYear, $currentYear) {
    return $currentYear - $birthYear;
}

// ================================
// Function 2: ตรวจสอบสิทธิ์
// ================================
function checkAccess($age, $birthYear, $currentYear) {
    if ($birthYear > $currentYear) {
        return "ข้อมูลปีเกิดไม่ถูกต้อง";
    } elseif ($age > 120) {
        return "กรุณาตรวจสอบข้อมูลอีกครั้ง";
    } elseif ($age < 18) {
        return "❌ ไม่อนุญาตให้เข้าใช้งาน (อายุต่ำกว่า 18 ปี)";
    } else {
        return "✅ อนุญาตให้เข้าใช้งาน";
    }
}

// ================================
// ประมวลผล Form
// ================================
$resultMessage = "";
if (isset($_POST['username']) && isset($_POST['birthYear'])) {
    $name = htmlspecialchars($_POST['username']);
    $year = intval($_POST['birthYear']);
    
    $age = calculateAge($year, $currentYear);
    $status = checkAccess($age, $year, $currentYear);

    // เก็บข้อมูลลงใน Array เพื่อนำไปแสดงผลด้วย Loop
    $users[] = [
        "name" => $name,
        "age" => $age,
        "status" => $status
    ];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Age Gate Web App</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; margin: 20px; }
        .result-box { margin-top: 20px; padding: 10px; border: 1px solid #ccc; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2 f2 f2; }
    </style>
</head>
<body>

<h2>ระบบตรวจสอบสิทธิ์เข้าใช้งานเว็บไซต์</h2>

<form method="post">
    ชื่อผู้ใช้:<br>
    <input type="text" name="username" required><br><br>
    ปีเกิด (พ.ศ.):<br>
    <input type="number" name="birthYear" placeholder="เช่น 2540" required><br><br>
    <button type="submit">เพิ่มผู้ใช้</button>
</form>

<hr>

<?php if (!empty($users)): ?>
    <h3>รายชื่อผู้ใช้ที่เพิ่งตรวจสอบ:</h3>
    <table>
        <tr>
            <th>ชื่อผู้ใช้</th>
            <th>อายุ (ปี)</th>
            <th>สถานะสิทธิ์</th>
        </tr>
        <?php 
        // ================================
        // Loop แสดงผลข้อมูลผู้ใช้
        // ================================
        foreach ($users as $user): 
        ?>
        <tr>
            <td><?php echo $user['name']; ?></td>
            <td><?php echo ($user['age'] < 0 || $user['age'] > 120) ? "-" : $user['age']; ?></td>
            <td><?php echo $user['status']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
</html>