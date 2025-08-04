@extends('layouts.form')
@section('styles')

@endsection


@section('content')
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Forms</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
<a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Form Elements</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary"><i class="fadeIn animated bx bx-arrow-back"></i> กลับ</button>
                </div>
            </div>
        </div>
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-xl-11 mx-auto">

                <div class="card border-top border-0 border-4 border-primary">
                    <div class="card-body p-5">
                        <div class="card-title d-flex align-items-center">
                            <div><i class="bx bxs-user me-1 font-22 text-primary"></i>
                            </div>
                            <h5 class="mb-0 text-primary">User Registration</h5>
                        </div>
                        <hr>
                        <form class="row g-3">
                            <div class="col-md-6">
                                <label for="inputFirstName" class="form-label">Text</label>
                                <input type="email" class="form-control" id="Text">
                            </div>
                            <div class="col-md-6">
                                <label for="inputNumber" class="form-label">number</label>
                                <input type="number" class="form-control" id="inputNumber">
                            </div>
                            <div class="col-md-6">
                                <label for="inputEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="inputEmail">
                            </div>
                            <div class="col-md-6">
                                <label for="inputPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="inputPassword">
                            </div>
                            <div class="col-12">
                                <label for="inputAddress" class="form-label">Textarea</label>
                                <textarea class="form-control" id="inputAddress" placeholder="Address..." rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <label for="inputAddress2" class="form-label">Text editor</label>
                                <textarea class="form-control texteditor" id="inputAddress2" placeholder="Address 2..." rows="3"></textarea>
                            </div>
                           
                            <div class="col-md-4">
                                <label for="inputState" class="form-label">Select</label>
                                <select id="inputState" class="form-select">
                                    <option selected>value 1</option>
                                    <option>value 2</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="inputState" class="form-label">Select2</label>
                                <select id="inputState" class="form-select select2">
                                    <option selected>value 1</option>
                                    <option>value 2</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="inputState" class="form-label">Select2</label>
                                <select id="inputState" class="form-select select2-show-search">
                                    <option selected>value 1</option>
                                    <option>value 2</option>
                                </select>
                            </div>
                           
                            <div class="col-12">
                                <label class="form-label">select2-ajax-with-image</label>
                                <select id="category_id" name="category_id" class="form-control select2-ajax-with-image form-select" 
                                    data-selected-id="{{ !empty($data->category_id) ? $data->category_id : '0' }}" 
                                    data-selected-text="{{ !empty($data->category) ? $data->category->name_th : '' }}" 
                                    data-selected-image="{{ !empty($data->category) ? $data->category->image : '' }}" 
                                    data-ajax-url="/admin/default/category/get_category" 
                                    data-lang-placeholder="เลือกหมวดหมู่" 
                                    data-lang-searching="กำลังโหลด" 
                                    data-parent-id="" >
                                </select>
                            </div>

                            <div class="form-group mb-3  col-lg-12">
                                <label class="form-label">select2-multiple-ajax-with-image</label>
                                <select id="vendors" name="vendors" class="form-control select2-ajax-with-image-multiple"      
                                    data-selected-id="{{ !empty($selectd_vendor['id']) ? implode(',',$selectd_vendor['id']) : '0' }}" 
                                    data-selected-text="{{ !empty($selectd_vendor['name']) ?implode(',',$selectd_vendor['name']) : '' }}" 
                                    data-selected-image="{{ !empty($selectd_vendor['image']) ? implode(',',$selectd_vendor['image']) : '' }}" 
                                    data-ajax-url="/admin/product/vendor/get_vendor" 
                                    data-lang-placeholder="ตัวแทนจำหน่าย" 
                                    data-lang-searching="กำลังโหลด" 
                                    data-parent-id="" >
                                </select>
                            </div>

                            <div class="form-group mb-3  col-lg-12 ">
                                <label class="form-label">Select2 Tag</label>
                                <select id="tags" name="tags[]" class="form-control select2-ajax-tags form-select" multiple="multiple" 
                                    data-selected-id="{{ !empty($product->tags) ? implode(',',json_decode($product->tags)) : '' }}" 
                                    data-selected-text="{{ !empty($product->tags) ? implode(',',json_decode($product->tags)) : '' }}" data-selected-image="" 
                                    data-ajax-url="/admin/hashtag/get_hashtag" 
                                    data-lang-placeholder="เลือกแท็ก" 
                                    data-lang-searching="กำลังโหลด" 
                                    data-parent-id="" >
                                </select>
                            </div>

                            <div class="col-12">
                                <?= image_upload($id = '3', $name = 'favicon', $label = __('websetting::module.field.favicon'), $image = !empty($data->favicon) ? $data->favicon : '', $size_recommend = ' 32 x 32px') ?>
                            </div>

                            <?php 
                            $files = (!empty($product->images))?json_decode($product->images,1): '' ;
                            ?>
                            <div class="row group-image"> 
                                <?php 
                                echo image_upload_multiple(
                                    $id="image_multi",
                                    $name="image_multi",
                                    $files=$files,
                                    $action="/admin/product/save_image_multi",
                                    $thumbnail_path="",
                                    $accept_files=".jpg,.png,.jpeg,.gif",
                                    $max_file=5,
                                    $upload_msg="Drop image here (or click) to capture/upload",
                                    $remove_msg="remove",
                                    $max_file_msg="You can not upload any more files.",
                                    $inputs = [
                                        'name_th'=>'ชื่อสินค้า (TH)',
                                        'name_en'=>'ชื่อสินค้า (EN)'
                                    ]
                                ) ;
                                ?>
                            </div> 

                            <div class="col-md-2">
                                <div class="form-check form-switch">
									<input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>
									<label class="form-check-label" for="flexSwitchCheckChecked">switch</label>
								</div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gridCheck">
                                    <label class="form-check-label" for="gridCheck">Check me out</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
									<label class="form-check-label" for="inlineRadio1">1</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
									<label class="form-check-label" for="inlineRadio2">2</label>
								</div>
                            </div>

                            <div class="col-12">
                                <label for="start_date" class="form-label">datetime picker</label>
                                <input type="text" class="form-control datetimepicker " id="start_date" name="start_date" placeholder="">
                            </div>

                            <div class="col-12">
                                <label for="start_date" class="form-label">date picker</label>
                                <input type="text" class="form-control datepicker " id="start_date" name="start_date" placeholder="">
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-info px-5"><i class="lni lni-save"></i> Save</button>
                                <button type="submit" class="btn btn-warning px-5"><i class="lni lni-close"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
               
            </div>
        </div>
        <!--end row-->
    </div>
</div>
<!--end page wrapper -->
@endsection('content')

@section('scripts')
   
    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/default.css') }}">
    <script src="{{ mix('js/default.js') }}"></script>
@endsection
