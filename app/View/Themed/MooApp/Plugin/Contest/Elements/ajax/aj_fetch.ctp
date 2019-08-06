<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooContest"], function($, mooContest) {
        mooContest.initSubmitBtnVideo();
    });
</script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>
        mooContest.initSubmitBtnVideo();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
    <div class="create_form">
        
        <ul class="list6 list6sm2">
            <?php echo $this->Form->hidden('contest_id', array('value' => $contest_id)); ?>
            <?php echo $this->Form->hidden('source', array('value' => $video['Video']['source'])); ?>
            <?php echo $this->Form->hidden('source_id', array('value' => $video['Video']['source_id'])); ?>
            <?php echo $this->Form->hidden('thumbnail', array('value' => $video['Video']['thumb'])); ?>
            <li>
                <div class="col-md-2">
                    <label><?php echo __d('contest', 'Thumbnail')?></label>
                </div>
                <div class="col-md-10">
                    <div id="entry_preview" style="text-align: left;" >
                        <img width="150"  src="<?php echo $video['Video']['thumb']; ?>">
                    </div>
                </div>
                <div class="clear"></div>
            </li>
            <li>
                <div class="col-md-2">
                    <label><?php echo __d('contest', 'Caption')?></label>
                </div>
                <div class="col-md-10">
                    <?php echo $this->Form->textarea('caption', array('value' => $video['Video']['title'])); ?>
                </div>
                <div class="clear"></div>
            </li>
            <li>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                </div>
                <div class="col-md-10">
                    <button type='button' class='mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored' id="saveVideoEntryBtn"><?php echo __d('contest', 'Submit Video')?></button>
                </div>
                <div class="clear"></div>
            </li>
        </ul>
    </div>
<div class="error-message" style="display:none;margin-top:10px;"></div>