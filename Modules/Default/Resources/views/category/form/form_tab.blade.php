<div class="tab-pane fade show active" id="main_tab_content" role="tabpanel">
    <ul class="nav nav-tabs nav-info" role="tablist" id="sub-tab-lang">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#sub_tab_th" role="tab" aria-selected="true">
                <div class="d-flex align-items-center">
                    <div class="tab-title"><i class="flag flag-th"></i></div>
                </div>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#sub_tab_en" role="tab" aria-selected="false">
                <div class="d-flex align-items-center">     
                    <div class="tab-title"><i class="flag flag-gb"></i></div>
                </div>
            </a>
        </li>
    </ul>
    <div class="tab-content py-3">
        <!-- tab::main_tab_1 -->
            @include('default::category.form.form_th')
        <!-- tab::main_tab_1 -->
        <!-- tab::main_tab_2 -->
            @include('default::category.form.form_en')
        <!-- tab::main_tab_2 -->
    </div>

    <!-- form_image -->
        @includeIf('default::category.form.form_image')
    <!-- .form_image -->   

    <!-- form_status -->
        @includeIf('default::category.form.form_status')
    <!-- form_status --> 

</div>