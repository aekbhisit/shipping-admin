<!-- form-th -->
<div class="tab-pane fade show active " id="sub_tab_th" role="tabpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-outline-info shadow-sm alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-info"><i class='bx bx-info-square'></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-primary">คุณกำลังใส่ข้อมูล</h6>
                        <div>ภาษาไทย !</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <div class="mb-3">
                <label for="name_th" class="form-label">ชื่อ TH</label>
                <input type="text" class="form-control" id="name_th" name="name_th" placeholder="ชื่อ" value="{{ !empty($category->name_th) ? $category->name_th : '' }}">
                <input id="param" type="hidden" class="form-control" name="params"
                    value="" placeholder="param">
            </div>
            
            <div class="mb-3">
                <label for="name_th" class="form-label">คำอธิบาย</label>
                <textarea  class="form-control" id="desc_th" name="desc_th" rows="4" placeholder="คำอธิบาย">{{ !empty($category->desc_th) ? $category->desc_th : '' }}</textarea>
            </div>

             <div class="mb-3">
                <label for="name_th" class="form-label">รายละเอียด</label>
                <textarea class="form-control texteditor" id="detail_th"  name="detail_th" rows="4" placeholder="รายละเอียด">{{ !empty($category->detail_th) ? $category->detail_th : '' }}</textarea>
            </div>
        </div>

    </div>
</div>
<!-- .form-th -->
     