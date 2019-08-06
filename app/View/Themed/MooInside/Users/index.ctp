<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooUser"], function($,mooUser) {
        mooUser.initOnUserIndex();
        <?php if ( !empty( $values ) || !empty($online_filter) ): ?>
        $('#searchPeople').trigger('click');
        <?php endif; ?>
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooUser'), 'object' => array('$', 'mooUser'))); ?>
mooUser.initOnUserIndex();
<?php if ( !empty( $values ) || !empty($online_filter) ): ?>
$('#searchPeople').trigger('click');
<?php endif; ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php $this->setNotEmpty('north');?>
<?php $this->start('north'); ?>
<div class="headline video-color">
    <h1 class="header_mobile_item"><?php echo __('People') ?></h1>
    <ul id="browse">
        <li class="current home">
            <span href="<?php echo $this->request->base?>/blogs/"><i class="material-icons">home</i></span>
        </li>
        
    </ul>
    <div class="clear"></div>
</div>
<?php $this->end(); ?>


<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
	


	<?php echo $this->element('user/search_form'); ?>
<?php $this->end(); ?>
<a class="menu_mobile_left video-color" href="#" onclick="$('.menu_left').toggle()"><?php echo __('Everyone') ?></a>
<ul class="menu_left video-color" id="browse">
        <li class="current" id="everyone"><a data-url="<?php echo $this->request->base?>/users/ajax_browse/all" href="<?php echo $this->request->base?>/users"><?php echo __('Everyone')?></a></li>
        <?php if (!empty($cuser)): ?>
        <li><a data-url="<?php echo $this->request->base?>/users/ajax_browse/friends" href="#"><?php echo __('My Friends')?></a></li>
        <?php endif; ?>
</ul>
    <div class="bar-content">
        <div class="content_center full_content p_m_10">
        
            
            
            <ul class="users_list" id="list-content">
                    <?php 
                    if ( !empty( $values ) || !empty($online_filter) )
                            echo __('Loading...');
                    else
                            echo $this->element( 'lists/users_list', array( 'more_url' => '/users/ajax_browse/all/page:2' ) );
                    ?>
            </ul>
            <div class="clear"></div>
        </div>
    </div>

