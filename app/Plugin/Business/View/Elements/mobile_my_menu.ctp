<?php if(!$is_app):?>
<div class="list_option">
    <div class="dropdown">
        <button id="dropdown-edit" data-target="#" data-toggle="dropdown" >
            <i class="material-icons">more_vert</i>
        </button>
        <ul role="menu" class="dropdown-menu" aria-labelledby="dropdown-edit">
            <?php if($cuser != null):?>
            <li <?php if(!empty($task) && $task == 'my'):?>class="current"<?php endif;?>>
                <a href="<?php echo $this->request->base;?>/businesses/my"><?php echo __d('business', 'My Businesses');?></a>
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
<?php endif;?>