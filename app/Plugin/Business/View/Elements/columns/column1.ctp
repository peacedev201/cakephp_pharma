<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooBusiness"], function($,mooBusiness) {
        mooBusiness.initSaveBusinessCover();
        mooBusiness.initFollow();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooBusiness'), 'object' => array('$', 'mooBusiness'))); ?>
mooBusiness.initSaveBusinessCover();
mooBusiness.initFollow();
mooBusiness.initCheckIn();
mooBusiness.showMoreLessContent();
mooBusiness.initForMobile();
<?php $this->Html->scriptEnd(); ?> 
<?php endif; ?>
<?php if( !$this->isEmpty('north') ): ?>
    <?php echo $north ;?>
<?php endif; ?>

    <?php if (!empty($is_profile_page)): ?>
       <?php echo $this->element('user/header_profile'); ?>   
    <?php endif; ?>
        <?php if( !$this->isEmpty('west') ): ?>
        <div id="leftnav" class="sl-rsp-modal col-md-3">
            <div class="visible-xs visible-sm closeButton">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo __d('business', 'Close');?></span></button>
            </div>
            <?php echo $west; ?>
        </div>
        <?php endif; ?>

        <div id="center-right" class="col-md-9">
            <?php
                $businessPackage = $business['BusinessPackage'];
                $business = $business['Business'];
                //$businessHelper = MooCore::getInstance()->getHelper('Business_Business');
            ?>

            <div class="bar-content full_content p_m_10">
                <div>
                    <div id="cover">
                        <?php
                            if (!empty($business['cover']))
                            {
                                $business['cover'] = BUSINESS_COVER_IMAGE_WIDTH . '_' . $business['cover'];
                            }
                        ?>
                        <img id="cover_img_display" src="<?php  echo $this->storage->getUrl($business["id"],'',$business['cover'],"business_covers"); ?>" width="100%">
                        <?php 
                            $mBusiness = MooCore::getInstance()->getModel('Business.Business');
                            $mBusinessAdmin = MooCore::getInstance()->getModel('Business.BusinessAdmin');
                            if(($cuser != null && $cuser['Role']['is_admin']) || 
                            $mBusiness->isBusinessOwner($business['id']) ||
                            $mBusinessAdmin->isBusinessAdmin($business['id'], MooCore::getInstance()->getViewer(true))):
                        ?>
                            <div id="cover_upload">
                            
                                <?php
                                    $this->MooPopup->tag(array(
                                            'href'=>$this->Html->url(array("controller" => "businesses",
                                                                            "action" => "change_cover",
                                                                            "plugin" => false,
                                                                            $business['id']
                                                                        )),
                                            'title' => __d('business', 'Edit Cover Picture'),
                                            'innerHtml'=> __d('business', 'Edit Cover Picture'),
                                            'data-backdrop' => 'static',
                                            'data-target' => '#coverModal',
                                            'target' => 'coverModal'
                                    ));
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="bus-detail-breadcrumb-cover">
                        <div class="list_option">
                            <?php 
                                if(($cuser != null && $cuser['Role']['is_admin']) || 
                                $mBusiness->isBusinessOwner($business['id']) ||
                                $mBusinessAdmin->isBusinessAdmin($business['id'], MooCore::getInstance()->getViewer(true))):
                            ?>
                            <div class="dropdown">
                                <button class="button" data-toggle="dropdown" data-target="#" id="dropdown-edit"><!--dropdown-user-box-->
                                    <i class="material-icons dp-18">more_vert</i>
                                </button>
                                
                                    <ul aria-labelledby="dropdown-edit" class="dropdown-menu" role="menu">
                                        <li>
                                            <?php
                                                $this->MooPopup->tag(array(
                                                        'href'=>$this->Html->url(array("controller" => "businesses",
                                                                                        "action" => "change_cover",
                                                                                        "plugin" => false,
                                                                                        $business['id']
                                                                                    )),
                                                        'title' => __d('business', 'Edit Cover'),
                                                        'innerHtml'=> __d('business', 'Edit Cover'),
                                                        'data-backdrop' => 'static',
                                                        'data-target' => '#coverModal',
                                                        'target' => 'coverModal'
                                                ));
                                            ?>
                                            <a id="change_default_photo" href="javascript:void(0)" data-id="<?php echo $business['id'];?>">
                                                <?php echo __d('business', 'Change to default photo');?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            <?php endif; ?> 
                        </div>
                        <div class="clear"></div>
                        <?php if($businessPackage['checkin'] && $cuser != null):?>
                            <a class="button btn_checkin" href="javascript:void(0)" data-id="<?php echo $business['id'];?>">
                                <?php echo __d('business', 'Check-in');?>
                            </a>
                        <?php endif;?>
                        <?php if($business['user_id'] != $uid && $businessPackage['follow'] && !$is_banned):?>
                            <a href="javascript:void(0)" class="button btn_follow" data-id="<?php echo $business['id'];?>">
                                <?php if(!$is_followed):?>
                                    <?php echo __d('business', 'Follow');?>
                                <?php else:?>
                                    <?php echo __d('business', 'Unfollow');?>
                                <?php endif;?>
                            </a>
                        <?php endif;?>
                        <?php if(MooCore::getInstance()->getViewer(true) > 0):?>
                            <?php if(!$is_app):?>
                                <a href="javascript:void(0)" class="button shareFeedBtn" share-url="<?php echo $business['moo_hrefshare'];?>" data-placement="top">
                                    <?php echo __d('business', 'Share');?>
                                </a>
                            <?php else:?>
                                <div class="list_option bus_list_option_share">
                                    <div class="dropdown">
                                        <button id="business_detail_<?php echo $business["id"] ?>">
                                            <?php echo __d('business', 'Share');?>
                                        </button>
                                        <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="business_detail_<?php echo $business["id"] ?>">
                                            <?php echo $this->element('share/menu', array(
                                                'param' => 'Business_Business',
                                                'action' => 'business_item_detail_share',
                                                'id' => $business['id']
                                            ));?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif;?>
                        <?php endif;?>
                        <?php if($businessPackage['contact_form']):?>
                            <?php if(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_CONTACT):?>
                                <a href="javascript:void(0)" class="button scroll_to_detail">
                            <?php else:?>
                                <a href="<?php echo $business['moo_hrefcontact'];?>"  class="button">
                            <?php endif;?>
                                <?php echo __d('business', 'Contact us');?>
                            </a>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>

        <?php if( !$this->isEmpty('east') ): ?>
        <div id="right"  class="sl-rsp-modal col-md-3 pull-right">
            <div class="visible-xs visible-sm closeButton">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span> <span class="sr-only">Close</span></button>
            </div>
            <?php echo $east; ?>
        </div>
        <?php endif; ?>

        <div id="center" <?php if( !$this->isEmpty('east') &&  !$this->isEmpty('west') ): echo 'class="col-md-6"'; 
                               elseif (($this->isEmpty('east') && !$this->isEmpty('west')) || (!$this->isEmpty('east') && $this->isEmpty('west'))): echo 'class="col-md-9"';
                               endif; ?>>
            
        <?php echo $center; ?>
        </div>
        <div class="clear"></div>
    
        

        



