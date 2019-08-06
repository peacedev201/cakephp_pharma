<script type="text/javascript">
    $(document).on('loaded.bs.modal', function (e) {
        Metronic.init();
    });
    $(document).on('hidden.bs.modal', function (e) {
        $(e.target).removeData('bs.modal');
    });
    $(document).ready(function(){
        $("#moderator").tokenInput("<?php echo $this->request->base?>/users/do_get_json",
            {
                preventDuplicates: true,
                hintText: "<?php echo addslashes(__d('forum','Enter one or more users'))?>",
                noResultsText: "<?php echo addslashes(__d('forum','No results'))?>",
                tokenLimit: 20,
                <?php if(!empty($friends)): ?>
                prePopulate: <?php echo  $friends; ?>,
                <?php endif; ?>
                resultsFormatter: function(item)
                {
                    return '<li>' + item.avatar + item.name + '</li>';
                }
            }
        );

        var uploader = new qq.FineUploader({
            element: $('#photos_upload')[0],
            autoUpload: true,
            text: {
                uploadButton: '<div class="upload-section"><i class="icon-camera"></i>' + '<?php echo __d('forum','Select');?>' + '</div>'
            },
            multiple:false,
            validation: {
                allowedExtensions : mooConfig.photoExt,
                sizeLimit : mooConfig.sizeLimit,
                itemLimit: 1,
                image: {
                    maxHeight: 48,
                    maxWidth: 48,
                    minHeight: 48,
                    minWidth: 48
                }
            },
            request: {
                endpoint: mooConfig.url.base + "/forum/forum_upload/icon/48"
            },
            callbacks: {
                onError: function(id, fileName, reason) {
                    if ($(this._element).find('.qq-upload-list .error-message').length > 0){
                        $(this._element).find('.qq-upload-list .error-message').html(reason);
                    }else {
                        $(this._element).find('.qq-upload-list').prepend('<div class="error-message">' + reason + '</div>');
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
                        console.log(response);
                        $('#thumb').val(response.file_path);
                    }
                }
            }
        });

        $('#createButton').click(function(){
            mooAjax.post({
                url : "<?php echo $this->request->base?>/admin/forum/forums/save_forum",
                data: jQuery("#createForm").serialize()
            }, function(data){
                enableButton('createButton');
                var json = $.parseJSON(data);
                if ( json.result == 1 )
                {
                    <?php
                        if(!isset($flag_sub)):
                    ?>
                    window.location = '<?php echo $this->request->base?>/admin/forum/forums/';
                    <?php else: ?>
                    window.location = '<?php echo $this->request->base?>/admin/forum/forums/sub/<?php echo $parent_id;?>';
                    <?php endif;?>
                }
                if ( json.result == 0 )
                {
                    $('#errorMessage').text(json.message);
                    $('#errorMessage').show();
                }
            });
        });

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
    <h4 class="modal-title"><?php if(!empty($bIsEdit)): echo __d('forum','Edit forum'); else:echo __d('forum','Add New forum');endif; ?></h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <?php if(!empty($bIsEdit)):echo $this->Form->hidden('id', array('value' => $forum['Forum']['id']));endif; ?>
        <input type="hidden" id="thumb" name="thumb" value=""/>
        <input type="hidden" name="category_id" id="category_id" value="<?php echo $cat_id;?>" />
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('forum','Forum title');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('name', array('placeholder' => __d('forum','Enter text'), 'class' => 'form-control', 'value' => $forum['Forum']['name'])); ?>

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('forum','Description');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->textarea('description', array('value' => $forum['Forum']['description'], 'id' => 'page-body-textarea', 'class' => 'form-control', 'rows' => 6)); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php
                    if(isset($flag_sub)) echo __d('forum','Parent forum');
                    else echo __d('forum','Forum Category');
                    ?></label>
                <div class="col-md-9">
                    <select class="form-control input-medium input-inline" name="<?php
                    if(isset($flag_sub)) echo "parent_id";
                    else echo "category_id";

                    ?>">
                        <?php if(isset($flag_sub)):?>
                        <option value="0"></option>
                        <?php endif;?>
                        <?php foreach ($parents as $parent): ?>
                            <?php if(isset($flag_sub)):?>
                                <option <?php if ($parent_id ==  $parent['Forum']['id']) echo 'selected="selected"';?> value="<?php echo $parent['Forum']['id'];?>"><?php echo $parent['Forum']['name'];?></option>
                            <?php else:?>
                                <option <?php if ($cat_id ==  $parent['ForumCategory']['id']) echo 'selected="selected"';?> value="<?php echo $parent['ForumCategory']['id'];?>"><?php echo $parent['ForumCategory']['name'];?></option>
                            <?php endif;?>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="row">
                <label class="col-md-3 control-label"><?php echo __d('forum','Thumb Image');?></label>

                <div class="col-md-9">
                    <div id="images-uploader" style="margin-top: 3px;">
                        <div id="photos_upload"></div>
                    </div>
                </div>

            </div>
            <div class="row">
                <label class="col-md-3 control-label"></label>
                <?php if(!empty($forum['Forum']['id'])): $helper = MooCore::getInstance()->getHelper('Forum_Forum');?>
                    <div class="col-md-9" id="wall_photo_preview" style="margin-bottom:5px;">
                        <span id="feed_1" style="background-image:url(<?php echo $helper->getIconForum($forum);?>);width:80px;height:80px">
                        </span>

                        <span id="addMoreImage" style="display:none;" class="addMoreImage"></span>
                    </div>
                <?php else:?>
                <div class="col-md-9" id="wall_photo_preview" style="display:none;margin-bottom:5px;">
                    <span id="addMoreImage" style="display:none;" class="addMoreImage"></span>
                </div>
                <?php endif;?>
            </div>
            <div class="row">
                <label class="col-md-3 control-label"></label>

                <div class="col-md-9">
                    <?php echo __d('forum','Recommended size: 48 x 48');?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('forum','Status');?> (<a data-html="true" href="javascript:void(0)" class="tooltips" data-original-title="<?php echo __d( 'forum','Lock means that noone can post new topic into forum except for moderators and site admin')?>" data-placement="top">?</a>)</label>
                <div class="col-md-9">
                    <?php
                    $open = $lock = '';
                    if(!empty($bIsEdit))
                    {
                        $open = 'selected';
                        if($forum['Forum']['status'] == 1) $open = 'selected'; else $lock = 'selected';
                    }
                    else
                    {
                        $open = 'selected';
                    }
                    ?>
                    <select class="form-control input-medium input-inline" name="status">
                        <option <?php echo $open;unset($open);?> value="1"><?php echo __d('forum','Open');?></option>
                        <option <?php echo $lock;unset($lock);?> value="0"><?php echo __d('forum','Lock');?></option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('forum','Visibility');?> (<a data-html="true" href="javascript:void(0)" class="tooltips" data-original-title="<?php echo __d( 'forum','Configure view permission for each user roles. Uncheck if you want to disable this forum from all members')?>" data-placement="top">?</a>)</label>
                <div class="col-md-9">
                    <?php echo $this->element('admin/permissions', array('permission' => $forum['Forum']['permission'])); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('forum','Moderator');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('moderator', array('class' => 'form-control', 'value' => $forum['Forum']['moderator'], 'style' => 'display: none;')); ?>
                </div>
            </div>
        </div>
    </form>
    <div class="alert alert-danger error-message" id="errorMessage" style="display:none;margin-top:10px;">
    </div>
</div>
<div class="modal-footer">
    <a href="#" id="createButton" class="btn btn-action"><?php echo  __d('forum','Save') ?></a>
    <button type="button" class="btn default" data-dismiss="modal"><?php echo  __d('forum','Cancel') ?></button>
</div>