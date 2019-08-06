<?php if($user_id == $uid):?>
<ul class="mini_tab">
    <?php if($this->Business->hasBusinesses($user_id)):?>
    <li id="tab-business">
        <a href="#" data-wrapper="profile-content" data-url="<?php echo $this->request->base;?>/businesses/my/<?php echo $user_id?>">
            <?php echo __d('business', 'My Businesses');?>	
        </a>
    </li>
    <?php endif;?>
    <?php if($user_id == $uid):?>
    <li id="tab-business-follow">
        <a href="#" data-wrapper="profile-content" data-url="<?php echo $this->request->base;?>/business_follow/myfollow">
            <?php echo __d('business', 'Following');?>				
        </a>
    </li>
    <li id="tab-business-favourite">
        <a href="#" data-wrapper="profile-content" data-url="<?php echo $this->request->base;?>/businesses/favourites">
            <?php echo __d('business', 'Favourites');?>				
        </a>
    </li>
    <?php endif;?>
</ul>
<?php endif;?>