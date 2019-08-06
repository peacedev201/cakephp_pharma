<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
<div class="box2 filter_block">
    <h3 class="visible-xs visible-sm"><?php echo __d('quiz', 'Browse'); ?></h3>
    <div class="box_content">
        <?php echo $this->element('sidebar/menu'); ?>
        <?php echo $this->element('lists/categories_list'); ?>
        <?php echo $this->element('sidebar/search'); ?>
    </div>
</div>
<?php $this->end(); ?>

<div class="bar-content">  
    <div class="content_center">
        <div id="list-content">
            <?php 
            if (!empty($cat_id)){
                echo $this->element('lists/quizzes_list', array('more_url' => '/quizzes/browse/category/' . $cat_id . '/page:2'));
            } else {
                echo $this->element('lists/quizzes_list', array('more_url' => '/quizzes/browse/all/page:2'));
            }
            ?>
        </div>
    </div>
</div>
