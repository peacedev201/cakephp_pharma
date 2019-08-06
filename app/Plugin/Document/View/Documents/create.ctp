<?php	
	$helper = MooCore::getInstance()->getHelper('Document_Document');
	$this->addPhraseJs(array(
			'drag_photo' => __d('document', "Drag or click here to upload photo"),
			'drag_file' => __d('document', "Drag or click here to upload document"),
			'delete' => __d('document', "Delete")			
	));	
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooDocument'), 'object' => array('$', 'mooDocument'))); ?>
mooDocument.initCreateDocument(
	'<?php echo (!empty($document['Document']['id'])) ? $document['Document']['moo_href'] : ''?>',
	'<?php echo implode(',', $helper->support_extention);?>'
);
<?php $this->Html->scriptEnd(); ?>
<div class="create_form">
	<div class="bar-content">
		<div class="content_center">
			<div class="box3">
				<form action="<?php echo  $this->request->base; ?>/documents/document/save" enctype="multipart/form-data" id="createForm" method="post">
					<div class="mo_breadcrumb">
					   <?php if (!empty($document['Document']['id'])) : ?>
							<h1><?php echo __d('document','Edit document');?></h1>
					   <?php else: ?>
							<h1><?php echo __d('document','Add new document');?></h1>
					   <?php endif; ?>
			        </div>
			        <div class="full_content p_m_10">
			        	<?php
						if (!empty($document['Document']['id']))
							echo $this->Form->hidden('id', array('value' => $document['Document']['id']));
							
						echo $this->Form->hidden('thumbnail', array('value' => $document['Document']['thumbnail']));
						?>
	            		<div class="form_content">
	            			<ul>	            				
	            				<?php if (!$is_edit):?>
	            				<li>
		                            <div class="col-md-2">
		                                <label><?php echo __d('document','Document File')?></label>
		                            </div>
		                            <div class="col-md-10">
		                               	<div id="document_file_upload"></div>
		                               	<input type="hidden" name="document_file" id="document_file">
		                               	<input type="hidden" name="original_filename" id="original_filename">
		                               	<a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __d('document','Allowed file types')?>: <?php echo implode(' , ',$helper->support_extention);?>">(?)</a>
		                            </div>
		                            <div class="clear"></div>
		                        </li>
		                        <?php endif;?>
		                        <li>
		                            <div class="col-md-2">
		                                <label><?php echo __d('document','Title')?></label>
		                            </div>
		                            <div class="col-md-10">
		                                <?php echo $this->Form->text('title',array('value'=>htmlspecialchars_decode($document['Document']['title']))); ?>
		                            </div>
		                            <div class="clear"></div>
		                        </li>
		                        <li>
		                            <div class="col-md-2">
		                                <label><?php echo __d('document','Description')?></label>
		                            </div>
		                            <div class="col-md-10">
		                            	<?php echo $this->Form->tinyMCE('description', array('value' => $document['Document']['description'], 'id' => 'description')); ?>
		                            </div>
		                            <div class="clear"></div>
		                        </li>
		                        <li>
		                            <div class="col-md-2">
		                                <label><?php echo __d('document','Category')?></label>
		                            </div>
		                            <div class="col-md-10">
		                                <?php echo $this->Form->select('category_id',$categories,array('value'=>$document['Document']['category_id'])); ?>
		                            </div>
		                            <div class="clear"></div>
		                        </li>
		                        <li>
		                            <div class="col-md-2">
		                                <label><?php echo __d('document','License')?></label>
		                            </div>
		                            <div class="col-md-10">
		                                <?php echo $this->Form->select('document_license_id',$licenses,array('value'=>$document['Document']['document_license_id'])); ?>
		                            </div>
		                            <div class="clear"></div>
		                        </li>
		                        <li>
									<div class="col-md-2">
										<label><?php echo __d('document', 'Thumbnail')?> </label>
									</div>
									<div class="col-md-10">
										<div id="document_thumnail"></div>
										<div id="document_thumnail_preview">
											<?php if (!empty($document['Document']['thumbnail'])): ?>
												<img width="111" height="144" src="<?php echo  $helper->getImage($document) ?>" />
											<?php else: ?>
												<img width="111" height="144" style="display: none;" src="" />
											<?php endif; ?>
										</div>
									</div>
									<div class="clear"></div>
								</li>
		                        <li>
		                            <div class='col-md-2'>
		                                <label><?php echo __d('document','Privacy')?></label>
		                            </div>
		                            <div class='col-md-10'>
		                                 <?php
				                            echo $this->Form->select( 'privacy',
				                                                      array( PRIVACY_EVERYONE => __d('document', 'Everyone'),
				                                                             PRIVACY_FRIENDS  => __d('document', 'Friends Only'),
				                                                             PRIVACY_ME 	  => __d('document', 'Only Me')
				                                                            ),
				                                                      array('empty' => false,'value'=>$document['Document']['privacy'])
				                                                    );
				                         ?>
		                            </div>
		                            <div class="clear"></div>
		                        </li>
		                        <li>
		                            <div class="col-md-2">
		                                <label><?php echo __d('document', 'Tags')?></label>
		                            </div>
		                            <div class="col-md-10">
		                                <?php echo $this->Form->text('tags',array('value'=>$document['Document']['tags'])); ?> <a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __d('document','Separated by commas or space')?>">(?)</a>
		                            </div>
		                            <div class="clear"></div>
		                        </li>
		                        <li>
		                            <div class="col-md-2">
		                                <label>&nbsp;</label>
		                            </div>
		                            <div class="col-md-10">
		                                <button type="submit" id="document_button" class='btn btn-action'><?php echo __d('document' ,'Save')?></button>
		                                <?php if (!empty($document['Document']['id'])) : ?>
											<a href="<?php echo $document['Document']['moo_href']?>" class="button"><?php echo __('Cancel');?></a>
											<a href="javascript:void(0)" data-id="<?php echo $document['Document']['id'];?>" class="button deleteDocument"><?php echo __('Delete')?></a>											
										<?php endif;?>	                               
		                            </div>
		                            <div class="clear"></div>		                            
		                        </li>
		                        <li>
		                        	<div class="error-message" id="errorMessage" style="display:none"></div>
		                        </li>
	            			</ul>
	            		</div>
            		</div>
				</form>
			</div>
		</div>
	</div>
</div>