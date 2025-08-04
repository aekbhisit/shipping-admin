@extends('layouts.form')
@section('styles')
@endsection


@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Module</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Page</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.admin_menu.admin_menu.index') }}">
                            <button type="button" class="btn btn-primary"><i class="fadeIn animated bx bx-arrow-back"></i>
                                กลับ</button>
                        </a>
                    </div>
                </div>
            </div>
            <!--end breadcrumb-->
            <div class="row">
                <div class="col-xl-11 mx-auto">
                    {{-- <h6 class="mb-0 text-uppercase">Basic Form</h6>
                <hr/> --}}
                    <div class="card border-top border-0 border-4 border-primary">
                        <div class="card-body p-5">
                            <div class="card-title d-flex align-items-center">
                                <div><i class="bx bxs-user me-1 font-22 text-primary"></i>
                                </div>
                                <h5 class="mb-0 text-primary">User Registration</h5>
                            </div>
                            <hr>
                            <form class="row g-3" id="menu_frm" name="menu_frm" method="POST"
                                onsubmit="setSave(); return false;" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" id="user_id"
                                    value="{{ !empty($data->id) ? $data->id : '0' }}">
                                {{-- field --}}
                                <div class="col-md-2">
                                    <label class="form-label">Icon</label>
                                    <p>
                                        <select class="icon-selector" id="icon" name="icon">
                                            <option value="">No icon</option>
                                            @if (!empty($icons))
                                                @foreach ($icons as $icon)
                                                    <option value="bx bx-{{ $icon }}"
                                                        {{ !empty($data->icon) && $data->icon == 'bx bx-' . $icon ? 'selected' : '' }}>
                                                        bx bx-{{ $icon }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </p>
                                </div>

                                <div class="col-md-10">
                                    <label for="name_th" class="form-label">เมนู</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="ชื่อ" value="{{ !empty($data->name) ? $data->name : '' }}">
                                </div>

                                <div class="col-2">
                                    <label for="inputEmail" class="form-label ">ประเภท</label>
                                    <select class="form-select" name="link_type" id="link_type"
                                        aria-label="Default select example" onchange="setLinkType(this.value)">
                                        <option value="1"
                                            {{ empty($data->link_type) || $data->link_type == 1 ? 'selected' : '' }}>Route
                                        </option>
                                        <option value="2"
                                            {{ !empty($data->link_type) && $data->link_type == 2 ? 'selected' : '' }}>URL
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-8 menu-link-type" id="show-route-select" style="display:none;">
                                    <label class="form-label">Route name</label>
                                    <select id="route_name" name="route_name" class="form-control select2-ajax-with-image"
                                        data-selected-id="{{ !empty($data->route_name) ? $data->route_name : '' }}"
                                        data-selected-text="{{ !empty($data->route_name) ? $data->route_name : '' }}"
                                        data-selected-image="" data-ajax-url="/admin/user/permission/get_route_name"
                                        data-lang-placeholder="Route name" data-lang-searching="กำลังโหลด"
                                        data-parent-id="">
                                    </select>
                                </div>

                                <div class="col-md-8 menu-link-type" id="show-url-input">
                                    <label for="name_th" class="form-label">เมนู</label>
                                    <input type="text" class="form-control" id="url" name="url"
                                        placeholder="URL" value="{{ !empty($data->url) ? $data->url : '' }}">
                                </div>

                                <div class="col-2">
                                    <label for="target" class="form-label ">ปิดหน้าใหม่</label>
                                    <select class="form-select" name="target" id="target">
                                        <option value="_self"
                                            {{ empty($data->target) || $data->target == '_self' ? 'selected' : '' }}>
                                            หน้าเดิม</option>
                                        <option value="_blank"
                                            {{ !empty($data->target) && $data->target == '_blank' ? 'selected' : '' }}>
                                            เปิดหน้าใหม่</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">เมนูหลัก</label>
                                    <select name="parent_id" class="form-control select2" data-placeholder="หมวดหมู่หลัก">
                                        <option value="0">-- เมนูหลัก --</option>
                                        @foreach ($parents as $parent)
                                            @if (!empty($data->id) && $parent->id == $data->parent_id)
                                                <option value="{{ $parent->id }}" selected="selected">
                                                    {{ str_pad('', $parent->level, '-', STR_PAD_LEFT) . $parent->name }}
                                                </option>
                                            @else
                                                @if (empty($data->id) || (!empty($data->id) && $parent->id != $data->id))
                                                    <option value="{{ $parent->id }}">
                                                        {{ str_pad('', $parent->level, '-', STR_PAD_LEFT) . $parent->name }}
                                                    </option>
                                                @endif
                                            @endif
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-12">
                                    <p></p>
                                    <div class="form-check form-switch">
                                        <label class="form-check-label" for="flexSwitchCheckChecked">Status</label>
                                        <input class="form-check-input" type="checkbox" name="status" id="status"
                                            value="1"
                                            {{ empty($data->id) || (!empty($data->status) && $data->status) ? 'checked' : '' }}>
                                    </div>
                                </div>

                                {{-- .field --}}
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-info px-5"><i class="lni lni-save"></i>
                                        Save</button>
                                    <a href="{{ route('admin.admin_menu.admin_menu.index') }}">
                                        <button type="button" class="btn btn-warning px-5"><i class="lni lni-close"></i>
                                            Cancel</button>
                                    </a>
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
    <!-- fontIconPicker -->
    <script src="{{ URL::asset('assets/plugins/fontIconPicker/js/jquery.fonticonpicker.min.js') }}"></script>
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/fontIconPicker/css/jquery.fonticonpicker.min.css') }}" />
    <link rel="stylesheet"
        href="{{ URL::asset('assets/plugins/fontIconPicker/css/jquery.fonticonpicker.grey.min.css') }}" />


    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/admin_menu.css') }}">
    <script src="{{ mix('js/admin_menu.js') }}"></script>
@endsection
