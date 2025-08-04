<!-- Modal -->
<div class="modal fade " id="customer-manualcredit-topup-frm" tabindex="-1" aria-labelledby="customer-user-frm-label" aria-hidden="true">
    <div class="modal-dialog ">
        <form class="row g-3" id="user_frm" name="user_frm" method="POST" onsubmit="setSave(); return false;" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="user_id" value="{{ !empty($user->id) ? $user->id : '0' }}">
            <div class="modal-content text-bg-success">
                <div class="modal-header ">
                    <h5 class="modal-title" id="customer-user-frm-label">ลดเครดิต</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="col-md-12 mt-3">
                        <label for="name" class="form-label">ยอดเงิน</label>
                        <input type="text" id="name" name="name" class="form-control" >
                    </div>
                    <div class="col-md-12 mt-3">
                        <label for="name" class="form-label">เหตุผล</label>
                        <input type="text" id="mobile" name="mobile" class="form-control" >
                    </div>
                
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info px-5"><i class="lni lni-save"></i> เพิ่ม</button>
                    <button type="button" class="btn btn-warning px-5" data-bs-dismiss="modal" ><i class="lni lni-close"></i> ยกเลิก</button>       
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal -->
<div class="modal fade " id="customer-manualcredit-withdraw-frm" tabindex="-1" aria-labelledby="customer-user-frm-label" aria-hidden="true">
    <div class="modal-dialog ">
        <form class="row g-3" id="user_frm" name="user_frm" method="POST" onsubmit="setSave(); return false;" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="user_id" value="{{ !empty($user->id) ? $user->id : '0' }}">
            <div class="modal-content text-bg-warning ">
                <div class="modal-header ">
                    <h5 class="modal-title" id="customer-user-frm-label">ลดเครดิต</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 mt-3">
                        <label for="name" class="form-label">ยอดเงิน</label>
                        <input type="text" id="name" name="name" class="form-control" >
                    </div>
                    <div class="col-md-12 mt-3">
                        <label for="name" class="form-label">เหตุผล</label>
                        <input type="text" id="mobile" name="mobile" class="form-control" >
                    </div>
                
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info px-5"><i class="lni lni-save"></i> เพิ่ม</button>
                    <button type="button" class="btn btn-warning px-5" data-bs-dismiss="modal" ><i class="lni lni-close"></i> ยกเลิก</button>       
                </div>
            </div>
        </form>
    </div>
</div>