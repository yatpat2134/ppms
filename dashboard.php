<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดเก็บโครงงานวิชาชีพ</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
                <img src="logo.jpg" alt="Logo" width="40" height="40" class="me-2 rounded-circle border border-white" onerror="this.style.display='none'">
                Professional project storage system
            </a>

            <div class="d-flex align-items-center ms-auto">
                
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
                    <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
                </button>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <div id="main-content">
            <div class="card p-4 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h4 class="text-primary fw-bold m-0"><i class="bi bi-folder2-open"></i> รายการโครงงานทั้งหมด</h4>
                    <button class="btn btn-success shadow-sm" onclick="openAddModal()">
                        <i class="bi bi-plus-lg"></i> เพิ่มโครงงานใหม่
                    </button>
                </div>

                <div class="row g-2 mb-3 mt-1 p-3 bg-light rounded border mx-0">
                    <div class="col-md-12 text-muted small fw-bold mb-1">
                        <i class="bi bi-funnel-fill"></i> ค้นหาและกรองข้อมูล
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="customSearch" class="form-control" placeholder="🔍 พิมพ์ชื่อโครงงาน...">
                    </div>
                    <div class="col-md-3">
                        <select id="filterDept" class="form-select"><option value="">ทุกแผนกวิชา</option></select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterYear" class="form-select"><option value="">ทุกปีการศึกษา</option></select>
                    </div>
                    <div class="col-md-2">
                        <button onclick="clearFilter()" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-counterclockwise"></i> รีเซ็ต
                        </button>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table id="projectTable" class="table table-hover align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">รูปปก</th>
                                <th width="30%">ชื่อโครงงาน</th>
                                <th width="15%" class="text-center">แผนก/ปี</th> 
                                <th width="20%">ผู้จัดทำ</th> 
                                <th width="10%" class="text-center">ไฟล์</th> 
                                <th width="15%" class="text-center">จัดการ</th>
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
                                <label class="form-label fw-bold">ชื่อโครงงาน (ไทย) <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Project Name (English)</label> 
                                <input type="text" name="title_en" id="title_en" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">แผนกวิชา <span class="text-danger">*</span></label>
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
                                    <option value="คอมพิวเตอร์กราฟิก">คอมพิวเตอร์กราฟิก</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ปีการศึกษา <span class="text-danger">*</span></label>
                                <select name="academic_year" id="academic_year" class="form-select" required>
                                    <?php 
                                        $y = date("Y") + 543; 
                                        for($i = $y; $i >= $y-5; $i--) {
                                            echo "<option value='$i'>$i</option>"; 
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">สมาชิกผู้จัดทำ</label>
                                <input type="text" name="student" id="student" class="form-control" placeholder="เช่น นาย ก, นาย ข">
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
                                <label class="form-label fw-bold">รูปปก (JPG/PNG)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ไฟล์เล่มโครงงาน (PDF) <span class="text-danger">*</span></label>
                                <input type="file" name="doc" id="docInput" class="form-control" accept=".pdf,.doc,.docx">
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="script.js"></script>
</body>
</html>