@php
    $isLiked = \Modules\Favorites\Entities\Favorite::isFavoritedByUser(
        auth()->id(), 
        $favorableType, 
        $favorableId, 
        $favoriteType ?? 'like'
    );
    
    $favoriteCount = \Modules\Favorites\Entities\Favorite::getFavoriteCount(
        $favorableType, 
        $favorableId, 
        $favoriteType ?? 'like'
    );
@endphp

<button class="btn btn-sm {{ $isLiked ? 'btn-danger' : 'btn-outline-danger' }} like-button" 
        data-type="{{ $favorableType }}" 
        data-id="{{ $favorableId }}" 
        data-favorite-type="{{ $favoriteType ?? 'like' }}"
        title="{{ $isLiked ? 'ลบออกจากรายการโปรด' : 'เพิ่มรายการโปรด' }}">
    <i class="bx {{ $isLiked ? 'bxs-heart' : 'bx-heart' }}"></i>
    @if($favoriteCount > 0)
        <span class="counter ms-1">{{ $favoriteCount }}</span>
    @endif
</button> 