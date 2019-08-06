<?php $helper = MooCore::getInstance()->getHelper('Contest_Contest'); ?>
<?php if (!empty($entries) && count($entries) > 0) : ?>
    <ul class="csmall_list">
        <?php foreach ($entries as $entry): ?>
            <li class="contest-item-small full_content p_m_10" id ="contest_<?php echo $entry['ContestEntry']['id']; ?>">
                <a class="cs_left contest_list_image" href="<?php echo $entry['ContestEntry']['moo_href']; ?>" style="background-image:url('<?php echo $helper->getEntryImage($entry, array('prefix' => '450')) ?>');">
                    <span class="contest_type">
                        <?php if($entry['ContestEntry']['source'] == 'photo'): ?>
                            <i class="contest_type_photo"></i> 
                        <?php elseif($entry['ContestEntry']['source'] == 'music'): ?>
                            <i class="contest_type_music"></i> 
                        <?php else: ?>
                            <i class="contest_type_video"></i> 
                        <?php endif; ?>
                    </span>
                </a>
				
                <div class="cs_right">
					<div class="entry-footer">
                        <div class="entry_vote_count">
                            <p><i class="material-icons">check</i> <a id="" href="<?php echo $this->request->base ?>/contests/ajax_show_voted/<?php echo $entry['ContestEntry']['id']; ?>" data-target="#themeModal" data-toggle="modal" class="" title="<?php echo __d('contest', 'People Who Vote This'); ?>" data-dismiss="modal" data-backdrop="true" style=""><span id="vote_count_<?php echo $entry['ContestEntry']['id']; ?>"><?php echo $entry['ContestEntry']['contest_vote_count']; ?></span></a> <?php echo ($entry['ContestEntry']['contest_vote_count'] > 1) ? __d('contest', 'Votes') : __d('contest', 'Vote');?></p>
                        </div>
                        <div class="entry_view_count">
                            <p><i class="material-icons">remove_red_eye</i> <span ><?php echo $entry['ContestEntry']['view_count']; ?></span> <?php echo ($entry['ContestEntry']['view_count'] > 1) ? __d('contest', 'Views') : __d('contest', 'View');?></p>
                        </div>
                    </div>
                    <p>
                        <a class="small_title" href="<?php echo $entry['ContestEntry']['moo_href']; ?>" title="<?php echo $entry['ContestEntry']['moo_title']; ?>">
                           <?php echo $this->Text->truncate( $entry['ContestEntry']['caption'], 75, array('eclipse' => '')) ; ?>       
                        </a>
                    </p>
                    
                </div>
            </li>
        <?php endforeach; ?>  
    </ul>
<?php endif; ?>