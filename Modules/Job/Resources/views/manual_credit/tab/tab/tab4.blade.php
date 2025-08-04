<div class="tab-pane fade show" id="main_tab_4" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">

                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item active" aria-current="page">รายการ ฝาก-ถอน</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    {{-- <div class="btn-group">
                        <a href="{{ route('admin.customer.customer.index') }}">
                            <button type="button" class="btn btn-info"><i class="lni lni-plus"></i> เพิ่มบัญชี</button>
                        </a>
                    </div> --}}
                </div>
            </div>
            <hr>
            <div class="table-responsive">
                <table id="customer-job-datatable" class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ประเภท</th>
                            <th scope="col">รหัส</th>
                            <th scope="col">ยอด</th>
                            <th scope="col">ธนาคาร</th>
                            <th scope="col">สถานะ</th>
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
