<?php
echo $this->Html->css(array('fineuploader','button',), null, array('inline' => false));

echo $this->Html->script(array('tinymce/tinymce.min', 'vendor/jquery.fileuploader'), array('inline' => false));
$this->Html->addCrumb(__('Popup Manager'), array('controller' => 'popups', 'action' => 'admin_index'));
if ($popup['Popup']['id']){
    $this->Html->addCrumb(__d('popup','Edit Popup'), array('controller' => 'popups', 'action' => 'admin_create'));
}
else{
    $this->Html->addCrumb(__d('popup','Create New Popup'), array('controller' => 'popups', 'action' => 'admin_create'));
}



$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Popup"));
$this->end();
?>
<?php echo $this->Moo->renderMenu('Popup', __('General'));?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>

    $(document).ready(function(){

        tinymce.init({
            selector: "textarea",
            language : mooConfig.tinyMCE_language,
            theme: "modern",
            skin: 'light',
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor"
            ],
            toolbar1: "styleselect | bold italic | bullist numlist outdent indent | forecolor backcolor emoticons | link unlink anchor image media | preview fullscreen code",
            image_advtab: true,
            height: 500,
            relative_urls : false,
            remove_script_host : true,
            document_base_url : '<?php echo FULL_BASE_URL . $this->request->root?>'
        });

        $('#createButton').click(function(){
            var checked = false;
            $('#permission_list :checkbox').each(function(){
                if ($(this).is(':checked'))
                    checked = true;
            })

            if (!checked)
            {
                mooAlert('<?php echo __d('popup','Please check at least one user role in the Permissions tab');?>');
                return;
            }
			if($.trim($('#title').val()) == ''){
				mooAlert('<?php echo __d('popup','Popup title can not empty');?>');
                return;
			}



            $('#page-body-textarea').val(tinyMCE.activeEditor.getContent());



			if($.trim($('#page-body-textarea').val()) == ''){
				mooAlert('<?php echo __d('popup','Popup content can not empty');?>');
                return;
			}
			disableButton('createButton');
            mooAjax.post({
                    url : "<?php echo $this->request->base?>/admin/popups_for_page/popups/save",
                    data: jQuery("#createForm").serialize()
                }, function(data){
                    enableButton('createButton');
                    var json = $.parseJSON(data);
                    if ( json.result == 1 )
                    {
                        window.location = '<?php echo $this->request->base?>/admin/popups_for_page/popups/';
                    }
                    if ( json.result == 2 )
                    {
                        confirm_disable_allpage(json.message);
                    }
                    if ( json.result == 0 )
                    {
                        mooAlert(json.message);
                    }
            });

        });

        $('#cancelButton').click(function(){
			window.location = '<?php echo $this->request->base?>/admin/popups_for_page/popups/';
		});

        $('#alias').on('blur', function(){
            $('#alias').val( $('#alias').val().replace(/[^a-zA-Z0-9-_]/g, '_').toLowerCase() );
        });

        $('#language').change(function(e){
            window.location.href = "<?php echo $this->request->base;?>/admin/popups_for_page/popups/create/<?php echo $popup['Popup']['id'];?>/" +$('#language').val();
        });

        $('#onetime').change(function(){
            if($(this).is(":checked")) {
                $('#popup_option').attr('disabled',true);
            }
            else {
                $('#popup_option').removeAttr('disabled',false);
            }
        });
		var uploader = new qq.FineUploader({
            element: $('#photos_upload')[0],
            autoUpload: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="icon-camera"></i>' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
            },
            validation: {
                allowedExtensions : mooConfig.photoExt,
                sizeLimit : mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/popup/popup_upload/images"
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
                onComplete: function (id, fileName, response) {
                    $('#popup_photo_ids').val($('#popup_photo_ids').val() + ',' + response.photo_id);
                    tinyMCE.activeEditor.insertContent('<p align="center"><a href="' + response.large + '" class="attached-image"><img src="' + response.thumb + '"></a></p><br>');
                }
            }
        });
		$('#toggleUploader').unbind('click');
        $('#toggleUploader').on('click', function(){
            $('#images-uploader').slideToggle();
        });
        $('#triggerUpload').unbind('click');
        $('#triggerUpload').click(function () {
            uploader.uploadStoredFiles();
        });

    });

    function confirm_disable_allpage(msg){
		// Set title
		$($('#portlet-config  .modal-header .modal-title')[0]).html(mooPhrase.__('please_confirm'));
		// Set content
		$($('#portlet-config  .modal-body')[0]).html(msg);
		// OK callback
		$('#portlet-config  .modal-footer .ok').attr("data-dismiss","modal");
		$('#portlet-config  .modal-footer .ok').click(function(){
			disable_allpage();
		});
		$('#portlet-config').modal('show');
    }
    function disable_allpage(){
		 $.post('<?php echo $this->request->base?>/admin/popups_for_page/popups/disable/',function(data){
			 var json = $.parseJSON(data);
			 mooAlert(json.message);
		 });
    }


