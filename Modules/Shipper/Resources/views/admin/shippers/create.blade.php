@extends('layouts.datatable')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Add New Shipper</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard.index') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.shippers.index') }}">Shippers</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Add New</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.shippers.index') }}">
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
                            <h5 class="mb-0"><i class="bx bx-plus me-2"></i>Shipper Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.shippers.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Shipper Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="code" class="form-label">Shipper Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                                   id="code" name="code" value="{{ old('code') }}" required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Unique identifier for the shipper (e.g., THAILAND_POST, JT_EXPRESS)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="api_base_url" class="form-label">API Base URL <span class="text-danger">*</span></label>
                                            <input type="url" class="form-control @error('api_base_url') is-invalid @enderror" 
                                                   id="api_base_url" name="api_base_url" value="{{ old('api_base_url') }}" required>
                                            @error('api_base_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Base URL for the carrier's API (e.g., https://api.dhl.com)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="api_version" class="form-label">API Version</label>
                                            <input type="text" class="form-control @error('api_version') is-invalid @enderror" 
                                                   id="api_version" name="api_version" value="{{ old('api_version') }}" 
                                                   placeholder="e.g., v1, v2">
                                            @error('api_version')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="api_documentation_url" class="form-label">API Documentation URL</label>
                                            <input type="url" class="form-control @error('api_documentation_url') is-invalid @enderror" 
                                                   id="api_documentation_url" name="api_documentation_url" value="{{ old('api_documentation_url') }}">
                                            @error('api_documentation_url')
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
                                    <label class="form-label">Supported Services</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="supported_services[]" 
                                                       value="express" id="service_express" {{ in_array('express', old('supported_services', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="service_express">
                                                    Express Delivery
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="supported_services[]" 
                                                       value="standard" id="service_standard" {{ in_array('standard', old('supported_services', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="service_standard">
                                                    Standard Delivery
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="supported_services[]" 
                                                       value="economy" id="service_economy" {{ in_array('economy', old('supported_services', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="service_economy">
                                                    Economy Delivery
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="supported_services[]" 
                                                       value="same_day" id="service_same_day" {{ in_array('same_day', old('supported_services', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="service_same_day">
                                                    Same Day Delivery
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="supported_services[]" 
                                                       value="next_day" id="service_next_day" {{ in_array('next_day', old('supported_services', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="service_next_day">
                                                    Next Day Delivery
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="supported_services[]" 
                                                       value="international" id="service_international" {{ in_array('international', old('supported_services', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="service_international">
                                                    International
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="supported_services[]" 
                                                       value="freight" id="service_freight" {{ in_array('freight', old('supported_services', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="service_freight">
                                                    Freight
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="supported_services[]" 
                                                       value="warehouse" id="service_warehouse" {{ in_array('warehouse', old('supported_services', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="service_warehouse">
                                                    Warehouse
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('supported_services')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="logo" class="form-label">Carrier Logo</label>
                                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                                   id="logo" name="logo" accept="image/*">
                                            @error('logo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Max size: 2MB. Allowed formats: JPEG, PNG, JPG, GIF</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.carriers.index') }}" class="btn btn-secondary">
                                        <i class="bx bx-x me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i>Create Carrier
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
    // Auto-generate code from name if empty
    document.getElementById('name').addEventListener('blur', function() {
        const codeField = document.getElementById('code');
        if (!codeField.value) {
            const name = this.value;
            if (name) {
                // Simple code generation - can be enhanced
                const code = name.replace(/[^a-zA-Z0-9]/g, '').toUpperCase().substring(0, 10);
                codeField.value = code;
            }
        }
    });
});
</script>
@endsection 