<?php 

if( (Configure::read('ProfileCompletion.profile_completion_enabled') && !empty($viewer) && $total_per == 100)){
	if(isset($current_view_user_id) && $current_view_user_id == $viewer['User']['id']):
		$total_percent = (count($profile_completion) > 0) ? array_sum($profile_completion) : 0;
	 ?>
		<div class="box2" style="<?php echo (Configure::read('ProfileCompletion.not_show_widget_100') && $total_percent == 100) ? 'display: none;' : '';?>">
			<?php if($title_enable): ?>
			    <h3><?php echo h($title) ?></h3>
			<?php endif; ?>
		    <div class="box_content">
		    	<div class="row">
		    		<div class="col-md-12">
		    			<div class="p_10">
				    		<?php 
				    			$tmp = $total_percent.'%'; 
				    		 	echo sprintf(__d('profile_completion', '%s Profile Completeness'), $tmp); 
				    		?>
				    	</div>
						<div class="p_10">
							<div class="progress">
							  	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $total_percent;?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $total_percent;?>%">
							    	<span class="sr-only"><?php echo $total_percent.__d('profile_completion', '% Complete');?></span>
							  	</div>
							</div>
						</div>
						<?php if($total_percent != 100): ?>
							<div class="p_10">
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
							<div class="p_10">
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
		</div>
	<?php elseif(!isset($current_view_user_id)): ?>
		<?php 
			$total_percent = (count($profile_completion) > 0) ? array_sum($profile_completion) : 0;
		 ?>
		<div class="box2" style="<?php echo (Configure::read('ProfileCompletion.not_show_widget_100') && $total_percent == 100) ? 'display: none;' : '';?>">
			<?php if($title_enable): ?>
			    <h3><?php echo h($title) ?></h3>
			<?php endif; ?>
		    <div class="box_content">
		    	<div class="row">
		    		<div class="col-md-12">
		    			<div class="p_10">
				    		<?php 
				    			$tmp = $total_percent.'%'; 
				    		 	echo sprintf(__d('profile_completion', '%s Profile Completeness'), $tmp); 
				    		?>
				    	</div>
						<div class="p_10">
							<div class="progress">
							  	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $total_percent;?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $total_percent;?>%">
							    	<span class="sr-only"><?php echo $total_percent.__d('profile_completion', '% Complete');?></span>
							  	</div>
							</div>
						</div>
						<?php if($total_percent != 100): ?>
							<div class="p_10">
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
							<div class="p_10">
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
		</div>

	
	<?php endif; ?>
<?php 
}
?>

<?php 
	$remaining_bar_color 	= Configure::read('ProfileCompletion.remaining_bar_color');
	$progress_bar_color 	= Configure::read('ProfileCompletion.progress_bar_color');

	if(empty($remaining_bar_color)) $remaining_bar_color = '#ccc';
	if(empty($progress_bar_color)) $progress_bar_color = '#d8601f';
 ?>

<style type="text/css" media="screen">
	.progress{
		margin-bottom: 0;
		background-color: <?php echo $remaining_bar_color;?>;
	}
	.progress-bar{
		background-color: <?php echo $progress_bar_color;?>;
	}
</style>