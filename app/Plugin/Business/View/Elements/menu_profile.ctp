<li id="tab-poll">
    <a data-url="<?php echo $this->request->base."/businesses/my/".$profile_uid;?>" rel="profile-content" href="#">
        <i class="material-icons">business</i>
        <?php echo __d('business','Businesses')?>
        <span class="badge_counter">
            <?php echo $count?>
        </span>
    </a>
</li>
