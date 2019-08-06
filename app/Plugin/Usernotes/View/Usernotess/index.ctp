<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooUsernotes"], function($,mooUsernotes) {
        mooUsernotes.initOnUserIndex();
        <?php if ( !empty( $values ) || !empty($online_filter) ): ?>
        $('#searchPeople').trigger('click');
        <?php endif; ?>
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooUsernotes'), 'object' => array('$', 'mooUsernotes'))); ?>
mooUsernotes.initOnUserIndex();
<?php if ( !empty( $values ) || !empty($online_filter) ): ?>
$('#searchPeople').trigger('click');
<?php endif; ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
	<div class="box2 filter_block">
            <h3 class="visible-xs visible-sm"><?php echo __d('usernotes','Browse')?></h3>
            <div class="box_content">
		<ul class="list2 menu-list" id="browse">
			<li class="current" id="everyone"><a data-url="<?php echo $this->request->base?>/usernotess/ajax_browse/all" href="<?php echo $this->request->base?>/usernotess"><?php echo __d('usernotes','Everyone')?></a></li>
			<?php if (!empty($cuser)): ?>
                        <li><a data-url="<?php echo $this->request->base?>/usernotess/ajax_browse/friends" href="<?php echo $this->request->base?>/usernotess"><?php echo __d('usernotes','My Friends')?></a></li>
                        <?php endif; ?>
		</ul>
            </div>
	</div>

        <?php echo $this->element('Usernotes./search_form'); ?>
<?php $this->end(); ?>

    <div class="bar-content">
        <div class="content_center full_content p_m_10">
        
            <div class="mo_breadcrumb">
                <h1><?php echo __d('usernotes','My notes')?></h1>
            </div>
            
            <ul class="users_list usernotes-content" id="list-content">
                    <?php 
                    if ( !empty( $values ) || !empty($online_filter) )
                            echo __d('usernotes','Loading...');
                    else
                            echo $this->element( 'Usernotes.lists/users_list', array( 'more_url' => '/usernotess/ajax_browse/all/page:2' ) );
                    ?>
            </ul>
            <div class="clear"></div>
        </div>
    </div>
<section class="modal fade" id="unoteModal" role="basic" aria-labelledby="myModalLabel" aria-hidden="true">
    <input type="hidden" class="unote-target-id" value="">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="font-size: 14px;">
                <?php echo __d('usernotes', 'Edit note') ?>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <textarea   name="content" class="form-control " id="unote-content">&nbsp;</textarea> 
            </div>
            <div class="modal-footer">
                <div class="error-message unoteErrorMessage"  style="display:none;text-align: left;margin-bottom: 2px;"></div>
                <a href="javascript:void(0);" id="unote-btn-save" class="btn btn-action"><?php echo __d('usernotes', 'Save') ?></a>
            </div>
        </div>
    </div>
</section>  
