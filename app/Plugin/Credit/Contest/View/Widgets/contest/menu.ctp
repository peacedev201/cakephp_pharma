<div class="box2 filter_block">
    <div class="box_content">
        <ul class="list2 menu-list" id="browse">
            <li <?php if ($type == 'active'):?>class="current"<?php endif;?>><a  class="json-view" href="<?php echo $this->base?>/contest/contests" data-url="<?php echo $this->base?>/contest/contests/browse/active"><?php echo __d('contest','Ongoing Contests');?></a></li>
            <li <?php if ($type=='upcoming' && !$params):?>class="current"<?php endif;?> ><a class="json-view" data-url="<?php echo $this->base?>/contest/contests/browse/upcoming" href="<?php echo $this->base?>/contest/contests/index/type:upcoming"><?php echo __d('contest','Upcoming Contests');?></a></li> 
            <li <?php if ($type == 'close'):?>class="current"<?php endif;?>><a  class="json-view" href="<?php echo $this->base?>/contest/contests/index/type:close" data-url="<?php echo $this->base?>/contest/contests/browse/close"><?php echo __d('contest','Closed Contests');?></a></li>
           
            <?php if (!empty($uid)): ?>
                <li <?php if ($type == 'my'): ?>class="current"<?php endif; ?>><a  class="json-view" href="<?php echo $this->base ?>/contest/contests/index/type:my" data-url="<?php echo $this->base ?>/contest/contests/browse/my"><?php echo __d('contest', 'My Contests'); ?></a>
                <li <?php if ($type == 'join'): ?>class="current"<?php endif; ?>><a  class="json-view" href="<?php echo $this->base ?>/contest/contests/index/type:join" data-url="<?php echo $this->base ?>/contest/contests/browse/join"><?php echo __d('contest', 'My Joined Contests'); ?></a>
            <?php endif; ?>                  
            <li class="separate"></li>
            <li class="cat-header <?php if (Configure::read('core.enable_category_toggle')) echo 'cat_toggle' ?>"><?php echo __d('contest', 'Categories') ?></li>
            <?php
            $categories = $this->requestAction(
                "contests/categories_list/"
            );
            ?>
            <?php foreach ($categories as $cat): ?>
                <?php if ($cat['Category']['header']): ?>
                    <li class="category_header"><?php echo $cat['Category']['name'] ?></li>

                    <?php foreach ($cat['children'] as $subcat): ?>

                        <li id="cat_<?php echo $subcat['Category']['id'] ?>" class="sub-cat <?php if (!empty($cat_id) && $cat_id == $subcat['Category']['id']) echo 'current'; ?>">
                            <a href="<?php echo $this->base ?>/contest/contests/index/category:<?php echo $subcat['Category']['id']; ?>" data-url="<?php echo $this->base ?>/contest/contests/browse/category/<?php echo $subcat['Category']['id']; ?>" <?php if (!empty($subcat['Category']['description'])): ?>class="tip" title="<?php echo nl2br($subcat['Category']['description']) ?>"<?php endif ?> ><?php echo $subcat['Category']['name'] ?>
                                <span class="badge_counter"><?php echo $subcat['Category']['item_count'] ?></span></a>
                        </li>

                    <?php endforeach; ?>
                <?php else: ?>

                    <li id="cat_<?php echo $cat['Category']['id'] ?>" <?php if (!empty($cat_id) && $cat_id == $cat['Category']['id']) echo 'class="current"'; ?>>
                        <a class="json-view" href="<?php echo $this->base ?>/contest/contests/index/category:<?php echo $cat['Category']['id']; ?>" data-url="<?php echo $this->base ?>/contest/contests/browse/category/<?php echo $cat['Category']['id']; ?>" <?php if (!empty($cat['Category']['description'])): ?>class="tip" title="<?php echo nl2br($cat['Category']['description']) ?>"<?php endif ?> ><?php echo $cat['Category']['name'] ?>
                            <span class="badge_counter"><?php echo $cat['Category']['item_count'] ?></span></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>

        </ul>    
        <?php if (Configure::read('core.guest_search') || empty($uid)): ?>
            <div id="filters" style="margin-top:5px">
                <input name="data[keyword]" placeholder="<?php echo __d('contest', 'Enter keyword to search'); ?>" rel="contests" class="json-view" type="text" id="keyword">
            </div>
        <?php endif; ?>
    </div>
</div>