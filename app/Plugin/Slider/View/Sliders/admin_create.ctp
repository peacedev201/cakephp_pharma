<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->Html->addCrumb(__d('slider','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('slider', 'Slideshows Manager'), '/admin/slider/sliders');
//$this->Html->addCrumb(__d('slider', 'Create New Slideshow'), array('controller' => 'slider', 'action' => 'admin_create'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Slider'));
$this->end();

echo $this->Html->script(array('Slider.colorpicker'), array('inline' => false));
echo $this->Html->css(array('Slider.colorpicker'), null, array('inline' => false));

?>
<?php echo $this->Moo->renderMenu('Slider', __d('slider','Slideshows'));?>

    <div class="modal-header">
        <h4 class="modal-title">
            <?php
            if(isset($slider['Slider']['id']) && $slider['Slider']['id']) {
                echo __d('slider','Edit Slideshow');
            }
            else {
                echo __d('slider','Create New Slideshow');
            }
            ?>
        </h4>
    </div>
    <div class="modal-body">
        <form id="createForm" class="form-horizontal" role="form">
            <?php echo $this->Form->hidden('id', array('value' => $slider['Slider']['id'])); ?>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Slideshow name');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->text('slider_name', array('placeholder' => __d('slider','Enter text'), 'class' => 'form-control', 'value' => $slider['Slider']['slider_name'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('slider','Duration');?>
                        (<a data-placement="top" data-original-title="<?php echo __d('slider','Time in milliseconds that the slide stays.');?>" class="tooltips" href="javascript:void(0)" data-html="true">?</a>)
                    </label>
                    <div class="col-md-6">
                        <?php
                        if(!$slider['Slider']['duration'] && !$slider['Slider']['id']) {$slider['Slider']['duration'] = '4000';}
                        echo $this->Form->text('duration', array('placeholder' => __d('slider','Enter number'), 'class' => 'form-control', 'value' => $slider['Slider']['duration'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Width');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->text('width', array('placeholder' => __d('slider','Enter number'), 'class' => 'form-control', 'value' => $slider['Slider']['width'])); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Height');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->text('height', array('placeholder' => __d('slider','Enter number'), 'class' => 'form-control', 'value' => $slider['Slider']['height'])); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-6" style="color: red;">
                        <?php echo __d('slider', 'Recommended size: left and right column (width: 295px), middle column (width: 590px)');?>
                    </div>
                </div>
            </div>

            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('slider','Transition Speed');?>
                        (<a data-placement="top" data-original-title="<?php echo __d('slider','Transition effect time between slides in milliseconds.');?>" class="tooltips" href="javascript:void(0)" data-html="true">?</a>)
                    </label>
                    <div class="col-md-6">
                        <?php
                        if(!$slider['Slider']['transition_speed'] && !$slider['Slider']['id']) {$slider['Slider']['transition_speed'] = '1000';}
                        echo $this->Form->text('transition_speed', array('placeholder' => __d('slider','Enter number'), 'class' => 'form-control', 'value' => $slider['Slider']['transition_speed'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('slider','Show navigation');?>
                        (<a data-placement="top" data-original-title="<?php echo __d('slider','Show/Hide navigation.');?>" class="tooltips" href="javascript:void(0)" data-html="true">?</a>)
                    </label>
                    <div class="col-md-9">
                        <?php echo $this->Form->checkbox('show_navigation', array('checked' => $slider['Slider']['show_navigation'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Navigation Color');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->hidden('navigation_color', array('placeholder' => __d('slider','Enter text'), 'class' => 'form-control', 'value' => $slider['Slider']['navigation_color'])); ?>
                        <div id="navigation_color_colorSelector">
                            <div style="background-color: <?php if($slider['Slider']['navigation_color']){ echo $slider['Slider']['navigation_color']; }else{ echo '#ffffff';}?>"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Navigation Hover Color');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->hidden('navigation_hover_color', array('placeholder' => 'Enter text', 'class' => 'form-control', 'value' => $slider['Slider']['navigation_hover_color'])); ?>
                        <div id="navigation_hover_color_colorSelector">
                            <div style="background-color: <?php if($slider['Slider']['navigation_hover_color']){ echo $slider['Slider']['navigation_hover_color']; }else{ echo '#ffffff';}?>"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Navigation Hightlight Color');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->hidden('navigation_hightlight_color', array('placeholder' => 'Enter text', 'class' => 'form-control', 'value' => $slider['Slider']['navigation_hightlight_color'])); ?>
                        <div id="navigation_hightlight_colorSelector">
                            <div style="background-color: <?php if($slider['Slider']['navigation_hightlight_color']){ echo $slider['Slider']['navigation_hightlight_color']; }else{ echo '#ffffff';}?>"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('slider','Position Navigation');?>
                        (<a data-placement="top" data-original-title="<?php echo __d('slider','Positioning the navigation.');?>" class="tooltips" href="javascript:void(0)" data-html="true">?</a>)
                    </label>
                    <div class="col-md-6">
                        <?php echo $this->Form->select('position_navigation', $position_navigation_array ,array('placeholder' => 'Enter text', 'class' => 'form-control', 'value' => $slider['Slider']['position_navigation'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('slider','Navigation Type');?>
                        (<a data-placement="top" data-original-title="<?php echo __d('slider','Navigation shape (number, circle, square)');?>" class="tooltips" href="javascript:void(0)" data-html="true">?</a>)
                    </label>
                    <div class="col-md-6">
                        <?php echo $this->Form->select('navigation_type', $navigation_type, array('placeholder' => __d('slider','Enter text'), 'class' => 'form-control', 'value' => $slider['Slider']['navigation_type'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('slider','Show control');?>
                        (<a data-placement="top" data-original-title="<?php echo __d('slider','Show/Hide Previous-Next controls on hover.');?>" class="tooltips" href="javascript:void(0)" data-html="true">?</a>)
                    </label>
                    <div class="col-md-6">
                        <?php echo $this->Form->checkbox('show_control', array('checked' => $slider['Slider']['show_control'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Control color');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->hidden('control_color', array('placeholder' => __d('slider','Enter text'), 'class' => 'form-control', 'value' => $slider['Slider']['control_color'])); ?>
                        <div id="control_color_colorSelector">
                            <div style="background-color: <?php if($slider['Slider']['control_color']){ echo $slider['Slider']['control_color']; }else{ echo '#ffffff';}?>"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Control background color');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->hidden('control_background_color', array('placeholder' => __d('slider','Enter text'), 'class' => 'form-control', 'value' => $slider['Slider']['control_background_color'])); ?>
                        <div id="control_background_colorSelector">
                            <div style="background-color: <?php if($slider['Slider']['control_background_color']){ echo $slider['Slider']['control_background_color']; }else{ echo '#ffffff';}?>"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('slider','Position Control');?>
                        (<a data-placement="top" data-original-title="<?php echo __d('slider','Positioning the control buttons.');?>" class="tooltips" href="javascript:void(0)" data-html="true">?</a>)
                    </label>
                    <div class="col-md-6">
                        <?php echo $this->Form->select('position_control', $position_control_array, array('placeholder' => __d('slider','Enter text'), 'class' => 'form-control', 'value' => $slider['Slider']['position_control'], 'empty' => false)); ?>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Transition Effect');?>
                        <?php echo __d('slider','(between slide)');?>
                        (<a data-placement="top" data-original-title="<?php echo __d('slider','Transition effect between slides.');?>" class="tooltips" href="javascript:void(0)" data-html="true">?</a>)
                    </label>
                    <div class="col-md-6">
                        <?php echo $this->Form->select('transition_effect', $transition, array('placeholder' => 'Enter text', 'class' => 'form-control', 'value' => $slider['Slider']['transition_effect'], 'empty' => false)); ?>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('slider','Show progress');?>
                        (<a data-placement="top" data-original-title="<?php echo __d('slider','Show/Hide progress bar');?>" class="tooltips" href="javascript:void(0)" data-html="true">?</a>)
                    </label>
                    <div class="col-md-6">
                        <?php echo $this->Form->checkbox('show_progress', array('checked' => $slider['Slider']['show_progress'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Progress Color');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->hidden('progress_color', array('placeholder' => __d('slider','Enter text'), 'class' => 'form-control', 'value' => $slider['Slider']['progress_color'])); ?>
                        <div id="progress_color_colorSelector">
                            <div style="background-color: <?php if($slider['Slider']['progress_color']){ echo $slider['Slider']['progress_color']; }else{ echo '#ffffff';}?>"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-body" style="display: none;">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('slider','Pause on hover');?>
                        (<a data-placement="top" data-original-title="<?php echo __d('slider','Pause when the mouse is on the slide');?>" class="tooltips" href="javascript:void(0)" data-html="true">?</a>)
                    </label>
                    <div class="col-md-6">
                        <?php //echo $this->Form->checkbox('pause_on_hover', array('checked' => $slider['Slider']['pause_on_hover'])); ?>
                        <?php echo $this->Form->checkbox('pause_on_hover', array('checked' => true)); ?>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Background color of the captions');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->hidden('background_caption', array('class' => 'form-control', 'value' => $slider['Slider']['background_caption'])); ?>
                        <div id="background_captionSelector">
                            <div style="background-color: <?php if($slider['Slider']['background_caption']){ echo $slider['Slider']['background_caption']; }else{ echo '#ffffff';}?>"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('slider','Opacity');?>
                        (<a data-placement="top" data-original-title="<?php echo __d('slider','transparency-level, where 1 is not transparent at all, 0.5 is 50% see-through, and 0 is completely transparent');?>" class="tooltips" href="javascript:void(0)" data-html="true">?</a>)
                    </label>
                    <div class="col-md-6">
                        <?php
                        if(!$slider['Slider']['opacity'] && !$slider['Slider']['id']) {$slider['Slider']['opacity'] = '1';}
                         echo $this->Form->text('opacity', array('placeholder' => __d('slider','Enter number'), 'class' => 'form-control', 'value' => $slider['Slider']['opacity'])); ?>
                    </div>
                </div>
            </div>
        </form>
        <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" id="createButton" class="btn btn-action"><?php echo  __d('slider','Save') ?></a>
        <a href="javascript:history.back()" class="btn default"><?php echo  __d('slider','Close') ?></a>
    </div>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).ready(function () {
    $('#createButton').click(function () {

    disableButton('createButton');
    $.post("<?php echo  $this->request->base ?>/admin/slider/sliders/save", $("#createForm").serialize(), function (data) {
    enableButton('createButton');
    var json = $.parseJSON(data);
    if (json.result == 1)
    {
    location.href = "<?php echo  $this->request->base ?>/admin/slider/sliders";
    }
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

    $('#navigation_color_colorSelector').ColorPicker({
    color: '#ffffff',
    onShow: function (colpkr) {
    $(colpkr).fadeIn(500);
    return false;
    },
    onHide: function (colpkr) {
    $(colpkr).fadeOut(500);
    return false;
    },
    onChange: function (hsb, hex, rgb) {
    $('#navigation_color_colorSelector div').css('backgroundColor', '#' + hex);
    $('#navigation_color').val('#' + hex);
    }
    });
    $('#navigation_hover_color_colorSelector').ColorPicker({
    color: '#ffffff',
    onShow: function (colpkr) {
    $(colpkr).fadeIn(500);
    return false;
    },
    onHide: function (colpkr) {
    $(colpkr).fadeOut(500);
    return false;
    },
    onChange: function (hsb, hex, rgb) {
    $('#navigation_hover_color_colorSelector div').css('backgroundColor', '#' + hex);
    $('#navigation_hover_color').val('#' + hex);
    }
    });
    $('#control_color_colorSelector').ColorPicker({
    color: '#ffffff',
    onShow: function (colpkr) {
    $(colpkr).fadeIn(500);
    return false;
    },
    onHide: function (colpkr) {
    $(colpkr).fadeOut(500);
    return false;
    },
    onChange: function (hsb, hex, rgb) {
    $('#control_color_colorSelector div').css('backgroundColor', '#' + hex);
    $('#control_color').val('#' + hex);
    }
    });
    $('#control_background_color_colorSelector').ColorPicker({
    color: '#ffffff',
    onShow: function (colpkr) {
    $(colpkr).fadeIn(500);
    return false;
    },
    onHide: function (colpkr) {
    $(colpkr).fadeOut(500);
    return false;
    },
    onChange: function (hsb, hex, rgb) {
    $('#control_background_color_colorSelector div').css('backgroundColor', '#' + hex);
    $('#control_background_color').val('#' + hex);
    }
    });
    $('#progress_color_colorSelector').ColorPicker({
    color: '#ffffff',
    onShow: function (colpkr) {
    $(colpkr).fadeIn(500);
    return false;
    },
    onHide: function (colpkr) {
    $(colpkr).fadeOut(500);
    return false;
    },
    onChange: function (hsb, hex, rgb) {
    $('#progress_color_colorSelector div').css('backgroundColor', '#' + hex);
    $('#progress_color').val('#' + hex);
    }
    });
    $('#navigation_hightlight_colorSelector').ColorPicker({
    color: '#ffffff',
    onShow: function (colpkr) {
    $(colpkr).fadeIn(500);
    return false;
    },
    onHide: function (colpkr) {
    $(colpkr).fadeOut(500);
    return false;
    },
    onChange: function (hsb, hex, rgb) {
    $('#navigation_hightlight_colorSelector div').css('backgroundColor', '#' + hex);
    $('#navigation_hightlight_color').val('#' + hex);
    }
    });
    $('#control_background_colorSelector').ColorPicker({
    color: '#ffffff',
    onShow: function (colpkr) {
    $(colpkr).fadeIn(500);
    return false;
    },
    onHide: function (colpkr) {
    $(colpkr).fadeOut(500);
    return false;
    },
    onChange: function (hsb, hex, rgb) {
    $('#control_background_colorSelector div').css('backgroundColor', '#' + hex);
    $('#control_background_color').val('#' + hex);
    }
    });
    $('#background_captionSelector').ColorPicker({
    color: '#ffffff',
    onShow: function (colpkr) {
    $(colpkr).fadeIn(500);
    return false;
    },
    onHide: function (colpkr) {
    $(colpkr).fadeOut(500);
    return false;
    },
    onChange: function (hsb, hex, rgb) {
    $('#background_captionSelector div').css('backgroundColor', '#' + hex);
    $('#background_caption').val('#' + hex);
    }
    });
<?php $this->Html->scriptEnd(); ?>