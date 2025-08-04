<hr>
<div class="row justify-content-between">
    <div class="col-auto">
        @if (roles('admin.job.manualcredit.deposit'))
            
        <a href="{{ route('admin.job.manualcredit.deposit') }}">
            <button type="button" class="btn btn-success px-5"> + เพิ่มเครดิต</button>
        </a>
        @endif
        @if (roles('admin.job.manualcredit.withdraw'))
            <a href="{{ route('admin.job.manualcredit.withdraw') }}">
                <button type="button" class="btn btn-warning px-5"> - ลดเครดิต</button>
            </a>
        @endif
    </div>
    <div class="col-auto">
        @if (roles('admin.job.manualcredit.promotion'))
            <div class="col text-right">
                <a href="{{ route('admin.job.manualcredit.promotion') }}">
                    <button type="button" class="btn btn-primary px-5"> ++ เพิ่มโปรโมชั่น</button>
                </a>
            </div>
        @endif
    </div>
</div>
<hr>
