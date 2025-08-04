@extends('layouts.datatable')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Customer Management</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.customers.index') }}">Customers</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.customers.show', $customer->id) }}">{{ $customer->name }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Back to Customer
                        </a>
                    </div>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-edit me-2"></i>Edit Customer: {{ $customer->name }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_type" class="form-label">Customer Type <span class="text-danger">*</span></label>
                                            <select name="customer_type" id="customer_type" class="form-select @error('customer_type') is-invalid @enderror" required>
                                                <option value="">Select Customer Type</option>
                                                <option value="business" {{ $customer->customer_type === 'business' ? 'selected' : '' }}>Business</option>
                                                <option value="corporate" {{ $customer->customer_type === 'corporate' ? 'selected' : '' }}>Corporate</option>
                                                <option value="individual" {{ $customer->customer_type === 'individual' ? 'selected' : '' }}>Individual</option>
                                            </select>
                                            @error('customer_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                                   value="{{ old('name', $customer->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                                   value="{{ old('email', $customer->email) }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                                   value="{{ old('phone', $customer->phone) }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tax_id" class="form-label">Tax ID</label>
                                            <input type="text" name="tax_id" id="tax_id" class="form-control @error('tax_id') is-invalid @enderror" 
                                                   value="{{ old('tax_id', $customer->tax_id) }}">
                                            @error('tax_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="branch_id" class="form-label">Branch</label>
                                            <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                                                <option value="">Select Branch</option>
                                                @foreach($branches ?? [] as $branch)
                                                    <option value="{{ $branch->id }}" {{ $customer->created_by_branch == $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('branch_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $customer->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-secondary">
                                                <i class="bx bx-x me-1"></i>Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bx bx-save me-1"></i>Update Customer
                                            </button>
                                        </div>
                                    </div>
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
            // Auto-fill name field based on customer type
            $('#customer_type').on('change', function() {
                var customerType = $(this).val();
                var nameField = $('#name');
                
                if (customerType === 'business' || customerType === 'corporate') {
                    nameField.attr('placeholder', 'Enter company name');
                } else if (customerType === 'individual') {
                    nameField.attr('placeholder', 'Enter individual name');
                } else {
                    nameField.attr('placeholder', 'Enter name');
                }
            });
            
            // Trigger change event on page load
            $('#customer_type').trigger('change');
        });
    </script>
@endsection 