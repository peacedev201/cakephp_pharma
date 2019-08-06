<?php $helper = MooCore::getInstance()->getHelper('Forum_Forum');?>
<?php if(!empty($files)):?>
<div class="forum-files-list">
    <ul class="forum-list-file-download">
    <?php foreach ($files as $file):?>
        <li><a class="forum-download-link" download="<?php echo $file['ForumFile']['download_url'];?>" href="<?php echo $helper->getDocument($file);?>" title="<?php echo __d('forum','Download Attachment')?>"><i class="material-icons">file_download</i><?php echo $file['ForumFile']['download_url'];?></a></li>
    <?php endforeach;?>
    </ul>
</div>
<?php endif;?>