<?php $helper = MooCore::getInstance()->getHelper('Document_Document');	?>
<ul class="document-content-block">
    <?php foreach ($documents as $document): ?>
		<li class="list-item-inline list-item-inline-text">
			<div class="document_img">
				<a href="<?php echo $document['Document']['moo_href']?>">
					<img alt="" src="<?php echo $helper->getImage($document);?>">
					<?php if ($document['Document']['feature']):?>
						<span class="page_feature"><img alt="" src="<?php echo FULL_BASE_URL . $this->request->webroot . 'document/img/document_featured.png';?>"></span>
					<?php endif;?>			
				</a>
			</div>
			<div class="document_detail">
				<div class="title-list">
					<a href="<?php echo $document['Document']['moo_href']; ?>"><?php echo $this->Text->truncate($document['Document']['moo_title'],15);?></a>									
				</div>
				<div class="extra_count">
					<p><?php echo __dn('document','%s comment', '%s comments', $document['Document']['comment_count'], $document['Document']['comment_count'] )?> </p>
					<p><?php echo __dn('document','%s like', '%s likes', $document['Document']['like_count'], $document['Document']['like_count'] )?></p>
					<p><?php echo __dn('document','%s download', '%s downloads', $document['Document']['download_count'], $document['Document']['download_count'] )?></p>
					<p><?php echo __dn('document','%s view', '%s views', $document['Document']['view_count'], $document['Document']['view_count'] )?></p>
					<p><?php echo __dn('document','%s share', '%s shares', $document['Document']['share_count'], $document['Document']['share_count'] )?></p>
				</div>
			</div>
			<div class="clear"></div>
		</li>
	<?php endforeach; ?>
</ul>