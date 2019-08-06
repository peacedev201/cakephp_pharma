<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (Configure::read('GifComment.gif_comment_enabled')) :
?>
<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooGifComment"], function($,mooGifComment) {
        mooGifComment.initLoadGif();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooGifComment'), 'object' => array('$', 'mooGifComment'))); ?>
	mooGifComment.initLoadGif();

<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
    <div class="gif_holder <?php if ($this->request->is('androidApp') || $this->request->is('iosApp')) : ?>gif_mooapp<?php endif; ?>" data-class="<?php echo $typeId.'_'.$gId ?>">
        <div data-id="<?php echo $gId ?>" data-type="<?php echo $typeId ?>" class="gif-icon" id="gif_icon_<?php echo $typeId ?>_<?php echo $gId; ?>" ><i class="material-icons">gif</i></div>
        <div class="gif_form_content_hoder">
            <div class="gif_form">
                <input  type="text" id="gif_auto_search_<?php echo $typeId ?>_<?php echo $gId; ?>" placeholder="<?php echo __d('gif_comment','Search GIFs across apps...') ?>" >
            </div>
            <div class="gif_content initSlimScroll" id="gif_content_<?php echo $typeId ?>_<?php echo $gId; ?>" >
                <img class="gif_content_loadding" src="<?php echo $this->request->webroot?>img/indicator.gif">
            </div>
        </div>
    </div> 

<?php endif; ?>
