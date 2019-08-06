<?php	
	$helper = MooCore::getInstance()->getHelper('Forum_Forum');
	$this->addPhraseJs(array(
			'drag_photo' => __d('forum', "Drag or click here to upload photo"),
			'drag_file' => __d('forum', "Drag or click here to upload files"),
			'delete' => __d('forum', "Delete")			
	));
	$new_files = array();
	$new_original_files = array();
    (!isset($this->request->query['page']))? $page = 1 : $page = $this->request->query['page'];
?>
<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooViewForum"], function($,mooViewForum) {
        mooViewForum.initCreateTopic('<?php echo implode(',', $helper->support_extention());?>');
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooViewForum'), 'object' => array('$', 'mooViewForum'))); ?>
    mooViewForum.initCreateTopic('<?php echo implode(',', $helper->support_extention());?>');
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<div class="create_form">
	<div class="bar-content">
		<div class="content_center">
			<div class="box3">
				<form action="<?php echo  $this->request->base; ?>/forums/topic/save" enctype="multipart/form-data" id="createTopicForm" method="post">
					<div class="mo_breadcrumb">
					   <?php if (!empty($topic['ForumTopic']['id'])) :
                            if(isset($topic_status)):?>
							    <h1><?php echo __d('forum','Edit topic').': '.$forum['Forum']['moo_title'];?></h1>
                            <?php else: ?>
                                <h1><?php echo __d('forum','Edit reply').': '.$forum['Forum']['moo_title'];?></h1>
                            <?php endif;?>
					   <?php else: ?>
						<h1><?php echo __d('forum','Create new topic');?>: <a href="<?php echo $forum['Forum']['moo_href'];?>"><?php echo $forum['Forum']['moo_title'];?></a></h1>
					   <?php endif; ?>
			        </div>
			        <div class="full_content p_m_10">
						<?php if(!$forum['Forum']['status']):?>
							<p><?php echo __d('forum','This forum is marked as closed to new topic, however your posting capabilities still allow you to do so.');?></p>
						<?php endif;?>

			        	<?php
						if (!empty($topic['ForumTopic']['id'])){
							echo $this->Form->hidden('id', array('value' => $topic['ForumTopic']['id']));
						}
						echo $this->Form->hidden('thumb', array('value' => $topic['ForumTopic']['thumb']));
						echo $this->Form->hidden('forum_id', array('value' => $forum_id));
						echo $this->Form->hidden('forum_topic_photo_ids');
                        echo $this->Form->hidden('page', array('value' => $page));
						?>
	            		<div class="form_content">
							<ul>
                                <?php
                                if(isset($topic_status)):
                                ?>
                                    <li>
                                        <div class="col-md-2">
                                            <label><?php echo __d('forum','Title')?></label>
                                        </div>
                                        <div class="col-md-10">
                                            <?php echo $this->Form->text('title', array('value' => $topic['ForumTopic']['title'])); ?>
                                        </div>
                                        <div class="clear"></div>
                                    </li>
                                <?php endif;?>
								<li>
									<div class="col-md-2">
										<label><?php echo __d('forum','Description')?></label>
									</div>
									<div class="col-md-10">
										<?php echo $this->Form->textarea('description', array('value' => $topic['ForumTopic']['description'], 'class' => 'forum-textarea', 'id' => 'description')); ?>
                                        <span><?php echo __d('forum', "To tag member, please click on icon %s at the editor header","<img src='{$this->request->base}/forum/img/user-64x64.png' style='max-width: 16px;' />")?></span><br>
										<div id="images-uploader" style="display:none;margin:10px 0;">
											<div id="attachments_upload"></div>
											<a href="javascript:void(0)" class="button button-primary" id="triggerUpload"><?php echo __d('forum', 'Upload Files')?></a>
										</div>
										<?php if(empty($isMobile)): ?>
										<a id="toggleUploader" href="javascript:void(0)"><?php echo __d('forum', 'Image Upload')?></a>
										<?php endif; ?>
									</div>
									<div class="clear"></div>
								</li>
								<li>
									<div class="col-md-2">
										<label><?php echo __d('forum', 'Thumbnail')?> </label>
									</div>
									<div class="col-md-10">
										<div id="topic_thumnail"></div>
										<div id="topic_thumnail_preview">
											<?php if (!empty($topic['ForumTopic']['thumb'])): ?>
											<img width="150"src="<?php echo  $helper->getTopicImage($topic, array('prefix' => '100')) ?>" />
											<?php else: ?>
											<img width="150" style="display: none;" src="" />
											<?php endif; ?>
										</div>
									</div>
									<div class="clear"></div>
								</li>
								<li>
									<div class="col-md-2">
										<label><?php echo __d('forum','Attachements')?></label>
									</div>
									<div class="col-md-10">
										<?php if(!empty($files)): ?>
											<?php foreach ($files as $file):
												$new_files[] = $file['ForumFile']['file_name'];
												$new_original_files[] = $file['ForumFile']['download_url'];
											?>
												<div class="tp-file-item" id="file_item_<?php echo $file['ForumFile']['id'];?>"><?php echo $file['ForumFile']['download_url'];?>
													<a class="btn-delete-file" data-id="<?php echo $file['ForumFile']['id'];?>" data-file="<?php echo $file['ForumFile']['file_name'];?>" data-originalfile="<?php echo $file['ForumFile']['download_url'];?>"><?php echo __d('forum','delete');?></a>
												</div>
											<?php endforeach;?>
										<?php endif;?>
										<div id="topic_file_upload"></div>
										<div id="topic_file_review"></div>

										<input type="hidden" name="new_files" id="new_files" value="<?php echo implode(',', $new_files);?>">
										<input type="hidden" name="new_original_files" id="new_original_files" value="<?php echo implode(',', $new_original_files);?>">
									</div>
									<div class="clear"></div>
								</li>
								<li>
									<div class="col-md-2">
										<label><?php echo __d('forum', 'Tags')?></label>
									</div>
									<div class="col-md-10">
										<?php echo $this->Form->text('tags',array('value'=> $tags)); ?> <a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __d('forum','Separated by commas or space')?>">(?)</a>
									</div>
									<div class="clear"></div>
								</li>
								<li>
									<div class="col-md-2">
										<label></label>
									</div>
									<div class="col-md-10">
										<?php echo $this->Form->checkbox('subscribe', array('checked' => ($is_subscribe || !$id) ? 'true' : '', 'id' => 'chk_subscribe')); ?>
										<?php echo __d('forum','Send me notification when other members reply to this topic');?>
									</div>
									<div class="clear"></div>
								</li>
                                <li>
                                    <?php if(!isset($topic_status)){
                                            $enable_captcha = $helper->isReplyRecaptchaEnabled();
                                        }else{
                                            $enable_captcha = $helper->isCreateTopicRecaptchaEnabled();
                                        }
                                    ?>
                                    <?php if ($enable_captcha): ?>
                                        <div class="col-md-2">
                                            <label></label>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="captcha_box">
                                                <script src='<?php echo $this->Moo->getRecaptchaJavascript();?>'></script>
                                                <div class="g-recaptcha" data-sitekey="<?php echo $this->Moo->getRecaptchaPublickey(); ?>"></div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    <?php endif; ?>
                                </li>
								<li>
									<div class="col-md-2">
										<label>&nbsp;</label>
									</div>
									<div class="col-md-10">
										<a id="button_save_topic" class='btn btn-action'><?php echo __d('forum' ,'Save')?></a>
										<a href="<?php
                                        if(empty($topic['ForumTopic']['id']))
                                            echo $this->request->base.'/forums';
                                        elseif($topic['ForumTopic']['parent_id'] != '0')
                                            echo $this->request->base.'/forums/topic/view/'.$topic['ForumTopic']['parent_id'].'?page='.$page;
                                        else
                                            echo $topic['ForumTopic']['moo_href'].'?page='.$page;
                                        ?>" class="button"><?php echo __d('forum','Cancel');?></a>
									</div>
									<div class="clear"></div>
								</li>
								<li>
									<div class="error-message" id="errorMessage" style="display:none"></div>
								</li>
							</ul>
	            		</div>
            		</div>
				</form>
			</div>
		</div>
	</div>
</div>