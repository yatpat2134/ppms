<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดเก็บโครงงาน BNCC</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark mb-4 shadow-sm" style="background-color: #425eff;">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <img src="logo.jpg" alt="Logo" width="40" height="40" class="me-2 rounded-circle border border-white" onerror="this.style.display='none'">
                ระบบจัดเก็บโครงงาน
            </a>

            <div class="d-flex align-items-center">
                <a href="about.php" class="btn btn-outline-light btn-sm me-5 fw-bold shadow-sm"> <i class="bi bi-people-fill"></i> About Us</a>
                <div class="text-white me-4 border-end pe-3 d-none d-md-block">
                    <small><i class="bi bi-graph-up-arrow"></i> ผู้เข้าชม:</small> 
                    <span id="totalVisitors" class="fw-bold text-warning">...</span>
                </div>

                <div id="user-info" class="text-white me-3 d-flex flex-column text-end" style="display:none;">
                    <div>
                        <span id="user-name" class="fw-bold">User</span> 
                        <span id="role-badge" class="badge bg-warning text-dark ms-1" style="display:none">Admin</span>
                    </div>
                    <small id="user-email" class="text-white-50" style="font-size: 0.8em;"></small>
                </div>

                <button id="logout-btn" onclick="logout()" class="btn btn-sm btn-light text-primary fw-bold ms-2" style="display:none;">
                    ออกจากระบบ
                </button>

                <div id="google-btn-wrapper">
                    <div id="g_id_onload"
                         data-client_id="600380763972-ne5eumj3qa3jrcint2ieag1bkbmrikur.apps.googleusercontent.com"
                         data-callback="handleCredentialResponse"
                         data-auto_prompt="false">
                    </div>
                    <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline" data-text="signin_with" data-size="medium"></div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div id="login-alert" class="alert alert-light text-center shadow-sm p-5 mt-5 border">
            <h2 class="mb-3 text-primary"><i class="bi bi-lock-fill"></i> กรุณาเข้าสู่ระบบ</h2>
            <p class="text-muted">เข้าสู่ระบบด้วยอีเมล @bncc.ac.th เท่านั้นเพื่อใช้งาน</p>
        </div>

        <div id="main-content" style="display:none;">
            <div class="card p-4 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="text-primary fw-bold m-0"><i class="bi bi-folder2-open"></i> รายการโครงงาน </h4>
                    <button class="btn btn-success" onclick="openAddModal()">
                        <i class="bi bi-plus-lg"></i> เพิ่มโครงงานใหม่
                    </button>
                </div>

                <div class="row g-2 mb-3 mt-1 p-3 bg-light rounded border mx-0">
                    <div class="col-md-12 text-muted small fw-bold mb-1">
                        <i class="bi bi-funnel-fill"></i> ค้นหาและกรองข้อมูล
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="customSearch" class="form-control form-control-sm" placeholder="🔍 พิมพ์ค้นหา...">
                    </div>
                    <div class="col-md-3">
                        <select id="filterDept" class="form-select form-select-sm"><option value="">ทุกแผนกวิชา</option></select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterYear" class="form-select form-select-sm"><option value="">ทุกปีการศึกษา</option></select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button onclick="clearFilter()" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="bi bi-arrow-counterclockwise"></i> รีเซ็ต
                        </button>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table id="projectTable" class="table table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th width="10%">รูปปก</th>
                                <th width="30%">ชื่อโครงงาน</th>
                                <th width="15%">แผนก/ปี</th> 
                                <th width="20%">ผู้จัดทำ</th> 
                                <th width="10%">ไฟล์</th> 
                                <th width="15%">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="projectModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">จัดการโครงงาน</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="projectForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id" id="projectId">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ชื่อโครงงาน (ไทย) </label>
                                <input type="text" name="title" id="title" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Project Name (English)</label> 
                                <input type="text" name="title_en" id="title_en" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">แผนกวิชา </label>
                                <select name="department" id="department" class="form-select" required>
                                    <option value="">-- เลือกแผนก --</option>
                                    <option value="เทคโนโลยีสารสนเทศ">เทคโนโลยีสารสนเทศ</option>
                                    <option value="เทคโนโลยีธุรกิจดิจิทัล">เทคโนโลยีธุรกิจดิจิทัล</option>
                                    <option value="การบัญชี">การบัญชี</option>
                                    <option value="การตลาด">การตลาด</option>
                                    <option value="ภาษาต่างประเทศ">ภาษาต่างประเทศ</option>
                                    <option value="การโรงแรม">การโรงแรม</option>
                                    <option value="การจัดการโลจิสติกส์">การจัดการโลจิสติกส์</option>
                                    <option value="การจัดการสำนักงาน">การจัดการสำนักงาน</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ปีการศึกษา </label>
                                <select name="academic_year" id="academic_year" class="form-select" required>
                                    <?php $y=date("Y")+543; for($i=$y;$i>=$y-5;$i--) echo "<option value='$i'>$i</option>"; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">สมาชิกผู้จัดทำ </label>
                                <input type="text" name="student" id="student" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ครูที่ปรึกษา</label>
                                <input type="text" name="advisor" id="advisor" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ครูประจำวิชา</label>
                                <input type="text" name="subject_teacher" id="subject_teacher" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">บทคัดย่อ</label> 
                                <textarea name="abstract" id="abstract" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">รายละเอียดเพิ่มเติม</label>
                                <textarea name="desc" id="desc" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">รูปปก (A4)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ไฟล์เล่ม <span class="text-danger">*</span></label>
                                <input type="file" name="doc" id="docInput" class="form-control" accept=".pdf,.doc,.docx,.zip,.rar">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-card-text"></i> รายละเอียดโครงงาน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h4 id="dt_title" class="fw-bold text-primary"></h4>
                    <p id="dt_title_en" class="text-muted fst-italic"></p>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img id="dt_img" src="" class="img-fluid rounded shadow-sm border" style="max-width:100%; max-height:300px;">
                        </div>
                        <div class="col-md-8">
                            <p><strong>ผู้จัดทำ:</strong> <span id="dt_student"></span></p>
                            <p><strong>ครูที่ปรึกษา:</strong> <span id="dt_advisor"></span></p>
                            <p><strong>ครูประจำวิชา:</strong> <span id="dt_subject_teacher"></span></p>
                            <p><strong>บทคัดย่อ:</strong></p>
                            <div class="bg-light p-2 rounded" id="dt_abstract" style="max-height:150px; overflow-y:auto;"></div>
                        </div>
                    </div>
                    
                    <hr class="mt-4">
                    <h5 class="fw-bold">แสดงความคิดเห็น / ให้คะแนน</h5>
                    <div class="card bg-light border-0 p-3 mb-3">
                        <form id="commentForm">
                            <input type="hidden" id="cmt_pid" name="project_id">
                            <div class="d-flex gap-2">
                                <select id="cmt_rating" name="rating" class="form-select w-auto">
                                    <option value="5">⭐⭐⭐⭐⭐ 5</option>
                                    <option value="4">⭐⭐⭐⭐ 4</option>
                                    <option value="3">⭐⭐⭐ 3</option>
                                    <option value="2">⭐⭐ 2</option>
                                    <option value="1">⭐ 1</option>
                                </select>
                                <input type="text" id="cmt_text" name="comment" class="form-control" placeholder="เขียนความคิดเห็น..." required>
                                <button type="submit" class="btn btn-primary">ส่ง</button>
                            </div>
                        </form>
                    </div>
                    <div id="commentList" style="max-height: 200px; overflow-y: auto;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="script.js"></script>
</body>
</html>