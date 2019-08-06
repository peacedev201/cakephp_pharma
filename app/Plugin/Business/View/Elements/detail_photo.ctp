<?php 
    $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
    $c = count($photos); 
?>
<div class="list4 activity_content p_photos photo_addlist">
    
    <?php if($c == 1): ?>
        <?php foreach ( $photos as $key => $photo ): 
            $photo = $photo['BusinessPhoto'];
        ?>
            <div class="div_single">
                
                        <a class="layer_square photoModal" href="<?php echo $this->request->base?>/photos/view/<?php echo $photo['id']?>">
                        <img class="single_img" src="<?php echo $photoHelper->getImage(array('Photo' => $photo), array('prefix' => '300_square')); ?>" alt="" />
                    </a>	   
               
            </div>					
        <?php endforeach; ?>
    <?php elseif ($c==2): ?>
        <?php foreach ( $photos as $key => $photo ): 
            $photo = $photo['BusinessPhoto'];
        ?>
            <div class="col-xs-6">
                <div class="p_2">
                    <a class="layer_square photoModal" href="<?php echo $this->request->base?>/photos/view/<?php echo $photo['id']?>">
                        <img src="<?php echo $photoHelper->getImage(array('Photo' => $photo), array('prefix' => '300_square')); ?>" alt="" />
                    </a>
                </div>
            </div>					
        <?php endforeach; ?>
    <?php elseif ($c==3): ?>
          <?php foreach ( $photos as $key => $photo ): 
              $photo = $photo['BusinessPhoto'];
            ?>
            <?php if($key == 0): ?>   
            <div class="PE">
                <div class="ej">
                    <a class="layer_square photoModal" href="<?php echo $this->request->base?>/photos/view/<?php echo $photo['id']?>">
                        <img src="<?php echo $photoHelper->getImage(array('Photo' => $photo), array('prefix' => '300_square')); ?>" alt="" />
                    </a>	   
                </div>
            </div>
            <?php else: ?>
                <?php if($key == 1): ?>
                <div class="QE">
                <?php endif; ?> 
                    <div class="sp <?php if($key == 2): ?>eq<?php endif; ?>">
                        <a class="layer_square photoModal" href="<?php echo $this->request->base?>/photos/view/<?php echo $photo['id']?>">
                            <img src="<?php echo $photoHelper->getImage(array('Photo' => $photo), array('prefix' => '150_square')); ?>" alt="" />
                        </a>	   
                    </div>
                <?php if($key == 1): ?>
                
                <?php endif; ?>   
            <?php endif; ?>
        <?php endforeach; ?>  
        </div>
    <?php elseif ($c>=4): ?>   
        <?php foreach ( $photos as $key => $photo ): 
            $photo = $photo['BusinessPhoto'];
        ?>
           <?php if($key == 0): ?>   
            <div class="PE">
                <div class="ej1">
                    <a class="layer_square photoModal" href="<?php echo $this->request->base?>/photos/view/<?php echo $photo['id']?>">
                        <img src="<?php echo $photoHelper->getImage(array('Photo' => $photo), array('prefix' => '300_square')); ?>" alt="" />
                    </a>	   
                </div>
            </div>
            <?php elseif($key < 4):?>
                <?php if($key == 1): ?>
                <div class="QE">
                <?php endif; ?> 
                    <div class="sp1 <?php if($key == 2): ?>eq1<?php endif; ?>">
                        <a class="layer_square photoModal" href="<?php echo $this->request->base?>/photos/view/<?php echo $photo['id']?>">
                            <?php if ($key == 3 && count($photos) > 4): ?>
                                <div class="photo-add-more">
                                    <div>
                                        +<?php echo count($photos) - 4; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <img src="<?php echo $photoHelper->getImage(array('Photo' => $photo), array('prefix' => '150_square')); ?>" alt="" />
                        </a>	   
                    </div>
            <?php endif; ?>
            
        <?php endforeach; ?> 
        </div>
    <?php endif; ?>
</div>
<div class="clear"></div>