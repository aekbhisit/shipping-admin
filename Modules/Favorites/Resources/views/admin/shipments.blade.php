@extends('layouts.master')

@section('title', 'การจัดส่งที่ชอบ')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    <i class="bx bx-package me-2"></i>
                                    การจัดส่งที่ชอบ
                                </h5>
                                <p class="mb-0 text-muted">รายการจัดส่งที่คุณได้ทำเครื่องหมายว่าเป็นรายการโปรด</p>
                            </div>
                            <div class="ms-auto">
                                <span class="badge bg-primary">{{ $favorites->total() }} รายการ</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($favorites->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>รหัสการจัดส่ง</th>
                                            <th>ลูกค้า</th>
                                            <th>สถานะ</th>
                                            <th>วันที่สร้าง</th>
                                            <th>หมายเหตุ</th>
                                            <th>การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($favorites as $favorite)
                                            @if($favorite->favorable)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $favorite->favorable->tracking_number ?? 'N/A' }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ $favorite->favorable->customer_name ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusClass = match($favorite->favorable->status ?? '') {
                                                                'draft' => 'bg-secondary',
                                                                'quoted' => 'bg-info',
                                                                'confirmed' => 'bg-warning',
                                                                'picked_up' => 'bg-primary',
                                                                'in_transit' => 'bg-info',
                                                                'delivered' => 'bg-success',
                                                                default => 'bg-secondary'
                                                            };
                                                            $statusText = match($favorite->favorable->status ?? '') {
                                                                'draft' => 'ร่าง',
                                                                'quoted' => 'เสนอราคาแล้ว',
                                                                'confirmed' => 'ยืนยันแล้ว',
                                                                'picked_up' => 'รับพัสดุแล้ว',
                                                                'in_transit' => 'กำลังจัดส่ง',
                                                                'delivered' => 'จัดส่งสำเร็จ',
                                                                default => 'ไม่ทราบสถานะ'
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                                    </td>
                                                    <td>
                                                        {{ $favorite->created_at->format('d/m/Y H:i') }}
                                                    </td>
                                                    <td>
                                                        <span class="favorite-notes" data-id="{{ $favorite->id }}">
                                                            {{ $favorite->notes ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary edit-notes" 
                                                                    data-id="{{ $favorite->id }}" 
                                                                    data-notes="{{ $favorite->notes ?? '' }}">
                                                                <i class="bx bx-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-favorite" 
                                                                    data-id="{{ $favorite->id }}">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-3">
                                {{ $favorites->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bx bx-package bx-lg text-muted mb-3"></i>
                                <h5 class="text-muted">ยังไม่มีรายการจัดส่งที่ชอบ</h5>
                                <p class="text-muted">คุณสามารถทำเครื่องหมายรายการจัดส่งเป็นรายการโปรดได้จากหน้าจัดการการจัดส่ง</p>
                                <a href="{{ route('admin.shipment.branch.index') }}" class="btn btn-primary">
                                    <i class="bx bx-plus me-2"></i>
                                    ไปยังหน้าจัดการการจัดส่ง
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Notes Modal -->
<div class="modal fade" id="editNotesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">แก้ไขหมายเหตุ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editNotesForm">
                    <input type="hidden" id="favoriteId" name="favorite_id">
                    <div class="mb-3">
                        <label for="notes" class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="เพิ่มหมายเหตุสำหรับรายการนี้..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" id="saveNotes">บันทึก</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Edit notes
    $('.edit-notes').click(function() {
        const id = $(this).data('id');
        const notes = $(this).data('notes');
        
        $('#favoriteId').val(id);
        $('#notes').val(notes);
        $('#editNotesModal').modal('show');
    });
    
    // Save notes
    $('#saveNotes').click(function() {
        const id = $('#favoriteId').val();
        const notes = $('#notes').val();
        
        $.ajax({
            url: `/admin/favorites/notes/${id}`,
            method: 'PUT',
            data: {
                notes: notes,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Update the notes display
                    $(`.favorite-notes[data-id="${id}"]`).text(notes || '-');
                    $(`.edit-notes[data-id="${id}"]`).data('notes', notes);
                    
                    $('#editNotesModal').modal('hide');
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: 'อัปเดตหมายเหตุเรียบร้อยแล้ว',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'ไม่สามารถอัปเดตหมายเหตุได้'
                });
            }
        });
    });
    
    // Remove favorite
    $('.remove-favorite').click(function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'ยืนยันการลบ',
            text: 'คุณต้องการลบรายการนี้ออกจากรายการโปรดหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/favorites/remove/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove the row
                            $(`.remove-favorite[data-id="${id}"]`).closest('tr').fadeOut();
                            
                            // Update count
                            const currentCount = parseInt($('.badge').text().split(' ')[0]);
                            $('.badge').text(`${currentCount - 1} รายการ`);
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: 'ลบรายการออกจากรายการโปรดเรียบร้อยแล้ว',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: 'ไม่สามารถลบรายการได้'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush 