<?php $this->setCurrentStyle(7) ?>
<?php
echo $this->Html->css(array('jquery.mp'), null, array('inline' => false));
echo $this->Html->script(array('jquery.mp.min'), array('inline' => false)); 
?>
<?php $this->setNotEmpty('north');?>
<?php $this->start('north'); ?>
<div class="headline blog-color">
    <h1 class="header_mobile_item"><?php echo __('Blogs') ?></h1>
    <?php if (!empty($uid)): ?>
    <a href="<?php echo $this->request->base?>/blogs/create" class="button button-action topButton button-mobi-top"><?php echo __('Write New Entry')?></a>
    <?php endif; ?>
    <ul id="browse">
        <li class="current home">
            <span ><i class="material-icons">home</i></span>
        </li>
        <?php echo $this->element('lists/categories_list')?>
    </ul>
    
    <div class="clear"></div>
</div>
<?php $this->end(); ?>
<?php $this->setNotEmpty('east');?>
<?php $this->start('east'); ?>
 <!-- <div id="filters" style="margin-top:5px">
    <?php if(!Configure::read('core.guest_search') && empty($uid)): ?>
    <?php else: ?>
        <?php echo $this->Form->text( 'keyword', array( 'placeholder' => __('Enter keyword to search'), 'rel' => 'blogs', 'class' => 'json-view') );?>
    <?php endif; ?>
</div> -->
<?php $this->end(); ?>

   
<?php echo $this->element('sidebar/menu') ?>
       
   
<div class="bar-content">
    <div class="content_center">
	
        
        <ul id="list-content">
	        <?php echo $this->element( 'lists/blogs_list', array( 'more_url' => '/blogs/browse/all/page:2' ) ); ?>
        </ul>
    </div>
</div>
