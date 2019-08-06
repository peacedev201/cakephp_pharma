<?php $giftHelper = MooCore::getInstance()->getHelper('Gift_Gift');?>
<div class="box2">
    <h3><?php echo __d('gift', 'Popular Gifts');?></h3>
    <div class="box_content">
        <ul class="event_block_list">
            <?php foreach($gifts as $gift):
                $gift_category = $gift["GiftCategory"];
                $gift = $gift["Gift"];
            ?>
                <li>
                    <a href="<?php echo $gift['moo_href']; ?>" class="event_thumb">                                
                        <img width="75px" src="<?php echo $giftHelper->getImage($gift, array('prefix' => 'thumb'))?>" />
                    </a>
                    <div class="event_info">
                        <a href="<?php echo $gift['moo_href']; ?>" class="title">
                            <?php echo $this->Text->truncate(h($gift['title']), 50, array('eclipse' => '...')) ?>                                
                        </a>
                        <div>
                            <?php echo __d('gift', 'Category');?>: 
                            <a href="<?php echo $this->request->base.'/gifts/index/cat/' . $gift_category['id'] . '/' . seoUrl($gift_category['name']); ?>">
                                <?php echo $gift_category['name'];?>
                            </a>
                        </div>
                        <div>
                            <?php if($gift['price'] > 0):?>
                                <?php echo $gift['price'];?> <?php echo __d('gift', 'Credits'); ?>
                            <?php else:?>
                                <?php echo __d('gift', 'Free'); ?>
                            <?php endif;?>
                        </div>
                    </div>
                </li>
            <?php endforeach;?>
        </ul>
    </div>
</div>