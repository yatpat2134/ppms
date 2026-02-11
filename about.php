<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เกี่ยวกับผู้จัดทำ | Professional project storage system</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .dev-card { 
            transition: transform 0.3s; 
            border: none; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
            height: 100%; 
            padding-top: 0;
        }
        .dev-card:hover { transform: translateY(-10px); }
        .profile-img { 
            width: 150px; 
            height: 150px; 
            object-fit: cover; 
            border-radius: 50%; 
            border: 5px solid #fff; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
            margin-top: -75px; 
            background-color: #fff; 
            position: relative;
            z-index: 10;
        }
        .header-bg { 
            background: linear-gradient(135deg, #0d6efd, #0dcaf0); 
            height: 350px; 
            border-radius: 0 0 50% 50%; 
        }
        /* จัดระเบียบรายการในการ์ด */
        .card-text ul { padding-left: 20px; margin-bottom: 0; }
        .card-text li { margin-bottom: 3px; }
        .info-label { color: #0d6efd; font-weight: bold; margin-bottom: 2px; margin-top: 10px; display: block; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Professional project storage system</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">หน้าแรก</a></li>
                    <li class="nav-item"><a class="nav-link active" href="about.php">ผู้จัดทำ</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="header-bg mb-5"></div>

    <div class="container text-center" style="margin-top: -250px;">
        
        <h2 class="fw-bold text-white" style="margin-bottom: 100px;">ผู้จัดทำ</h2>
        
        <div class="row justify-content-center">
            
            <div class="col-lg-4 col-md-6 mb-5">
                <div class="card dev-card p-3">
                    <div class="text-center">
                        <img src="images/19365.jpg" class="profile-img" alt="Member 1">
                    </div>
                    <div class="card-body mt-3">
                        <h5 class="card-title fw-bold">นาย ประณิธาน เป้าเปี่ยมทรัพย์</h5>
                        <p class="text-muted small">รหัสนักศึกษา: 66209010016</p>
                        <hr>
                        <div class="text-start small text-muted">
                            <span class="info-label"><i class="bi bi-briefcase-fill"></i> หน้าที่:</span>
                            ออกแบบ / ดีไซน์เว็บไซต์
                            
                            

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-5">
                <div class="card dev-card p-3">
                    <div class="text-center">
                        <img src="images/S__156139554.jpg" class="profile-img" alt="Member 2">
                    </div>
                    <div class="card-body mt-3">
                        <h5 class="card-title fw-bold">นาย อลงกรณ์ หงษ์คะนาน</h5>
                        <p class="text-muted small">รหัสนักศึกษา: 66209010017</p>
                        <hr>
                        <div class="text-start small text-muted">
                            <span class="info-label"><i class="bi bi-briefcase-fill"></i> หน้าที่:</span>
                            พัฒนาระบบ
                            
                            

                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-5">
                <div class="card dev-card p-3">
                    <div class="text-center">
                        <img src="images/19367.jpg" class="profile-img" alt="Member 3">
                    </div>
                    <div class="card-body mt-3">
                        <h5 class="card-title fw-bold">นาย กาจพน มีมา</h5>
                        <p class="text-muted small">รหัสนักศึกษา: 66209010022</p>
                        <hr>
                        <div class="text-start small text-muted">
                            <span class="info-label"><i class="bi bi-briefcase-fill"></i> หน้าที่:</span>
                            รวบรวมข้อมูลที่ต้องใช้ในเว็บไซต์
                        

                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-5">
                <div class="card dev-card p-3">
                    <div class="text-center">
                        <img src="images/19368.jpg" class="profile-img" alt="Member 4">
                    </div>
                    <div class="card-body mt-3">
                        <h5 class="card-title fw-bold">นาย จตุเชษฐ์ ประเทือง</h5>
                        <p class="text-muted small">รหัสนักศึกษา: 66209010025</p>
                        <hr>
                        <div class="text-start small text-muted">
                            <span class="info-label"><i class="bi bi-briefcase-fill"></i> หน้าที่:</span>
                            ตรวจสอบระบบ (Tester)
                            
                           
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-5">
                <div class="card dev-card p-3">
                    <div class="text-center">
                        <img src="images/19369.jpg" class="profile-img" alt="Member 5">
                    </div>
                    <div class="card-body mt-3">
                        <h5 class="card-title fw-bold">นาย ปฏิภาณ ชุยรัมย์</h5>
                        <p class="text-muted small">รหัสนักศึกษา: 66209010026</p>
                        <hr>
                        <div class="text-start small text-muted">
                            <span class="info-label"><i class="bi bi-briefcase-fill"></i> หน้าที่:</span>
                            นำเสนอเว็บไซต์

                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row justify-content-center mt-2 mb-5">
            <div class="col-md-8">
                <div class="alert alert-light border shadow-sm text-start p-4">
                    <h4 class="fw-bold text-primary">เกี่ยวกับโครงงานนี้</h4>
                    <p>
                        ระบบจัดเก็บโครงงานวิชาชีพ (Professional project storage system) พัฒนาขึ้นเพื่อรวบรวมข้อมูลโครงงานของนักเรียน/นักศึกษา 
                        เพื่อให้ผู้สนใจสามารถเข้ามาสืบค้น ศึกษา และนำไปต่อยอดได้สะดวกยิ่งขึ้น 
                    </p>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>