<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>

<script type="text/javascript">
    require(["jquery","mooForum"], function($,mooForum) {
        mooForum.initOnReport();
    });
</script>
 
<div class="title-modal">
    <?php echo __d('forum','Report')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="error-message" style="display:none;"></div>
<div class='create_form'>
<form id="forum_report_form">
<?php echo $this->Form->hidden('forum_topic_id', array( 'value' => $forum_topic_id ) ); ?>
<ul class="list6 list6sm2" style="position:relative">
	<li>
            <div class='col-md-2'>
                <label><?php echo __d('forum','Reason')?></label>
            </div>
            <div class='col-md-10'>
                <?php echo $this->Form->textarea('reason'); ?>
            </div>
            <div class='clear'></div>
	</li>
	<li>
            <div class='col-md-2'>
                <label>&nbsp;</label>
            </div>
            <div class='col-md-10'>
                <a href="javascript:void(0);" class="button" id="forum_report_btn"><?php echo __d('forum','Report')?></a>
            </div>
            <div class='clear'></div>
	</li>
</ul>
</form>
</div>
</div>