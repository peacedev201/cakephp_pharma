<?php
$documentHelper = MooCore::getInstance()->getHelper('Document_Document');
?>
<div class="comment_message">
	<?php echo $this->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
    
    <div class="share-content">
    	<?php
	    	$activityModel = MooCore::getInstance()->getModel('Activity');
	    	$parentFeed = $activityModel->findById($activity['Activity']['parent_id']);
	    	$document = MooCore::getInstance()->getItemByType($parentFeed['Activity']['item_type'], $parentFeed['Activity']['item_id']);
    	?>
    	<div class="activity_feed_content">
            
            <div class="activity_text">
                <?php echo $this->Moo->getName($parentFeed['User']) ?>
                <?php echo __d('document','created a new document'); ?>
            </div>
            
            <div class="parent_feed_time">
                <span class="date"><?php echo $this->Moo->getTime($parentFeed['Activity']['created'], Configure::read('core.date_format'), $utz) ?></span>
            </div>
            
        </div>
		<div class="activity_item">
		     <div class="document_img">
				<a href="<?php echo $document['Document']['moo_href']?>">
					<img alt="" src="<?php echo $documentHelper->getImage($document);?>">			
				</a>		
				<?php if ($document['Document']['feature']):?>
					<span class="page_feature"><img alt="" src="<?php echo FULL_BASE_URL . $this->request->webroot . 'document/img/document_featured.png';?>"></span>
				<?php endif;?>
			</div>
		    <div class="activity_right ">
		        <div class="activity_header">
		            <a href="<?php echo  $document['Document']['moo_href'] ?>"><?php echo  $document['Document']['moo_title'] ?></a>
		        </div>
		        <?php echo  $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $document['Document']['description'])), 200, array('exact' => false)),Configure::read('Document.document_hashtag_enabled') )?>
		      </div>
		    <div class="clear"></div>
		</div>
	</div>
</div>