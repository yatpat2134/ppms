<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดเก็บโครงงานวิชาชีพ</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <img src="logo.jpg" alt="Logo" width="40" height="40" class="me-2" onerror="this.style.display='none'">
                Professional project storage system
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="about.php"><i class="bi bi-people-fill"></i> About us</a>
                    </li>
                    <li class="nav-item ms-2">
                        <button class="btn btn-light rounded-pill px-3 fw-bold text-primary" onclick="handleTopLogin()">
                             <i class="bi bi-google"></i> เข้าสู่ระบบ
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3" data-aos="fade-down" data-aos-duration="1000">
                        ระบบจัดเก็บโครงงานวิชาชีพ
                    </h1>
                    <h4 class="fw-light mb-4" data-aos="fade-down" data-aos-delay="100" data-aos-duration="1000">
                        วิทยาลัยพณิชยการบางนา
                    </h4>
                    
                    <p class="lead mb-5 opacity-75" data-aos="fade-in" data-aos-delay="200">
                        แหล่งรวบรวม องค์ความรู้ และผลงานทางวิชาการ<br>
                        สะดวก ปลอดภัย เข้าถึงง่าย 
                    </p>

                    <div id="main-login-card" 
                         class="card p-4 d-inline-block shadow-lg border-0" 
                         style="border-radius: 20px; min-width: 320px;"
                         data-aos="zoom-in" data-aos-delay="300">
                         
                        <p class="text-dark fw-bold mb-3 text-muted small">
                            <i class="bi bi-lock-fill"></i> เข้าสู่ระบบเพื่อใช้งาน
                        </p>
                        
                        <div class="d-flex justify-content-center">
                            <div id="g_id_onload"
                                 data-client_id="600380763972-ne5eumj3qa3jrcint2ieag1bkbmrikur.apps.googleusercontent.com"
                                 data-callback="handleCredentialResponse"
                                 data-auto_prompt="false">
                            </div>
                            <div class="g_id_signin" 
                                 data-type="standard" 
                                 data-shape="pill" 
                                 data-theme="filled_blue" 
                                 data-text="continue_with" 
                                 data-size="large" 
                                 data-logo_alignment="left"
                                 data-width="280">
                            </div>
                        </div>

                        <div class="mt-3 text-muted" style="font-size: 0.8rem;">
                            * เฉพาะอีเมล @bncc.ac.th เท่านั้น
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </header>

    <section class="content-box">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card feature-card p-4">
                        <div class="card-body d-flex flex-column align-items-center">
                            <div class="icon-box">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold">จัดเก็บออนไลน์</h5>
                            <p class="card-text text-muted"> จัดเก็บไฟล์รูปเล่มและข้อมูลโครงงานไว้อย่างเป็นระบบ ไม่สูญหาย</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card feature-card p-4">
                        <div class="card-body d-flex flex-column align-items-center">
                            <div class="icon-box">
                                <i class="bi bi-search"></i>
                            </div>
                            <h5 class="card-title fw-bold">ค้นหาง่าย</h5>
                            <p class="card-text text-muted">ระบบกรองข้อมูล ค้นหาโครงงานได้ตามปีการศึกษา แผนกวิชาหรือชื่อเรื่อง</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="card feature-card p-4">
                        <div class="card-body d-flex flex-column align-items-center">
                            <div class="icon-box">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold">ปลอดภัย</h5>
                            <p class="card-text text-muted">จำกัดสิทธิ์การเข้าถึงเฉพาะบุคลากรและนักศึกษาภายในวิทยาลัย ด้วยบัญชี Google </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date("Y"); ?> Bangna Commercial College </p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script src="script.js"></script>

</body>
</html>