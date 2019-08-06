<?php 
	$helper = MooCore::getInstance()->getHelper('Document_Document');	
	$documentModel = MooCore::getInstance()->getModel('Document_Document');
	$no_id = isset($no_list_id) ? $no_list_id : false;
?>
<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooDocument"], function($,mooDocument) {
    	mooDocument.initOnListing();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooDocument'), 'object' => array('$', 'mooDocument'))); ?>
	mooDocument.initOnListing();
<?php $this->Html->scriptEnd(); ?> 
<?php endif; ?>

<?php if (count($documents)): ?>
	<?php if ($no_id):?>
		<ul id="list-content" class="document-content-list">
	<?php endif;?>
	<?php foreach ($documents as $document):?>
		<?php $like = $documentModel->getLikeDocumentByUser($document['Document']['id'],$uid);?>
		<li class="full_content p_m_10 document_list">
			<div class="document_img">
				<a href="<?php echo $document['Document']['moo_href']?>">
					<img alt="" src="<?php echo $helper->getImage($document);?>">					
					<?php if ($document['Document']['feature']):?>
						<span class="page_feature"><img alt="" src="<?php echo FULL_BASE_URL . $this->request->webroot . 'document/img/document_featured.png';?>"></span>
					<?php endif;?>			
				</a>				
			</div>
			<?php if ($helper->canEdit($document,MooCore::getInstance()->getViewer())):?>
			<div class="list_option">
				<div class="dropdown">
					<button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
						<i class="material-icons">more_vert</i>
					</button>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">					
							<li><a href="<?php echo $this->request->base?>/documents/create/<?php echo $document['Document']['id']?>" title="<?php echo __d('document','Edit Document')?>"><?php echo __d('document','Edit Document')?></a></li>
							<li><a href="javascript:void(0)"  data-id="<?php echo $document['Document']['id'];?>" class="deleteDocument" ><?php echo __d('document','Delete Document')?></a></li>					
					</ul>
				</div>
			</div>
			<?php endif; ?>
			<div class="document-info">
				<a href="<?php echo $document['Document']['moo_href']; ?>" class="title"><?php echo $document['Document']['moo_title'];?></a>				
				<div class="extra_info">
					<?php echo __d( 'document','Posted by')?> <?php echo $this->Moo->getName($document['User'], false)?>
					<?php echo $this->Moo->getTime( $document['Document']['created'], Configure::read('core.date_format'), $utz )?> &nbsp;
					<?php
						switch($document['Document']['privacy']){
							case 1:
								$icon = '<i class="material-icons">public</i>';
								$tooltip = __d('document','Shared with: Everyone');
								break;
							case 2:
								$icon = '<i class="material-icons">people</i>';
								$tooltip = __d('document','Shared with: Friends Only');
								break;
							case 3:
								$icon = '<i class="material-icons">lock</i>';
								$tooltip = __d('document','Shared with: Only Me');
								break;
						}
					?>
					<a class="tip" href="javascript:void(0);" original-title="<?php echo  $tooltip ?>"> <?php echo $icon?></a>
				</div>
				<div class="document-description-truncate">
					<div>
					<?php 									
						echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $document['Document']['description'])), 200, array('eclipse' => '')), Configure::read('Document.document_hashtag_enabled'));						
					?>
					</div>
					<div class="like-section">
						<div class="like-action">
							<a href="<?php echo $document['Document']['moo_href']; ?>/#comments">
								<i class="material-icons">comment</i>&nbsp;<span id="comment_count"><?php echo $document['Document']['comment_count']?></span>
							</a>
							<a href="<?php echo $document['Document']['moo_href']; ?>" class="<?php if ($like && $like['Like']['thumb_up']): ?>active<?php endif; ?>">
								<i class="material-icons">thumb_up</i>
							</a>
							<a href="<?php echo $this->request->base?>/likes/ajax_show/Document_Document/<?php echo $document['Document']['id']?>" data-target="#themeModal" data-toggle="modal" class="" title="<?php echo __d('document','People Who Liked This')?>">
								<span id="like_count"><?php echo $document['Document']['like_count']?></span>
							</a>
							<?php if(empty($hide_dislike)): ?>
								<a href="<?php echo $document['Document']['moo_href']; ?>" class="<?php if ($like && !$like['Like']['thumb_up']): ?>active<?php endif; ?>">
									<i class="material-icons">thumb_down</i>
								</a>
								<a href="<?php echo $this->request->base?>/likes/ajax_show/Document_Document/<?php echo $document['Document']['id']?>/1" data-target="#themeModal" data-toggle="modal" class="" title="<?php echo __d('document','People Who Disliked This')?>">
									<span id="dislike_count"><?php echo $document['Document']['dislike_count']?></span>
								</a>
							<?php endif;?>
							<a href="<?php echo $document['Document']['moo_href']; ?>">
								<i class="material-icons">cloud_download</i>&nbsp;<span><?php echo $document['Document']['download_count']?></span>
							</a>
							<a href="<?php echo $document['Document']['moo_href']; ?>">
								<i class="material-icons">remove_red_eye</i>&nbsp;<span><?php echo $document['Document']['view_count']?></span>
							</a>
							<a href="<?php echo $document['Document']['moo_href']; ?>">
								<i class="material-icons">share</i>&nbsp;<span><?php echo $document['Document']['share_count']?></span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</li>	
	<?php endforeach;?>
	<?php if (isset($is_view_more) && $is_view_more): ?>
		<?php $this->Html->viewMore($url_more) ?>
	<?php endif; ?>
	<?php if ($no_id):?>
		</ul>
	<?php endif;?>
<?php else: ?>
	<li class="clear text-center"><?php echo __d('document','No more results found');?></li>
<?php endif;?>