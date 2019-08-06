
<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooEmoji"], function($,mooEmoji) {
        mooEmoji.init('message');
    });
</script>
<?php endif; ?>

<div class="bar-content full_content ">
    <div class="content_center">
        <div class="post_body">
       
        
        
        <div class="p_m_10">
            <h2 class="header_h2" style="margin-top: 0px"><?php echo  __('Information') ?></h2>
       
        <ul class="group-info info">
            <li><label><?php echo  __('Category') ?>:</label> 
                <div>
                <a href="<?php echo  $this->request->base ?>/groups/index/<?php echo  $group['Group']['category_id'] ?>/<?php echo  seoUrl($group['Category']['name']) ?>">
                    <?php echo  $group['Category']['name'] ?></a>
                </div>
            </li>
            <li><label><?php echo  __('Type') ?>:</label>
                <div>
                <?php
                switch ($group['Group']['type']) {
                    case PRIVACY_PUBLIC:
                        echo __('Public (anyone can view and join)');
                        break;

                    case PRIVACY_PRIVATE:
                        echo __('Private (only group members can view details)');
                        break;

                    case PRIVACY_RESTRICTED:
                        echo __('Restricted (anyone can join upon approval)');
                        break;
                }
                ?>
                </div>
            </li>
            <?php
            if ($group['Group']['type'] != PRIVACY_PRIVATE || (!empty($cuser) && $cuser['Role']['is_admin'] ) ||
                    (!empty($my_status) && ( $my_status['GroupUser']['status'] == GROUP_USER_MEMBER || $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) )
            ):
                ?>
                <li><label><?php echo  __('Description') ?>:</label>
                    <div>
                        <div class="video-description truncate" data-more-text="<?php echo __( 'Show More')?>" data-less-text="<?php echo __( 'Show Less')?>">
                            <?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags( $group['Group']['description'] , Configure::read('Group.group_hashtag_enabled')))?>
                        </div>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
            <?php $this->Html->rating($group['Group']['id'],'groups', 'Group'); ?>
        </div>
    </div>
    </div>
</div>
<?php
$photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
if ($group['Group']['type'] != PRIVACY_PRIVATE || (!empty($cuser) && $cuser['Role']['is_admin'] ) ||
        (!empty($my_status) && ($my_status['GroupUser']['status'] == GROUP_USER_MEMBER || $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) )
):
    ?>
    <?php if (!empty($photos)): ?>
        <div class="bar-content full_content p_m_10">
            <div class="content_center">
                <h2 class="header_h2"><?php echo  __('Photos') ?></h2>
                <ul class="photo-list">
                    <?php foreach ($photos as $photo): ?>
                        <li class="photoItem" >
                            <div class="p_2">
                                <a href="<?php echo $photo['Photo']['moo_href']?>" class="layer_square photoModal" style="background-image:url(<?php echo $photoHelper->getImage($photo, array('prefix' => '150_square'));?>)" href="<?php echo  $this->request->base ?>/photos/view/<?php echo  $photo['Photo']['id'] ?>#content"></a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="clear"></div>
                <?php
                    if ($photo_count > Configure::read('Photo.photo_item_per_pages')):
                ?>
                        <a href="<?php echo $this->request->base; ?>/groups/view/<?php echo $group['Group']['id']; ?>/tab:photos"><?php echo __('View More'); ?></a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    
        <h2 class="header_title"><?php echo  __('Recent Activities') ?></h2>
        <?php $this->MooActivity->wall($groupActivities)?>
   
<?php endif; ?>
   
