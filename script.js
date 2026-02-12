const admins = ['anuchar2446@gmail.com']; 
var table;
var isAdmin = false;
var currentUserEmail = ''; 
var currentUserName = '';  

$(document).ready(function() {
    // ✅ 1. สั่งให้ Animation (AOS) ทำงาน
    // (ต้องมีบรรทัดนี้ ไม่งั้นที่ใส่ data-aos ไปจะไม่ขยับครับ)
    if (typeof AOS !== 'undefined') {
        AOS.init();
    }

    // 2. ตรวจสอบสิทธิ์การเข้าใช้งานทันทีที่เปิดหน้าเว็บ
    checkSession();
    
    // 3. ถ้าหน้านี้มีตาราง (แปลว่าเป็นหน้า dashboard.php) ถึงจะรันโค้ดจัดการข้อมูล
    if ($('#projectTable').length > 0) {
        
        // นับคนเข้าเว็บ
        $.post('api.php', { action: 'site_visit' }, function(res) {
            $('#totalVisitors').text(res.visits);
        }, 'json');

        // ==========================================
        // Logic การกรองข้อมูล (Filter)
        // ==========================================
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var selectedYear = $('#filterYear').val();
                var selectedDept = $('#filterDept').val();
                
                if (!table) return true;
                var rowData = table.row(dataIndex).data();

                if (!rowData) return true;

                if (selectedYear && rowData.academic_year != selectedYear) {
                    return false;
                }
                if (selectedDept && rowData.department != selectedDept) {
                    return false;
                }
                return true;
            }
        );

        // Event Listeners สำหรับหน้า Dashboard
        $('#customSearch').on('keyup', function() { table.search(this.value).draw(); });
        $('#filterYear, #filterDept').on('change', function() { table.draw(); });

        // บันทึกโครงงาน
        $('#projectForm').on('submit', function(e) {
            e.preventDefault();
            
            let action = $('#formAction').val();
            let docFile = $('#docInput').val();

            if (action === 'create' && !docFile) {
                Swal.fire('ข้อมูลไม่ครบ', 'กรุณาอัปโหลดไฟล์เล่มโครงงานก่อนบันทึก', 'warning');
                return;
            }

            var formData = new FormData(this);
            formData.append('uploaded_by', currentUserEmail);

            $.ajax({
                url: 'api.php', type: 'POST', data: formData, contentType: false, processData: false,
                success: function(response) {
                    let res = (typeof response === 'string') ? JSON.parse(response) : response;
                    
                    if(res.status === 'success'){
                        $('#projectModal').modal('hide');
                        table.ajax.reload(null, false);
                        Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อย', 'success');
                    } else {
                        Swal.fire('ผิดพลาด', res.message || 'เกิดข้อผิดพลาด', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ผิดพลาด', 'เชื่อมต่อ Server ไม่ได้', 'error');
                }
            });
        });
    }
});

// ==========================================
// ฟังก์ชันจัดการ Session และ User Interface
// ==========================================

function checkSession() {
    const storedData = localStorage.getItem('user_data');
    const path = window.location.pathname;
    const page = path.split("/").pop(); // ชื่อไฟล์ปัจจุบัน

    // กรณีที่ 1: ยังไม่ล็อกอิน
    if (!storedData) {
        if (page === 'dashboard.php') {
            window.location.href = 'index.php';
        }
        return; 
    }

    // กรณีที่ 2: มีข้อมูลล็อกอินแล้ว
    const data = JSON.parse(storedData);
    
    // ตรวจสอบความถูกต้องของอีเมลอีกครั้ง
    if (!admins.includes(data.email) && !data.email.endsWith('@bncc.ac.th')) {
        logout(); 
        return;
    }

    // ถ้าล็อกอินแล้ว แต่อยู่หน้า Landing Page ให้ดีดไป Dashboard
    if (page === 'index.php' || page === '') {
        window.location.href = 'dashboard.php';
        return;
    }

    // ถ้าอยู่หน้า Dashboard แล้ว ให้แสดงผลข้อมูลผู้ใช้
    if (page === 'dashboard.php') {
        updateUserInterface(data);
    }
}

