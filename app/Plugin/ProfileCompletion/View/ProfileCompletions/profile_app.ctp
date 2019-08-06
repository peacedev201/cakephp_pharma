<?php 
if(Configure::read('ProfileCompletion.profile_completion_enabled') && $total_per == 100 && !empty($core_content) && !empty($viewer) && $current_view_user_id == $viewer['User']['id']){
	$total_percent = (count($profile_completion) > 0) ? array_sum($profile_completion) : 0;

	 ?>
		<div class="profile_info_tab full_content" style="<?php echo (Configure::read('ProfileCompletion.not_show_widget_100') && $total_percent == 100) ? 'display: none;' : '';?>">
			<?php if($title_enable): ?>
			    <h2 class="header_h2" style="margin-top: 0px;"><?php echo h($title) ?></h2>
			<?php endif; ?>
		    	<div class="row">
		    		<div class="col-md-12">
		    			<div style="padding-bottom: 10px;">
				    		<?php 
				    			$tmp = $total_percent.'%'; 
				    		 	echo sprintf(__d('profile_completion', '%s Profile Completeness'), $tmp); 
				    		?>
				    	</div>
						<div style="padding-bottom: 10px;">
							<div class="progress">
							  	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $total_percent;?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $total_percent;?>%">
							    	<span class="sr-only"><?php echo $total_percent.__d('profile_completion', '% Complete');?></span>
							  	</div>
							</div>
						</div>
						<?php if($total_percent != 100): ?>
							<div style="padding-bottom: 10px;">
								<?php 
									echo __d('profile_completion', 'Next: ');
									if($tmp_key != 'avatar'):
								 ?>
									<a href="<?php echo $this->base.'/users/profile'; ?>">
										<?php echo $next; ?>
										( <?php echo $next_percent;  ?> % )
									</a>
								<?php else: ?>
									<a href="<?php echo $this->base.'/users/avatar'; ?>">
										<?php echo $next; ?>
										( <?php echo $next_percent;  ?> % )
									</a>
								<?php endif; ?>
							</div>
							<div style="padding-bottom: 10px;">
								<a href="<?php echo $this->base.'/users/profile'; ?>">
									<?php 
										echo __d('profile_completion', 'Update Profile');
									 ?>
								</a>					
							</div>
						<?php endif; ?>
		    		</div>
		    	</div>
		</div>

	<style type="text/css" media="screen">
		.progress{
			margin-bottom: 0;
			background-color: <?php echo (!empty(Configure::read('ProfileCompletion.remaining_bar_color')) ? Configure::read('ProfileCompletion.remaining_bar_color') : '#ccc');?>;
		}
		.progress-bar{
			background-color: <?php echo (!empty(Configure::read('ProfileCompletion.progress_bar_color')) ? Configure::read('ProfileCompletion.progress_bar_color') : '#d8601f');?>;
		}
		.box2{
		    border-top: 1px solid #d3d3d3;
		    border-bottom: 1px solid #d3d3d3;
		    background: #fff;
		    margin-bottom: 9px;
		}
		.box2 h3{
			border-bottom: 1px solid #d3d3d3;
		}
	</style>

<?php 
}
?>