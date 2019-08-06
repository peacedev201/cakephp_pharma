
    <div class="modal-header">
        <?php echo __d('slider', 'Preview');?>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
    </div>

        <a href="<?php echo $data['link'];?>"
           style="background: url('<?php echo $image_url;?>'); display: block; height: <?php echo $slider['Slider']['height'];?>px; width: <?php echo $slider['Slider']['width'];?>px; position: relative;"
           <?php if($data['new_tab']):?>target="_blank"<?php endif;?>>
            <div class="bg_text_slide" <?php if($slider['Slider']['background_caption']):?> style="background-color: <?php echo $slider['Slider']['background_caption']?>" <?php endif;?>>
                <?php if($data['slide_name']):?>
                    <div style="font-weight: bold; font-size: <?php echo ($data['caption_font_size']) ? $data['caption_font_size'] : '12';?>px; color: <?php if($data['caption_color']){echo $data['caption_color'];}else{ echo '#000';}?>">
                        <?php echo htmlspecialchars($data['slide_name']);?>
                    </div>
                <?php endif;?>
                <?php if($data['text']):?>
                    <div style="color:<?php if($data['color']){echo $data['color'];}else{ echo '#000';}?>;font-size: <?php echo ($data['font_size']) ? $data['font_size'] : '12';?>px;"
                         data-pos="" data-duration="" data-effect="move">
                        <?php echo nl2br(htmlspecialchars($data['text']));?>
                    </div>
                <?php endif;?>
            </div>
        </a>


<style type="text/css">
.bg_text_slide
{
    left: 0;
    position: absolute;
    bottom: 0;
    width: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    padding: 10px;
    opacity: <?php echo $slider['Slider']['opacity'];?>;
}
.modal-dialog {
    width: <?php echo $slider['Slider']['width'] + 10;?>px;
}
</style>