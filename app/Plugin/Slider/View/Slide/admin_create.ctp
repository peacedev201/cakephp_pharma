<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->Html->addCrumb(__d('slider','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('slider', 'Slideshows Manager'), '/admin/slider/sliders');
//$this->Html->addCrumb(__d('slider', 'Manage Slides'), array('controller' => 'slide', 'action' => 'admin_create'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Slider'));
$this->end();

echo $this->Html->script(array('Slider.colorpicker'), array('inline' => false));
echo $this->Html->css(array('Slider.colorpicker'), null, array('inline' => false));

echo $this->Html->script(array('Slider.jquery.fileuploader'), array('inline' => false));
echo $this->Html->css(array( 'fineuploader' ), array('inline' => false));

$slideHelper = MooCore::getInstance()->getHelper('Slider_Slide');

?>
<?php echo $this->Moo->renderMenu('Slider', __d('slider','Slideshows'));?>
    <div class="modal-header">
        <h4 class="modal-title"><?php echo __d('slider','Add New Slide');?></h4>
    </div>

    <div class="modal-body">
        <form id="createForm" class="form-horizontal" role="form">
            <?php echo $this->Form->hidden('id', array('value' => $slide['Slide']['id'])); ?>
            <?php echo $this->Form->hidden('slider_id', array('value' => $slider_id)); ?>
            <?php echo $this->Form->hidden('image', array('value' => $slide['Slide']['image'])); ?>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Slide caption heading');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->text('slide_name', array('placeholder' => __d('slider','Enter text'), 'class' => 'form-control', 'value' => $slide['Slide']['slide_name'])); ?>

                    <?php if (!$bIsEdit) : ?>
                        <div class="tips">*<?php echo __d('slider','You can add translation language after creating slide') ?></div>
                    <?php else : ?>
                        <div class="tips">
                            <?php
                            $this->MooPopup->tag(array(
                                'href'=>$this->Html->url(array("controller" => "slide",
                                    "action" => "admin_ajax_translate",
                                    "plugin" => "slider",
                                    $slide['Slide']['id'],
                                )),
                                'title' => __('Translation'),
                                'innerHtml'=> __('Translation'),
                            ));
                            ?>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Caption heading font size');?></label>
                    <div class="col-md-6">
                        <input type="text" value="<?php if(!$slide['Slide']['caption_font_size'] && !$slide['Slide']['id']){echo '20';}else{ echo $slide['Slide']['caption_font_size'];}?>" class="form-control" placeholder="<?php echo __d('slider','Enter number');?>" name="caption_font_size">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Caption heading color');?></label>
                    <div class="col-md-6">
                        <?php
                        if( !$slide['Slide']['color']){
                            $slide['Slide']['color'] = '#ffffff';
                        }
                        echo $this->Form->hidden('caption_color', array('value' => $slide['Slide']['caption_color']));
                        ?>
                        <div id="caption_color_colorSelector">
                            <div style="background-color: <?php if( $slide['Slide']['caption_color']){ echo  $slide['Slide']['caption_color']; }else{ echo '#ffffff';}?>"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Caption content');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->textarea('text', array('style' => 'height:100px', 'class' => 'form-control', 'value' => $slide['Slide']['text'])); ?>
                        <?php if (!$bIsEdit) : ?>
                            <div class="tips">*<?php echo __d('slider','You can add translation language after creating slide') ?></div>
                        <?php else : ?>
                            <div class="tips">
                                <?php
                                $this->MooPopup->tag(array(
                                    'href'=>$this->Html->url(array("controller" => "slide",
                                        "action" => "admin_ajax_translate",
                                        "plugin" => "slider",
                                        $slide['Slide']['id'],
                                    )),
                                    'title' => __('Translation'),
                                    'innerHtml'=> __('Translation'),
                                ));
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Caption content font size');?></label>
                    <div class="col-md-6">
                        <input type="text" value="<?php if($slide['Slide']['font_size'] && $slide['Slide']['id']){echo $slide['Slide']['font_size'];}else{echo '11';}?>" class="form-control" placeholder="<?php echo __d('slider','Enter number');?>" name="font_size">

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Caption content color');?></label>
                    <div class="col-md-6">
                        <?php
                        if( !$slide['Slide']['color']){
                            $slide['Slide']['color'] = '#ffffff';
                        }
                        echo $this->Form->hidden('color', array('value' => $slide['Slide']['color']));
                        ?>
                        <div id="slide_color_colorSelector">
                            <div style="background-color: <?php if( $slide['Slide']['color']){ echo  $slide['Slide']['color']; }else{ echo '#ffffff';}?>"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Image Slide');?></label>
                    <div class="col-md-6">
                        <div id="select-0" style="margin: 10px 0 0 0px;"></div>
                        <?php if (!empty($slide['Slide']['image'])): ?>
                            <img width="150" src="<?php echo $slideHelper->getImage($slide, array('prefix' => '150_square'))?>" id="item-avatar" class="img_wrapper">
                        <?php else: ?>
                            <img width="150" src="" id="item-avatar" class="img_wrapper" style="display: none;">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','Link');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->text('link', array('class' => 'form-control', 'value' => $slide['Slide']['link'])); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('slider','New tab');?></label>
                    <div class="col-md-6">
                        <?php echo $this->Form->checkbox('new_tab', array('checked' => $slide['Slide']['new_tab'])); ?>
                    </div>
                </div>
            </div>

            <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">
        </form>
    </div>
    <div class="modal-footer">
        <a href="#" id="createButton" class="btn btn-action"><?php echo  __d('slider','Save') ?></a>
        <a href="#" id="reviewButton" class="btn btn-action"><?php echo  __d('slider','Preview') ?></a>
        <a href="javascript:history.back()" class="btn default"><?php echo  __d('slider','Close') ?></a>
    </div>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).ready(function () {
    $('#createButton').click(function () {
    disableButton('createButton');
    $.post("<?php echo  $this->request->base ?>/admin/slider/slide/save", $("#createForm").serialize(), function (data) {
    enableButton('createButton');
    var json = $.parseJSON(data);
    if (json.result == 1)
    {
    location.href = "<?php echo  $this->request->base ?>/admin/slider/slide/index/<?php echo $slider_id;?>";
    }
    else
    {
    $(".error-message").show();
    $(".error-message").html('<strong>Error! </strong>' + json.message);
    }
    });

    return false;
    });

    $('#reviewButton').click(function () {
    disableButton('reviewButton');
    $.post("<?php echo  $this->request->base ?>/admin/slider/slide/review", $("#createForm").serialize(), function (data) {
    enableButton('reviewButton');
    $('#themeModal .modal-content').empty().append(data);
    $('#themeModal').modal();
    });

    return false;
    });

    var errorHandler = function(event, id, fileName, reason) {
    if ($('.qq-upload-list .errorUploadMsg').length > 0){
    $('.qq-upload-list .errorUploadMsg').html('<?php echo __d('slider','Can not upload file more than ') . $file_max_upload?>');
    }else {
    $('.qq-upload-list').prepend('<div class="errorUploadMsg"><?php echo __d('slider','Can not upload file more than ') . $file_max_upload?></div>');
    }
    $('.qq-upload-fail').remove();
    };
    var uploader = new qq.FineUploader({
    element: $('#select-0')[0],
    multiple: false,
    text: {
    uploadButton: '<div class="upload-section"><i class="icon-camera"></i><?php echo __d('slider', 'Drag or click here to upload photo')?></div>'
    },
    validation: {
    allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
    },
    request: {
    endpoint: "<?php echo $this->request->base?>/slider/slide_upload/avatar"
    },
    callbacks: {
    onError: errorHandler,
    onComplete: function(id, fileName, response) {
    $('#item-avatar').attr('src', response.file_url);
    $('#item-avatar').show();
    $('#image').val(response.file_path);
    }
    }
    });

    });

    function toggleField()
    {
    $('.opt_field').toggle();
    }


    $('#slide_color_colorSelector').ColorPicker({
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
    $('#slide_color_colorSelector div').css('backgroundColor', '#' + hex);
    $('#color').val('#' + hex);
    }
    });

    $('#caption_color_colorSelector').ColorPicker({
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
    $('#caption_color_colorSelector div').css('backgroundColor', '#' + hex);
    $('#caption_color').val('#' + hex);
    }
    });
    function toggleField()
    {
    $('.opt_field').toggle();
    }
<?php $this->Html->scriptEnd(); ?>