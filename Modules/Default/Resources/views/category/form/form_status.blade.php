<!-- form status -->
 <div class="col-md-12">
    <label class="form-label">หมวดหมู่หลัก</label>
    <select name="parent_id" class="form-control select2" data-placeholder="หมวดหมู่หลัก">
        <option value="0">-- เลือกหมวดหมู่หลัก --</option>
        @foreach ($parents as $parent)
            @if (!empty($category->id) && $parent->id == $category->parent_id)
                <option value="{{ $parent->id }}" selected="selected">
                    {{ str_pad('', $parent->level, '-', STR_PAD_LEFT) . $parent->name_th }}
                </option>
            @else
                @if (empty($category->id) || (!empty($category->id) && $parent->id != $category->id) ) 
                    <option value="{{ $parent->id }}">
                        {{ str_pad('', $parent->level, '-', STR_PAD_LEFT) . $parent->name_th }}
                    </option>
                @endif
            @endif
        @endforeach
    </select>
</div>

<div class="col-md-12">
    <p></p>
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="status" name="status" checked>
        <label class="form-check-label" for="flexSwitchCheckChecked">สถานะ</label>
    </div>
</div>
<!-- .form status -->

