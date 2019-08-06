<form id="followerForm">
    <div class="mo_breadcrumb">
        <h1><?php echo __d('business', 'Followers');?></h1>
        <div class="follower_search">
            <?php if($permission_can_ban || ($cuser != null && $cuser['Role']['is_admin'])):?>
            <?php echo $this->Form->select('follower_type', array(
                0 => __d('business', 'All'),
                1 => __d('business', 'Banned members'),
                2 => __d('business', 'Followed members')
            ), array(
                'empty' => false
            ));?>
            <?php endif;?>
            <?php echo $this->Form->input('keyword',array('label'=>false,"placeholder" => __d('business', 'Keyword')))?>
            <a class="button" id="btn_search_follower" href="javascript:void(0)">
                <?php echo __d('business', 'Search');?>
            </a>
        </div>
        <div class="clear"></div>
    </div>
</form>
<div id="follower-content"></div>