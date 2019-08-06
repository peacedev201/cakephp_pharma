<?php $helper = MooCore::getInstance()->getHelper('Document_Document');?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooDocument', 'hideshare'),'object'=>array('$', 'mooDocument'))); ?>
	mooDocument.initViewDocument();
<?php $this->Html->scriptEnd(); ?>
<div class="bar-content">
    <div>
    	<div class="content_center full_content p_m_10">	
	    	<h1 class="document-detail-title"><?php echo $document['Document']['moo_title']?></h1>	    	
	        <div class="document-detail-action">
	            <div class="list_option">
	                <div class="dropdown">
	                    <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
	                        <i class="material-icons">more_vert</i>
	                    </button>
	                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
	                        <?php if ($helper->canEdit($document,MooCore::getInstance()->getViewer())):?>
		                        <li><a href="<?php echo $this->request->base?>/documents/create/<?php echo $document['Document']['id']?>" title="<?php echo __d('document','Edit Document')?>"><?php echo __d('document','Edit Document')?></a></li>
		                        <li><a href="javascript:void(0)" data-id="<?php echo $document['Document']['id'];?>" class="deleteDocument"><?php echo __d('document','Delete Document')?></a></li>
	                        <?php endif; ?>
	                        <li><a download="<?php echo $document['Document']['file_name'];?>" href="<?php echo $helper->getDocument($document);?>" title="<?php echo __d('document','Download Document')?>"><?php echo __d('document','Download Document')?></a></li>
	                        <li class="seperate"></li>
	                        <li><a href="<?php echo $this->request->base?>/reports/ajax_create/document_document/<?php echo $document['Document']['id'];?>" data-target="#themeModal" data-toggle="modal" title="<?php echo __d('document','Report Document')?>"><?php echo __d('document', 'Report Document')?></a></li>
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
	        <div class="extra_info">
	    	<?php echo __d('document', 'Posted by %s', $this->Moo->getName($document['User']))?> <?php echo __d('document','in')?> <a href="<?php echo $this->request->base?>/documents/index/category:<?php echo $document['Document']['category_id']?>/<?php echo seoUrl($document['Category']['name'])?>"><?php echo $document['Category']['name']?></a> <?php echo $this->Moo->getTime($document['Document']['created'], Configure::read('core.date_format'), $utz)?>
	    	&nbsp;&middot;&nbsp;<?php if ($document['Document']['privacy'] == PRIVACY_PUBLIC): ?>
	                        <?php echo __d('document','Public') ?>
	                        <?php elseif ($document['Document']['privacy'] == PRIVACY_ME): ?>
	                        <?php echo __d('document','Private') ?>
	                        <?php elseif ($document['Document']['privacy'] == PRIVACY_FRIENDS): ?>
	                        <?php echo __d('document','Friend') ?>
	                        <?php endif; ?>
							&nbsp&nbsp&nbsp<i class="material-icons">cloud_download</i>&nbsp;<span><?php echo $document['Document']['download_count']?></span>							
							&nbsp&nbsp&nbsp<i class="material-icons">remove_red_eye</i>&nbsp;<span><?php echo $document['Document']['view_count']?></span>
	       
	        </div> 	        
		    <div class="document_scribd">
		    	<iframe id="iframe_preview" height="100%" width="100%" src="https://docs.google.com/viewer?embedded=true&url=<?php echo $helper->getDocument($document);?>"></iframe>	    	
		    </div>
	        <div class="documents_license">
	        	<?php echo $helper->renderLicense($document);?>
	        </div>
	    </div>	    
    </div>
</div>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
		<?php
			$options = array();
			if ($document['Document']['privacy'] != PRIVACY_ME)
			{
				$options = array('shareUrl' => $this->Html->url(array(
					'plugin' => false,
					'controller' => 'share',
					'action' => 'ajax_share',
					'Document_Document',
					'id' => $document['Document']['id'],
					'type' => 'document_item_detail'
				), true));
			}
		?>
    	<?php echo $this->renderLike($options);?>
    </div>
</div>
<div class="bar-content full_content p_m_10 blog-comment">
   	<?php echo $this->renderComment();?>
</div>