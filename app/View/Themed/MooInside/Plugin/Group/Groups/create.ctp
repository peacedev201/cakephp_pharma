<?php $this->setCurrentStyle(4) ?>
<?php
$groupHelper = MooCore::getInstance()->getHelper('Group_Group');
?>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooGroup'), 'object' => array('$', 'mooGroup'))); ?>
mooGroup.initOnCreate();
<?php $this->Html->scriptEnd(); ?>
<div class="create_form">
    <div class="bar-content">
        <div class="content_center">
            <div class="box3">
                <form id="createForm">
                    <?php
                    if (!empty($group['Group']['id'])){
                        echo $this->Form->hidden('id', array('value' => $group['Group']['id']));
                        echo $this->Form->hidden('photo', array('value' => $group['Group']['photo']));
                    }else{
                        echo $this->Form->hidden('photo', array('value' => ''));
                    }
                    ?>
                    <div class="mo_breadcrumb">
                        <h1><?php if (empty($group['Group']['id'])) echo __( 'Add New Group');
                    else echo __( 'Edit Group'); ?></h1>
                    </div>
                    <div class="full_content p_m_10">
                        <div class="form_content new_form">
                            <div class="col-md-7">
                                <div class="input_container">
                                    <label><?php echo  __( 'Group Name') ?></label>
                                    <div>
                                        <?php echo $this->Form->text('name', array('value' => $group['Group']['name'])); ?>
                                    </div>
                                </div>
                                <div class="input_container">
                                    <label><?php echo  __( 'Description') ?></label>
                                    <div>
                                       <?php echo $this->Form->textarea('description', array('style' => 'height:100px', 'value' => $group['Group']['description'])); ?>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-5">
                                <div class="input_container">
                                    <label><?php echo  __( 'Category') ?></label>
                                    <div>
                                       <?php echo $this->Form->select('category_id', $categories, array('value' => $group['Group']['category_id'])); ?>
                                    </div>
                                </div>
                                <div class="input_container">
                                    <label><?php echo  __( 'Group Type') ?><a href="javascript:void(0)" class="tip profile-tip" title="<?php echo  __( "<p style='display:inline-block; width:150px;'>Public: anyone can view and join<br />Private: only members can view group's details<br />Restricted: anyone can view but join request has to be accepted by group admins</p>") ?>">(?)</a></label>
                                    <div>
                                       <?php
                                        echo $this->Form->select('type', array(PRIVACY_PUBLIC => __( 'Public'),
                                            PRIVACY_PRIVATE => __( 'Private'),
                                            PRIVACY_RESTRICTED => __( 'Restricted')
                                                ), array('value' => $group['Group']['type'], 'empty' => false)
                                        );
                                        ?>
                                    </div>
                                </div>
                                <div class="input_container">
                                    <label><?php echo  __( 'Photo') ?></label>
                                    <div>
                                        <div id="select-0" style="margin: 10px 0 0 0px;"></div>
                                        <?php if (!empty($group['Group']['photo'])): ?>
                                        <img width="150" src="<?php echo $groupHelper->getImage($group, array('prefix' => '150_square'))?>" id="item-avatar" class="img_wrapper">
                                        <?php else: ?>
                                        <img width="150" src="" id="item-avatar" class="img_wrapper" style="display: none;">
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>
                            <div class="clear" style="padding: 0;"></div>
                            <div style="padding:0 15px;">
                                <button type='button' id='saveBtn' class='btn btn-action'><?php echo __( 'Save'); ?></button>

                                        <?php if (!empty($group['Group']['id'])): ?>

                                <a href="<?php echo  $this->request->base ?>/groups/view/<?php echo  $group['Group']['id'] ?>" class="button"><?php echo  __( 'Cancel') ?></a>

                                            <?php if (in_array('group_delete', $uacos) && ( ($group['Group']['user_id'] == $uid ) || (!empty($cuser['Role']['is_admin']) ) )): ?>
                                <a href="javascript:void(0)" onclick="mooConfirm('<?php echo  __( 'Are you sure you want to remove this group?<br />All group contents will also be deleted!') ?>', '<?php echo  $this->request->base ?>/groups/do_delete/<?php echo  $group['Group']['id'] ?>')" class="button"><?php echo  __( 'Delete') ?></a>
                                            <?php endif; ?>

                                        <?php endif; ?>
                                <div class="error-message" style="display:none;"></div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>