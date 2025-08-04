@extends('layouts.datatable')

@section('styles')
<style>
.custom-switch .custom-control-label::before {
    left: -2.25rem;
    width: 1.75rem;
    height: 1rem;
    pointer-events: all;
    border-radius: 0.5rem;
}

.custom-switch .custom-control-label::after {
    top: calc(0.25rem + 2px);
    left: calc(-2.25rem + 2px);
    width: calc(1rem - 4px);
    height: calc(1rem - 4px);
    border-radius: 0.5rem;
}

.badge-sm {
    font-size: 0.7em;
}
</style>
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Carrier Management</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard.index') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Carriers</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.carriers.create') }}">
                            <button type="button" class="btn btn-primary"><i class="bx bx-plus me-1"></i>Add New Carrier</button>
                        </a>
                    </div>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-truck me-2"></i>Carrier Management</h5>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carriers as $carrier)
                                <tr>
                                    <td class="text-center">
                                        @if($carrier->logo_url)
                                            <img src="{{ $carrier->logo_url }}" alt="{{ $carrier->name }}" class="img-thumbnail" style="max-width: 50px;">
                                        @else
                                            <span class="text-muted">No Logo</span>
                                        @endif
                                    </td>
                                    <td>{{ $carrier->name }}</td>
                                    <td><span class="badge badge-secondary">{{ $carrier->code }}</span></td>
                                    <td><small>{{ Str::limit($carrier->api_base_url, 40) }}</small></td>
                                    <td>
                                        @if($carrier->supported_services)
                                            @foreach($carrier->supported_services as $service)
                                                <span class="badge badge-info badge-sm">{{ $service }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">None</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Enable/Disable Toggle --}}
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" 
                                                   class="custom-control-input status-toggle" 
                                                   id="status-{{ $carrier->id }}"
                                                   data-carrier-id="{{ $carrier->id }}"
                                                   {{ $carrier->is_active ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="status-{{ $carrier->id }}">
                                                @if($carrier->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.carriers.show', $carrier) }}" 
                                               class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.carriers.edit', $carrier) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.carriers.destroy', $carrier) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to deactivate this carrier?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Deactivate">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
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
        responsive: true,
        pageLength: 25,
        order: [[1, 'asc']], // Sort by name
        columnDefs: [
            { orderable: false, targets: [0, 6] } // Disable sorting for logo and actions
        ]
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
</script>
@endsection 