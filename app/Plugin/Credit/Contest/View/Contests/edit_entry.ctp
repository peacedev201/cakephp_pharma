<?php $this->setCurrentStyle(4) ?>
<?php $helper = MooCore::getInstance()->getHelper('Contest_Contest'); ?>
<div class="bar-content">
    <div class="content_center">
        <form id="edit_entries" action="<?php echo $this->request->base ?>/contests/edit_entry/<?php echo $entry['ContestEntry']['id'] ?>" method="post">
            <?php echo $this->Form->hidden('id', array('value' => $contest['Contest']['id'])); ?>
            <div class="box3">
                <div class="mo_breadcrumb">
                    <h1><?php echo __d('contest', 'Edit entry of %s', htmlspecialchars($contest['Contest']['name'])) ?></h1>
                    <a href="<?php echo $contest['Contest']['moo_href']; ?>" class="button button-action topButton button-mobi-top"><?php echo __d('contest', 'View Contest') ?></a>
                </div>
                <ul class="photos_edit">
                    <li class="col-md-3 full_content" style="padding-right:8px;">
                        <div class="entry_status">
                            <?php echo $helper->getEntryStatus($entry); ?>
                        </div>
                        <div class="">
                            <div class="albums_photo_edit" style="background-image: url(<?php echo $helper->getEntryImage($entry, array('prefix' => '450')); ?>)"></div>
                            <div class="album_info_edit">
                                <?php echo $this->Form->textarea('caption_' . $entry['ContestEntry']['id'], array('value' => $entry['ContestEntry']['caption'], 'placeholder' => __d('contest', 'Caption'), 'class' => 'no-grow')) ?><br />
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="clear"></div>
                <div class='full_content p_m_10'>
                    <div class='clear'></div>    
                    <div align="center" style="margin-top: 30px">
                        <input type="submit" value="<?php echo __d('contest', 'Save Changes') ?>" class="btn btn-action">
                        <a class="button" href="<?php echo $entry['ContestEntry']['moo_href']; ?>"><?php echo __d('contest', 'Cancel'); ?></a>
                    </div>
                    <div class='clear'></div>
                </div>
            </div>
        </form>
    </div>
</div>