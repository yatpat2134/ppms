<?php
require_once 'db.php';

// ✅ เริ่ม Session และตั้งค่า Timezone ไทย
session_start(); 
date_default_timezone_set('Asia/Bangkok'); 

header('Content-Type: application/json');

if (!file_exists('uploads')) mkdir('uploads', 0777, true);
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if (!isset($conn)) throw new Exception("Database connection failed.");

    // --- 0. Visitor Counter (นับคนเข้าเว็บ) ---
    if ($action == 'site_visit') {
        // เช็คว่ามีตารางหรือยัง ถ้าไม่มีให้สร้าง
        $checkTable = $conn->query("SHOW TABLES LIKE 'site_stats'");
        if ($checkTable->rowCount() == 0) {
             $conn->exec("CREATE TABLE site_stats (id INT PRIMARY KEY, total_visits INT DEFAULT 0)");
             $conn->exec("INSERT INTO site_stats VALUES (1, 0)");
        }

        // เช็ค Session ว่าคนนี้เคยนับหรือยัง
        if (!isset($_SESSION['visited_site'])) {
            $conn->query("UPDATE site_stats SET total_visits = total_visits + 1 WHERE id = 1");
            $_SESSION['visited_site'] = true;
        }

        $stmt = $conn->query("SELECT total_visits FROM site_stats WHERE id = 1");
        echo json_encode(['visits' => $stmt->fetchColumn()]);
        exit;
    }

    // --- 1. Read All ---
    if ($action == 'read') {
        $stmt = $conn->prepare("SELECT * FROM projects WHERE is_deleted = 0 ORDER BY id DESC");
        $stmt->execute();
        echo json_encode(['data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    // --- 2. Read Single ---
    if ($action == 'read_single') {
        $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        exit;
    }

    // --- 3. Create ---
    if ($action == 'create') {
        $check = $conn->prepare("SELECT id FROM projects WHERE title = ? AND is_deleted = 0");
        $check->execute([$_POST['title']]);
        if ($check->rowCount() > 0) { echo json_encode(['status' => 'error', 'message' => 'ชื่อซ้ำ']); exit; }

        $sql = "INSERT INTO projects (title, title_en, department, academic_year, student_members, advisor, subject_teacher, abstract, description, uploaded_by, is_deleted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
        $conn->prepare($sql)->execute([$_POST['title'], $_POST['title_en']??'', $_POST['department'], $_POST['academic_year'], $_POST['student'], $_POST['advisor']??'', $_POST['subject_teacher']??'', $_POST['abstract']??'', $_POST['desc'], $_POST['uploaded_by']??'']);
        $id = $conn->lastInsertId();

        $target_dir = "uploads/" . $id . "/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $img = ""; $doc = "";
        if (!empty($_FILES['image']['name'])) { $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION); move_uploaded_file($_FILES['image']['tmp_name'], $target_dir."cover.".$ext); $img = $target_dir."cover.".$ext; }
        if (!empty($_FILES['doc']['name'])) { $ext = pathinfo($_FILES['doc']['name'], PATHINFO_EXTENSION); move_uploaded_file($_FILES['doc']['tmp_name'], $target_dir."document.".$ext); $doc = $target_dir."document.".$ext; }
        
        $conn->prepare("UPDATE projects SET image_path=?, file_path=? WHERE id=?")->execute([$img, $doc, $id]);
        echo json_encode(['status' => 'success']);
        exit;
    }

    // --- 4. Update ---
    if ($action == 'update') {
        $id = $_POST['id'];
        $target_dir = "uploads/" . $id . "/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $sql = "UPDATE projects SET title=?, title_en=?, department=?, academic_year=?, student_members=?, advisor=?, subject_teacher=?, abstract=?, description=? WHERE id=?";
        $conn->prepare($sql)->execute([$_POST['title'], $_POST['title_en']??'', $_POST['department'], $_POST['academic_year'], $_POST['student'], $_POST['advisor']??'', $_POST['subject_teacher']??'', $_POST['abstract']??'', $_POST['desc'], $id]);

        if (!empty($_FILES['image']['name'])) { $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION); move_uploaded_file($_FILES['image']['tmp_name'], $target_dir."cover.".$ext); $conn->prepare("UPDATE projects SET image_path=? WHERE id=?")->execute([$target_dir."cover.".$ext, $id]); }
        if (!empty($_FILES['doc']['name'])) { $ext = pathinfo($_FILES['doc']['name'], PATHINFO_EXTENSION); move_uploaded_file($_FILES['doc']['tmp_name'], $target_dir."document.".$ext); $conn->prepare("UPDATE projects SET file_path=? WHERE id=?")->execute([$target_dir."document.".$ext, $id]); }
        echo json_encode(['status' => 'success']);
        exit;
    }

    // --- 5. Delete ---
    if ($action == 'delete') {
        $conn->prepare("UPDATE projects SET is_deleted = 1 WHERE id = ?")->execute([$_POST['id']]);
        echo json_encode(['status' => 'success']);
        exit;
    }

    // --- 6. Add Comment ---
    if ($action == 'add_comment') {
        if(empty($_POST['project_id']) || empty($_POST['comment'])) {
            echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO project_comments (project_id, user_email, user_name, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['project_id'], 
            $_POST['user_email'], 
            $_POST['user_name'],  
            $_POST['rating'], 
            $_POST['comment']
        ]);
        echo json_encode(['status' => 'success']);
        exit;
    }

    // --- 7. Get Comments ---
    if ($action == 'get_comments') {
        $stmt = $conn->prepare("SELECT * FROM project_comments WHERE project_id = ? ORDER BY id DESC");
        $stmt->execute([$_GET['project_id']]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ฟังก์ชันคำนวณเวลา
        function timeAgo($datetime) {
            if (empty($datetime)) return "เมื่อสักครู่";
            
            $time = strtotime($datetime);
            $diff = time() - $time;
            
            if ($diff < 60) return 'เมื่อสักครู่';
            
            $seconds = [
                12 * 30 * 24 * 60 * 60  =>  'ปี',
                30 * 24 * 60 * 60       =>  'เดือน',
                24 * 60 * 60            =>  'วัน',
                60 * 60                 =>  'ชั่วโมง',
                60                      =>  'นาที'
            ];
            
            foreach ($seconds as $secs => $str) {
                $d = $diff / $secs;
                if ($d >= 1) {
                    return round($d) . ' ' . $str . 'ที่แล้ว';
                }
            }
            return 'นานมาแล้ว';
        }

        foreach ($comments as &$c) {
            $c['time_ago'] = isset($c['created_at']) ? timeAgo($c['created_at']) : 'เมื่อสักครู่';
        }

        echo json_encode(['data' => $comments]);
        exit;
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>