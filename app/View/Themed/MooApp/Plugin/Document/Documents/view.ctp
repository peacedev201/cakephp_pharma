<?php 
	$helper = MooCore::getInstance()->getHelper('Document_Document');
	$tagModel = MooCore::getInstance()->getModel("Tag");
	$tags = $tagModel->getContentTags($document['Document']['id'], 'Document_Document');
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooDocument', 'hideshare'),'object'=>array('$', 'mooDocument'))); ?>
	mooDocument.initViewDocument();
<?php $this->Html->scriptEnd(); ?>
<style>
button.close {
    display: block;
}
</style>
<div class="bar-content">
    <div>
    	<div class="content_center full_content">	
	    	<h1 class="document-detail-title"><?php echo $document['Document']['moo_title']?></h1>	    	
	        <div class="document-detail-action moo_app_document_action">
	        	<div class="list_option">
	                <div class="dropdown">
	                    <button id="video_edit_<?php echo $document["Document"]["id"] ?>" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon">
	                        <i class="material-icons">more_vert</i>
	                    </button>
	                    <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="video_edit_<?php echo $document["Document"]["id"] ?>">
	                    	<?php if ($helper->canEdit($document,MooCore::getInstance()->getViewer())):?>
		                        <li class="mdl-menu__item"><a href="<?php echo $this->request->base?>/documents/create/<?php echo $document['Document']['id']?>?app_no_tab=1" title="<?php echo __d('document','Edit Document')?>"><?php echo __d('document','Edit Document')?></a></li>
		                        <li class="mdl-menu__item"><a href="javascript:void(0)" data-id="<?php echo $document['Document']['id'];?>" class="deleteDocument"><?php echo __d('document','Delete Document')?></a></li>
	                        <?php endif; ?>	                        	                        
	                        <li class="mdl-menu__item"><a href="<?php echo $this->request->base?>/reports/ajax_create/document_document/<?php echo $document['Document']['id'];?>" title="<?php echo __d('document','Report Document')?>"><?php echo __d('document', 'Report Document')?></a></li>
	                        <?php if ($document['Document']['privacy'] != PRIVACY_ME): ?>
		                        <?php echo $this->element('share/menu',array('param' => 'Document_Document','action' => 'document_item_detail' ,'id'=>$document['Document']['id'])); ?>
                        	<?php endif; ?>
	                    </ul>
	                </div>
            	</div>
	        </div>
	       	<?php if (!$document['Document']['approve']):?>
		    	<div class="document_alert-message">
		       		<?php echo __d('document',"Document is pending for admin's approval.");?>
		       	</div>
	       	<?php endif;?>
	    	<div class="document_description">
	    		<?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags( $document['Document']['description']  , Configure::read('Document.document_hashtag_enabled') ))?>
	    	</div>	    	
	    	<?php if (count($tags)):?>
	    		<div><?php echo $this->element( 'blocks/tags_item_block',array("tags"=>$tags) ); ?></div>
	    	<?php endif;?>
	        <div class="extra_info">
	    	<?php echo __d('document', 'Posted by %s', $this->Moo->getName($document['User']))?> <?php echo __d('document','in')?> <a href="<?php echo $this->request->base?>/documents/index/category:<?php echo $document['Document']['category_id']?>/<?php echo seoUrl($document['Category']['name'])?>"><?php echo $document['Category']['name']?></a> <?php echo $this->Moo->getTime($document['Document']['created'], Configure::read('core.date_format'), $utz)?>
	    	&nbsp;&middot;&nbsp;<?php if ($document['Document']['privacy'] == PRIVACY_PUBLIC): ?>
	                        <?php echo __d('document','Public') ?>
	                        <?php elseif ($document['Document']['privacy'] == PRIVACY_ME): ?>
	                        <?php echo __d('document','Private') ?>
	                        <?php elseif ($document['Document']['privacy'] == PRIVACY_FRIENDS): ?>
	                        <?php echo __d('document','Friend') ?>
	                        <?php endif; ?>
							&nbsp&nbsp&nbsp<i class='fa fa-cloud-download'></i>&nbsp;<span><?php echo $document['Document']['download_count']?></span>							
							&nbsp&nbsp&nbsp<i class='fa fa-eye'></i>&nbsp;<span><?php echo $document['Document']['view_count']?></span>
	       
	        </div> 	        
		    <div class="document_scribd">
		    	<iframe id="iframe_preview" height="100%" width="100%" src="https://docs.google.com/viewer?embedded=true&url=<?php echo $helper->getDocument($document);?>"></iframe>	    	
		    </div>
	        <div class="documents_license">
	        	<?php echo $helper->renderLicense($document);?>
	        </div>
	        <?php echo $this->renderLike();?>
	        <div class="clear"></div>
	    </div>	    
    </div>
</div>
<?php echo $this->renderComment();?>
