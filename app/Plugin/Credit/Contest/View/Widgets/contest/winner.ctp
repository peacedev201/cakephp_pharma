<?php $helper = MooCore::getInstance()->getHelper('Contest_Contest'); ?>
<?php if($contest['Contest']['contest_status'] == 'closed'): ?>
<div class="box2">        
    <?php if (isset($title_enable) && $title_enable): ?>       
        <h3><?php echo $title ?></h3>
    <?php endif; ?>
    <div class="box_content">
        <?php if (!empty($win_entries)):
            $mUBlock = MooCore::getInstance()->getModel('UserBlock');
            $uid = MooCore::getInstance()->getViewer(true);
         ?>
            <?php foreach ($win_entries as $entry): ?>
                <div class="winner_block">
                    <div class="user-idx-item">
                        <a class="winner_owner" href="<?php echo $entry['User']['moo_href']; ?>">
                            <?php 
                                $is_block = $helper->areUserBlocks($uid, $entry['User']['id']);
                            ?>
                            <?php if(!$is_block): ?>
                                <?php echo $this->Moo->getImage(array('User' => $entry['User']), array( "data-item_id" => $entry['User']['id'], "data-item_type" => "user", "class" => 'moocore_tooltip_link', "prefix" => "50_square", "alt" => htmlspecialchars($entry['User']['name']))); ?>
                            <?php else: ?>
                                <?php echo $this->Moo->getImage(array('User' => $entry['User']), array( "prefix" => "50_square", "alt" => htmlspecialchars($entry['User']['name']))); ?>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="winner_entry">
                        <a class="entry_image ajax-popup-link" href="<?php echo $entry['ContestEntry']['moo_href']; ?>">
                            <img src="<?php echo $helper->getEntryImage($entry, array('prefix' => '450')); ?>">
                            <div class="entry_medal">
                                <i class="material-icons">highlight</i>
                            </div>
                            <span class="contest_type">
                                <?php if($entry['Contest']['type'] == 'photo'): ?>
                                    <i class="contest_type_photo"></i> 
                                <?php endif; ?>
                                 <?php if($entry['Contest']['type'] == 'music'): ?>
                                    <i class="contest_type_music"></i> 
                                <?php endif; ?>
                                <?php if($entry['Contest']['type'] == 'video'): ?>
                                    <i class="contest_type_video"></i> 
                                <?php endif; ?>
                            </span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?> 
        <?php else: ?>
            <?php echo __d('contest', 'No winning entries') ?>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>