@php
    $isBookmarked = \Modules\Favorites\Entities\Favorite::isFavoritedByUser(
        auth()->id(), 
        $favorableType, 
        $favorableId, 
        'bookmark'
    );
@endphp

<button class="btn btn-sm {{ $isBookmarked ? 'btn-warning' : 'btn-outline-warning' }} bookmark-button" 
        data-type="{{ $favorableType }}" 
        data-id="{{ $favorableId }}" 
        data-favorite-type="bookmark"
        title="{{ $isBookmarked ? 'ลบออกจากรายการที่บันทึก' : 'เพิ่มรายการที่บันทึก' }}">
    <i class="bx {{ $isBookmarked ? 'bxs-bookmark' : 'bx-bookmark' }}"></i>
</button> 