<div class="tab-pane fade show" id="main_tab_2" role="tabpanel">

    <div class="card">
        <div class="card-body">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item active" aria-current="page">รายการยูเซอร์</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#customer-user-frm"><i class="lni lni-plus"></i> เพิ่มยูเซอร์</button>
                    </div>
                </div>
            </div>
            

            <!-- tab::tab2 form -->
            @includeIf('customer::customer.tab.tab.tab2_form')
            <!-- tab::tab2 form -->
      

            <hr>
            <div class="table-responsive">
                
                <table id="customer-user-datatable" class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">เกมส์</th>
                            <th scope="col">ยูเซอร์</th>
                            <th scope="col">รหัสผ่าน</th>
                            <th scope="col">สถานะ</th>
                            <th scope="col">Login ล่าสุด</th>
                            <th scope="col">อัพเดท</th>
                            <th scope="col">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>