<?php $this->setCurrentStyle(7) ?>
<?php $this->setNotEmpty('north');?>
<?php $this->start('north'); ?>
<?php $upload_video = Configure::read('UploadVideo.uploadvideo_enabled');?>
<div class="headline video-color">
    <h1 class="header_mobile_item"><?php echo __('Videos') ?></h1>
           <?php if (!empty($uid)): ?>	
        <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "videos",
                                            "action" => "create",
                                            "plugin" => 'video',
                                            
                                        )),
             'title' => __( 'Share New Video'),
             'innerHtml'=> __( 'Share New Video'),
          	'data-backdrop' => 'static',
          'class' => 'button button-action topButton button-mobi-top'
     ));
 ?>
        <?php if($upload_video): ?>
        <!-- check enabled upload video from pc -->
        <?php
            $this->MooPopup->tag(array(
                   'href'=>$this->Html->url(array("controller" => "upload_videos",
                                                  "action" => "ajax_upload",
                                                  "plugin" => 'upload_video',

                                              )),
                   'title' => __( 'Upload Video'),
                   'innerHtml'=> __( 'Upload Video'),
                	'data-backdrop' => 'static',
                    'data-keyboard' => 'false',
                'class' => 'button button-action topButton button-mobi-top'
           ));
       ?>
        <?php endif; ?>
        
        <?php endif; ?>
    <ul id="browse">
        <li class="current home">
            <span href="<?php echo $this->request->base?>/blogs/"><i class="material-icons">home</i></span>
        </li>
        <?php echo $this->element( 'lists/categories_list'); ?>
    </ul>
    
    <div class="clear"></div>
</div>
<?php $this->end(); ?>

<?php $this->setNotEmpty('east');?>
<?php $this->start('east'); ?>
<?php //echo $this->element('sidebar/search'); ?>
<?php //echo $this->element( 'core/tags'); ?>	
        
<?php $this->end(); ?>

<?php echo $this->element( 'sidebar/menu'); ?>


<div class="bar-content">
    <div class="content_center">

    <?php echo $this->element( 'videos/popular'); ?>
    <ul class="video-content-list" id="list-content">
        <?php 
        if ( !empty( $this->request->named['category_id'] )  || !empty($cat_id) ){
            
            if (empty($cat_id)){
                $cat_id = $this->request->named['category_id'];
            }
            
            echo $this->element( 'lists/videos_list', array( 'more_url' => '/videos/browse/category/' . $cat_id . '/page:2' ) );
        }
        else{
            echo $this->element( 'lists/videos_list', array( 'more_url' => '/videos/browse/all/page:2' ) );
        }
        ?>		
    </ul>
    </div>
</div>