
<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooContest"], function($, mooContest) {
        mooContest.initSelectVideo();
    });
</script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>
        mooContest.initSelectVideo();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
<?php $videoHelper = MooCore::getInstance()->getHelper('Video_Video'); ?>
<div class="bar-content">
    <div class="content_center">
        <form id="select_entry">
            <?php echo $this->Form->hidden('contest_id', array('value' => $contest['Contest']['id'])); ?>
            <?php echo $this->Form->hidden('thumbnail', array('value' => '')); ?>
            <?php echo $this->Form->hidden('thumbnail_name', array('value' => '')); ?>
            <?php echo $this->Form->hidden('caption', array('value' => '')); ?>
            <?php echo $this->Form->hidden('select_video', array('value' => '1')); ?>
            <?php echo $this->Form->hidden('item_id', array('value' => 0)); ?>
            <div class="box3">
                <div class="mo_breadcrumb">
                    <a href="<?php echo $contest['Contest']['moo_href']; ?>" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored topButton button-mobi-top"><?php echo __d('contest', 'View Contest') ?></a>
                </div>
                <ul class="photos_edit">
                    <?php foreach ($videos as $video): ?>
                        <li class="col-md-3 full_content select_entry_li">
                            <div class="albums_edit_item">
                                <a href="javascript:void(0)" data-id="<?php echo $video['Video']['id']  ?>" data-fname="<?php echo $video['Video']['thumb'];  ?>" data-file="<?php echo $videoHelper->getImage($video, array('prefix' => '450'))?>" class="entry_select">
                                    <div class="albums_photo_edit" style="background-image: url(<?php echo $videoHelper->getImage($video, array('prefix' => '450'))?>)"></div>
                                    <div class="album_info_edit">
                                        <?php echo $this->Form->textarea('caption_' . $video['Video']['id'], array('class' => 'photo_caption no-grow', 'value' => $video['Video']['title'], 'placeholder' => __d('contest', 'Caption'))) ?><br />
                                    </div>
                                    <input id="video_<?php echo $video['Video']['id']  ?>" style="float:right;margin-bottom:5px;" data-id="<?php echo $video['Video']['id']  ?>" data-fname="<?php echo $video['Video']['thumb'];  ?>" data-file="<?php echo $videoHelper->getImage($video, array('prefix' => '450'))?>" type="submit" value="<?php echo __d('contest', 'Add to contest') ?>" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1 photo_submit_btn">
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                 <div class="clear"></div>
            </div>
        </form>
    </div>
</div>
       