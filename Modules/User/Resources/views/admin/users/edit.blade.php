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
                                <a href="{{ route('admin.users.index') }}">All Users</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Edit User</li>
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
                                <h5 class="mb-0 text-primary">Edit User: {{ $user->name }}</h5>
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

                            <form class="row g-3" id="main_user_edit_frm" name="main_user_edit_frm" method="POST" onsubmit="setMainUserEditSave(); return false;" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                
                                <div class="col-md-6">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" 
                                           value="{{ old('name', $user->name) }}" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" 
                                           value="{{ old('email', $user->email) }}" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password">
                                    <small class="text-muted">Leave blank to keep current password</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">User Type <span class="text-danger">*</span></label>
                                    <select name="user_type" class="form-control" required>
                                        <option value="">Select User Type</option>
                                        @foreach($userTypes as $key => $value)
                                            <option value="{{ $key }}" {{ old('user_type', $user->user_type) == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-6" id="branch-field" style="display: none;">
                                    <label class="form-label">Branch <span class="text-danger">*</span></label>
                                    <select name="branch_id" class="form-control">
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary px-4">
                                        Update User
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary px-4 ms-2">
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
    <script>
        $(document).ready(function() {
            $('select[name="user_type"]').change(function() {
                var userType = $(this).val();
                var branchField = $('#branch-field');
                
                if (userType === 'company_admin') {
                    branchField.hide();
                    $('select[name="branch_id"]').prop('required', false);
                } else {
                    branchField.show();
                    $('select[name="branch_id"]').prop('required', true);
                }
            });
            
            // Trigger on page load
            $('select[name="user_type"]').trigger('change');
        });
        
        setMainUserEditSave = function setMainUserEditSave() {
            event.preventDefault();
            var frm_data = new FormData($('#main_user_edit_frm')[0]);
            
            $.ajax({
                url: '{{ route("admin.users.update", $user->id) }}',
                type: 'POST',
                contentType: false,
                data: frm_data,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function beforeSend(xhr) {
                    // Simple validation
                    var isValid = true;
                    var errorMessages = [];
                    
                    // Check required fields
                    if (!$('input[name="name"]').val()) {
                        isValid = false;
                        errorMessages.push('Name is required');
                    }
                    
                    if (!$('input[name="email"]').val()) {
                        isValid = false;
                        errorMessages.push('Email is required');
                    } else if (!isValidEmail($('input[name="email"]').val())) {
                        isValid = false;
                        errorMessages.push('Please enter a valid email');
                    }
                    
                    // Check password if provided
                    if ($('input[name="password"]').val()) {
                        if ($('input[name="password"]').val().length < 6) {
                            isValid = false;
                            errorMessages.push('Password must be at least 6 characters');
                        }
                        
                        if (!$('input[name="password_confirmation"]').val()) {
                            isValid = false;
                            errorMessages.push('Password confirmation is required when changing password');
                        } else if ($('input[name="password"]').val() !== $('input[name="password_confirmation"]').val()) {
                            isValid = false;
                            errorMessages.push('Passwords do not match');
                        }
                    }
                    
                    if (!$('select[name="user_type"]').val()) {
                        isValid = false;
                        errorMessages.push('User type is required');
                    }
                    
                    // Check branch_id if user_type is not company_admin
                    if ($('select[name="user_type"]').val() !== 'company_admin') {
                        if (!$('select[name="branch_id"]').val()) {
                            isValid = false;
                            errorMessages.push('Branch is required for this user type');
                        }
                    }
                    
                    if (!isValid) {
                        alert('Please fix the following errors:\n' + errorMessages.join('\n'));
                        return false;
                    }
                    
                    return true;
                },
                success: function success(resp) {
                    if (resp.success) {
                        alert('Success: ' + resp.msg);
                        window.location.href = '{{ route("admin.users.index") }}';
                    } else {
                        alert('Error: ' + resp.msg);
                    }
                },
                error: function error(xhr, status, error) {
                    alert('Error: ' + error);
                }
            });
        };
        
        function isValidEmail(email) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    </script>
@endsection 