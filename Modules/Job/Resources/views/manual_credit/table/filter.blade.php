
<form class="row g-3" onsubmit="setReloadDataTable(); return false; ">
    <div class="col-md-10">
        <div class="row">
            <div class="col-2">
                <label for="start_date" class="form-label">วันที่เริ่ม</label>
                <input type="text" class="form-control datepicker datatable_filter " id="start_date" name="start_date" placeholder="">
            </div>
            <div class="col-2">
                <label for="end_date" class="form-label">ถึงวันที่</label>
                <input type="text" class="form-control datepicker datatable_filter " id="end_date" name="end_date" placeholder="">
            </div>
             <div class="col-2">
                <label for="job_type" class="form-label datatable_filter">ประเภท</label>
                <select class="form-select datatable_filter" name="job_type"  name="job_type" >
                <option value="0" selected>ทั้งหมด</option>
                <option value="1">เพิ่มเครดิต</option>
                <option value="2">ลดเครดิต</option>
                </select>
            </div>
            <div class="col-2">
                <label for="job_type" class="form-label datatable_filter">สถานะ</label>
                <select class="form-select datatable_filter" name="status"  name="status" >
                <option value="0" selected>ทั้งหมด</option>
                <option value="1">สำเร็จ</option>
                <option value="2">ล้มเหลว</option>
                </select>
            </div>
           
         </div>
    </div>
    <div class="col-md-2">
        <div class="col-auto"><p></p>
            <button type="submit"  class="btn btn-primary mb-3"><i class="lni lni-search-alt"></i> ค้นหา</button>
            <button type="buttion" onclick="window.location.reload(true);"  class="btn btn-outline-secondary mb-3">เคลีย</button>
        </div>
    </div>
</form><hr/>