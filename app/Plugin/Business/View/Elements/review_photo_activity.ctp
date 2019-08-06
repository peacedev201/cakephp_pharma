<?php if (isset($photos) && count($photos)):
    $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
?>
	<?php $c = count($photos);?>            
    <div class="activity_content p_photos photo_addlist">
	    <?php if($c == 1): ?>
	        <?php foreach ( $photos as $key => $photo ): ?>
	            <div class="div_single comment_thumb">
                            
	                    <a href="<?php echo $photoHelper->getImage($photo, array());?>" data-dismiss="modal">
                            <img class="single_img" src="<?php echo $photoHelper->getImage($photo, array('prefix' => '850'));?>" alt="" style="border: none" />
	                    </a>	   
	               
	            </div>					
	        <?php endforeach; ?>
	    <?php elseif ($c==2): ?>
	        <?php foreach ( $photos as $key => $photo ): ?>
	            <div class="col-xs-6 photoAdd2File">
	                <div class="p_2 comment_thumb">
	                    <a class="layer_square" data-dismiss="modal" style="background-image:url(<?php echo $photoHelper->getImage($photo, array('prefix' => '450'));?>);" href="<?php echo $photoHelper->getImage($photo, array());?>" ></a>
	                </div>
	            </div>					
	        <?php endforeach; ?>
	    <?php elseif ($c==3): ?>
	          <?php foreach ( $photos as $key => $photo ): ?>
	            <?php if($key == 0): ?>   
	            <div class="PE">
	                <div class="ej comment_thumb">
	                    <a class="layer_square" data-dismiss="modal" href="<?php echo $photoHelper->getImage($photo, array());?>" style="background-image:url(<?php echo $photoHelper->getImage($photo, array('prefix' => '850'));?>)" >
	                        
	                    </a>	   
	                </div>
	            </div>
	            <?php else: ?>
	                <?php if($key == 1): ?>
	                <div class="QE">
	                <?php endif; ?> 
	                    <div class="sp <?php if($key == 2): ?>eq<?php endif; ?> comment_thumb">
	                        <a class="layer_square" data-dismiss="modal" href="<?php echo $photoHelper->getImage($photo, array());?>" >
	                            <img src="<?php echo $photoHelper->getImage($photo, array('prefix' => '300_square'));?>" alt="" style="border: none" />
	                        </a>	   
	                    </div>
	                <?php if($key == 1): ?>
	                
	                <?php endif; ?>   
	            <?php endif; ?>
	        <?php endforeach; ?>  
	        </div>
	    <?php elseif ($c==4): ?>   
	        <?php foreach ( $photos as $key => $photo ): ?>
	           <?php if($key == 0): ?>   
	            <div class="PE">
	                <div class="ej1 comment_thumb">
                        <a href="<?php echo $photoHelper->getImage($photo, array());?>" data-dismiss="modal" style="background-image:url(<?php echo $photoHelper->getImage($photo, array('prefix' => '850'));?>)">
	                        
	                    </a>	   
	                </div>
	            </div>
	            <?php else: ?>
	                <?php if($key == 1): ?>
	                <div class="QE">
	                <?php endif; ?> 
	                    <div class="sp1 <?php if($key == 2): ?>eq1<?php endif; ?> comment_thumb">
                                 
	                        <a class="layer_square" data-dismiss="modal" href="<?php echo $photoHelper->getImage($photo, array());?>" >
	                            <?php if ($key == 3 && count($photos_total) > 4): ?>
                                    <div class="photo-add-more">
                                        <div>
                                            +<?php echo count($photos_total) - 4; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <img src="<?php echo $photoHelper->getImage($photo, array('prefix' => '300_square'));?>" alt="" style="border: none" />
	                        </a>	   
	                    </div>
	            <?php endif; ?>
	            
	        <?php endforeach; ?> 
	        </div>
	    <?php endif; ?>
	</div>
<?php endif;?>