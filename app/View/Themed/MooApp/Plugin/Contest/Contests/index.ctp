<?php
echo $this->Html->css(array('jquery.mp'), null, array('inline' => false));
echo $this->Html->script(array('jquery.mp.min'), array('inline' => false));
$contestModel = MooCore::getInstance()->getModel('Contest.Contest');
$categories = $contestModel->getCategories(array('user_id' => $uid));
?>
<div class="box2 box_app">
    <!--
    <ul class="nav nav-tabs contest_countdown_tab" role="tablist">
        <?php if(!in_array($type, array('my','join'))): ?>
        <li role="presentation" <?php if ($type == 'active'):?>class="active"<?php endif;?>><a href="<?php echo $this->base?>/contest/contests?app_no_tab=1" ><?php echo __d('contest', 'Ongoing'); ?></a></li>
        <li role="presentation" <?php if ($type == 'upcoming'):?>class="active"<?php endif;?>><a href="<?php echo $this->base?>/contest/contests/index/type:upcoming?app_no_tab=1" ><?php echo __d('contest', 'Upcoming'); ?></a></li>
        <li role="presentation" <?php if ($type == 'close'):?>class="active"<?php endif;?>><a href="<?php echo $this->base?>/contest/contests/index/type:close?app_no_tab=1"><?php echo __d('contest', 'Closed') ?></a></li>
        <?php else: ?>
            <li style="width:50%" role="presentation" <?php if ($type == 'my'):?>class="active"<?php endif;?>><a href="<?php echo $this->base?>/contest/contests/index/type:my?app_no_tab=1" ><?php echo __d('contest', 'Posted Contests'); ?></a></li>
            <li style="width:50%" role="presentation" <?php if ($type == 'join'):?>class="active"<?php endif;?>><a href="<?php echo $this->base?>/contest/contests/index/type:join?app_no_tab=1" ><?php echo __d('contest', 'Joined Contests'); ?></a></li>
        <?php endif; ?>
    </ul>
    -->
</ul>
<div class="bar-content">
    <div class="content_center">
        <div class="mo_breadcrumb">
            
        <?php if ($type == "all"): ?>
            <div class="dropdown cat_select_dropdown">
                <a href="#" data-toggle="dropdown"><span class="text"><?php echo __d('contest', 'All Categories') ?></span> <i class="material-icons">arrow_drop_down</i></a>
                <ul class="dropdown-menu" id="browse">
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <a class="json-view" href="<?php echo $_SERVER['REQUEST_URI']?>"  data-url="<?php echo $this->base ?>/contest/contests/browse/category/<?php echo $category['Category']['id']; ?>" ><?php echo $category['Category']['name'] ?><span class="badge_counter"><?php echo $category['Category']['item_count'] ?></span></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php if ($uid): ?>
                <a href="<?php echo $this->request->base ?>/contests/create" class="topButton mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1"><?php echo __d('contest', 'Create New Contest'); ?></a>
            <?php endif; ?>
        </div>
        <?php if ($type == "all"): ?>
        <?php if (Configure::read('core.guest_search') || empty($uid)): ?>
            <div id="filters" style="margin-top:5px">
                <input name="data[keyword]" placeholder="<?php echo __d('contest', 'Enter keyword to search'); ?>" rel="contests" class="json-view" type="text" id="keyword">
            </div>
        <?php endif; ?>
        <?php endif; ?>
        <ul id="list-content" class="contest-content-list">
            <?php if (count($contests)): ?>
                <?php echo $this->element('lists/contests', array('is_view_more' => $is_view_more, 'url_more' => $url_more, 'contests' => $contests), array('plugin' => 'Contest')); ?>
            <?php else: ?>		
                <li class="clear text-center"><?php echo __d('contest', 'No more results found'); ?></li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<script>
function doRefesh()
{
	location.reload();
}
</script>