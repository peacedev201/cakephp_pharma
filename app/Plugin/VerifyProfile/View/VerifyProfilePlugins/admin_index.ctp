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
    $this->Html->addCrumb(__d('verify_profile', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('verify_profile', 'Profile Verify Manager'), array('controller' => 'verify_profile_plugins', 'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Profile Verify'));
    $this->end();
?>

<?php $this->Paginator->options(array('url' => $this->passedArgs)); ?>

<?php $oModelPhoto = MooCore::getInstance()->getModel('Photo.Photo'); ?>
<?php $oPhotoHelper = MooCore::getInstance()->getHelper('Photo_Photo'); ?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).on('loaded.bs.modal', function (e) {
        Metronic.init();
    });
    $(document).on('hidden.bs.modal', function (e) {
        $(e.target).removeData('bs.modal');
    });
<?php $this->Html->scriptEnd(); ?>
    
<?php 
__d('verify_profile', 'All');
__d('verify_profile', 'Male');
__d('verify_profile', 'Female');
__d('verify_profile', 'Unknown');
__d('verify_profile', 'Pending');
__d('verify_profile', 'Verified');
__d('verify_profile', 'Unverified');
__d('verify_profile', '_EMAIL_VERIFY_PROFILE_VERIFIED_TITLE_');
__d('verify_profile', '_EMAIL_VERIFY_PROFILE_VERIFIED_DESCRIPTION_');
__d('verify_profile', '_EMAIL_VERIFY_PROFILE_UNVERIFIED_TITLE_');
__d('verify_profile', '_EMAIL_VERIFY_PROFILE_UNVERIFIED_DESCRIPTION_');
?>

<style>
    table.table-bordered tbody th, table.table-bordered tbody td{
        vertical-align: middle
    }
    
    table.table-bordered tbody td.documents{
        padding-bottom: 3px;
        padding-right: 3px;
    }
    
    ul.list_documents{
        padding: 0;
        list-style: outside none none;
    }
    
    ul.list_documents li{
        float: left;
        margin-right: 8px;
        margin-bottom: 5px;
    }
</style>

