<?php $this->setCurrentStyle(7) ?>
<?php $this->setNotEmpty('north');?>
<?php $this->start('north'); ?>
<div class="headline topic-color">
    <h1 class="header_mobile_item"><?php echo __('News') ?></h1>
    <?php 
            if (!empty($uid)):
            ?>
                <?php
                echo $this->Html->link(__('Create New Topic'), array(
                    'plugin' => 'Topic',
                    'controller' => 'topics',
                    'action' => 'create'
                ), array(
                    'class' => 'button button-action topButton button-mobi-top'
                ));
                ?>

            <?php
            endif;
            ?>
    <ul id="browse">
        <li class="current home">
            <span href="<?php echo $this->request->base?>/topics/"><i class="material-icons">home</i></span>
        </li>
        <?php echo $this->element('lists/categories_list'); ?> 
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
    <?php echo $this->element('topics/popular'); ?>
	<ul class="topic-content-list" id="list-content">
		<?php 
		if ( !empty( $this->request->named['category_id'] )  || !empty($cat_id)){
                    if (empty($cat_id)){
                        $cat_id = $this->request->named['category_id'];
                    }
                    echo $this->element( 'lists/topics_list', array( 'more_url' => '/topics/browse/category/' . $cat_id . '/page:2' ) );
                }
		else {
                    echo $this->element( 'lists/topics_list', array( 'more_url' => '/topics/browse/all/page:2' ) );
                }
		?>
	</ul>	
    </div>
</div>
