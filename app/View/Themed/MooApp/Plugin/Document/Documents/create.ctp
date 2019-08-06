<?php
	$helper = MooCore::getInstance()->getHelper('Document_Document');
	$this->addPhraseJs(array(
			'drag_photo' => '',
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
			        <div class="full_content">
			        	<?php
						if (!empty($document['Document']['id']))
							echo $this->Form->hidden('id', array('value' => $document['Document']['id']));
							
						echo $this->Form->hidden('thumbnail', array('value' => $document['Document']['thumbnail']));
						?>
	            		<div class="form_content">
	            			<ul>	 
	            				<li>
		                            <div class="thumb_content">
		                                <div class="thumb_item">
			                                <div id="document_thumnail_preview">
			                                    <?php if (!empty($document['Document']['thumbnail'])): ?>
			                                    	<img width="111" height="144" id="item-avatar" class="img_wrapper" style="background-image:url(<?php echo $helper->getImage($document)?>)" src="<?php echo $this->request->webroot?>theme/<?php echo $this->theme ?>/img/s.png" />
			                                    <?php else: ?>
			                                        <img width="111" height="144" id="item-avatar" class="img_wrapper" style="display: none;" src="<?php echo $this->request->webroot?>theme/<?php echo $this->theme ?>/img/s.png" />
			                                    <?php endif; ?>
			                                    </div>
			                                </div>
		                                <div class="thumb_qq" id="document_thumnail"></div>
		                                <div class="thumb_text">
		                                    <h4><?php echo __d('document','Upload Document Thumb') ?></h4>
		                                    <div><?php echo __d('document','Click thumb to upload') ?></div>
		                                </div>
		                            </div>
		                        </li>           				
	            				<?php if (!$is_edit):?>
	            				<li>
		                            <div>
		                               	<div id="document_file_upload"></div>
		                               	<input type="hidden" name="document_file" id="document_file">
		                               	<input type="hidden" name="original_filename" id="original_filename">		                               	
		                            </div>
		                            <div class="clear"></div>
		                        </li>
		                        <?php endif;?>
		                        <li>
		                        	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
		                                <input name="title" class="mdl-textfield__input" type="text" value="<?php echo htmlspecialchars_decode($document['Document']['title']) ?>" />
		                                <label class="mdl-textfield__label" ><?php echo __d('document','Title')?></label>
		                            </div>
		                        </li>
		                        <li>
		                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
		                                <textarea class="mdl-textfield__input" name="description" id="editor"><?php echo $document['Document']['description'] ?></textarea>
		                                <label class="mdl-textfield__label" for="sample3"><?php echo __d('document','Description')?></label>
		                            </div>
		                        </li>
		                        <li>		                        	
									<?php echo $this->Form->select('category_id',$categories,array('empty' => false,'value'=>$document['Document']['category_id'])); ?>
		                        </li>
		                        <li>
		                        	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is-dirty is-upgraded">
		                        		<?php echo $this->Form->select('document_license_id',$licenses,array('empty' => false,'value'=>$document['Document']['document_license_id'])); ?>
		                        	</div>
		                        </li>
		                        <li>
	                                 <?php
			                            echo $this->Form->select( 'privacy',
			                                                      array( PRIVACY_EVERYONE => __d('document', 'Everyone'),
			                                                             PRIVACY_FRIENDS  => __d('document', 'Friends Only'),
			                                                             PRIVACY_ME 	  => __d('document', 'Only Me')
			                                                            ),
			                                                      array('empty' => false,'value'=>$document['Document']['privacy'])
			                                                    );
			                         ?>
		                        </li>
		                        <li>
		                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
		                                <input name="tags" class="mdl-textfield__input" type="text" value="<?php echo $document['Document']['tags'] ? implode(" ", $document['Document']['tags']) : ''; ?>" />
		                                <label class="mdl-textfield__label" ><?php echo __d('document', 'Tags')?></label>
		                            </div>
		                        </li>
		                        <li>
		                            <div class="col-md-2">
		                                <label>&nbsp;</label>
		                            </div>
		                            <div class="col-md-10">
		                                <button type="submit" id="document_button" class='mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1'><?php echo __d('document' ,'Save')?></button>
		                                <?php if (!empty($document['Document']['id'])) : ?>
											<a href="<?php echo $document['Document']['moo_href']."?app_no_tab=1"?>" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1"><?php echo __('Cancel');?></a>											
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