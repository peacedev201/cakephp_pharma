<style type="text/css">
    #slide_color_colorSelector<?php echo $key;?> div,
    #slide_textbg_colorSelector<?php echo $key;?> div
    {
        /*background: rgba(0, 0, 0, 0) url("../img/images/select.png") repeat scroll center center;*/
        height: 30px;
        left: 3px;
        position: absolute;
        top: 3px;
        width: 30px;
        margin-left: 12px;
    }

</style>
<script>
    $('#slide_color_colorSelector<?php echo $key;?>').ColorPicker({
        color: '#0000ff',
        onShow: function (colpkr) {
            $(colpkr).fadeIn(500);
            return false;
        },
        onHide: function (colpkr) {
            $(colpkr).fadeOut(500);
            return false;
        },
        onChange: function (hsb, hex, rgb) {
            $('#slide_color_colorSelector<?php echo $key;?> div').css('backgroundColor', '#' + hex);
            $('#slide_color<?php echo $key;?>').val('#' + hex);
        }
    });
    $('#slide_textbg_colorSelector<?php echo $key;?>').ColorPicker({
        color: '#0000ff',
        onShow: function (colpkr) {
            $(colpkr).fadeIn(500);
            return false;
        },
        onHide: function (colpkr) {
            $(colpkr).fadeOut(500);
            return false;
        },
        onChange: function (hsb, hex, rgb) {
            $('#slide_textbg_colorSelector<?php echo $key;?> div').css('backgroundColor', '#' + hex);
            $('#slide_textbg<?php echo $key;?>').val('#' + hex);
        }
    });
</script>
<div style="border-top: 1px solid #ccc;">
    <div class="form-group">
        <inupt type="hidden" value="<?php echo $slidetext['SlideText']['slide_id'];?>" name="data[SlideText][slide_id][]"/>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label"><?php echo __d('slider','Text');?></label>
        <div class="col-md-6">
            <input type="text" value="<?php echo $slidetext['SlideText']['text']?>" class="form-control" placeholder="Enter text" name="data[SlideText][text][]">
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label"><?php echo __d('slider','Font size');?></label>
        <div class="col-md-6">
            <input type="text" value="<?php echo $slidetext['SlideText']['font_size']?>" class="form-control" placeholder="Enter number" name="data[SlideText][font_size][]">
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label"><?php echo __d('slider','Color');?></label>
        <div class="col-md-6">
            <inupt type="hidden" value="<?php if($slidetext['SlideText']['color']){ echo $slidetext['SlideText']['color']; }else{ echo '#0000ff';}?>" name="data[SlideText][color][]"/>
            <div id="slide_color_colorSelector<?php echo $key;?>">
                <div style="background-color: <?php if($slidetext['SlideText']['color']){ echo $slidetext['SlideText']['color']; }else{ echo '#0000ff';}?>"></div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label"><?php echo __d('slider','Text Bg');?></label>
        <div class="col-md-6">
            <inupt type="hidden" value="<?php if($slidetext['SlideText']['text_bg']){ echo $slidetext['SlideText']['text_bg']; }else{ echo '#0000ff';}?>" name="data[SlideText][text_bg][]"/>
            <div id="slide_textbg_colorSelector<?php echo $key;?>">
                <div style="background-color: <?php if($slidetext['SlideText']['text_bg']){ echo $slidetext['SlideText']['text_bg']; }else{ echo '#0000ff';}?>"></div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label"><?php echo __d('slider','Transition');?></label>
        <div class="col-md-6">
            <input type="text" value="<?php echo $slidetext['SlideText']['position_transition']?>" class="form-control" placeholder="Enter number" name="data[SlideText][position_transition][]">
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label"><?php echo __d('slider','Position From');?></label>
        <div class="col-md-9">
            <div class="col-md-2">
                <label class="col-md-3 control-label"><?php echo __d('slider','Top');?></label>
                <div>
                    <input type="text" value="<?php echo $slidetext['SlideText']['position_top']?>" class="form-control" placeholder="Enter number" name="data[SlideText][position_top][]">
                </div>
            </div>
            <div class="col-md-2">
                <label class="col-md-3 control-label"><?php echo __d('slider','Left');?></label>
                <div>
                    <input type="text" value="<?php echo $slidetext['SlideText']['position_right']?>" class="form-control" placeholder="Enter number" name="data[SlideText][position_right][]">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label"><?php echo __d('slider','Position To');?></label>
        <div class="col-md-9">
            <div class="col-md-2">
                <label class="col-md-3 control-label"><?php echo __d('slider','Top');?></label>
                <div>
                    <input type="text" value="<?php echo $slidetext['SlideText']['position_bottom']?>" class="form-control" placeholder="Enter number" name="data[SlideText][position_bottom][]">
                </div>
            </div>
            <div class="col-md-2">
                <label class="col-md-3 control-label"><?php echo __d('slider','Left');?></label>
                <div>
                    <input type="text" value="<?php echo $slidetext['SlideText']['position_left']?>" class="form-control" placeholder="Enter number" name="data[SlideText][position_left][]">
                </div>
            </div>
        </div>
    </div>
</div>