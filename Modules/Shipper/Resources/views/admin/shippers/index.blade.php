@extends('layouts.datatable')

@section('styles')
<style>
/* DataTable Styling */
#carriersTable {
    font-size: 0.9rem;
}

#carriersTable th {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    font-weight: 600;
    color: #495057;
}

#carriersTable td {
    vertical-align: middle;
    padding: 0.75rem;
}

/* Badge Styling */
.badge {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.35em 0.65em;
}

.badge.bg-primary {
    background-color: #0d6efd !important;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}

/* Code Styling */
code {
    background-color: #f8f9fa;
    color: #0d6efd;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}

/* Button Group Styling */
.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.btn-outline-info:hover {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: white;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: black;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

/* Logo Placeholder */
.logo-placeholder {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    color: #6c757d;
}

/* Service Badges */
.service-badge {
    display: inline-block;
    margin-bottom: 0.25rem;
}

/* Tools Column Styling - Horizontal Layout like Branch Module */
.tools-column .btn {
    margin-right: 0.25rem;
}

.tools-column .btn:last-child {
    margin-right: 0;
}

/* Tool Button Hover Effects */
.btn-outline-info:hover {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: white;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: black;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.btn-outline-success:hover {
    background-color: #198754;
    border-color: #198754;
    color: white;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #carriersTable {
        font-size: 0.8rem;
    }
    
    .badge {
        font-size: 0.7rem;
    }
}
</style>
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Shipper Management</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard.index') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Shippers</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.shippers.create') }}">
                            <button type="button" class="btn btn-primary"><i class="bx bx-plus me-1"></i>Add New Shipper</button>
                        </a>
                    </div>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-ship me-2"></i>Shipper Management</h5>
                        </div>
                <div class="card-body">
                    
                    {{-- Status Filter --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="statusFilter" class="form-control">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    {{-- DataTable Interface --}}
                    <div class="table-responsive">
                        <table id="carriersTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Logo</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>API Base URL</th>
                                    <th>Supported Services</th>
                                    <th>Status</th>
                                    <th>Tools</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                                                        <tbody>
                                {{-- DataTable will populate this via AJAX --}}
                            </tbody>
                            </tbody>
                        </table>
                    </div>
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
    // Initialize DataTable
    var table = $('#carriersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.shippers.datatable_ajax") }}',
            type: 'GET'
        },
        columns: [
            { data: 'logo', name: 'logo', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'code', name: 'code' },
            { data: 'api_endpoint', name: 'api_endpoint' },
            { data: 'supported_services', name: 'supported_services', orderable: false },
            { data: 'status', name: 'is_active' },
            { data: 'tools', name: 'tools', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        responsive: true,
        pageLength: 25,
        order: [[1, 'asc']], // Sort by name
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        }
    });

    // Status filter
    $('#statusFilter').on('change', function() {
        var status = $(this).val();
        if (status === '') {
            table.column(5).search('').draw();
        } else {
            var searchTerm = status === 'active' ? 'Active' : 'Inactive';
            table.column(5).search(searchTerm).draw();
        }
    });

    // Status toggle functionality
    $('.status-toggle').on('change', function() {
        var carrierId = $(this).data('carrier-id');
        var isActive = $(this).is(':checked');
        var toggle = $(this);
        var label = toggle.next('label').find('.badge');
        
        $.ajax({
            url: '/admin/carriers/' + carrierId + '/toggle-status',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                toggle.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    if (isActive) {
                        label.removeClass('badge-secondary').addClass('badge-success').text('Active');
                    } else {
                        label.removeClass('badge-success').addClass('badge-secondary').text('Inactive');
                    }
                    
                    // Show success message
                    toastr.success('Carrier status updated successfully');
                } else {
                    // Revert toggle state
                    toggle.prop('checked', !isActive);
                    toastr.error('Failed to update carrier status');
                }
            },
            error: function() {
                // Revert toggle state
                toggle.prop('checked', !isActive);
                toastr.error('An error occurred while updating carrier status');
            },
            complete: function() {
                toggle.prop('disabled', false);
            }
        });
    });
});

// Tool Functions
function testConnection(carrierId) {
    if (confirm('Test API connection for this carrier?')) {
        $.ajax({
            url: '/admin/shippers/' + carrierId + '/test-connection',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Connection test successful!');
                } else {
                    toastr.error('Connection test failed: ' + response.msg);
                }
            },
            error: function() {
                toastr.error('Connection test failed. Please try again.');
            }
        });
    }
}

function manageConfig(carrierId) {
    window.location.href = '/admin/shippers/' + carrierId + '/config';
}

function viewStats(carrierId) {
    window.location.href = '/admin/shippers/' + carrierId;
}

// Deactivate carrier function
function deactivateCarrier(carrierId) {
    if (confirm('Are you sure you want to deactivate this carrier?')) {
        $.ajax({
            url: '/admin/shippers/' + carrierId + '/deactivate',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Reload the DataTable to reflect changes
                $('#carriersTable').DataTable().ajax.reload();
                toastr.success('Carrier deactivated successfully');
            },
            error: function() {
                toastr.error('Failed to deactivate carrier');
            }
        });
    }
}
</script>
@endsection 