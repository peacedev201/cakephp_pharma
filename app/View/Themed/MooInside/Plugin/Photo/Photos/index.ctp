<?php $this->setCurrentStyle(7) ?>
<?php $this->setNotEmpty('north');?>
<?php $this->start('north'); ?>
<div class="headline photo-color">
    <h1 class="header_mobile_item"><?php echo __('Photos') ?></h1>
    <?php if (!empty($uid)): ?>
    <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "albums",
                                            "action" => "create",
                                            "plugin" => 'photo',
                                            
                                        )),
             'title' => __( 'Create New Album'),
             'innerHtml'=> __( 'Create New Album'),
          'data-backdrop' => 'static',
          'class' => 'button button-action topButton button-mobi-top'
     ));
 ?>
    <?php endif; ?>
    <ul id="browse">
        <li class="current home">
            <span href="<?php echo $this->request->base?>/blogs/"><i class="material-icons">home</i></span>
        </li>
        <?php echo $this->element('lists/categories_list')?>
    </ul>
    
    <div class="clear"></div>
</div>
<?php $this->end(); ?>

<?php $this->setNotEmpty('east');?>
<?php $this->start('east'); ?>
	
<?php //echo $this->element('sidebar/search'); ?>
            
<?php $this->end(); ?>

<?php echo $this->element('sidebar/menu'); ?>

 <div class="bar-content">   
     <div class="content_center">	
	<ul class="albums photo-albums" id="album-list-content">
            <?php 
            if ( !empty( $this->request->named['category_id'] ) || !empty($cat_id) ){
                if (empty($cat_id)){
                    $cat_id = $this->request->named['category_id'];
                }
                
                echo $this->element( 'lists/albums_list', array( 'album_more_url' => '/albums/browse/category/' . $cat_id . '/page:2' ) );
            }
            else {
                echo $this->element( 'lists/albums_list', array( 'album_more_url' => '/albums/browse/all/page:2' ) );
            }
            ?>	
	</ul>
        <div class="clear"></div>
     </div>
 </div>