
<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooContest"], function($, mooContest) {
        mooContest.initSelectPhoto();
    });
</script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>
        mooContest.initSelectPhoto();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
<?php $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo'); ?>
<div class="bar-content">
    <div class="content_center">
        <form id="select_entry">
            <?php echo $this->Form->hidden('contest_id', array('value' => $contest['Contest']['id'])); ?>
            <?php echo $this->Form->hidden('thumbnail', array('value' => '')); ?>
            <?php echo $this->Form->hidden('thumbnail_name', array('value' => '')); ?>
            <?php echo $this->Form->hidden('caption', array('value' => '1')); ?>
            <?php echo $this->Form->hidden('select_photo', array('value' => '1')); ?>
            <?php echo $this->Form->hidden('item_id', array('value' => 0)); ?>
            <div class="box3">
                <div class="mo_breadcrumb">
                    <a href="<?php echo $contest['Contest']['moo_href']; ?>" class="button button-action topButton button-mobi-top"><?php echo __d('contest', 'View Contest') ?></a>
                </div>
                <ul class="photos_edit">
                    <?php foreach ($photos as $photo): ?>
                        <li class="col-md-3 full_content select_entry_li">
                            <div class="albums_edit_item">
                                <?php 
                                if ($photo['Photo']['year_folder']){  // hacking for MOOSOCIAL-2771 
                                    $year = date('Y', strtotime($photo['Photo']['created']));
                                    $month = date('m', strtotime($photo['Photo']['created']));
                                    $day = date('d', strtotime($photo['Photo']['created']));
                                    $url = 'uploads'.DS.'photos'.DS.'thumbnail'.DS . $year.DS.$month .DS .$day. DS. $photo['Photo']['id'] . DS . $photo['Photo']['thumbnail'];
                                }else{
                                    $url = 'uploads'.DS.'photos'.DS.'thumbnail'.DS . $photo['Photo']['id'] . DS . $photo['Photo']['thumbnail'];
                                } 
                                ?>
                                <a href="javascript:void(0)" data-id="<?php echo $photo['Photo']['id']  ?>" data-fname="<?php echo $photo['Photo']['thumbnail'];  ?>" data-file="<?php echo $url; ?>" class="entry_select">
                                    <div class="albums_photo_edit" style="background-image: url(<?php echo $photoHelper->getImage($photo, array('prefix' => '450'));?>)"></div>
                                    <div class="album_info_edit">
                                        <?php echo $this->Form->textarea('caption_' . $photo['Photo']['id'], array('class' => 'photo_caption no-grow', 'value' => $photo['Photo']['caption'], 'placeholder' => __d('contest', 'Caption'))) ?><br />
                                    </div>
                                    <input id="photo_<?php echo $photo['Photo']['id']  ?>" style="float:right;margin-bottom:5px;" type="submit" value="<?php echo __d('contest', 'Add to contest') ?>" class="btn btn-action photo_submit_btn">
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
       