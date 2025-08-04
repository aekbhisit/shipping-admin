<div class="tab-pane fade show" id="main_tab_3" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">

                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item active" aria-current="page">รายการบัญชีลูกค้า</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                     <div class="btn-group">
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#customer-bank-frm"><i class="lni lni-plus"></i> เพิ่มบัญชี</button>
                    </div>
                </div>
            </div>
            <!-- tab::tab3 form -->
            @includeIf('customer::customer.tab.tab.tab3_form')
            <!-- tab::tab3 form -->

            <hr>
            <div class="table-responsive">
                <table id="customer-bank-datatable" class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ธนาคาร</th>
                            <th scope="col">ชื่อบัญชี</th>
                            <th scope="col">เลขที่บัญชี</th>
                            <th scope="col">วันที่อัพทเดท</th>
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
