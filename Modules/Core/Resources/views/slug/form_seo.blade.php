<!-- product seo tab  -->
<div class="tab_content mb-3">
        <input type="hidden" name="data_id" value="{{ !empty($data_id) ? $data_id : '' }}" >
    <div class="tab_wrapper" id="slug-seo-tab">
        <ul class="tab_list">
            <li class="icons-list-item" style="height: 36px;"><i class="flag flag-th"></i>
            </li>
            <li class="icons-list-item" style="height: 36px;"><i class="flag flag-gb"></i>
            </li>
        </ul>
        <div class="content_wrapper">
            <!-- form_seo_th -->
                @includeIf('websetting::slug.form_seo_th')
            <!-- .form_seo_th -->
           <!-- form_seo_en -->
                @includeIf('websetting::slug.form_seo_en')
            <!-- .form_seo_en -->
        </div>
    </div>
</div>
<!-- product seo tab  -->        


