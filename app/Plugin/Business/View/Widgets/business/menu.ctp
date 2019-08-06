<div class="box2">
    <div class="box_content">
        <ul class="list2 menu-list">
            <li <?php if(empty($task)):?>class="current"<?php endif;?>>
                <a href="<?php echo $this->request->base;?>/businesses"><?php echo __d('business', 'All Businesses');?></a>
            </li>
            <?php if($cuser != null):?>
            <li <?php if(!empty($task) && $task == 'my'):?>class="current"<?php endif;?>>
                <a href="<?php echo $this->request->base;?>/businesses/my"><?php echo __d('business', 'My Businesses');?></a>
            </li>
            <li <?php if(!empty($task) && $task == 'my_reviews'):?>class="current"<?php endif;?>>
                <a href="<?php echo $this->request->base;?>/businesses/my_reviews"><?php echo __d('business', 'My Reviews');?></a>
            </li>
            <li <?php if(!empty($task) && $task == 'my_following'):?>class="current"<?php endif;?>>
                <a href="<?php echo $this->request->base;?>/businesses/my_following"><?php echo __d('business', 'My Following Businesses');?></a>
            </li>
            <li <?php if(!empty($task) && $task == 'my_favourites'):?>class="current"<?php endif;?>>
                <a href="<?php echo $this->request->base;?>/businesses/my_favourites"><?php echo __d('business', 'My Favorite Businesses');?></a>
            </li>
            <?php endif;?>
        </ul>
    </div>
</div>