<div class="portlet-body form">
    <div class="portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed ">
            <?php echo$this->Moo->renderMenu('VerifyProfile', __d('verify_profile', 'General')); ?>
            <div class="portlet-body" style="margin-top: 10px;">
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group">
                                <button type="button" class="btn btn-fit-height btn-gray dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-angle-down"></i>
                                    <?php echo __d('verify_profile', $sStatus); ?>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo $this->Html->url(array('plugin' => 'verify_profile', 'controller' => 'verify_profile_plugins', 'action' => 'admin_index', 'filter' => 'pending')); ?>"><?php echo __d('verify_profile', 'Pending'); ?></a></li>
                                    <li><a href="<?php echo $this->Html->url(array('plugin' => 'verify_profile', 'controller' => 'verify_profile_plugins', 'action' => 'admin_index', 'filter' => 'verified')); ?>"><?php echo __d('verify_profile', 'Verified'); ?></a></li>
                                    <li><a href="<?php echo $this->Html->url(array('plugin' => 'verify_profile', 'controller' => 'verify_profile_plugins', 'action' => 'admin_index')); ?>"><?php echo __d('verify_profile', 'All'); ?></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr class="tbl_head">
                            <th width="30"><?php echo $this->Paginator->sort('id', __d('verify_profile', 'ID')); ?></th>
                            <th><?php echo $this->Paginator->sort('User.name', __d('verify_profile', 'Name'));?></th>
                            <?php if(Configure::read('VerifyProfile.verify_profile_birthday')): ?>
                            <th><?php echo $this->Paginator->sort('User.birthday', __d('verify_profile', 'Birthday'));?></th>
                            <?php endif; ?>
                            <?php if(Configure::read('VerifyProfile.verify_profile_gender')): ?>
                            <th><?php echo $this->Paginator->sort('User.gender', __d('verify_profile', 'Gender'));?></th>
                            <?php endif; ?>
                            <?php if(Configure::read('VerifyProfile.verify_profile_avatar')): ?>
                            <th class="text-center" width="68"><?php echo __d('verify_profile', 'Avatar');?></th>
                            <?php endif; ?>
                            <th width="186"><?php echo __d('verify_profile', 'Documents');?></th>
                            <th><?php echo $this->Paginator->sort('created', __d('verify_profile', 'Date Requested'));?></th>
                            <th><?php echo $this->Paginator->sort('status', __d('verify_profile', 'Status'));?></th>
                            <th width="215"><?php echo __d('verify_profile', 'Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 0; ?>
                        <?php foreach ($aVerifyProfiles as $aVerifyProfile): ?>
                        <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                            <td><?php echo $aVerifyProfile['VerifyProfile']['id']; ?></td>
                            <td><a href="<?php echo $aVerifyProfile['User']['moo_href']; ?>" target="_blank"><?php echo $aVerifyProfile['User']['name']; ?></a></td>
                            <?php if(Configure::read('VerifyProfile.verify_profile_birthday')): ?>
                            <td><?php echo strftime('%B %d, %Y', strtotime($aVerifyProfile['User']['birthday'])); ?></td>
                            <?php endif; ?>
                            <?php if(Configure::read('VerifyProfile.verify_profile_gender')): ?>
                            <td><?php echo __d('verify_profile', $aVerifyProfile['User']['gender']); ?></td>
                            <?php endif; ?>
                            <?php if(Configure::read('VerifyProfile.verify_profile_avatar')): ?>
                            <td class="text-center"><?php echo $this->Moo->getItemPhoto(array('User' => $aVerifyProfile['User']), array("height" => "50px" , "class" => "img_wrapper", 'prefix' => '50_square')); ?></td>
                            <?php endif; ?>
                            <td class="documents">
                                <?php 
                                    $aPhotosId = explode(',', $aVerifyProfile['VerifyProfile']['images']);
                                    $aPhotos = $oModelPhoto->find('all', array('conditions' => array('Photo.id' => $aPhotosId), 'fields' => array('Photo.thumbnail', 'Photo.year_folder', 'Photo.created')));
                                ?>
                                <ul class="list_documents">
                                    <?php foreach ($aPhotos as $aPhoto): ?>
                                    <li>
                                        <a data-backdrop="true" data-toggle="modal" data-target="#themeModal" href="<?php echo $this->Html->url(array("plugin" => "verify_profile", "controller" => "verify_profile_plugins", "action" => "admin_photo_document", $aPhoto['Photo']['id'])); ?>">
                                            <img height="50px" src="<?php echo $oPhotoHelper->getImage($aPhoto, array('prefix' => '75_square')); ?>">
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td><?php echo $this->Time->niceShort($aVerifyProfile['VerifyProfile']['created']); ?></td>
                            <td><?php echo __d('verify_profile', ucfirst($aVerifyProfile['VerifyProfile']['status'])); ?></td>
                            <td>
                                <?php if($aVerifyProfile['VerifyProfile']['status'] == 'pending'): ?>
                                    <?php
                                        $this->MooPopup->tag(array(
                                            'href'=>$this->Html->url(array(
                                                "plugin" => "verify_profile", 
                                                "controller" => "verify_profile_plugins",
                                                "action" => "admin_view_document",
                                                $aVerifyProfile['VerifyProfile']['id']
                                            )),
                                            'title' => '',
                                            'innerHtml'=> __d('verify_profile', 'View Document'),
                                            'target' => 'themeModal'
                                        ));
                                    ?> - 
                                    <?php
                                        $this->MooPopup->tag(array(
                                            'href'=>$this->Html->url(array(
                                                "plugin" => "verify_profile", 
                                                "controller" => "verify_profile_plugins",
                                                "action" => "admin_unverify",
                                                $aVerifyProfile['User']['id']
                                            )),
                                            'title' => '',
                                            'innerHtml'=> __d('verify_profile', 'Deny')
                                        ));
                                    ?> - 
                                    <?php
                                        $this->MooPopup->tag(array(
                                            'href'=>$this->Html->url(array(
                                                "plugin" => "verify_profile", 
                                                "controller" => "verify_profile_plugins",
                                                "action" => "admin_verify",
                                                $aVerifyProfile['User']['id']
                                            )),
                                            'title' => '',
                                            'innerHtml'=> __d('verify_profile', 'Verify'),
                                            'target' => 'themeModal'
                                        ));
                                    ?>
                                <?php elseif($aVerifyProfile['VerifyProfile']['status'] == 'verified'): ?>
                                    <?php
                                        $this->MooPopup->tag(array(
                                            'href'=>$this->Html->url(array(
                                                "plugin" => "verify_profile", 
                                                "controller" => "verify_profile_plugins",
                                                "action" => "admin_view_document",
                                                $aVerifyProfile['VerifyProfile']['id']
                                            )),
                                            'title' => '',
                                            'innerHtml'=> __d('verify_profile', 'View Document'),
                                            'target' => 'themeModal'
                                        ));
                                    ?> - 
                                    <?php
                                        $this->MooPopup->tag(array(
                                            'href'=>$this->Html->url(array(
                                                "plugin" => "verify_profile", 
                                                "controller" => "verify_profile_plugins",
                                                "action" => "admin_unverify",
                                                $aVerifyProfile['User']['id']
                                            )),
                                            'title' => '',
                                            'innerHtml'=> __d('verify_profile', 'Unverify')
                                        ));
                                    ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
                            
            <div class="pagination">
		<?php echo $this->Paginator->prev('« '.__d('verify_profile', 'Previous'), null, null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next(__d('verify_profile', 'Next').' »', null, null, array('class' => 'disabled')); ?>
            </div>
        </div>
    </div>
</div>
