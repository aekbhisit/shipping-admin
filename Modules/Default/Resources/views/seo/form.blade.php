<!-- content seo tab  -->
<div class="tab-pane fade" id="main_tab_seo" role="tabpanel">
    <ul class="nav nav-tabs nav-warning" role="tablist" id="sub-tab-seo">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#seo_tab_th" role="tab" aria-selected="true">
                <div class="d-flex align-items-center">
                    <div class="tab-title"><i class="flag flag-th"></i></div>
                </div>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#seo_tab_en" role="tab" aria-selected="false">
                <div class="d-flex align-items-center">     
                    <div class="tab-title"><i class="flag flag-gb"></i></div>
                </div>
            </a>
        </li>
    </ul>
    <div class="tab-content py-3">
        <!-- tab::main_tab_1 -->
            @include('default::seo.form_th')
        <!-- tab::main_tab_1 -->
        <!-- tab::main_tab_2 -->
            @include('default::seo.form_en')
        <!-- tab::main_tab_2 -->
    </div>
    <!-- .content seo tab  -->   
</div>
