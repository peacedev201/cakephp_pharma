<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true,'requires'=>array('jquery', 'mooUser'), 'object' => array('$', 'mooUser'))); ?>
    mooUser.initOnProfilePicture();
<?php $this->Html->scriptEnd(); ?>
<?php
$options = array(
    AVATAR_PUBLIC => '&nbsp;'.__('Public'),
    AVATAR_MEMBER => '&nbsp;'.__('All members'),
    AVATAR_FRIEND => '&nbsp;'.__('Friends'),
    AVATAR_DISABLE => '&nbsp;'.__('Disable'),
);
?>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1> <?php echo  __('Profile Picture') ?></h1>
        </div>
        <div class="ava_content">
            <div class="col-md-6 col-sm-8 col-xs-12">
                <div id="avatar_wrapper" style="vertical-align: top;margin: 0 10px 10px 0">
                    <img src="<?php echo $this->Moo->getImageUrl(array('User' => $cuser), array('prefix' => '200_square'))?>"  id="av-img2">
                </div>

                <div class="Metronic-alerts alert alert-warning fade in ava-upload" style="margin-bottom: 20px;"><?php echo __("Optimal size 200x200px"); ?></div>

                <div id="select-0" class="ava-upload"></div>
            </div>
            <div class="col-md-6 col-sm-4 col-xs-12 wrap-show-avatar-info">
                <h3><?php echo __('Info');?></h3>
                <form id="form_show_info">
                    <label>
                    <?php echo $this->Form->checkbox('show_info_company', array('checked' => $cuser['show_info_company'])); ?>
                    <?php echo __('Company (Pharmacy)');?>
                    </label><br/>
                    <label>
                        <?php echo $this->Form->checkbox('show_info_univ', array('checked' => $cuser['show_info_univ'])); ?>
                        <?php echo __('University');?>
                    </label><br/>
                    <label>
                        <?php echo $this->Form->checkbox('show_info_title', array('checked' => $cuser['show_info_title'])); ?>
                        <?php echo __('Title (department)');?>
                    </label><br/>
                    <h3><?php echo __('Photo can be seen');?></h3>
                    <?php echo $this->Form->radio('show_avatar', $options, array('separator' => '<br/>', 'value' => $cuser['show_avatar'], 'legend' => false, 'label' => array('class' => 'radio-setting')));?>
                    <br/>
                </form>
            </div>
            <div class="clear">
                <button id="save-avatar" data-url="<?php echo $this->Moo->getProfileUrl( $cuser )?>" data-upload="0" type="button" class="btn btn-action save-avatar"><span aria-hidden="true"><?php echo  __('Save') ?></span>
                </button>
                <a id="submit-avatar" href="<?php echo $this->request->base; ?>/users/view/<?php echo $cuser['id']; ?>"; type="button" class="btn btn-action submit-avatar hide"><span aria-hidden="true"><?php echo  __('Submit') ?></span>
                </a>
            </div>
        </div>
        
    </div>
</div>
