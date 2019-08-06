<?php

//echo $this->Html->css(array('Spotlight.js_carousel'), null, array('inline' => false));
//echo $this->Html->css(array('Spotlight.main'), null, array('inline' => false));

if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;
$spotUsers = $topSpotlight['users'];
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooSpotlight'), 'object' => array('$', 'mooSpotlight'))); ?>
mooSpotlight.initCarousel({
'id': '<?php echo $content_id?>'
});
<?php $this->Html->scriptEnd();?>

<div class="box2 box_top_spotlight">
    <?php if($title_enable): ?>
    <h3><?php echo $title; ?></h3>
    <?php endif; ?>

        <div class="<?php if((isset($canJoin) && $canJoin == 0) || !$uid):?>has_btn_join <?php endif; ?>">
	<?php if(isset($canJoin) && $canJoin == 0 && $uid):?>
            <div class="btt_add_spotlight">

                <a id="" class="add-me-here-btn" style="" data-backdrop="true" data-dismiss="modal" data-toggle="modal" data-target="#themeModal" href="<?php echo $this->base?>/spotlights/register_form">
                    <i class="material-icons">add</i>
                    <?php echo __d('spotlight','Put me here!')?>
                </a>

            </div>
	 <?php endif; ?>

            <?php if(!$uid):?>
                <div class="btt_add_spotlight">
                    <a class="add-me-here-btn" href="<?php echo $this->request->base . '/users/member_login' ?>">
                        <i class="fa fa-plus"></i>
                        <?php echo __d('spotlight','Put me here!')?>
                    </a>
                </div>
            <?php endif;?>

            <?php if ( !empty( $spotUsers ) ): ?>
            <div class="slide_content">

                <div id="jsCarousel<?php echo $content_id;?>" class="list_block jcarousel">
                    <ul>
            <?php
                //$helper = MooCore::getInstance()->getHelper('Spotlight_Spotlight');
                foreach ($spotUsers as $user): ?>
                        <li class='spotlight_multiple'>
                        <?php echo $this->Moo->getItemPhoto(array('User' => $user['User']), array( 'prefix' => '200_square') ,array('class' => 'user_avatar_small'));?>
                        <?php if ( $user['User']['is_online'] == 1 ): ?>
                            <span class="online-stt" style="position: unset;"></span>
                        <?php endif; ?>
                        </li>
            <?php endforeach; ?>
                    </ul>
                </div>
                <a href="#" class="jcarousel-control-prev" id="jcarousel-control-prev<?php echo $content_id;?>">&lsaquo;</a>
                <a href="#" class="jcarousel-control-next" id="jcarousel-control-next<?php echo $content_id;?>">&rsaquo;</a>
                <div class='clear'></div>

            </div>
            <?php endif; ?>
        </div>
</div>
