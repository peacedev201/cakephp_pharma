<?php
	if (!empty($activity_logs) && count($activity_logs) > 0)
	{
		$enable_reaction = Configure::read('Reaction.reaction_enabled');
		$activitylogHelper = MooCore::getInstance()->getHelper('Activitylog_Activitylog');
		foreach ($activity_logs as $activity_log):
			$object = '';
			$object_item = '';
			$plugin = '';
			$plugin_item = '';
			$name ='';
			$name_item ='';
			$href = 'javascript:void(0)';
			$add_text = '';
			$item = array();

			if($activity_log['Activitylog']['item_type'] && $activity_log['Activitylog']['item_id']){
				list($plugin_item, $name_item) = mooPluginSplit($activity_log['Activitylog']['item_type']);
				$object_item = MooCore::getInstance()->getItemByType($activity_log['Activitylog']['item_type'],$activity_log['Activitylog']['item_id']);
			}
			if($activity_log['Activitylog']['type'] && $activity_log['Activitylog']['target_id']){
				list($plugin, $name) = mooPluginSplit($activity_log['Activitylog']['type']);
				$object = MooCore::getInstance()->getItemByType($activity_log['Activitylog']['type'],$activity_log['Activitylog']['target_id']);
			}

			if(!$object && $activity_log['Activitylog']['target_id'] != 0){
				$cakeEvent = new CakeEvent('Plugin.Controller.Activitylog.deleteActivitylog', $this, array('item' => $activity_log));
				$this->getEventManager()->dispatch($cakeEvent);
				continue;
			}

			if(!empty($object_item)){
				$href = !empty($object_item[$name_item]['moo_href']) ? $object_item[$name_item]['moo_href'] : $this->request->base . '/users/view/' . $activity_log['Owner']['id'] . '/activity_id:' . $activity_log['Activitylog']['item_id'];
				$item = $object_item;
			}else if(!empty($object)){
				$href = !empty($object[$name]['moo_href']) ? $object[$name]['moo_href'] : $this->request->base . '/users/view/' . $activity_log['Owner']['id'] . '/activity_id:' . $activity_log['Activitylog']['target_id'];
				$item = $object;
			}

			if(!empty($item['Activity']['status']) && $item['Activity']['status'] == ACTIVITY_WAITING){
			    continue;
            }
            if(!empty($item['Video']['in_process']) && $item['Video']['in_process']){
                continue;
            }

			if(substr($activity_log['Activitylog']['action'],0,5) == 'share'){
				$href = $this->request->base. '/users/view/' . $activity_log['Owner']['id'] .'/activity_id:' . $activity_log['Activitylog']['target_id'];
				if(!empty($object[$name]['target_id']) && $object[$name]['target_id']){
					switch($object[$name]['type']){
						case 'User':
							$to_user = MooCore::getInstance()->getItemByType('User',$object[$name]['target_id']);
							$add_text = __d('activitylog',' to %s timeline',  possession( $activity_log['User'], $to_user['User'], true));
							break;
						case 'Group_Group':
							$group = MooCore::getInstance()->getItemByType('Group_Group',$object[$name]['target_id']);
							$add_text = __d('activitylog',' into group %s',  '<a href="'.$group['Group']['moo_href'].'">'.$group['Group']['moo_title'].'</a>');
							break;
					}
				}
			}

			if(!empty($item[key($item)]['privacy'])){
				switch ($item[key($item)]['privacy']) {
					case '1':
						$text = __d('activitylog','Shared with: Everyone');
						$icon = 'public';
						break;
					case '2':
						$text = __d('activitylog','Shared with: Friend');
						$icon = 'people';
						break;
					case '3':
						$text = __d('activitylog','Shared with: Only Me');
						$icon = 'lock';
						break;
				}
				if($name == 'Activity'){
					if($item['Activity']['target_id']){
						if (strtolower($item['Activity']['type']) != 'user'){
							$target = MooCore::getInstance()->getItemByType($item['Activity']['type'],$item['Activity']['target_id']);
							list($plugin_target, $name_target) = mooPluginSplit($item['Activity']['type']);
							$show_subject = MooCore::getInstance()->checkShowSubjectActivity($target);
							if ($show_subject){
								$plugin_helper = MooCore::getInstance()->getHelper($plugin_target.'_'.$plugin_target);
								$is_public = true;
								if (method_exists($plugin_helper,'isPublicFeedIcon'))
								{
									$is_public = $plugin_helper->isPublicFeedIcon($target);
								}
								if($is_public){
									$text = __d('activitylog','Shared with: Everyone');
									$icon = 'public';
								}else{
									$text = __d('activitylog','Shared with: member of %s ', $target[$name_target]['moo_title']);
									$icon = 'people';
								}
							}
						}
					}
				}
			}else if($name == 'Event' || $name == 'Group'){
				switch ($item[key($item)]['type']) {
				case '2':
					$text = __d('activitylog','Private');
					$icon = 'lock';
					break;
				default:
					$text = __d('activitylog','Public');
					$icon = 'public';
					break;
				}
			}
		?>
		<li class="full_content p_m_10" id="log_item_<?php echo $activity_log['Activitylog']['id'];?>">
			<div class="log-section">
				<div class="col-md-5 log-info">
					<?php echo $this->element('misc/activity_texts', array( 'activity_log' => $activity_log , 'href' => $href, 'object' => $item, 'name' => key($item), 'add_text'=>$add_text, 'enable_reaction' => $enable_reaction, 'activitylogHelper' => $activitylogHelper));	?>
					<div>
						<a href="<?php echo $href;?>"><span class="date"><?php echo $this->Time->format($activity_log['Activitylog']['created'], '%b %d, %Y %H:%M', false, $utz)?></span></a>
						<span class="log-action-mobile">
							<?php if(!empty($icon)):?>
							<a aria-haspopup="true" aria-expanded="true" class="tip" href="javascript:void(0);" original-title="<?php echo $text;?>"><i class="material-icons"><?php echo $icon;?></i></a>
							<?php endif;?>
						</span>
					</div>
				</div>
				<div class="col-md-6 activity_feed_content_text log-item">
					<?php
						if($object){
							echo $this->element('misc/activity_content', array( 'activity_log' => $activity_log , 'object' => $object, 'name' => $name));
						}
					?>
				</div>
				<div class="col-md-1 col-xs-12 log-action">
					<?php if(!empty($icon)):?>
						<a aria-haspopup="true" aria-expanded="true" class="tip" href="javascript:void(0);" original-title="<?php echo $text;?>"><i class="material-icons"><?php echo $icon;?></i></a>
					<?php endif;?>
				</div>
				<?php if(!empty($object)):?>
					<div class="list_option">
						<div class="dropdown item-action">
							<a href="<?php echo $href;?>"><i class="material-icons">remove_red_eye</i></a>
						</div>
					</div>
				<?php endif;?>
			</div>
			<div class="clear"></div>
		</li>
		<?php
		endforeach;
	} else{
		echo '<div class="clear" align="center">' . __d('activitylog', 'No more results found') . '</div>';
	}
	?>
	<?php if (isset($more_url)&& !empty($more_result)): ?>
	<?php $this->Html->viewMore($more_url) ?>
	<?php endif; ?>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooActivitylog"], function($,mooActivitylog) {
        mooActivitylog.initOnListing();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooActivitylog'), 'object' => array('$', 'mooActivitylog'))); ?>
mooActivitylog.initOnListing();
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
