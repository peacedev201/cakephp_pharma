<?php $this->setCurrentStyle(8) ?>
<?php $this->setNotEmpty('north');?>
<?php $this->start('north'); ?>
<div class="headline group-color">
    <h1 class="header_mobile_item"><?php echo __('Groups') ?></h1>
    <?php if (!empty($uid)): ?>
    <a href="<?php echo $this->request->base?>/groups/create" class="button button-action topButton button-mobi-top"><?php echo __( 'Add Group')?></a>
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
   
	<ul class="list6" id="list-content">
            <?php 
            if ( !empty( $this->request->named['category_id'] )  || !empty($cat_id) ){

                if (empty($cat_id)){
                    $cat_id = $this->request->named['category_id'];
                }

                echo $this->element( 'lists/groups_list', array( 'more_url' => '/groups/browse/category/' . $cat_id . '/page:2' ) );
            }
            else{
                echo $this->element( 'lists/groups_list', array( 'more_url' => '/groups/browse/all/page:2' ) );
            }
            ?>
	</ul>
</div>
</div>
