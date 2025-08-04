<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <a href="{{ route('admin.homepage') }}">
            <img src="{{ !empty(setting()->logo_header) && CheckFileInServer(setting()->logo_header) ? setting()->logo_header : URL::asset('assets/images/logo-icon.png') }}"
                class="logo-icon">
        </a>
        <div>
            <h4 class="logo-text">
                <?=isset(setting()->meta_title) ? setting()->meta_title : 'CRM - Admin';?>
            </h4>
        </div>
        <div class="toggle-icon ms-auto">
            <i class='bx bx-arrow-to-left'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">

        
        <?php $side_menu = get_side_admin_menu(); ?>
        @if (!empty($side_menu) && count($side_menu) > 0)
            @foreach ($side_menu as $menu_lv_1)
                <?php $show_lv1 = side_roles($menu_lv_1); ?>
                @if ($show_lv1)
                    @php
                        if ($menu_lv_1->link_type == 1) {
                            $link = !empty($menu_lv_1->route_name) && \Illuminate\Support\Facades\Route::has($menu_lv_1->route_name) ? route($menu_lv_1->route_name) : 'javascript:void(0);';
                        } else {
                            $link = !empty($menu_lv_1->url) ? $menu_lv_1->url : 'javascript:void(0);';
                        }
                    @endphp
                    <li data-show_lv1="{{ $show_lv1 }}" class="{{ $menu_lv_1->attr_class }}">
                        <a class="{{ !empty($menu_lv_1->children->toArray()) ? 'has-arrow' : '' }}"
                            href="{{ $link }}"
                            target="{{ !empty($menu_lv_1->target) ? $menu_lv_1->target : '' }}">
                            <div class="parent-icon"><i
                                    class="{{ !empty($menu_lv_1->icon) ? $menu_lv_1->icon : 'bx bx-chevron-right' }}"></i>
                            </div>
                            <div class="menu-title">
                                {{ !empty($menu_lv_1->name) ? $menu_lv_1->name : '' }}
                                @if ($menu_lv_1->show_badge)
                                    <span id="{{ str_replace('.', '_', $menu_lv_1->route_name) }}"
                                        class="badge rounded-pill text-bg-secondary ms-1">
                                        {{ isset($adminInit) ? $adminInit['job_waiting']['job_waiting_cnt'] : 0 }}</span>
                                @endif
                            </div>
                        </a>
                        @if (!empty($menu_lv_1->children) && count($menu_lv_1->children) > 0)
                            @foreach ($menu_lv_1->children as $menu_lv_2)
                                <?php $show_lv2 = side_roles($menu_lv_2); ?>
                                @if ($show_lv2)
                                    <ul>
                                        @php
                                            if ($menu_lv_2->link_type == 1) {
                                                $link2 = !empty($menu_lv_2->route_name) && \Illuminate\Support\Facades\Route::has($menu_lv_2->route_name) ? route($menu_lv_2->route_name) : 'javascript:void(0);';
                                            } else {
                                                $link2 = !empty($menu_lv_2->url) ? $menu_lv_2->url : 'javascript:void(0);';
                                            }
                                        @endphp
                                        <li data-show_lv2="{{ $show_lv2 }}" class="{{ $menu_lv_2->attr_class }}">
                                            <a class="{{ !empty($menu_lv_2->children->toArray()) ? 'has-arrow' : '' }}"
                                                href="{{ $link2 }}"
                                                target="{{ !empty($menu_lv_2->target) ? $menu_lv_2->target : '' }}">
                                                <i
                                                    class="{{ !empty($menu_lv_2->icon) ? $menu_lv_2->icon : 'bx bx-chevron-right' }}"></i>
                                                {{ !empty($menu_lv_2->name) ? $menu_lv_2->name : '' }}
                                                @if ($menu_lv_2->show_badge)
                                                    <span id="{{ str_replace('.', '_', $menu_lv_2->route_name) }}"
                                                        class="badge rounded-pill text-bg-secondary ms-1">
                                                        @switch($menu_lv_2->attr_class)
                                                            @case('admin_menu_job')
                                                            @case('admin_menu_job_new')
                                                                {{ isset($adminInit) ? $adminInit['job_waiting']['job_waiting_cnt'] : 0 }}
                                                            @break

                                                            @case('admin_menu_job_doing')
                                                                {{ isset($adminInit) ? $adminInit['job_doing']['job_doing_cnt'] : 0 }}
                                                            @break

                                                            @default
                                                        @endswitch
                                                    </span>
                                                @endif
                                            </a>

                                            @if (!empty($menu_lv_2->children) && count($menu_lv_2->children) > 0)
                                                @foreach ($menu_lv_2->children as $menu_lv_3)
                                                    <?php $show_lv3 = side_roles($menu_lv_3); ?>
                                                    @if ($show_lv3)
                                                        <ul>
                                                            @php
                                                                if ($menu_lv_3->link_type == 1) {
                                                                    $link3 = !empty($menu_lv_3->route_name) && \Illuminate\Support\Facades\Route::has($menu_lv_3->route_name) ? route($menu_lv_3->route_name) : 'javascript:void(0);';
                                                                } else {
                                                                    $link3 = !empty($menu_lv_3->url) ? $menu_lv_3->url : 'javascript:void(0);';
                                                                }
                                                            @endphp
                                                            <li>
                                                                <a class="{{ !empty($menu_lv_3->children->toArray()) ? 'has-arrow' : '' }}"
                                                                    href="{{ $link3 }}"
                                                                    target="{{ !empty($menu_lv_3->target) ? $menu_lv_3->target : '' }}">
                                                                    <i
                                                                        class="{{ !empty($menu_lv_3->icon) ? $menu_lv_3->icon : 'bx bx-chevron-right' }}"></i>
                                                                    {{ !empty($menu_lv_3->name) ? $menu_lv_3->name : '' }}

                                                                </a>
                                                                @if (!empty($menu_lv_3->children) && count($menu_lv_3->children) > 0)
                                                                    <ul>
                                                                        @foreach ($menu_lv_3->children as $menu_lv_4)
                                                                            @if (side_roles($menu_lv_4))
                                                                                @php
                                                                                    if ($menu_lv_4->link_type == 1) {
                                                                                        $link4 = !empty($menu_lv_4->route_name) && \Illuminate\Support\Facades\Route::has($menu_lv_4->route_name) ? route($menu_lv_4->route_name) : 'javascript:void(0);';
                                                                                    } else {
                                                                                        $link4 = !empty($menu_lv_4->url) ? $menu_lv_4->url : 'javascript:void(0);';
                                                                                    }
                                                                                @endphp
                                                                                <li>
                                                                                    <a href="{{ $link4 }}"
                                                                                        target="{{ !empty($menu_lv_4->target) ? $menu_lv_4->target : '' }}">
                                                                                        <i
                                                                                            class="{{ !empty($menu_lv_4->icon) ? $menu_lv_4->icon : 'bx bx-chevron-right' }}"></i>
                                                                                        {{ !empty($menu_lv_4->name) ? $menu_lv_4->name : '' }}
                                                                                    </a>
                                                                                </li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </li>
                                                        </ul>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </li>
                                    </ul>
                                @endif
                            @endforeach
                        @endif
                    </li>
                @endif
            @endforeach
        @endif

        @if (roles('admin.admin_menu.admin_menu.index') ||
                roles('admin.admin_menu.admin_menu.add') ||
                roles('admin.admin_menu.admin_menu.edit') ||
                roles('admin.admin_menu.admin_menu.set_delete') ||
                roles('admin.setting.web.index') ||
                roles('admin.setting.web.save') ||
                roles('admin.setting.slug.index') ||
                roles('admin.setting.slug.edit') ||
                roles('admin.setting.slug.set_delete') ||
                roles('admin.setting.tag.index') ||
                roles('admin.setting.tag.add') ||
                roles('admin.setting.tag.edit') ||
                roles('admin.setting.tag.set_delete') ||
                roles('admin.log.log.index') ||
                roles('admin.filemanager.filemanager.index'))
            <li>
                <a class="has-arrow" href="javascript:void(0);">
                    <div class="parent-icon"><i class="bx bx-cog"></i>
                    </div>
                    <div class="menu-title">ตั้งค่า</div>
                </a>
                <ul>
                    @if (roles('admin.admin_menu.admin_menu.index') ||
                            roles('admin.admin_menu.admin_menu.add') ||
                            roles('admin.admin_menu.admin_menu.edit') ||
                            roles('admin.admin_menu.admin_menu.set_delete'))
                        <li><a href="{{ route('admin.admin_menu.admin_menu.index') }}" target="_blank"><i
                                    class="bx bx-right-arrow-alt"></i>เมนูผู้ใช้</a></li>
                    @endif
                    @if (roles('admin.setting.web.index') || roles('admin.setting.web.save'))
                        <li><a href="{{ route('admin.setting.web.index') }}"><i class="bx bx-right-arrow-alt"></i>
                                ตั้งค่าเว็บไซต์</a></li>
                    @endif
                    @if (roles('admin.setting.slug.index') || roles('admin.setting.slug.edit') || roles('admin.setting.slug.set_delete'))
                        <li><a href="{{ route('admin.setting.slug.index') }}"><i class="bx bx-right-arrow-alt"></i> SLUG
                            </a>
                        </li>
                    @endif
                    @if (roles('admin.setting.tag.index') ||
                            roles('admin.setting.tag.add') ||
                            roles('admin.setting.tag.edit') ||
                            roles('admin.setting.tag.set_delete'))
                        <li><a href="{{ route('admin.setting.tag.index') }}"><i class="bx bx-right-arrow-alt"></i>
                                TAG</a>
                        </li>
                    @endif
                    @if (roles('admin.log.log.index'))
                        <li><a href="{{ route('admin.log.log.index') }}"><i class="bx bx-right-arrow-alt"></i>
                                Activities
                                Log</a></li>
                    @endif
                    <li><a href="/admin/log-viewer" target="_blank"><i class="bx bx-right-arrow-alt"></i> Laravel
                            Log</a>
                    </li>
                    @if (roles('admin.filemanager.filemanager.index'))
                        <li>
                            <a href="{{ route('admin.filemanager.filemanager.index') }}">
                                <i class="bx bx-right-arrow-alt"></i> File</a>
                            </a>
                        </li>
                    @endif
                    {{-- <li><a href="{{ route('admin.apiclient.test.index') }}"><i class="bx bx-right-arrow-alt"></i>
                            Test API Client</a></li> --}}
                </ul>
            </li>
        @endif

        @if (roles('admin.user.user.index') ||
                roles('admin.user.user.add') ||
                roles('admin.user.user.edit') ||
                roles('admin.user.user.set_delete') ||
                roles('admin.user.role.index') ||
                roles('admin.user.role.add') ||
                roles('admin.user.role.edit') ||
                roles('admin.user.role.set_delete') ||
                roles('admin.user.permission.index') ||
                roles('admin.user.permission.add') ||
                roles('admin.user.permission.edit') ||
                roles('admin.user.permission.set_delete'))

            <li>
                <a class="has-arrow" href="javascript:void(0);">
                    <div class="parent-icon"><i class="bx bx-user-circle"></i>
                    </div>
                    <div class="menu-title">ผู้ใช้งาน</div>
                </a>
                <ul>
                    @if (roles('admin.user.user.index') ||
                            roles('admin.user.user.add') ||
                            roles('admin.user.user.edit') ||
                            roles('admin.user.user.set_delete'))
                        <li>
                            <a href="{{ route('admin.user.user.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>ผู้ดูแลระบบ</a>
                        </li>
                    @endif
                    @if (roles('admin.user.role.index') ||
                            roles('admin.user.role.add') ||
                            roles('admin.user.role.edit') ||
                            roles('admin.user.role.set_delete'))
                        <li>
                            <a href="{{ route('admin.user.role.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>สิทธิ์</a>
                        </li>
                    @endif
                    @if (roles('admin.user.permission.index') ||
                            roles('admin.user.permission.add') ||
                            roles('admin.user.permission.edit') ||
                            roles('admin.user.permission.set_delete'))
                        <li>
                            <a href="{{ route('admin.user.permission.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>ฟังก์ชั่น</a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
    </ul>
    <!--end navigation-->
</div>
