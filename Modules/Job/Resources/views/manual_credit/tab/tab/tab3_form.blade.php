<!-- Modal -->
<div class="modal fade" id="customer-bank-frm" tabindex="-1" aria-labelledby="customer-user-frm-label" aria-hidden="true">
    <div class="modal-dialog">
        <form class="row g-3" id="customer_bank_frm" name="customer_bank_frm" method="POST" onsubmit="setSaveBank(); return false;" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="cust_id" id="cust_id_user" value="{{ !empty($data->id) ? $data->id : '0' }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customer-user-frm-label">เพิ่มบัญชีธนาคาร</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 ">
                        <label for="bank_id" class="form-label">ธนาคาร</label>
                        <select id="bank_id" name="bank_id" class="form-select select2-show-search">
                            <?php foreach($banks as $bank){ ?>
                                <option value="<?=$bank->id?>"><?=$bank->name?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-12 mt-3">
                        <label for="name" class="form-label">ชื่อบัญชี</label>
                        <input type="text" id="acc_name" name="acc_name" class="form-control" >
                    </div>
                    <div class="col-md-12 mt-3">
                        <label for="name" class="form-label">เลขที่บัญชี</label>
                        <input type="text" id="acc_no" name="acc_no" class="form-control" >
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