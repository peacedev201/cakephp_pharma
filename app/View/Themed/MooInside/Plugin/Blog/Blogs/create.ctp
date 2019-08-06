<?php $this->setCurrentStyle(4) ?>
<?php
$tags_value = '';
$blogHelper = MooCore::getInstance()->getHelper('Blog_Blog');
if (!empty($tags))
    $tags_value = implode(', ', $tags);
?>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooBlog'), 'object' => array('$', 'mooBlog'))); ?>
mooBlog.initOnCreate();
<?php $this->Html->scriptEnd(); ?>
<div class="create_form">
<div class="bar-content">
<div class="content_center">
<div class="box3">
	<form id='createForm' action="<?php echo  $this->request->base; ?>/blogs/save" method="post">
	<?php
	if (!empty($blog['Blog']['id']))
		echo $this->Form->hidden('id', array('value' => $blog['Blog']['id']));
        echo $this->Form->hidden('thumbnail', array('value' => $blog['Blog']['thumbnail']));
                    echo $this->Form->hidden('blog_photo_ids');
	?>
	<div class="mo_breadcrumb">
            <h1><?php if (empty($blog['Blog']['id'])) echo __( 'Write New Entry'); else echo __( 'Edit Entry');?></h1>
        </div>
        <div class="full_content p_m_10">
            <div class="form_content new_form">
                <div class="col-md-7">
                    <div class="input_container">
                        <label><?php echo __( 'Title')?></label>
                        <div>
                            <?php echo $this->Form->text('title', array('value' => $blog['Blog']['title'])); ?>
                        </div>
                    </div>
                    <div class="input_container">
                        <label><?php echo __( 'Body')?></label>
                        <div>
                           <?php echo $this->Form->tinyMCE('body', array('value' => $blog['Blog']['body'], 'id' => 'editor')); ?>
                        </div>    
                    </div>
                    
                </div>
                <div class="col-md-5">
                    <div class="input_container">
                        <label><?php echo __( 'Tags')?><a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __( 'Separated by commas')?>">(?)</a> </label>
                        <div>
                             <?php echo $this->Form->text('tags', array('value' => $tags_value)); ?>   
                        </div>
                    </div>
                    <div class="input_container">
                            <label><?php echo __( 'Privacy')?></label>
                            <div>
                            <?php echo $this->Form->select( 'privacy', 
                                                                                            array( PRIVACY_EVERYONE => __( 'Everyone'), 
                                                                                                       PRIVACY_FRIENDS  => __( 'Friends Only'), 
                                                                                                       PRIVACY_ME       => __( 'Only Me') ), 
                                                                                            array( 'value' => $blog['Blog']['privacy'],
                                                                                                       'empty' => false
                                                                                     ) ); 
                                ?>
                            </div>
                    </div>
                    <div class="input_container">
                         <label><?php echo __( 'Thumbnail')?> (<a original-title="<?php echo __( 'Thumbnail only display on blog listing and share blog to facebook')?>" class="tip" href="javascript:void(0);">?</a>)</label>
                         <div>
                            <div id="blog_thumnail"></div>
                                <div id="blog_thumnail_preview">
                                    <?php if (!empty($blog['Blog']['thumbnail'])): ?>
                                        <img width="150" src="<?php echo  $blogHelper->getImage($blog, array('prefix' => '150_square')) ?>" />
                                    <?php else: ?>
                                        <img width="150" style="display: none;" src="" />
                                    <?php endif; ?>
                                </div>    
                         </div>   
                    </div>
                    <div class="input_container">
                        <div id="images-uploader" style="display:none;margin:10px 0;">
                        <div id="photos_upload"></div>
                        <a href="#" class="button button-primary" id="triggerUpload"><?php echo __( 'Upload Queued Files')?></a>
                        </div>
                        <?php if(empty($isMobile)): ?>
                                    <a id="toggleUploader" href="javascript:void(0)"><?php echo __('Toggle Images Uploader') ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="padding:0; " class="clear"></div>
                <div style="padding:0 15px;">
                            <button type='button' id='saveBtn' class='btn btn-action'><?php echo __( 'Save'); ?></button>
                            
                                <?php if ( !empty( $blog['Blog']['id'] ) ): ?>
                                <a href="<?php echo $this->request->base?>/blogs/view/<?php echo $blog['Blog']['id']?>" class="button"><?php echo __( 'Cancel')?></a>
                                <?php endif; ?>
                                <?php if ( ($blog['Blog']['user_id'] == $uid ) || ( !empty( $blog['Blog']['id'] ) && $cuser['Role']['is_admin'] ) ): ?>
                                        <a href="javascript:void(0)" data-id="<?php echo $blog['Blog']['id'] ?>" class="button deleteBlog"><?php echo __('Delete') ?></a>
                                <?php endif; ?>
                        </div>
                        <div class="error-message" id="errorMessage" style="display: none;"></div>
                

        </form>
                <div class="clear"></div>

        </div>
    </div>
</div>
</div>
</div>
</div>