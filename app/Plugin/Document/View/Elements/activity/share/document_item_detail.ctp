<?php
$document = $object;
$documentHelper = MooCore::getInstance()->getHelper('Document_Document');
?>
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