<!-- form en -->
<div class="tab-pane fade show" id="sub_tab_en" role="tabpanel">
    <div class="row">
        <div class="col-md-12">
             <div class="alert alert-outline-info shadow-sm alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-info"><i class='bx bx-info-square'></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-primary">คุณกำลังใส่ข้อมูล</h6>
                        <div>ภาษาอังกฤษ !</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <div class="mb-3">
                <label for="name_th" class="form-label">ชื่อ EN</label>
                <input type="text" class="form-control" id="name_en" name="name_en" placeholder="ชื่อ" value="{{ !empty($category->name_en) ? $category->name_en : '' }}">
                <input id="param" type="hidden" class="form-control" name="params"
                    value="" placeholder="param">
            </div>
            
            <div class="mb-3">
                <label for="name_th" class="form-label">คำอธิบาย</label>
                <textarea  class="form-control" id="desc_en" name="desc_en" rows="4" placeholder="คำอธิบาย">{{ !empty($category->desc_en) ? $category->desc_en : '' }}</textarea>
            </div>

             <div class="mb-3">
                <label for="name_th" class="form-label">รายละเอียด</label>
                <textarea class="form-control texteditor" id="detail_en"  name="detail_en" rows="4" placeholder="รายละเอียด">{{ !empty($category->detail_en) ? $category->detail_en : '' }}</textarea>
            </div>
        </div>

    </div>
</div>
<!-- .form en -->