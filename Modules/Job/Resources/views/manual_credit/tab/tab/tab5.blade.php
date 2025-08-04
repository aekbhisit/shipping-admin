<div class="tab-pane fade show" id="main_tab_5" role="tabpanel">
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
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#customer-manualcredit-topup-frm"><i class="lni lni-plus"></i> เพิ่มเครดิต</button>
                    </div>
                     <div class="btn-group">
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#customer-manualcredit-withdraw-frm"><i class="lni lni-minus"></i> ลดเครดิต</button>
                    </div>
                </div>
            </div>
            <!-- tab::tab3 form -->
            @includeIf('customer::customer.tab.tab.tab5_form')
            <!-- tab::tab3 form -->

            <hr>
            <div class="table-responsive">
                <table id="customer-manualcredit-datatable" class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ใบงาน</th>
                            <th scope="col">ประเภท</th>
                            <th scope="col">ยอดเงิน</th>
                            <th scope="col">เหตุผล</th>
                            <th scope="col">อ้างอิง</th>
                            <th scope="col">สถานะ</th>
                            <th scope="col">อัพเดท</th>
                            <th scope="col">โดย</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>