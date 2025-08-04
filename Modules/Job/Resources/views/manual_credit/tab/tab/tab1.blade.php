<div class="tab-pane fade show active" id="main_tab_1" role="tabpanel">
    <div class="col-xl-6 mx-auto">
    <form class="row g-3" id="customer_frm" name="customer_frm" method="POST" onsubmit="setSave(); return false;" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" id="id" value="{{ !empty($data->id) ? $data->id : '0' }}">
        {{-- field --}}

        <div class="col-md-12 ">
            <label for="name" class="form-label">ชื่อ</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ !empty($data->name) ? $data->name : '0' }}" >
        </div>
        <div class="col-md-6">
            <label for="name" class="form-label">เบอร์โทร</label>
            <input type="text" id="mobile" name="mobile" class="form-control" value="{{ !empty($data->mobile) ? $data->mobile : '0' }}" >
        </div>
        <div class="col-md-6">
            <label for="name" class="form-label">Line ID</label>
            <input type="text" id="line_id" name="line_id" class="form-control" value="{{ !empty($data->line_id) ? $data->line_id : '0' }}" >
        </div>
        <div class="col-md-6">
            <label for="password" class="form-label">รหัสผ่าน</label>
            <input type="password" id="password" name="password" class="form-control" >
        </div>
        <div class="col-md-6">
            <label for="re_password" class="form-label">ยืนยันรหัสผ่าน</label>
            <input type="password" id="re_password" name="re_password" class="form-control" >
        </div>
        <div class="col-md-6 mt-5">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="status" name="status" value="1"  {{ !empty($data->status) ? 'checked' : '' }}  >
                <label class="form-check-label" for="flexSwitchCheckChecked">สถานะ</label>
            </div>
        </div>
        <div class="col-md-6 mt-5">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox"  id="varify_otp" name="varify_otp" value="1" {{ !empty($data->varify_otp) ? 'checked' : '' }} >
                <label class="form-check-label" for="flexSwitchCheckChecked">ยืนยัน OTP</label>
            </div>
        </div>

        {{-- .field --}}
        <div class="col-12 text-center">
            <button type="submit" class="btn btn-info px-5"><i class="lni lni-save"></i> Save</button>
            <a href="{{ route('admin.customer.customer.index') }}">
                <button type="button" class="btn btn-warning px-5"><i class="lni lni-close"></i> Cancel</button>
            </a>
        </div>
    </form>
    </div>
</div>