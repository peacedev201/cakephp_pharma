<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php
$this->Html->addCrumb(__('Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('upload_video', 'Limitation'), array('controller' => 'upload_video_limitations', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Upload Video"));
$this->end();
?>

<?php $this->Paginator->options(array('url' => $this->passedArgs)); ?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).on('loaded.bs.modal', function (e) {
        Metronic.init();
    });
    
    $(document).on('hidden.bs.modal', function (e) {
        $(e.target).removeData('bs.modal');
    });
<?php $this->Html->scriptEnd(); ?>
    
<div class="portlet-body form">
    <div class="portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed ">
            <?php echo $this->Moo->renderMenu('UploadVideo', __d('upload_video', 'Limitation')); ?>
            <div class="portlet-body" style="margin-top: 10px;">
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group">
                                <button class="btn btn-gray" data-backdrop="true" data-toggle="modal" data-target="#themeModal" href="<?php echo $this->Html->url(array('plugin' => 'upload_video', 'controller' => 'upload_video_limitations', 'action' => 'admin_create')); ?>">
                                    <?php echo __d('upload_video', 'Add New'); ?>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
                <div class="table-toolbar" style="margin: 0">
                    <div class="row">
                        <div class="col-md-6">
                            <form method="post" action="<?php echo $this->Html->url(array('plugin' => 'upload_video', 'controller' => 'upload_video_limitations', 'action' => 'admin_multi_delete')); ?>" id="deleteForm">
                                <div><?php echo __d('upload_video', "Default is unlimited if user role is not add"); ?></div>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr class="tbl_head">
                                            <th class="text-center" width="50"><?php echo $this->Paginator->sort('id', __d('upload_video', 'ID')); ?></th>
                                            <th><?php echo $this->Paginator->sort('Role.name', __d('upload_video', 'Role')); ?></th>
                                            <th class="text-center">
                                                <?php echo __d('upload_video', 'Videos can upload'); ?>
                                                <br />
                                                <?php echo __d('upload_video', '(D:Day; M:Month; Y:Year)'); ?>
                                            </th>
                                            <th class="text-center"><?php echo __d('upload_video', 'File size (MB)'); ?></th>
                                            <th width="50"><?php echo __d('upload_video', 'Actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 0; ?>
                                        <?php foreach ($aLimitations as $aLimitation): ?>
                                            <tr class="gradeX <?php echo (++$count % 2 ? "odd" : "even") ?>" id="<?php echo $aLimitation['UploadVideoLimitations']['id']; ?>">
                                                <td class="text-center"><?php echo $aLimitation['UploadVideoLimitations']['id']; ?></td>
                                                <td>
                                                    <?php
                                                        $this->MooPopup->tag(array(
                                                            'href'=>$this->Html->url(array(
                                                                'plugin' => 'upload_video', 
                                                                "controller" => "upload_video_limitations",
                                                                "action" => "admin_create",
                                                                $aLimitation['UploadVideoLimitations']['id']
                                                            )),
                                                            'title' => '',
                                                            'target' => 'themeModal',
                                                            'innerHtml'=> $aLimitation['Role']['name'],
                                                        ));
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo !empty($aLimitation['UploadVideoLimitations']['value']) ? $aLimitation['UploadVideoLimitations']['value'] . ' / ' . $aLimitation['UploadVideoLimitations']['per_type'] : __d('upload_video', 'Unlimited'); ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo !empty($aLimitation['UploadVideoLimitations']['size']) ? $aLimitation['UploadVideoLimitations']['size'] : __d('upload_video', 'Unlimited'); ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $this->MooPopup->tag(array(
                                                            'href'=>$this->Html->url(array(
                                                                'plugin' => 'upload_video', 
                                                                "controller" => "upload_video_limitations",
                                                                "action" => "admin_create",
                                                                $aLimitation['UploadVideoLimitations']['id']
                                                            )),
                                                            'title' => '',
                                                            'innerHtml'=> '<i class="icon-edit icon-small"></i>',
                                                            'target' => 'themeModal'
                                                        ));
                                                    ?>
                                                    |
                                                    <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('upload_video', 'Are you sure you want to delete this limitation?'); ?>', '<?php echo $this->Html->url(array('plugin' => 'upload_video', 'controller' => 'upload_video_limitations', 'action' => 'admin_delete', $aLimitation['UploadVideoLimitations']['id'])); ?>')"><i class="icon-trash icon-small"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
            </div>
                            
            <div class="pagination" style="margin: 0">
            <?php echo $this->Paginator->prev('« ' . __d('upload_video', 'Previous'), null, null, array('class' => 'disabled')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next(__d('upload_video', 'Next') . ' »', null, null, array('class' => 'disabled')); ?>
            </div>
        </div>
    </div>
</div>