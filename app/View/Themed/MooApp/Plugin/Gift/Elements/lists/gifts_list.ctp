<?php if ($this->request->is('ajax')) $this->setCurrentStyle(4) ?>

<?php if (count($gifts) > 0): ?>
    <?php
      $giftHelper = MooCore::getInstance()->getHelper('Gift_Gift');
    foreach ($gifts as $key => $gift):
        $gift = $gift['Gift'];
        ?>
        <li class="gifts-item-m">
            <div class="list-content">
                <div class="user-idx-item">
                    <a class="title-gift" href="<?php echo $gift['moo_href']; ?>">
                        <?php echo $this->Text->truncate(h($gift['title']), 50, array('eclipse' => '...')) ?>
                    </a>
                    <div class="count-credits">
                        <?php if($gift['price'] > 0):?>
                            <?php echo round($gift['price'],2);?> <?php echo __d('gift', 'Credits'); ?>
                        <?php else:?>
                            <?php echo __d('gift', 'Free'); ?>
                        <?php endif;?>
                    </div>
                    <a class="gift-img" prefix="200_square" href="<?php echo $gift['moo_href']; ?>">
                        <img src="<?php echo $giftHelper->getImage($gift, array('prefix' => 'thumb'))?>" />
                    </a>
                    
                </div>
                <?php if($gift['type'] == GIFT_TYPE_AUDIO):?>
                    <i class="icon-gift gift-audio"></i>
                    <?php elseif($gift['type'] == GIFT_TYPE_VIDEO):?>
                    <i class="icon-gift gift-video"></i>
                    <?php endif;?>
            </div>
        </li>
    <?php endforeach ?>
<?php else: ?>
    <?php echo '<div align="center">' . __d('gift', 'No more results found') . '</div>' ?>
<?php endif ?>

<?php if (count($gifts) >= Configure::read('Gift.gift_items_per_page')): ?>
    <!--<div class="view-more">
        <a href="javascript:void(0)" onclick="moreResults('<?php echo $more_url ?>', 'list-content', this)"><?php echo __d('gift', 'Load More') ?></a>
    </div>-->
    <?php $this->Html->viewMore($more_url,'list-content') ?>
<?php endif; ?>

<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","mooBehavior"], function($, mooBehavior) {
            mooBehavior.initMoreResults();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooBehavior'), 'object' => array('$', 'mooBehavior'))); ?>
    mooBehavior.initMoreResults();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