function updateUserInterface(data) {
    currentUserEmail = data.email; 
    currentUserName = data.name;   

    if (admins.includes(data.email)) {
        isAdmin = true;
        $('#role-badge').show();
        $('.btn-success').show(); 
    } else {
        isAdmin = false;
        $('#role-badge').hide();
    }

    // แสดงข้อมูล User ใน Navbar
    $('#google-btn-wrapper').hide();
    $('#user-info').addClass('d-flex').show();
    $('#logout-btn').show();
    $('#user-name').text(data.name);
    $('#user-email').text(data.email);
    $('#login-alert').hide(); 
    $('#main-content').fadeIn(); 
    
    // โหลดตาราง
    initTable(); 
}

function initTable() {
    if ($.fn.DataTable.isDataTable('#projectTable')) { $('#projectTable').DataTable().destroy(); }

    table = $('#projectTable').DataTable({
        "pageLength": 5,
        "lengthMenu": [ [5, -1], ["แสดงปกติ", "แสดงทั้งหมด"] ],
        "language": {
            "emptyTable": "ไม่มีข้อมูลในตาราง",
            "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            "infoEmpty": "แสดง 0 ถึง 0 จาก 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "search": "ค้นหา:",
            "zeroRecords": "ไม่พบข้อมูลที่ตรงกัน",
            "paginate": { "first": "หน้าแรก", "last": "หน้าสุดท้าย", "next": "ถัดไป", "previous": "ก่อนหน้า" }
        },
        "autoWidth": false,
        "order": [[ 2, "desc" ]], 
        "ajax": "api.php?action=read",
        "dom": 'lrtip', 
        
        "initComplete": function(settings, json) {
            // สร้างตัวเลือกใน Dropdown Filter
            if(json.data) {
                let years = [...new Set(json.data.map(item => item.academic_year))].sort().reverse();
                let depts = [...new Set(json.data.map(item => item.department))].sort();
                
                let yearSelect = $('#filterYear');
                yearSelect.html('<option value="">ทั้งหมด</option>');
                years.forEach(y => { if(y) yearSelect.append(`<option value="${y}">${y}</option>`); });

                let deptSelect = $('#filterDept');
                deptSelect.html('<option value="">ทั้งหมด</option>');
                depts.forEach(d => { if(d) deptSelect.append(`<option value="${d}">${d}</option>`); });
            }
        },

        "columns": [
            { 
                "data": "image_path", "className": "text-center", "width": "80px",
                "render": function(data) {
                    return data ? `<img src="${data}" class="cover-img shadow-sm">` : '<div class="cover-img bg-light text-muted small d-flex align-items-center justify-content-center">No Pic</div>';
                }
            },
            { 
                "data": "title",
                "render": function(data, type, row) {
                    let en = row.title_en ? `<div class="text-muted small mt-1"><i>${row.title_en}</i></div>` : '';
                    return `<div class="fw-bold text-primary" style="font-size:1.1em">${data}</div>${en}<div class="small text-muted mt-1">อัปโดย: ${row.uploaded_by || '-'}</div>`;
                }
            },
            { 
                "data": "department", "className": "text-center", "width": "120px",
                "render": function(data, type, row) {
                    return `<span class="badge badge-year text-dark mb-1">${row.academic_year}</span><br><span class="badge-dept">${data}</span>`;
                }
            },
            { 
                "data": "student_members",
                "render": function(data) { return `<div><i class="bi bi-people-fill"></i> ${data}</div>`; }
            },
            { 
                "data": "file_path", "className": "text-center", "width": "100px",
                "render": function(data) {
                    return data ? `<a href="${data}" target="_blank" class="btn btn-sm btn-outline-success w-100"><i class="bi bi-file-earmark-arrow-down"></i> โหลด</a>` : '-';
                }
            },
            {
                "data": "id", "className": "text-center", "width": "120px",
                "render": function(data, type, row) {
                    let adminBtns = isAdmin ? `<button class="btn btn-sm btn-warning mb-1" onclick="editProject(${data})"><i class="bi bi-pencil"></i></button> <button class="btn btn-sm btn-danger mb-1" onclick="deleteProject(${data})"><i class="bi bi-trash"></i></button>` : '';
                    return `<a href="detail.php?id=${data}" target="_blank" class="btn btn-sm btn-info text-white w-100 mb-1"><i class="bi bi-eye"></i> รายละเอียด</a><div>${adminBtns}</div>`;
                }
            }
        ]
    });
}

