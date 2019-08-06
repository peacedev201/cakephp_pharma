<?php
	echo $this->Html->css(array('Document.slippry'), null, array('inline' => false));
	$helper = MooCore::getInstance()->getHelper('Document_Document');	
	$documentModel = MooCore::getInstance()->getModel('Document_Document');
?>

<?php
	$helper = MooCore::getInstance()->getHelper('Document_Document');	
	if (count($documents)) : 
?>
	<div class="box2 filter_block feature_block">
		<?php if (isset($title_enable) && $title_enable):?>       
        	<h1><?php echo $title ?></h1>
        <?php endif;?>
	    <div class="box_content">
	        <ul id="slippry">
	            <?php
	                foreach ($documents as $document):
	            ?>
	            <li class="full_content p_m_10">
	                <?php $like = $documentModel->getLikeDocumentByUser($document['Document']['id'],$uid);?>
	                <div class="document_img">
						<a href="<?php echo $document['Document']['moo_href']?>">
							<img alt="" src="<?php echo $helper->getImage($document);?>">							
							<?php if ($document['Document']['feature']):?>
								<span class="page_feature"><img alt="" src="<?php echo FULL_BASE_URL . $this->request->webroot . 'document/img/document_featured.png';?>"></span>
							<?php endif;?>			
						</a>				
					</div>					
					<div class="document-info">
						<a href="<?php echo $document['Document']['moo_href']; ?>" class="title"><?php echo $this->Text->truncate($document['Document']['moo_title'], 45, array('eclipse' => ''));?></a>				
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
								echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $document['Document']['description'])), 100, array('eclipse' => '')), Configure::read('Document.document_hashtag_enabled'));						
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
		
									<a href="<?php echo $document['Document']['moo_href']; ?>" class="<?php if ($like && !$like['Like']['thumb_up']): ?>active<?php endif; ?>">
										<i class="material-icons">thumb_down</i>
									</a>
									<a href="<?php echo $this->request->base?>/likes/ajax_show/Document_Document/<?php echo $document['Document']['id']?>/1" data-target="#themeModal" data-toggle="modal" class="" title="<?php echo __d('document','People Who Disliked This')?>">
										<span id="dislike_count"><?php echo $document['Document']['dislike_count']?></span>
									</a>
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
	        </ul>
	    </div>
	</div><?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooDocumentSlippry'),'object'=>array('$'))); ?>
        $("#slippry").slippry({
            auto: false,
            controls: false
        });
	<?php $this->Html->scriptEnd(); ?>
<?php endif;?>
