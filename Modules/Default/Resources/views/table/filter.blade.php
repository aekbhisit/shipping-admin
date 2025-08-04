
<form class="row g-3" onsubmit="setReloadDataTable(); return false; ">
    <div class="col-md-10">
        <div class="row">
            <div class="col-2">
                <label for="start_date" class="form-label">start</label>
                <input type="text" class="form-control datetimepicker datatable_filter " id="start_date" name="start_date" placeholder="">
            </div>
            <div class="col-2">
                <label for="end_date" class="form-label">end</label>
                <input type="text" class="form-control datetimepicker datatable_filter " id="end_date" name="end_date" placeholder="">
            </div>
            <div class="col-2">
                <label for="inputEmail" class="form-label datatable_filter">Select</label>
                <select class="form-select" aria-label="Default select example">
                <option selected>Open this select menu</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
                </select>
            </div>
            <div class="col-2">
                <label for="inputEmail" class="form-label datatable_filter">Select</label>
                <select class="form-select" aria-label="Default select example">
                <option selected>Open this select menu</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
                </select>
            </div>
            <div class="col-2">
                <label for="inputEmail" class="form-label datatable_filter">Select</label>
                <select class="form-select" aria-label="Default select example">
                <option selected>Open this select menu</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
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