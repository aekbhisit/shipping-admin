<!-- Modal -->
<div class="modal fade" id="customer-user-frm" tabindex="-1" aria-labelledby="customer-user-frm-label" aria-hidden="true">
    <div class="modal-dialog">
        <form class="row g-3" id="customer_user_frm" name="customer_user_frm" method="POST" onsubmit="setSaveUser(); return false;" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="cust_id" id="cust_id_user" value="{{ !empty($data->id) ? $data->id : '0' }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customer-user-frm-label">เพิ่มยูเซอร์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="col-md-12">
                        <label for="game_id" class="form-label">เลือกเกมส์</label>
                        <select id="gm_id" name="gm_id" class="form-select select2-show-search">
                            <?php foreach($games as $game){ ?>
                            <option value="<?=$game->id?>"><?=$game->name?></option>
                            <?php } ?>
                        </select>
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
