<?php 
    $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
?>
<div id="images-uploader" style="margin:0 0px 10px;">
    <div id="business_uploader"></div>
    <input type="button" class="button button-primary" id="triggerUpload" value="<?php echo __d('business', 'Upload Queued Files') ?>">
</div>
<?php echo $this->Form->hidden('photo_delete_id');?>
<div class="error-message" id="errorMessage" style="display:none"></div>
<ul class="photos_edit" id="wrap_photo_content">
    <?php if($business_photos != null):?>
        <?php foreach($business_photos as $business_photo):
            $business_photo = $business_photo['BusinessPhoto'];
        ?>
            <li class="col-md-3 full_content">
                <div class="albums_edit_item">
                    <div class="albums_photo_edit" style="background-image: url(<?php echo $photoHelper->getImage(array('Photo' => $business_photo), array('prefix' => '150_square'));?>)"></div>
                    <div class="album_info_edit">
                        <textarea placeholder="<?php echo __d('business', 'Caption');?>" name="photo_caption_exist[<?php echo $business_photo['id'];?>]" class="no-grow"><?php echo $business_photo['caption'];?></textarea>
                        <a class="photo_edit_checkbox" href="javascript:void(0)" data-id="<?php echo $business_photo['id'];?>">
                            <i class="icon-delete"></i>
                        </a>
                    </div>
                </div>
            </li>
        <?php endforeach;?>
    <?php endif;?>
</ul>
<div class="clear"></div>

<script id="photo_content" type="text/x-handlebars-template">
    <li class="col-md-3 full_content">
        <?php echo $this->Form->hidden('photo_filename.', array(
            'id' => '',
            'class' => 'photo_filename'
        ));?>
        <div class="albums_edit_item">
            <div class="albums_photo_edit"></div>
            <div class="album_info_edit">
                <textarea placeholder="<?php echo __d('business', 'Caption');?>" name="photo_caption[]" class="no-grow"></textarea>
                <a class="photo_edit_checkbox" href="javascript:void(0)" onclick="jQuery(this).closest('li').remove()">
                    <i class="icon-delete"></i>
                </a>
            </div>
        </div>
    </li>
</script>