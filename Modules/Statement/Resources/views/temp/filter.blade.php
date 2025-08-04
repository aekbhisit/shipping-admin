<form class="row g-3" onsubmit="setReloadDataTable(); return false; ">
    <div class="col-md-10">
        <div class="row">
            <div class="col-md-3">
                <label for="start_date" class="form-label">วันที่</label>
                <input type="text" class="form-control datepicker datatable_filter" id="start_date" name="filter[start]"
                    placeholder="วันที่">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">ถึงวันที่</label>
                <input type="text" class="form-control datepicker datatable_filter" id="end_date" name="filter[end]"
                    placeholder="ถึงวันที่">
            </div>
            <div class="col-md-3">
                <label for="acc_id" class="form-label ">Account</label>
                <select id="acc_id" name="filter[acc_id]"
                    class="form-control select2-ajax-with-image form-group datatable_filter form-select"
                    data-ajax-url="/admin/partner/banks/get_bank" data-lang-placeholder="เลือก Account"
                    data-lang-searching="กำลังโหลด">
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label datatable_filter">Status</label>
                <select class="form-select datatable_filter select2" id="status" name="filter[status]"
                    aria-placeholder="Status">
                    <option value="">Status</option>
                    <option value="0">รอ</option>
                    <option value="1">สำเร็จ</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-2 align-self-end d-flex">
        <button type="submit" class="btn btn-primary flex-grow-1 mx-1"><i class="lni lni-search-alt"></i>ค้นหา</button>
        <button type="buttion" onclick="window.location.reload(true);"
            class="btn btn-outline-secondary flex-grow-1 mx-1">เคลีย</button>
    </div>
</form>
<hr />
