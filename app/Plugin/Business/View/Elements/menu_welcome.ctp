<li>
    <a id="my-businesses" data-url="<?php echo $this->request->base."/businesses/my/".MooCore::getInstance()->getViewer(true);?>" rel="home-content" href="<?php echo $this->request->base?>/home/index/tab:my-businesses">
        <i class="material-icons">business</i>
        <?php echo __d('business','My Businesses')?> 
        <span class="badge_counter">
            <?php echo $count;?>
        </span>
    </a>
</li>