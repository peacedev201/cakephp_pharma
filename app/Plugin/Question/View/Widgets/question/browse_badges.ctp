<?php
	$helper = MooCore::getInstance()->getHelper("Question_Question");
	$points = $helper->getListPoints();
	$badges = $helper->getAllBadges();
?>
<div class="bar-content">
    <div class="content_center">
    	<h3><?php echo __d("question","Badges");?></h3>
    	<div class="row points-system">
		   <div class="col-md-12">
		      <h4><?php echo __d("question","Points System");?></h4>
		   </div>
		   <div class="clearfix"></div>
		   <ul class="points-define">
		   	  <?php foreach ($points as $point):?>
		      <li>
		         <div>
		            <span class="points-count"><?php echo $point['point']?></span>
		            <span class="star">
						<i class="material-icons">star</i><br><?php echo $point['text'];?></span>
		         </div>
		      </li>
		      <?php endforeach;?>
		   </ul>
		</div>
		<?php if (count($badges)):?>
			<div class="row badges-system">
			   <div class="col-md-12">
			      <h4><?php echo __d("question","Badges System");?></h4>
			   </div>
			   <div class="clearfix"></div>
			   <?php foreach ($badges as $badge):?>
				   <div class="row">
					  	<div class="col-xs-3">
					  		<div class="user-badge" style="color:<?php echo $badge['QuestionBadge']['text_color']?>;background:<?php echo $badge['QuestionBadge']['background_color']?>;">
								<?php echo $badge['QuestionBadge']['name'];?>        
							</div>
							<div>
								<span class="points-count">
									<?php echo $badge['QuestionBadge']['point'];?>
								</span>
								<span class="star">
									<i class="material-icons">star</i>
									<br>
									<?php echo __d("question","Points require");?>
								</span>
							</div>
					  	</div>
					 	<div class="list_permission col-xs-6">
					 		<span><?php echo __d("question","You can");?>:</span>
					 		<ul>
						      	<?php foreach ($badge['permissions'] as $permission):?>
							      	<li>
							      		<?php if ($permission['check']):?>
							      			<i class="material-icons fa-check">check_circle</i>
							      		<?php else :?>
							      			<i class="material-icons fa-ban">highlight_off</i>
							      		<?php endif;?>
							         	<?php echo $permission['text']?>
							      	</li>
							  	<?php endforeach;?>
						  	</ul>
					 	</div>
				   </div>
			   <?php endforeach;?>
			</div>
		<?php endif;?>
    </div>
</div>