function editProject(id) {
    $.post('api.php', { action: 'read_single', id: id }, function(d) {
        $('#formAction').val('update');
        $('#projectId').val(d.id);
        $('#title').val(d.title);
        $('#title_en').val(d.title_en);
        $('#department').val(d.department);
        $('#academic_year').val(d.academic_year);
        $('#student').val(d.student_members);
        $('#advisor').val(d.advisor);
        $('#subject_teacher').val(d.subject_teacher); 
        $('#abstract').val(d.abstract);
        $('#desc').val(d.description);
        $('#modalTitle').text('แก้ไขโครงงาน');
        $('#projectModal').modal('show');
    }, 'json'); 
}

function openAddModal() { 
    $('#projectForm')[0].reset(); 
    $('#formAction').val('create'); 
    $('#projectId').val(''); 
    $('#modalTitle').text('เพิ่มโครงงานใหม่'); 
    $('#projectModal').modal('show'); 
}

function deleteProject(id) { 
    Swal.fire({ 
        title: 'ยืนยันการลบ?', 
        showCancelButton: true, confirmButtonText: 'ลบ', confirmButtonColor: '#d33' 
    }).then((result) => { 
        if (result.isConfirmed) { 
            $.post('api.php', { action: 'delete', id: id }, function(res) { 
                if(res.status === 'success') { 
                    table.ajax.reload(null, false); 
                    Swal.fire('ลบแล้ว', '', 'success'); 
                } 
            }, 'json'); 
        } 
    }); 
}

function clearFilter() {
    $('#customSearch').val('');
    $('#filterDept').val('');
    $('#filterYear').val('');
    if(table) table.search('').columns().search('').draw();
}

// ==========================================
// Google Login & Authentication Handlers
// ==========================================

function handleCredentialResponse(response) { 
    const data = JSON.parse(decodeURIComponent(window.atob(response.credential.split('.')[1].replace(/-/g, '+').replace(/_/g, '/')).split('').map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)).join(''))); 
    
    // ตรวจสอบเงื่อนไขอีเมล
    if (!admins.includes(data.email) && !data.email.endsWith('@bncc.ac.th')) {
        Swal.fire({
            icon: 'error',
            title: 'เข้าสู่ระบบไม่ได้',
            text: 'กรุณาใช้อีเมล @bncc.ac.th เท่านั้น'
        });
        return;
    }

    localStorage.setItem('user_data', JSON.stringify(data)); 
    
    // ล็อกอินผ่านแล้ว ให้กระโดดไปหน้า Dashboard
    window.location.href = 'dashboard.php';
}

function logout() { 
    localStorage.removeItem('user_data'); 
    // ออกระบบแล้ว ให้กระโดดกลับไปหน้า Landing Page
    window.location.href = 'index.php'; 
}

// ✅ ฟังก์ชันสำหรับปุ่ม Login มุมขวาบน (Navbar)
function handleTopLogin() {
    google.accounts.id.prompt((notification) => {
        if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
            // ถ้า Pop-up ไม่ขึ้น (เช่น ติด Cool-down หรือ User ปิดไป)
            console.log("เหตุผลที่ Popup ไม่ขึ้น:", notification.getNotDisplayedReason());

            // 1. เลื่อนหน้าจอลงไปหากล่อง Login ตัวหลัก
            const loginCard = document.getElementById('main-login-card');
            if(loginCard) {
                loginCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            // 2. เด้งแจ้งเตือนบอก User
            Swal.fire({
                icon: 'info',
                title: 'กรุณาเข้าสู่ระบบ',
                text: 'เลือกบัญชี Google ในกล่องด้านล่างเพื่อใช้งาน',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        }
    });
}