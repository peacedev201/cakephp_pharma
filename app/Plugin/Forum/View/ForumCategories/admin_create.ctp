<script type="text/javascript">
    $(document).ready(function(){

        var uploader = new qq.FineUploader({
            element: $('#photos_upload')[0],
            autoUpload: true,
            text: {
                uploadButton: '<div class="upload-section"><i class="icon-camera"></i>' + 'Select' + '</div>'
            },
            multiple:false,
            validation: {
                allowedExtensions : mooConfig.photoExt,
                sizeLimit : mooConfig.sizeLimit,
                itemLimit: 1,
                image: {
                    maxHeight: 32,
                    maxWidth: 32,
                    minHeight: 32,
                    minWidth: 32
                }
            },
            request: {
                endpoint: mooConfig.url.base + "/forum/forum_upload/icon/32                                             "
            },
            callbacks: {
                onError: function(id, fileName, reason) {
                    if ($(this._element).find('.qq-upload-list .errorUploadMsg').length > 0){
                        $(this._element).find('.qq-upload-list .errorUploadMsg').html(reason);
                    }else {
                        $(this._element).find('.qq-upload-list').prepend('<div class="errorUploadMsg">' + reason + '</div>');
                    }
                    $(this._element).find('.qq-upload-fail').remove();
                },
                onSubmit: function(id, fileName){
                    $('#feed_1').remove();
                    var element = $('<span id="feed_1" style="background-image:url(' + mooConfig.url.base + '/img/indicator.gif);background-size:inherit;background-repeat:no-repeat"></span>');
                    element.insertBefore('.addMoreImage');

                    $('#wall_photo_preview').show();
                    //$('#addMoreImage').show();
                },
                onComplete: function (id, fileName, response) {
                    if (response.success) {

                        $('[data-toggle="tooltip"]').tooltip('hide');
                        $(this.getItemByFileId(id)).remove();
                        var img = $('<img src="'+response.thumb+'">');
                        img.load(function() {
                            var element = $('#feed_1');
                            element.attr('style','background-image:url(' + response.thumb + ');width:80px;height:80px');
                            var deleteItem = $('<a href="#"><i class="material-icons thumb-review-delete">clear</i></a>');
                            element.append(deleteItem);

                            element.find('.thumb-review-delete').unbind('click');
                            element.find('.thumb-review-delete').click(function(e){
                                e.preventDefault();
                                $(this).parents('span').remove();
                                $('#thumb').val('');
                                $('body').trigger('afterDeleteWallPhotoCallback',[response]);
                            });
                        });
                        $('#thumb').val(response.file_path);
                    }
                }
            }
        });

        $('#createButton').click(function(){
            mooAjax.post({
                url : "<?php echo $this->request->base?>/admin/forum/forum_categories/save",
                data: jQuery("#createForm").serialize()
            }, function(data){
                enableButton('createButton');
                var json = $.parseJSON(data);
                if ( json.result == 1 )
                {
                    window.location = '<?php echo $this->request->base?>/admin/forum/forums/';
                }
                if ( json.result == 0 )
                {
                    $('#errorMessage').text(json.message);
                    $('#errorMessage').show();
                }
            });
        });

    });
    $(document).on('loaded.bs.modal', function (e) {
        Metronic.init();
    });
    $(document).on('hidden.bs.modal', function (e) {
        $(e.target).removeData('bs.modal');
    });
</script>
<style>
    div#wall_photo_preview span {
        display: inline-block;
        width: 80px;
        height: 80px;
        background-size: cover;
        background-position: center center;
        border: 1px solid #999;
        margin: 0 2px;
        position: relative;
        vertical-align: top;
        cursor: pointer;
    }
    .thumb-review-delete{
        float: right;
    }
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('forum','Add New');?></h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $forum_cat['ForumCategory']['id'])); ?>
        <input type="hidden" id="thumb" name="thumb" value=""/>
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('forum','Name');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('name', array('placeholder' => __d('forum','Enter text'), 'class' => 'form-control', 'value' => $forum_cat['ForumCategory']['name'])); ?>

                    <?php if (!$bIsEdit) : ?>
                        <div class="tips">*<?php echo  __d('forum','You can add translation language after creating category') ?></div>
                    <?php else : ?>
                        <div class="tips">
                            <a href="<?php echo  $this->request->base ?>/admin/forum/forum_categories/ajax_translate/<?php echo  $forum_cat['ForumCategory']['id'] ?>"  data-toggle="modal" data-target="#ajax-translate" ><?php echo  __d('document','Translation') ?></a>
                        </div>
                    <?php endif; ?>
                </div>

                
            </div>
            <div class="row">
                <label class="col-md-3 control-label"><?php echo __d('forum','Icon');?></label>

                <div class="col-md-9">
                    <div id="images-uploader" style="margin-top: 3px;">
                        <div id="photos_upload"></div>
                    </div>
                    <div class="error-message" id="errorMessage" style="display: none;"></div>
                </div>

            </div>
            <div class="row">
                <label class="col-md-3 control-label"></label>

                <div class="col-md-9">
                    <?php echo __d('forum','Recommended size: 32 x 32');?>
                </div>
            </div>
            <div class="row">
                <label class="col-md-3 control-label"></label>
                <?php if(!empty($forum_cat['ForumCategory']['id'])): $helper = MooCore::getInstance()->getHelper('Forum_Forum');?>
                    <div class="col-md-9" id="wall_photo_preview" style="margin-bottom:5px;">
                        <span id="feed_1" style="background-image:url(<?php echo $helper->getIconForumCategory($forum_cat);?>);width:80px;height:80px">
                        </span>

                        <span id="addMoreImage" style="display:none;" class="addMoreImage"></span>
                    </div>
                <?php else:?>
                <div class="col-md-9" id="wall_photo_preview" style="display:none;margin-bottom:5px;">
                    <span id="addMoreImage" style="display:none;" class="addMoreImage"></span>
                </div>
                <?php endif;?>

            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('forum','Order');?></label>
                <div class="col-md-9">
                    <?php if(empty($forum_cat['ForumCategory']['order'])) $forum_cat['ForumCategory']['order'] = '1'; ?>
                    <?php echo $this->Form->number('order', array('placeholder' => __d('forum','Enter Number'), 'class' => 'form-control', 'value' => $forum_cat['ForumCategory']['order'] )); ?>
                </div>


            </div>
        </div>
    </form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">
    </div>
</div>
<div class="modal-footer">
    <a href="#" id="createButton" class="btn btn-action"><?php echo  __d('forum','Save') ?></a>
    <button type="button" class="btn default" data-dismiss="modal"><?php echo  __d('forum','Cancel') ?></button>
</div>