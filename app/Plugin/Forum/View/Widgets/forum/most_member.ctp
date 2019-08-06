<?php if (!empty($members)) :?>
    <div class="box2 suggestion_block forum-active-members-block">
        <?php if(empty($title)) $title = __d('forum', 'Most active members'); ?>
        <h3><?php echo __( $title)?></h3>
        <div class="box_content">
            <ul class="list6">
            <?php foreach ($members as $member): ?>
                <li><?php echo  $this->Moo->getItemPhoto(array('User' => $member['User']), array( 'prefix' => '100_square')) ?>
                    <div class="people_info">
                        <div>
                            <?php echo  $this->Moo->getName($member['User']) ?>
                        </div>
                        <div class="member-forum-info">
                            <span><?php echo __d('forum', 'Topics') .': '.$member[0]['topic'];?></span>
                            <span><?php echo __d('forum', 'Replies') .': '.$member[0]['reply'];?></span>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
            </ul>
            
        </div>
    </div>
<?php endif;?>