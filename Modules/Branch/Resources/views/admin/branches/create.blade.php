@extends('layouts.datatable')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Add New Branch</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard.index') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.branches.index') }}">Branches</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Add New</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.branches.index') }}">
                            <button type="button" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i>Back to List</button>
                        </a>
                    </div>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-plus me-2"></i>Branch Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.branches.store') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Branch Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="code" class="form-label">Branch Code</label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                                   id="code" name="code" value="{{ old('code') }}" 
                                                   placeholder="Leave empty for auto-generation">
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Will be auto-generated if left empty</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact_person" class="form-label">Contact Person <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                                   id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required>
                                            @error('contact_person')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone') }}" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="is_active" class="form-label">Status</label>
                                            <select class="form-select @error('is_active') is-invalid @enderror" 
                                                    id="is_active" name="is_active">
                                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('is_active')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Operating Hours</label>
                                    <div class="card">
                                        <div class="card-body">
                                            @php
                                                $days = [
                                                    'monday' => 'Monday',
                                                    'tuesday' => 'Tuesday', 
                                                    'wednesday' => 'Wednesday',
                                                    'thursday' => 'Thursday',
                                                    'friday' => 'Friday',
                                                    'saturday' => 'Saturday',
                                                    'sunday' => 'Sunday'
                                                ];
                                            @endphp
                                            
                                            @foreach($days as $dayKey => $dayName)
                                                <div class="row mb-2 align-items-center">
                                                    <div class="col-md-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input operating-day-toggle" 
                                                                   type="checkbox" 
                                                                   id="operating_{{ $dayKey }}" 
                                                                   name="operating_days[{{ $dayKey }}]" 
                                                                   value="1">
                                                            <label class="form-check-label fw-bold" for="operating_{{ $dayKey }}">
                                                                {{ $dayName }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small">Open Time</label>
                                                        <input type="time" 
                                                               class="form-control form-control-sm operating-time" 
                                                               name="operating_hours[{{ $dayKey }}][open]" 
                                                               disabled>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small">Close Time</label>
                                                        <input type="time" 
                                                               class="form-control form-control-sm operating-time" 
                                                               name="operating_hours[{{ $dayKey }}][close]" 
                                                               disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-secondary set-closed" 
                                                                data-day="{{ $dayKey }}">
                                                            <i class="bx bx-x"></i> Closed
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                            
                                            <hr>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="set-weekdays">
                                                        <i class="bx bx-calendar"></i> Set Weekdays (Mon-Fri)
                                                    </button>
                                                </div>
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="set-weekend">
                                                        <i class="bx bx-calendar"></i> Set Weekend (Sat-Sun)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Set operating hours for each day. Uncheck to mark as closed.</small>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">
                                        <i class="bx bx-x me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i>Create Branch
                                    </button>
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
document.addEventListener('DOMContentLoaded', function() {
    // Handle day toggle checkboxes
    document.querySelectorAll('.operating-day-toggle').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const dayKey = this.id.replace('operating_', '');
            const timeInputs = document.querySelectorAll(`input[name="operating_hours[${dayKey}][open]"], input[name="operating_hours[${dayKey}][close]"]`);
            
            timeInputs.forEach(function(input) {
                input.disabled = !checkbox.checked;
                if (!checkbox.checked) {
                    input.value = '';
                }
            });
        });
    });

    // Handle "Set Closed" buttons
    document.querySelectorAll('.set-closed').forEach(function(button) {
        button.addEventListener('click', function() {
            const dayKey = this.getAttribute('data-day');
            const checkbox = document.getElementById(`operating_${dayKey}`);
            const timeInputs = document.querySelectorAll(`input[name="operating_hours[${dayKey}][open]"], input[name="operating_hours[${dayKey}][close]"]`);
            
            checkbox.checked = false;
            timeInputs.forEach(function(input) {
                input.disabled = true;
                input.value = '';
            });
        });
    });

    // Set Weekdays (Mon-Fri) button
    document.getElementById('set-weekdays').addEventListener('click', function() {
        const weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        weekdays.forEach(function(dayKey) {
            const checkbox = document.getElementById(`operating_${dayKey}`);
            const openInput = document.querySelector(`input[name="operating_hours[${dayKey}][open]"]`);
            const closeInput = document.querySelector(`input[name="operating_hours[${dayKey}][close]"]`);
            
            checkbox.checked = true;
            openInput.disabled = false;
            closeInput.disabled = false;
            
            if (!openInput.value) openInput.value = '09:00';
            if (!closeInput.value) closeInput.value = '18:00';
        });
    });

    // Set Weekend (Sat-Sun) button
    document.getElementById('set-weekend').addEventListener('click', function() {
        const weekend = ['saturday', 'sunday'];
        weekend.forEach(function(dayKey) {
            const checkbox = document.getElementById(`operating_${dayKey}`);
            const openInput = document.querySelector(`input[name="operating_hours[${dayKey}][open]"]`);
            const closeInput = document.querySelector(`input[name="operating_hours[${dayKey}][close]"]`);
            
            checkbox.checked = true;
            openInput.disabled = false;
            closeInput.disabled = false;
            
            if (!openInput.value) openInput.value = '10:00';
            if (!closeInput.value) closeInput.value = '16:00';
        });
    });
});
</script>
@endsection 