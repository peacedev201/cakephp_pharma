<?php if($users != null):?>
<div class="box2">
    <h3>
        <?php echo $business['Business']['checkin_count'];?> 
        <?php echo $business['Business']['checkin_count'] > 1 ? __d('business', 'Check-Ins') : __d('business', 'Check-In');?>
    </h3>
    <div class="box_content box_bu_check_in">
        <ul class="list_block">
            <?php foreach($users as $user):
                $user = $user['User'];
            ?>
            <li>
                <?php
                    echo $this->Moo->getItemPhoto(array(
                        'User' => $user
                    ), array( 
                        'prefix' => '100_square'
                    ), array(
                        'class' => 'img_wrapper2 user_avatar_large'
                    ));
                ?>
            </li>
            <?php endforeach;?>
        </ul>
        <?php if($users != null):?>
        <div class="clear"></div>
        <div>
            <a href="<?php echo $business['Business']['moo_hrefcheckin'];?> ">
                <?php echo __d('business', 'View all');?>            
            </a>
        </div>
        <?php endif;?>
    </div>
</div>
<?php endif;?>