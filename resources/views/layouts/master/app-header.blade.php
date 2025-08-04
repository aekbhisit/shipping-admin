<!--start header -->
<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
            </div>
            <div class="search-bar flex-grow-1">
                <div class="position-relative search-bar-box">
                    {{-- <input type="text" class="form-control search-control" placeholder="ค้นหาลูกค้า"> <span
                        class="position-absolute top-50 search-show translate-middle-y"><i
                            class='bx bx-search'></i></span> --}}
                    <select id="username" name="username" class="form-control select2-ajax-with-image form-select" 
                        data-selected-id="0" 
                        data-selected-text="0" 
                        data-selected-image="" 
                        data-ajax-url="/admin/customer/get_cust" 
                        data-lang-placeholder="เลือกยูเซอร์" 
                        data-lang-searching="กำลังโหลด" 
                        data-parent-id=""
                        onchange="goCustomerDetail(this.value)"
                        >
                        
                    </select>
                    <span class="position-absolute top-50 search-close translate-middle-y"><i
                            class='bx bx-x'></i></span>
                </div>
            </div>
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item mobile-search-icon">
                        <a class="nav-link" href="#"> <i class='bx bx-search'></i>
                        </a>
                    </li>
                    {{-- <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false"> <i class='bx bx-category'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="row row-cols-3 g-3 p-3">
                                <div class="col text-center">
                                    <div class="app-box mx-auto bg-gradient-cosmic text-white"><i
                                            class='bx bx-group'></i>
                                    </div>
                                    <div class="app-title">Teams</div>
                                </div>

                            </div>
                        </div>
                    </li> --}}
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span
                                class="alert-count" id="job_noti_cnt" data-cnt="0"><?=(isset($adminInit))?$adminInit['job_waiting']['job_waiting_cnt']:0?></span>
                            <i class='bx bx-bell'></i>
                            <audio id="new_job_noti_mp3" >
                                <source src="/noti.mp3">
                            </audio>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">
                                <div class="msg-header">
                                    <p class="msg-header-title">ใบงานมาใหม่</p>
                                    {{-- <p class="msg-header-clear ms-auto">Marks all as read</p> --}}
                                </div>
                                
                            </a>
                            <div class="header-notifications-list" id="new_job_noti_containter">
                                <?php 
                                
                                if(!empty($adminInit['job_waiting']['job_waiting_list'])){
                                    foreach($adminInit['job_waiting']['job_waiting_list'] as $jw){
                                        $type_class = ($jw->type['value']==1)?'success':'danger';
                                        $type_class = ($jw->type['value']==1)?'success':'danger';
                                ?>
                                <a class="dropdown-item" href="/admin/job/<?=$jw->type['type']?>/<?=$jw->id?>">
                                    <div class="d-flex align-items-center">
                                        <div class="notify bg-light-<?=$type_class?> text-<?=$type_class?>">
                                            <div class="font-22 text-<?=$type_class?>"><i class=" bx bx-bell-plus"></i></div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="msg-name">ใบงาน<?=$jw->type['text']?> <?=$jw->total_amount?> บาท<span class="msg-time float-end">timeAgo($jw->created_date)</span></h6>
                                            <p class="msg-info">user: <?=$jw->customer_user->username?> ใบงาน #<?=$jw->id?></p>
                                        </div>
                                    </div>
                                </a>
                                <?php 
                                    }
                                } 
                                ?>
                                
                                

                            </div>
                            <a href="/admin/job/">
                                <div class="text-center msg-footer">ดูใบงานใหม่ทั้งหมด</div>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item dropdown dropdown-large" style="display:none;">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span
                                class="alert-count">8</span>
                            <i class='bx bx-comment'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">
                                <div class="msg-header">
                                    <p class="msg-header-title">Messages</p>
                                    <p class="msg-header-clear ms-auto">Marks all as read</p>
                                </div>
                            </a>
                            <div class="header-message-list">
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center">
                                        <div class="user-online">
                                            {{-- <img src="{{ URL::asset('/assets/images/logo-icon.png') }}" class="msg-avatar" alt="user avatar"> --}}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="msg-name">Daisy Anderson <span class="msg-time float-end">5 sec
                                                    ago</span></h6>
                                            <p class="msg-info">The standard chunk of lorem</p>
                                        </div>
                                    </div>
                                </a>

                            </div>
                            <a href="javascript:;">
                                <div class="text-center msg-footer">View All Messages</div>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="user-box dropdown">
                <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ !empty(setting()->logo_header) && CheckFileInServer(setting()->logo_header) ? setting()->logo_header : URL::asset('assets/images/logo-icon.png') }}" class="user-img" alt="user avatar">
                    <div class="user-info ps-3">
                        <p class="user-name mb-0">{{ Auth::guard('admin')->user()->username }}</p>
                        <p class="designattion mb-0">{{ Auth::guard('admin')->user()->name }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    {{-- <li><a class="dropdown-item"
                            href="{{ route('admin.user.user.edit', [Auth::guard('admin')->user()->id]) }}"><i
                                class="bx bx-user"></i><span>Profile</span></a>
                    </li>
                    <li><a class="dropdown-item" href="javascript:;"><i
                                class="bx bx-cog"></i><span>Settings</span></a>
                    </li>
                    <li><a class="dropdown-item" href="javascript:;"><i
                                class='bx bx-home-circle'></i><span>Dashboard</span></a>
                    </li>
                    <li><a class="dropdown-item" href="javascript:;"><i
                                class='bx bx-dollar-circle'></i><span>Earnings</span></a>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('rukada.template') }}" target="_blank"><i
                                class='bx bx-download'></i><span>Downloads</span></a>
                    </li>
                    <li>
                        <div class="dropdown-divider mb-0"></div>
                    </li> --}}
                    <li><a class="dropdown-item" href="{{ route('admin.logout') }}"><i
                                class='bx bx-log-out-circle'></i><span>Logout</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
<!--end header -->
