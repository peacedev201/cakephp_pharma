
<?php
echo $this->Html->script(array('tinymce/tinymce.min', 'jquery.fileuploader'), array('inline' => false));
echo $this->Html->css(array( 'fineuploader' ));

echo $this->Html->css('Feedback.feedback.css');
echo $this->Html->script('Feedback.feedback.js');

?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
    var newPhotos = new Array();
    
    var errorHandler = function(event, id, fileName, reason) {
        qq.log("id: " + id + ", fileName: " + fileName + ", reason: " + reason);
    };

    $(document).ready(function(){
        var uploader = new qq.FineUploader({
            element: $('#photos_upload')[0],
            autoUpload: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">camera_alt</i><?php echo __d('feedback', "Drag or click here to upload photo")?></div>'
            },
            validation: {
                allowedExtensions: ['jpg', 'jpeg', 'png'],
                sizeLimit: 10 * 1024 * 1024
            },
            request: {
                endpoint: "<?php echo $this->request->base?>/upload/photos/feedback/<?php echo $iId?><?php echo Configure::read('core.save_original_image')?>"
            },
            callbacks: {
                onError: errorHandler,
                onComplete: function(id, fileName, response) {
                    newPhotos.push( response.photo_id );

                    $('#new_photos').val( newPhotos.join(',') );
                    $('#nextStep').show();

                    //$('#photo_review > img').attr('src', response.thumb);
                    //$('#photo_review > img').show();
                }
            }
        });

        $('#triggerUpload').click(function() {
            uploader.uploadStoredFiles();
        });
    });

    function setNewPhotos()
    {
        jQuery('#new_photos').val( newPhotos.join(',') );
    }
    
<?php $this->Html->scriptEnd(); ?>

<div class="create_form create_form_feedback">
	<div class="bar-content">
		<div class="content_center">
			<div class="box3">
				<div class="mo_breadcrumb">
		            <h1><?php echo __d('feedback', 'Upload Photos');?></h1>	
		        </div>
                <div class="col-md-2">&nbsp;</div> 
    
                <form action="<?php echo $this->request->base.$url_feedback?>/add_image/" method="post">
                    <div id="photos_upload"></div>
                    <div id="photo_review"><img style="display: none;" src="" /></div>
                    <a href="#" class="button button-primary" id="triggerUpload"><?php echo __d('feedback', 'Upload Queued Files')?></a>
                    <input type="hidden" name="new_photos" id="new_photos">
                    <input type="hidden" name="target_id" value="">
                    <a type="button" href="<?php echo $this->request->base.$url_feedback.'/view/'.$iId?>" class="btn btn-action" id="nextStep" style="display:none"><?php echo __d('feedback', 'Go To Feedback')?></a>
                </form>
                <div class="clear"></div>
                
        	</div>
	    </div>
	</div>
</div>