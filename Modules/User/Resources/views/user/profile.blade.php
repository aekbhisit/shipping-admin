<div class="mb-3">
    <label for="name" class="form-label">ชื่อ</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="ชื่อ"
        value="{{ !empty($user->name) ? $user->name : '' }}" required>
</div>
<div class="mb-3">
    <label for="username" class="form-label">ชื่อผู้ใช้</label>
    <input <?= !empty($user->id) ? 'readonly' : '' ?> type="text" class="form-control" name="username" id="username"
        placeholder="Username" value="{{ !empty($user->username) ? $user->username : '' }}">
</div>
<div class="mb-3">
    <label for="email" class="form-label">อีเมล์</label>
    <input type="email" class="form-control" name="email" placeholder="youremail@domain.com"
        value="{{ !empty($user->email) ? $user->email : '' }}" required>
</div>
<div class="mb-3">
    <label for="password" class="form-label">รหัสผ่าน</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="รหัสผ่าน"
        value="{{ !empty($user->password) ? '********' : null }}">
</div>
<div class="mb-3">
    <label for="re_password" class="form-label">ยืนยันรหัสผ่าน</label>
    <input type="password" class="form-control" id="re_password" name="re_password" placeholder="ยืนยันรหัสผ่าน"
        value="{{ !empty($user->password) ? '********' : null }}">
</div>
<input type="hidden" name="group_id" value="{{ !empty($user->group_id) ? $user->group_id : 2 }}">
{{-- <div class="">
    <label for="group_id" class="form-label">กลุ่ม</label>
    <select name="group_id" id="group_id" class="form-control select2" data-placeholder="Choose Parent">
        @foreach ($groups as $group)
            @if (!empty($user->group_id) && $group->id == $user->group_id)
                <option value="{{ $group->id }}" selected="selected">
                    {{ $group->name }}</option>
            @else
                <option value="{{ $group->id }}">{{ $group->name }}</option>
            @endif
        @endforeach
    </select>
</div> --}}
<div>
    <label for="status" class="form-label required">สถานะ</label>
    <div class="button button-r btn-switch">
        <input type="checkbox" class="checkbox" id="status" name="status" value="1"
            {{ isset($user->status) ? ($user->status == 1 ? 'checked' : '') : 'checked' }}>
        <div class="knobs"></div>
        <div class="layer"></div>
    </div>
</div>
