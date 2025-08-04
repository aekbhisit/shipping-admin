@extends('layouts.form')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">ผู้ใช้งาน</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.user.staff.index') }}">Branch Staff</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ $mode === 'add' ? 'Add New' : 'Edit' }} Branch Staff
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!--end breadcrumb-->
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="card border-top border-0 border-4 border-primary">
                        <div class="card-body p-5">
                            <div class="card-title d-flex align-items-center">
                                <div><i class="bx bxs-user me-1 font-22 text-primary"></i></div>
                                <h5 class="mb-0 text-primary">
                                    {{ $mode === 'add' ? 'Add New' : 'Edit' }} Branch Staff
                                </h5>
                            </div>
                            <hr>
                            
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="row g-3" id="staff_user_frm" name="staff_user_frm" method="POST" onsubmit="setStaffSave(); return false;" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" id="user_id" value="{{ $user['id'] ?? '0' }}">
                                
                                <div class="col-md-4">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ $user['name'] ?? old('name') }}" required>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ $user['email'] ?? old('email') }}" required>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Branch <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_id" class="form-control" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" 
                                                {{ ($user['branch_id'] ?? old('branch_id')) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Password {!! $mode === 'add' ? '<span class="text-danger">*</span>' : '' !!}</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           {{ $mode === 'add' ? 'required' : '' }}>
                                    @if($mode !== 'add')
                                        <small class="text-muted">Leave blank to keep current password</small>
                                    @endif
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Confirm Password {!! $mode === 'add' ? '<span class="text-danger">*</span>' : '' !!}</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" 
                                           {{ $mode === 'add' ? 'required' : '' }}>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="status" value="1" 
                                               {{ ($user['status'] ?? 1) ? 'checked' : '' }}>
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary px-4">
                                        {{ $mode === 'add' ? 'Create' : 'Update' }} Branch Staff
                                    </button>
                                    <a href="{{ route('admin.user.staff.index') }}" class="btn btn-secondary px-4 ms-2">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->
@endsection

@section('scripts')
    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/user.css') }}">
    <script src="{{ mix('js/user_user.js') }}"></script>
    
    <script>
    // Override the setSave function for staff form
    setStaffSave = function setStaffSave() {
        event.preventDefault();
        var frm_data = new FormData($('#staff_user_frm')[0]);
        
        $.ajax({
            url: '{{ route("admin.user.staff.save") }}',
            type: 'POST',
            contentType: false,
            data: frm_data,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function beforeSend(xhr) {
                var rules = {
                    name: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    branch_id: {
                        required: true
                    },
                    password: {
                        minlength: 8,
                        required: function required() {
                            if ($('#user_id').val() == 0) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                    password_confirmation: {
                        minlength: 8,
                        equalTo: '#password',
                        required: function required() {
                            if ($('#user_id').val() == 0) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    }
                };
                
                var messages = {
                    name: 'Please enter user name',
                    email: {
                        required: 'Enter an email',
                        email: 'Enter valid email'
                    },
                    branch_id: {
                        required: 'Please select a branch'
                    },
                    password: {
                        required: 'Enter a password',
                        minlength: 'Enter at least {0} characters'
                    },
                    password_confirmation: {
                        required: 'Confirm your password',
                        minlength: 'Enter at least {0} characters',
                        equalTo: 'Passwords must match'
                    }
                };
                
                frm_validate($('#staff_user_frm'), rules, messages);

                if ($('#staff_user_frm').valid()) {
                    return $('#staff_user_frm').valid();
                } else {
                    global_loading(0);
                    noti('error', 'form data invalid');
                    return $('#staff_user_frm').valid();
                }
            },
            success: function success(resp) {
                if (resp.success) {
                    noti('success', resp.msg);
                    window.location.href = '{{ route("admin.user.staff.index") }}';
                } else {
                    noti('error', resp.msg);
                }
            },
            error: function error(xhr, status, error) {
                noti('error', 'An error occurred while saving');
            }
        });
    };
    </script>
@endsection 