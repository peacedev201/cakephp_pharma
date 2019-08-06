<script>
    $(document).ready(function () {
        $('#createButton').click(function () {

            disableButton('createButton');
            $.post("<?php echo  $this->request->base ?>/admin/slider/slide/saveText", $("#createForm").serialize(), function (data) {
                enableButton('createButton');
                var json = $.parseJSON(data);

                if (json.result == 1)
                    location.reload();
                else
                {
                    $(".error-message").show();
                    $(".error-message").html('<strong>Error! </strong>' + json.message);
                }
            });

            return false;
        });

    });

    function toggleField()
    {
        $('.opt_field').toggle();
    }

    $('#slide_color_colorSelector').ColorPicker({
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
            $('#slide_color_colorSelector div').css('backgroundColor', '#' + hex);
            $('#color').val('#' + hex);
        }
    });
    $('#slide_textbg_colorSelector').ColorPicker({
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
            $('#slide_textbg_colorSelector div').css('backgroundColor', '#' + hex);
            $('#text_bg').val('#' + hex);
        }
    });
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <?php if($slidetext['SlideText']['id']):?>
    <h4 class="modal-title"><?php echo __d('slider','Edit Text');?></h4>
    <?php else:?>
        <h4 class="modal-title"><?php echo __d('slider','Add Text');?></h4>
    <?php endif;?>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $slidetext['SlideText']['id'])); ?>
        <?php echo $this->Form->hidden('slide_id', array('value' => $slide_id)); ?>
        <div class="form-body">
            <!--<div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('slider','Slide');?></label>
                <div class="col-md-6">
                    <?php echo $this->Form->select('slide_id', $slides, array('class' => 'form-control', 'value' => $slidetext['SlideText']['slide_id'])); ?>
                </div>
            </div>-->
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('slider','Text');?></label>
                <div class="col-md-6">
                    <?php echo $this->Form->text('text', array('placeholder' => 'Enter text', 'class' => 'form-control', 'value' => $slidetext['SlideText']['text'])); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('slider','Font size');?></label>
                <div class="col-md-6">
                    <?php echo $this->Form->text('font_size', array('placeholder' => 'Enter text', 'class' => 'form-control', 'value' => $slidetext['SlideText']['font_size'])); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('slider','Color');?></label>
                <div class="col-md-6">
                    <?php echo $this->Form->hidden('color', array('placeholder' => 'Enter text', 'class' => 'form-control', 'value' => $slidetext['SlideText']['color'])); ?>
                    <div id="slide_color_colorSelector">
                        <div style="background-color: <?php if($slidetext['SlideText']['color']){ echo $slidetext['SlideText']['color']; }else{ echo '#0000ff';}?>"></div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('slider','Text Bg');?></label>
                <div class="col-md-6">
                    <?php echo $this->Form->hidden('text_bg', array('placeholder' => 'Enter text', 'class' => 'form-control', 'value' => $slidetext['SlideText']['text_bg'])); ?>
                    <div id="slide_textbg_colorSelector">
                        <div style="background-color: <?php if($slidetext['SlideText']['text_bg']){ echo $slidetext['SlideText']['text_bg']; }else{ echo '#0000ff';}?>"></div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('slider','Transition');?></label>
                <div class="col-md-6">
                    <?php echo $this->Form->text('position_transition', array('class' => 'form-control', 'value' => $slidetext['SlideText']['position_transition'])); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('slider','Position From');?></label>
                <div class="col-md-9">
                    <div class="col-md-3">
                        <label class="col-md-3 control-label"><?php echo __d('slider','Top');?></label>
                        <div>
                            <?php echo $this->Form->text('position_top', array('class' => 'form-control', 'value' => $slidetext['SlideText']['position_top'])); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="col-md-3 control-label"><?php echo __d('slider','Left');?></label>
                        <div>
                            <?php echo $this->Form->text('position_right', array('class' => 'form-control', 'value' => $slidetext['SlideText']['position_right'])); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('slider','Position To');?></label>
                <div class="col-md-9">
                    <div class="col-md-3">
                        <label class="col-md-3 control-label"><?php echo __d('slider','Top');?></label>
                        <div>
                            <?php echo $this->Form->text('position_bottom', array('class' => 'form-control', 'value' => $slidetext['SlideText']['position_bottom'])); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="col-md-3 control-label"><?php echo __d('slider','Left');?></label>
                        <div>
                            <?php echo $this->Form->text('position_left', array('class' => 'form-control', 'value' => $slidetext['SlideText']['position_left'])); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal"><?php echo  __('Close') ?></button>
    <a href="#" id="createButton" class="btn btn-action"><?php echo  __('Save') ?></a>

</div>