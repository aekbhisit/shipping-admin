@extends('layouts.form')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!-- page-header -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">ตั้งค่า</div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb ms-3 mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item">{{ __('admin.websetting') }}</li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="{{ route('admin.setting.tag.index') }}">Tag</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ !empty($data->id) ? 'แก้ไข' : 'เพิ่ม' }}
                        </li>
                    </ol>
                </nav>
            </div>
            <!-- End page-header -->


            <!-- row -->
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-top border-0 border-4 border-primary">
                        <form id="tag_frm" name="tag_frm" method="POST" onsubmit="setSave(); return false;"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ !empty($data->id) ? $data->id : '0' }}">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">ประเภท</label>
                                    <input type="text" class="form-control" name="type" placeholder="ประเภท"
                                        value="{{ !empty($data->type) ? $data->type : '' }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Head</label>
                                    <textarea class="form-control" name="head" id="head" rows="8" placeholder="<head>">{{ !empty($data->head) ? $data->head : '' }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Body</label>
                                    <textarea class="form-control" name="body" id="body" rows="8" placeholder="<body>">{{ !empty($data->body) ? $data->body : '' }}</textarea>
                                </div>
                                <div class="">
                                    <label for="status" class="form-label required">สถานะ</label>
                                    <div class="button button-r btn-switch">
                                        <input type="checkbox" class="checkbox" id="status" name="status" value="1"
                                            {{ isset($data->status) ? ($data->status == 1 ? 'checked' : '') : 'checked' }}>
                                        <div class="knobs"></div>
                                        <div class="layer"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer py-3">
                                <div class="btn-list">
                                    <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i>
                                        บันทึก</button>
                                    <a class="btn btn-warning" href="{{ route('admin.setting.tag.index') }}" role="button">
                                        <i class="bx bx-arrow-back"></i>ยกเลิก</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->
@endsection('content')

@section('scripts')
    <!-- core master js css -->
    <link rel="stylesheet" href="{{ mix('css/admin.css') }}">
    <script src="{{ mix('js/admin.js') }}"></script>
    <script src="{{ mix('js/lang.th.js') }}"></script>

    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/setting.css') }}">
    <script src="{{ mix('js/setting.tag.js') }}"></script>
@endsection
