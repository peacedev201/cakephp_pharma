<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["mooUploadVideo"], function(mooUploadVideo) {
        mooUploadVideo.initAjaxUpload();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooUploadVideo'), 'object' => array('mooUploadVideo'))); ?>
mooUploadVideo.initAjaxUpload();<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
    
<style>
    #createForm li {
        list-style: none;
    }
</style>

<div class="bar-content full_content p_m_10">
    <div class="content_center create_form">
        <form id="createForm">
            <ul class="list6 ">
                <?php echo $this->Form->hidden('group_id', array('value' => $group_id)); ?>
                <?php echo $this->Form->hidden('destination', array('value' => '')); ?>
                <li>    
                    <div class="col-md-2">
                        <label><?php echo __('Video Title') ?></label>
                    </div>
                    <div class="col-md-10">
                        <?php echo $this->Form->text('title', array('value' => '')); ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="col-md-2">
                        <label><?php echo __('Description') ?></label>
                    </div>
                    <div class="col-md-10">
                        <?php echo $this->Form->textarea('description', array('value' => '')); ?>
                    </div>
                    <div class="clear"></div>
                </li>

                <li>
                    <div class="col-md-2"></div>
                    <div class="col-md-10">
                        <div id="video_upload"></div>
                        <a href="javascript:void(0);" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored video-upload" id="triggerUpload"><?php echo __('Upload Queued Files') ?></a>
                        <a href="javascript:void(0);" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored video-upload" id="saveBtn" style="display: none"><?php echo __('Save Video'); ?></a>
                    </div>
                    <div class="clear"></div>
                </li>
            </ul>
        </form>
        <div class="error-message" style="display: none; margin-top: 10px;"></div>
    </div>
</div>