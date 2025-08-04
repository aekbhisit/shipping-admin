<!-- file upload -->
 <div class="col-md-12">
    <?= image_upload($id = '3', $name = 'image', $label = 'รูปภาพ', $image = !empty($data->image) ? $data->image : '', $size_recommend = ' 32 x 32px') ?>
    <p></p>
</div>
<!-- .file upload -->