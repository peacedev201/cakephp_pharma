<div class="box2 filter_block">
    <h3 class="visible-xs visible-sm"><?php echo __d('gift', 'Browse') ?></h3>
    <div class="box_content">
        <?php if($loadAjax):?>
            <ul class="list2 menu-list menu-gift" id="browse">
                <li <?php if(empty($type)):?>class="current"<?php endif;?> id="browse_all">
                    <a data-url="<?php echo $this->request->base . $url_gift ?>/ajax_browse/all" href="<?php echo $this->request->base . $url_gift ?>">
                        <?php echo __d('gift', 'All Gifts') ?>
                    </a>
                </li>
                <?php if(MooCore::getInstance()->getViewer(true) > 0):?>
                <li id="my-gift" <?php if(!empty($type) && $type == 'my'):?>class="current"<?php endif;?>>
                    <a data-url="<?php echo $this->request->base . $url_gift ?>/ajax_browse/my" href="#">
                        <?php echo __d('gift', 'My Gifts') ?>
                    </a>
                </li>
                <?php endif;?>
                <li class="separate"></li>
                <li class="cat-header"><?php echo __d('gift', 'Categories') ?></li>
                <?php if(!empty($aCategories)):?>
                    <?php foreach ($aCategories as $aCategory): ?>
                        <li <?php if(!empty($type) && $type == 'cat' && $param == $aCategory['GiftCategory']['id']):?>class="current"<?php endif;?>>
                            <a data-url="<?php echo $this->request->base . $url_gift ?>/ajax_browse/cat/<?php echo $aCategory['GiftCategory']['id'] ?>" href="#">
                                <?php echo h($aCategory['GiftCategory']['name']); ?>
                                <span class="badge_counter">
                                    <?php echo $aCategory['GiftCategory']['item_count'] ?>
                                </span>
                            </a>
                        </li>
                    <?php endforeach ?>
                <?php endif;?>
            </ul>
            <div id="filters" style="margin-top:5px">
                <?php echo $this->Form->text('keyword', array('placeholder' => __d('gift', 'Enter keyword to search'), 'rel' => 'gift/gift')); ?>
            </div>
        <?php else:?>
            <ul class="list2 menu-list">
                <li>
                    <a href="<?php echo $this->request->base . $url_gift ?>">
                        <?php echo __d('gift', 'All Gifts') ?>
                    </a>
                </li>
                <?php if(MooCore::getInstance()->getViewer(true) > 0):?>
                <li>
                    <a href="<?php echo $this->request->base . $url_gift?>/index/my">
                        <?php echo __d('gift', 'My Gifts') ?>
                    </a>
                </li>
                <?php endif;?>
                <li class="separate"></li>
                <li class="cat-header"><?php echo __d('gift', 'Categories') ?></li>
                <?php if(!empty($aCategories)):?>
                    <?php foreach ($aCategories as $aCategory): ?>
                        <li <?php if(!empty($type) && $type == 'cat' && $param == $aCategory['GiftCategory']['id']):?>class="current"<?php endif;?>>
                            <a href="<?php echo $this->request->base . $url_gift?>/index/cat/<?php echo $aCategory['GiftCategory']['id'] ?>">
                                <?php echo h($aCategory['GiftCategory']['name']); ?>
                                <span class="badge_counter">
                                    <?php echo $aCategory['GiftCategory']['item_count']; ?>
                                </span>
                            </a>
                        </li>
                    <?php endforeach ?>
                <?php endif;?>
            </ul>
        <?php endif;?>
    </div>
</div>