<?php $this->Html->scriptEnd(); ?>

<style>
.upload-section{
    padding: 5px;
    display: block;
    height: 42px;
    line-height: 32px;
};
</style>

<div class="portlet box">

    <div class="portlet-body form">

        <form id="createForm" class="form-horizontal">
            <div class="form-body">

                <?php echo $this->Form->hidden('id', array('value' => $popup['Popup']['id'])); ?>
                <?php echo $this->Form->hidden('popup_photo_ids');?>
                <?php if ($popup['Popup']['id']): ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo __('Language Pack');?>(<a data-html="true" href="javascript:void(0)" class="tooltips" data-original-title="<?php echo __('Select a language to translate for page title and page content only'); ?>" data-placement="top">?</a>)</label>
                        <div class="col-md-9">
                            <?php echo $this->Form->select('language', $languages, array('class'=>'form-control','value'=>$language,'empty'=>false)); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('popup','Popup Title');?></label>
                    <div class="col-md-9">
                        <?php echo $this->Form->text('title', array('placeholder'=>__('Enter text'),'class'=>'form-control ','value' => $popup['Popup']['title'])); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('popup','Appear at');?></label>
                    <div class="col-md-9">
                        <?php $attributes = array('value' => $popup['Popup']['page_id'], 'empty' => false); echo $this->Form->select('page_id', $all_pages,$attributes); ?>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('popup','Popup Content');?></label>
                    <div class="col-md-9">

                        <?php echo $this->Form->textarea('body', array('value' => $popup['Popup']['body'], 'id' => 'page-body-textarea')); ?>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">&nbsp;</div>

                    <div class="col-md-9">
                        <div id="images-uploader" style="margin:10px 0; display: none;">
                            <div id="photos_upload"></div>
                            <a href="#" class="button button-primary" id="triggerUpload"><?php echo __('Upload Queued Files') ?></a>
                        </div>
                        <?php if (empty($isMobile)): ?>
                            <a id="toggleUploader" href="javascript:void(0)"><?php echo __('Toggle Images Uploader') ?></a>
                        <?php endif; ?>
                        <div class="error-message" id="errorMessage" style="display: none;"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-9">

                        <?php echo $this->Form->checkbox('onetime',array('checked' => $popup['Popup']['onetime'],'id'=>'onetime'));?> <?php echo __d('popup','Appear one-time only'); ?>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <?php echo $this->Form->checkbox('popup_option',array('checked' => $popup['Popup']['popup_option'],'id'=> 'popup_option',));?> <?php echo __d('popup','Show option for member to select to hide it'); ?>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-9">

                        <?php echo $this->Form->checkbox('enable',array('checked' => $popup['Popup']['enable']));?> <?php echo __d('popup','Enable');?>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('popup','User roles can view?');?></label>
                    <div class="col-md-9">
                    </div>
                </div>
                <?php echo $this->element('admin/permissions', array('permission' => $popup['Popup']['permission'])); ?>

            </div>
        </form>
        <div class="form-actions">
            <div class="row">
                <div class="col-md-offset-3 col-md-9">
                    <button id="createButton" class="btn btn-circle btn-action"><?php echo __('Save');?></button>
					<button id="cancelButton" class="btn btn-circle btn-action"><?php echo __('Cancel');?></button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <div class="alert alert-danger error-message" style="display:none;margin-top:10px"></div>
                </div>
            </div>
        </div>

        <!-- END FORM-->
    </div>
</div>



