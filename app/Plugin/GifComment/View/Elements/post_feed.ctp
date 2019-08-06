<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$showHide = false;
$userModel = MooCore::getInstance()->getModel('User');
$userModel->cacheQueries = false;
$user = $userModel->find('first', array('conditions' => array('User.id' => $_SESSION['Auth']['User']['id'])))['User'];
if (Configure::read('GifComment.gif_comment_enabled') && ($user['GIF'] == GIFT_EXTEND_FOREVER || ($user['GIF'] && !empty($user['GIF_valid'])  && date('Ymd') <= date('Ymd',strtotime($user['GIF_valid'])))))
    $showHide = true;

if (Configure::read('GifComment.gif_comment_enabled')) :
?>
<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooGifPostFeed"], function($,mooGifPostFeed) {
        mooGifPostFeed.initLoadGif();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooGifPostFeed'), 'object' => array('$', 'mooGifPostFeed'))); ?>
	mooGifPostFeed.initLoadGif();

<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

    <div class="gif_form_feed">
        <div id="gif_icon_form" data-popup="<?= $showHide ?>" <?php if ($this->request->is('androidApp') || $this->request->is('iosApp')) : ?>style="border-right: none"<?php endif; ?> ><i class="material-icons">gif</i></div>
        <div class="gif_form_content_hoder">
            <div class="gif_form">
                <input  type="text" id="gif_auto_search_form" placeholder="<?php echo __d('gif_comment','Search GIFs across apps...') ?>" >
            </div>
            <div class="gif_content initSlimScroll" id="gif_content_form_feed" >
                <img class="gif_content_loadding" src="<?php echo $this->request->webroot?>img/indicator.gif">
            </div>
        </div>
    </div> 

<?php endif; ?>
