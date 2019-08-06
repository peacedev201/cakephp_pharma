<?php
$photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
?>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooPhoto', 'hideshare'),'object'=>array('$', 'mooPhoto'))); ?>
$(".sharethis").hideshare({media: '<?php echo $photoHelper->getAlbumCover($album['Album']['cover'], array('prefix' => '300_square'))?>', linkedin: false});
mooPhoto.initOnViewAlbum();
<?php $this->Html->scriptEnd(); ?>
<?php $this->setNotEmpty('east');?>
<?php $this->start('east'); ?>
	

    <?php if (!empty($tags)): ?>
	<div class="box2">
		<h3><?php echo __( 'Tags')?></h3>
		<div class="box_content">
			<?php echo $this->element( 'blocks/tags_item_block' ); ?>
		</div>
	</div>
	<?php endif; ?>        
<?php $this->end(); ?>
<div class="bar-content full_content ">
        <div class="content_center">
            <div class=" post_body album_view_detail">
				<?php if ( empty( $album['Album']['type'] ) ): ?>
                    <ul class="list7 header-list header-button-list">
                        <?php if ( $uid == $album['User']['id'] ): ?>
                        <li class="btn-album">
                            <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "photos",
                                            "action" => "ajax_upload",
                                            "plugin" => 'photo',
                                            'Photo_Album',
                                            $album['Album']['id'],
                                        )),
             'title' => h($album['Album']['moo_title']),
             'innerHtml'=> __( 'Upload Photos'),
          'class' => 'button button-action topButton button-mobi-top',
          'data-backdrop' => 'static'
     ));
 ?>
                        
                        </li>
                        <?php endif; ?>
                         
                        <li class="list_option">
                            <div class="dropdown">
                                <button class="btn btn-default" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                                    <span><?php echo __('Edit') ?></span> <i class="material-icons vmiddle">expand_more</i>
                                </button>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                    <?php if ( $uid == $album['User']['id'] || ( !empty($cuser) && $cuser['Role']['is_admin'] ) ): ?>
                                    <li>
                                        <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "albums",
                                            "action" => "create",
                                            "plugin" => 'photo',
                                            $album['Album']['id']
                                           
                                        )),
             'title' => __( 'Edit Album'),
             'innerHtml'=> __( 'Edit Album'),
          "data-backdrop" => "static"
     ));
 ?>
                                          </li>
                                    <li><a href="javascript:void(0);" class="deleteAlbum" data-id="<?php echo $album['Album']['id']?>"><?php echo __( 'Delete Album')?></a></li>
                                    <li><a href="<?php echo $this->request->base?>/albums/edit/<?php echo $album['Album']['id']?>"><?php echo __( 'Edit Photos')?></a></li>
                                     <?php endif; ?>
                                    <li>
                                        <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "reports",
                                            "action" => "ajax_create",
                                            "plugin" => false,
                                            'photo_album',
                                            $album['Album']['id'],
                                        )),
             'title' =>  __( 'Report Album'),
             'innerHtml'=>  __( 'Report Album'),
     ));
 ?>
                                      </li>
                                      
                                      <?php if ($album['Album']['privacy'] != PRIVACY_ME): ?>
                                      <?php endif; ?>
                                </ul>
                            </div>
                        </li>
                       
                    </ul>
                     
                <?php endif; ?>
                <div class="mo_breadcrumb">
                <h1><?php echo h($album['Album']['title'])?></h1>
				
                    


                 </div>
				
                <div class="album-detail-info">
                    <?php echo __( 'Posted by %s', $this->Moo->getName($album['User']))?> <?php echo __( 'in')?> <a href="<?php echo $this->request->base?>/photos/index/<?php echo $album['Album']['category_id']?>/<?php echo seoUrl($album['Category']['name'])?>"><?php echo $album['Category']['name']?></a> <?php echo $this->Moo->getTime( $album['Album']['created'], Configure::read('core.date_format'), $utz )?>
                    &nbsp;&middot;&nbsp;<?php if ($album['Album']['privacy'] == PRIVACY_PUBLIC): ?>
                        <?php echo __('Public') ?>
                        <?php elseif ($album['Album']['privacy'] == PRIVACY_PRIVATE): ?>
                        <?php echo __('Private') ?>
                        <?php elseif ($album['Album']['privacy'] == PRIVACY_FRIENDS): ?>
                        <?php echo __('Friend') ?>
                        <?php endif; ?>
                </div>
			<div class="clear"></div>
<?php $this->Html->rating($album['Album']['id'], 'albums','Photo');  ?>
				<div class="">
                <div class="comment_message"><?php echo $this->Moo->formatText( $album['Album']['description'] )?></div>
				</div>
            <?php echo $this->element( 'lists/photos_list', array( 'type' => 'Photo_Album' ) ); ?>
            
			  <?php echo $this->element( 'likes', array('shareUrl' => $this->Html->url(array(
                    'plugin' => false,
                    'controller' => 'share',
                    'action' => 'ajax_share',
                    'Photo_Album',
                    'id' => $album['Album']['id'],
                    'type' => 'album_item_detail'
                ), true), 'item' => $album['Album'], 'type' => 'Photo_Album' ) ); ?>
			<div class="album-detail-comment">
				<?php echo $this->renderComment();?>
			</div>
        </div>
        </div>
		
</div>


