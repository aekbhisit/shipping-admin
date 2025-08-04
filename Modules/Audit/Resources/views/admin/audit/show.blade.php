@extends('layouts.datatable')
@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Audit Log Details</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.audit.index') }}">Audit Logs</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Details</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Audit Log #{{ $auditLog->id }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="150">User:</th>
                                            <td>{{ $auditLog->user_name ?? 'System' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Action:</th>
                                            <td>
                                                <span class="badge bg-{{ $auditLog->action == 'created' ? 'success' : ($auditLog->action == 'updated' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($auditLog->action) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Model:</th>
                                            <td>{{ $auditLog->subject_type }}</td>
                                        </tr>
                                        <tr>
                                            <th>IP Address:</th>
                                            <td>{{ $auditLog->ip_address }}</td>
                                        </tr>
                                        <tr>
                                            <th>User Agent:</th>
                                            <td>{{ $auditLog->user_agent }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="150">Created:</th>
                                            <td>{{ $auditLog->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated:</th>
                                            <td>{{ $auditLog->updated_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Description:</th>
                                            <td>{{ $auditLog->description }}</td>
                                        </tr>
                                        <tr>
                                            <th>Properties:</th>
                                            <td>
                                                @if($auditLog->properties)
                                                    <pre class="bg-light p-2 rounded">{{ json_encode($auditLog->properties, JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    <span class="text-muted">No properties</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($auditLog->properties && isset($auditLog->properties['old']) && isset($auditLog->properties['attributes']))
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6>Changes</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Field</th>
                                                    <th>Old Value</th>
                                                    <th>New Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($auditLog->properties['old'] as $field => $oldValue)
                                                    @if(isset($auditLog->properties['attributes'][$field]) && $auditLog->properties['attributes'][$field] != $oldValue)
                                                        <tr>
                                                            <td><strong>{{ ucfirst(str_replace('_', ' ', $field)) }}</strong></td>
                                                            <td>{{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}</td>
                                                            <td>{{ is_array($auditLog->properties['attributes'][$field]) ? json_encode($auditLog->properties['attributes'][$field]) : $auditLog->properties['attributes'][$field] }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('admin.audit.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->
@endsection

@section('scripts')
@endsection 