@extends('layouts.datatable')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Branch Markups</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard.index') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.branches.index') }}">Branches</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.branches.show', $branch) }}">{{ $branch->name }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Markups</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.branches.show', $branch) }}">
                            <button type="button" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i>Back to Branch</button>
                        </a>
                    </div>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bx bx-dollar-circle me-2"></i>Markup Rules for {{ $branch->name }}
                            </h5>
                            <p class="text-muted mb-0">Configure markup percentages and rules for different carriers</p>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.branches.markups.update', $branch) }}" method="POST">
                                @csrf
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Carrier</th>
                                                <th>Markup %</th>
                                                <th>Min Amount</th>
                                                <th>Max %</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($markupData as $data)
                                            <tr>
                                                <td>
                                                    <strong>{{ $data['carrier']->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $data['carrier']->code }}</small>
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           class="form-control @error('markups.' . $data['carrier']->id . '.markup_percentage') is-invalid @enderror"
                                                           name="markups[{{ $data['carrier']->id }}][markup_percentage]"
                                                           value="{{ old('markups.' . $data['carrier']->id . '.markup_percentage', $data['percentage']) }}"
                                                           min="0" max="100" step="0.01" required>
                                                    @error('markups.' . $data['carrier']->id . '.markup_percentage')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           class="form-control @error('markups.' . $data['carrier']->id . '.min_markup_amount') is-invalid @enderror"
                                                           name="markups[{{ $data['carrier']->id }}][min_markup_amount]"
                                                           value="{{ old('markups.' . $data['carrier']->id . '.min_markup_amount', $data['min_amount']) }}"
                                                           min="0" step="0.01">
                                                    @error('markups.' . $data['carrier']->id . '.min_markup_amount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           class="form-control @error('markups.' . $data['carrier']->id . '.max_markup_percentage') is-invalid @enderror"
                                                           name="markups[{{ $data['carrier']->id }}][max_markup_percentage]"
                                                           value="{{ old('markups.' . $data['carrier']->id . '.max_markup_percentage', $data['max_percentage']) }}"
                                                           min="0" max="100" step="0.01" required>
                                                    @error('markups.' . $data['carrier']->id . '.max_markup_percentage')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" 
                                                               class="form-check-input @error('markups.' . $data['carrier']->id . '.is_active') is-invalid @enderror"
                                                               name="markups[{{ $data['carrier']->id }}][is_active]"
                                                               value="1"
                                                               {{ old('markups.' . $data['carrier']->id . '.is_active', $data['is_active']) ? 'checked' : '' }}>
                                                        <input type="hidden" name="markups[{{ $data['carrier']->id }}][carrier_id]" value="{{ $data['carrier']->id }}">
                                                        @error('markups.' . $data['carrier']->id . '.is_active')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <a href="{{ route('admin.branches.show', $branch) }}" class="btn btn-secondary">
                                        <i class="bx bx-x me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i>Save Markups
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Markup Information -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-info-circle me-2"></i>Markup Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <h6 class="text-primary mb-2">Markup Percentage</h6>
                                        <p class="text-muted mb-0">The percentage added to the base shipping cost</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <h6 class="text-success mb-2">Minimum Amount</h6>
                                        <p class="text-muted mb-0">The minimum markup amount in currency</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <h6 class="text-warning mb-2">Maximum Percentage</h6>
                                        <p class="text-muted mb-0">The maximum allowed markup percentage</p>
                                    </div>
                                </div>
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
@endsection 