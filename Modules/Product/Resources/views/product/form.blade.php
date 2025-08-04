@extends('layouts.form')

@section('title', $id > 0 ? 'Edit Product' : 'Add New Product')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-{{ $id > 0 ? 'edit' : 'plus' }} me-2"></i>
                            {{ $id > 0 ? 'Edit Product' : 'Add New Product' }}
                        </h4>
                        <div class="card-actions">
                            <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <form id="product-form" method="POST" action="{{ route('admin.product.save') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Hidden ID field for editing -->
                        @if($id > 0)
                            <input type="hidden" name="id" value="{{ $id }}">
                        @endif
                        
                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Product Information Section -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-info-circle me-1"></i>Product Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="name" 
                                                   value="{{ $product->name ?? '' }}" 
                                                   placeholder="Enter product name"
                                                   required>
                                            <div class="form-text">Enter a unique name for the product</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" 
                                                      name="description" 
                                                      rows="4" 
                                                      placeholder="Enter product description">{{ $product->description ?? '' }}</textarea>
                                            <div class="form-text">Optional: Describe the product features and benefits</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pricing Section -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-dollar-sign me-1"></i>Pricing
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Price <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">฿</span>
                                                        <input type="number" 
                                                               class="form-control" 
                                                               name="price" 
                                                               value="{{ $product->price ?? '' }}" 
                                                               placeholder="0.00"
                                                               step="0.01"
                                                               min="0"
                                                               required>
                                                    </div>
                                                    <div class="form-text">Selling price to customers</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Cost</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">฿</span>
                                                        <input type="number" 
                                                               class="form-control" 
                                                               name="cost" 
                                                               value="{{ $product->cost ?? '' }}" 
                                                               placeholder="0.00"
                                                               step="0.01"
                                                               min="0">
                                                    </div>
                                                    <div class="form-text">Optional: Product cost for profit calculation</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if(isset($product) && $product->cost && $product->cost > 0)
                                        <div class="alert alert-info">
                                            <i class="fas fa-chart-line me-1"></i>
                                            <strong>Profit Margin:</strong> {{ $product->profit_margin }}%
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <!-- Product Image Section -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-image me-1"></i>Product Image
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        {!! image_upload(1, 'image', 'Product Image', $product->image ?? '', '800x600') !!}
                                        <div class="form-text mt-2">
                                            <small>
                                                <i class="fas fa-info-circle me-1"></i>
                                                Recommended size: 800x600 pixels<br>
                                                Supported formats: JPG, PNG, GIF<br>
                                                Maximum file size: 2MB
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Settings Section -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-cog me-1"></i>Settings
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="status" 
                                                       value="1"
                                                       id="status"
                                                       {{ (isset($product) && $product->status) || !isset($product) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status">
                                                    Active Status
                                                </label>
                                            </div>
                                            <div class="form-text">Enable to make product visible to customers</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times me-1"></i>Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i>
                                                {{ $id > 0 ? 'Update Product' : 'Save Product' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Form submission
    $('#product-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        // Disable submit button and show loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success == 1) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.msg,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '{{ route("admin.product.index") }}';
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        response.msg,
                        'error'
                    );
                    
                    // Show validation errors
                    if (response.errors) {
                        var errorText = '';
                        $.each(response.errors, function(key, value) {
                            errorText += value[0] + '\n';
                        });
                        Swal.fire(
                            'Validation Error!',
                            errorText,
                            'error'
                        );
                    }
                }
            },
            error: function(xhr) {
                var errorMsg = 'Something went wrong!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire(
                    'Error!',
                    errorMsg,
                    'error'
                );
            },
            complete: function() {
                // Re-enable submit button
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Price validation - ensure price >= cost
    $('input[name="price"], input[name="cost"]').on('input', function() {
        var price = parseFloat($('input[name="price"]').val()) || 0;
        var cost = parseFloat($('input[name="cost"]').val()) || 0;
        
        if (cost > 0 && price < cost) {
            $(this).addClass('is-invalid');
            if (!$(this).siblings('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Price should be greater than or equal to cost</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.card-header.bg-light {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.form-text {
    color: #6c757d;
}

.input-group-text {
    background-color: #e9ecef;
    border-color: #ced4da;
}

.form-check-label {
    font-weight: 500;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.gap-2 {
    gap: 0.5rem !important;
}
</style>
@endpush 