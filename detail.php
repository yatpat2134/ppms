<?php
require_once 'db.php';

// รับค่า ID จาก URL
$id = $_GET['id'] ?? 0;

// ดึงข้อมูลจากฐานข้อมูล
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();

// ถ้าไม่เจอข้อมูล ให้เด้งกลับหน้าแรก
if (!$project) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $project['title']; ?> - รายละเอียดโครงงาน</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark mb-4 shadow-sm" style="background-color: #425eff;">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-arrow-left-circle"></i> กลับหน้าหลัก
            </a>
            <span class="text-white">ระบบจัดเก็บโครงงาน BNCC</span>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-white p-4 border-bottom-0">
                <span class="badge bg-primary mb-2"><?php echo $project['department']; ?></span>
                <span class="badge bg-secondary mb-2"><?php echo $project['academic_year']; ?></span>
                <h2 class="fw-bold text-primary mb-1"><?php echo $project['title']; ?></h2>
                <h5 class="text-muted fst-italic"><?php echo $project['title_en']; ?></h5>
            </div>
            
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <?php if(!empty($project['image_path'])): ?>
                            <img src="<?php echo $project['image_path']; ?>" class="img-fluid rounded shadow border w-100">
                        <?php else: ?>
                            <div class="bg-light text-center py-5 border rounded text-muted">
                                <i class="bi bi-image" style="font-size: 3rem;"></i><br>ไม่มีรูปภาพ
                            </div>
                        <?php endif; ?>

                        <?php if(!empty($project['file_path'])): ?>
                            <a href="<?php echo $project['file_path']; ?>" target="_blank" class="btn btn-success w-100 mt-3 py-2 fw-bold shadow-sm">
                                <i class="bi bi-file-earmark-arrow-down-fill"></i> ดาวน์โหลดเล่มโครงงาน
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100 mt-3" disabled>ไม่พบไฟล์เล่ม</button>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-8">
                        <h5 class="fw-bold border-bottom pb-2"><i class="bi bi-info-circle-fill text-info"></i> ข้อมูลทั่วไป</h5>
                        
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">ผู้จัดทำ:</th>
                                <td><?php echo $project['student_members']; ?></td>
                            </tr>
                            <tr>
                                <th>ครูที่ปรึกษา:</th>
                                <td><?php echo $project['advisor']; ?></td>
                            </tr>
                            <tr>
                                <th>ครูประจำวิชา:</th>
                                <td><?php echo $project['subject_teacher']; ?></td>
                            </tr>
                            <tr>
                                <th>อัปโหลดโดย:</th>
                                <td class="text-muted small"><?php echo $project['uploaded_by']; ?></td>
                            </tr>
                        </table>

                        <h5 class="fw-bold border-bottom pb-2 mt-4"><i class="bi bi-journal-text text-warning"></i> บทคัดย่อ</h5>
                        <div class="bg-light p-3 rounded border text-secondary">
                            <?php echo nl2br($project['abstract']); ?>
                        </div>

                        <?php if(!empty($project['description'])): ?>
                            <h5 class="fw-bold border-bottom pb-2 mt-4"><i class="bi bi-list-stars text-danger"></i> รายละเอียดเพิ่มเติม</h5>
                            <p><?php echo nl2br($project['description']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-chat-dots-fill text-primary"></i> ความคิดเห็นและคะแนน</h5>
            </div>
            <div class="card-body">
                <div class="bg-light p-3 rounded mb-4 border">
                    <form id="commentForm">
                        <input type="hidden" id="cmt_pid" value="<?php echo $id; ?>">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">ให้คะแนน</label>
                                <select class="form-select" id="cmt_rating" required>
                                    <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                    <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                    <option value="3">⭐⭐⭐ (3/5)</option>
                                    <option value="2">⭐⭐ (2/5)</option>
                                    <option value="1">⭐ (1/5)</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-bold">ความคิดเห็น</label>
                                <input type="text" class="form-control" id="cmt_text" placeholder="เขียนความคิดเห็นของคุณที่นี่..." required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send"></i> ส่ง</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="commentList">
                    <div class="text-center text-muted py-3">กำลังโหลดความคิดเห็น...</div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ดึงข้อมูล User จาก LocalStorage (ที่ล็อกอินมาจากหน้า index)
        const storedData = localStorage.getItem('user_data');
        let currentUserEmail = '';
        let currentUserName = '';

        if (storedData) {
            const userData = JSON.parse(storedData);
            currentUserEmail = userData.email;
            currentUserName = userData.name;
        }

        $(document).ready(function() {
            loadComments(); // โหลดคอมเมนต์ทันทีที่เปิดหน้า

            // จัดการเมื่อกดปุ่มส่ง
            $('#commentForm').on('submit', function(e) {
                e.preventDefault();

                // 1. เช็คว่าล็อกอินหรือยัง
                if (!currentUserEmail || !currentUserName) {
                    Swal.fire('แจ้งเตือน', 'กรุณาล็อกอินที่หน้าหลักก่อนให้คะแนน', 'warning');
                    return;
                }

                let pid = $('#cmt_pid').val();
                let rating = $('#cmt_rating').val();
                let text = $('#cmt_text').val();

                // 2. ส่งข้อมูลไป api.php
                $.post('api.php', {
                    action: 'add_comment',
                    project_id: pid,
                    user_email: currentUserEmail,
                    user_name: currentUserName,
                    rating: rating,
                    comment: text
                }, function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกเรียบร้อย',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $('#cmt_text').val(''); // ล้างช่องกรอก
                        loadComments(); // โหลดคอมเมนต์ใหม่
                    } else {
                        Swal.fire('Error', res.message || 'เกิดข้อผิดพลาด', 'error');
                    }
                }, 'json');
            });
        });

        // ฟังก์ชันดึงคอมเมนต์มาแสดง
        function loadComments() {
            let pid = $('#cmt_pid').val();
            $.get('api.php', { action: 'get_comments', project_id: pid }, function(res) {
                let html = '';
                if (res.data && res.data.length > 0) {
                    res.data.forEach(c => {
                        // สร้างดาวตามคะแนน
                        let stars = '';
                        for(let i=1; i<=5; i++) {
                            stars += i <= c.rating ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>';
                        }

                        // ✅ แก้ไขตรงนี้: เปลี่ยน (เมื่อสักครู่) เป็น ${c.time_ago}
                        html += `
                        <div class="d-flex mb-3 border-bottom pb-3">
                            <div class="me-3">
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; font-size: 1.2rem;">
                                    ${c.user_name.charAt(0)}
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold">${c.user_name} <small class="text-muted fw-normal ms-2" style="font-size: 0.8rem;">(${c.time_ago})</small></div>
                                <div class="mb-1">${stars}</div>
                                <div class="text-secondary">${c.comment}</div>
                            </div>
                        </div>`;
                    });
                } else {
                    html = '<div class="text-center text-muted py-4"><i class="bi bi-chat-square-dots" style="font-size: 2rem;"></i><br> ยังไม่มีความคิดเห็น </div>';
                }
                $('#commentList').html(html);
            }, 'json');
        }
    </script>
</body>
</html>