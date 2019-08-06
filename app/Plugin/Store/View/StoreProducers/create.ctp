<?php $this->setCurrentStyle(4) ?>

<?php if(empty($isMobile)): ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function(){    

});
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>

<?php $this->Html->scriptEnd(); ?>
<div class="create_form">
<div class="bar-content">
<div class="content_center">
<div class="box3">
	<form class="form-horizontal" id='createForm' action="<?php echo  $this->request->base; ?>/stores/producers/save" method="post">
	<?php
	if (!empty($producer['StoreProducer']['id']))
		echo $this->Form->hidden('id', array('value' => $producer['StoreProducer']['id']));
	?>
		<div class="mo_breadcrumb">
            <h1><?php if (empty($producer['StoreProducer']['id'])) echo __d('store',  'Create New Producer'); else echo __d('store',  'Edit Producer');?></h1>
        </div>
        <div class="full_content p_m_10">
            <div class="form_content">
                <ul >
                        
						<li>
                            <div class="col-md-2">
                                <label><?php echo __d('store',  'Name')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->text('name', array('value' => $producer['StoreProducer']['name'])); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
						<li>
                            <div class="col-md-2">
                                <label><?php echo __d('store',  'Phone')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->text('phone', array('value' => $producer['StoreProducer']['phone'])); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
						<li>
                            <div class="col-md-2">
                                <label><?php echo __d('store',  'Email')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->text('email', array('value' => $producer['StoreProducer']['email']) ); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
						<li>
                            <div class="col-md-2">
                                <label><?php echo __d('store',  'Address')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->text('address', array('value' => $producer['StoreProducer']['address']) ); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
												
						<li>
                            <div class="col-md-2">
                                <label><?php echo __d('store',  'Publish')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->checkbox('publish', array('checked' => $producer['StoreProducer']['publish']) ); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
						<li>
                            <div class="col-md-2">
                                <label><?php echo __d('store',  'Ordering')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->number('ordering', array('value' => $producer['StoreProducer']['ordering']) ); ?>
                            </div>
                            <div class="clear"></div>
                        </li>                        
                        
                </ul>

                <div class="col-md-2">&nbsp;</div> 
    
                <div class="col-md-10">                    
                    
                        <div style="margin:20px 0"> 
							<?php echo $this->Form->submit(__d('store',  'Save'), array('id' => 'btnSave','class' => 'action btn btn-action','div' => false)); ?>
							
							<a id="btnCancel" href="<?php echo $this->request->base?>/stores/producers/view/" class="button"><?php echo __d('store',  'Cancel')?></a>
                                
                                <?php if ( ($producer['StoreProducer']['store_id'] == $uid ) || ( !empty( $producer['StoreProducer']['id'] ) && $cuser['Role']['is_admin'] ) ): ?>
                                <a href="javascript:void(0)" onclick="mooConfirm( '<?php echo __d('store',  'Are you sure you want to remove this producer?')?>', '<?php echo $this->request->base?>/stores/producers/delete/<?php echo $producer['StoreProducer']['id']?>' )" class="button"><?php echo __d('store',  'Delete')?></a>
                                <?php endif; ?>								
								
								
                        </div>
                        <div class="error-message" id="errorMessage" style="display: none;"></div>
                </div>
        </form>
                <div class="clear"></div>

        </div>
    </div>
</div>
</div>
</div>
</div>

<?php if($this->request->is('ajax')): ?>
	<script>
<?php else: ?>
	<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>

$(function(){
	$('.action').on('click',function(){
		$('.action').addClass('disable');
	})
})

<?php if($this->request->is('ajax')): ?>
	</script>
<?php else: ?>
	<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>