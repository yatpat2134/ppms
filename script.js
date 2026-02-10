const admins = ['anuchar2446@gmail.com']; 
var table;
var isAdmin = false;
var currentUserEmail = ''; 
var currentUserName = '';  

$(document).ready(function() {
    checkSession();
    
    // นับคนเข้าเว็บ
    $.post('api.php', { action: 'site_visit' }, function(res) {
        $('#totalVisitors').text(res.visits);
    }, 'json');

    // ==========================================
    // ส่วนที่เพิ่ม: Logic การกรองข้อมูล (Filter)
    // ==========================================
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var selectedYear = $('#filterYear').val();
            var selectedDept = $('#filterDept').val();
            
            // ดึงข้อมูลดิบของแถวนั้น (ไม่ใช่ HTML)
            if (!table) return true; // กัน Error ตอนโหลดครั้งแรก
            var rowData = table.row(dataIndex).data();

            if (!rowData) return true;

            // เช็คปีการศึกษา (ถ้าเลือกแล้วไม่ตรง ให้ซ่อน)
            if (selectedYear && rowData.academic_year != selectedYear) {
                return false;
            }
            // เช็คแผนกวิชา (ถ้าเลือกแล้วไม่ตรง ให้ซ่อน)
            if (selectedDept && rowData.department != selectedDept) {
                return false;
            }
            return true;
        }
    );
    // ==========================================
    // จบส่วนที่เพิ่ม
    // ==========================================

    // ตัวกรอง
    $('#customSearch').on('keyup', function() { table.search(this.value).draw(); });
    // เมื่อเปลี่ยน Dropdown ให้สั่งวาดตารางใหม่ (มันจะไปเรียกฟังก์ชันข้างบนทำงาน)
    $('#filterYear, #filterDept').on('change', function() { table.draw(); });

    // บันทึกโครงงาน (ทั้งเพิ่มและแก้ไข)
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
});

function checkSession() {
    const storedData = localStorage.getItem('user_data');
    if (storedData) {
        const data = JSON.parse(storedData);
        
        // ✅ เช็คซ้ำอีกรอบ ถ้าไม่ใช่ admin และไม่ใช่ @bncc.ac.th ให้ดีดออก
        if (!admins.includes(data.email) && !data.email.endsWith('@bncc.ac.th')) {
            logout(); 
            return;
        }
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

    $('#google-btn-wrapper').hide();
    $('#user-info').addClass('d-flex').show();
    $('#logout-btn').show();
    $('#user-name').text(data.name);
    $('#user-email').text(data.email);
    $('#login-alert').hide(); 
    $('#main-content').fadeIn(); 
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
            let years = [...new Set(json.data.map(item => item.academic_year))].sort().reverse();
            let depts = [...new Set(json.data.map(item => item.department))].sort();
            
            let yearSelect = $('#filterYear');
            yearSelect.html('<option value="">ทั้งหมด</option>');
            years.forEach(y => { if(y) yearSelect.append(`<option value="${y}">${y}</option>`); });

            let deptSelect = $('#filterDept');
            deptSelect.html('<option value="">ทั้งหมด</option>');
            depts.forEach(d => { if(d) deptSelect.append(`<option value="${d}">${d}</option>`); });
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
                    return `<span class="badge badge-year text-white mb-1">${row.academic_year}</span><br><span class="badge-dept">${data}</span>`;
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

// ✅✅✅ แก้ไขส่วนล็อกอิน ให้เช็คอีเมลก่อนเข้าใช้งาน ✅✅✅
function handleCredentialResponse(response) { 
    const data = JSON.parse(decodeURIComponent(window.atob(response.credential.split('.')[1].replace(/-/g, '+').replace(/_/g, '/')).split('').map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)).join(''))); 
    
    // ตรวจสอบเงื่อนไข: ต้องเป็น Admin หรือ อีเมลลงท้าย @bncc.ac.th เท่านั้น
    if (!admins.includes(data.email) && !data.email.endsWith('@bncc.ac.th')) {
        Swal.fire({
            icon: 'error',
            title: 'เข้าสู่ระบบไม่ได้',
            text: 'กรุณาใช้อีเมล @bncc.ac.th เท่านั้น'
        });
        return; // หยุดการทำงาน ไม่บันทึกข้อมูล
    }

    localStorage.setItem('user_data', JSON.stringify(data)); 
    updateUserInterface(data);
    location.reload(); // รีโหลดเพื่อให้สถานะล็อกอินทำงานสมบูรณ์
}

function logout() { 
    localStorage.removeItem('user_data'); 
    location.reload(); 
}
function clearFilter() {
    $('#customSearch').val('');
    $('#filterDept').val('');
    $('#filterYear').val('');
    table.search('').columns().search('').draw();
}