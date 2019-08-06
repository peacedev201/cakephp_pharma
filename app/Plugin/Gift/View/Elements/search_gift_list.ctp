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
<?php
if ($gifts != null):
$giftHelper = MooCore::getInstance()->getHelper('Gift_Gift');
?>
    <ul class="group-content-list">
        <?php
            foreach ($gifts as $gift):
                $user = $gift['User'];
                $giftCategory = $gift['GiftCategory'];
                $gift = $gift['Gift'];
        ?> 
            <li style="border-bottom:0" class="full_content p_m_10">
                <a href="<?php echo $gift['moo_href'];?>">
                    <img class="group-thumb" src="<?php echo $giftHelper->getImage($gift, array('prefix' => 'thumb'))?>">
                </a>
                <div class="group-info">
                    <a href="<?php echo $gift['moo_href'];?>" class="title">
                        <b><?php echo $gift['title'];?></b>
                    </a>
                    <div class="extra_info">
                        <?php echo $gift['price'];?> <?php echo __d('gift', 'Credits');?>
                    </div>
                    <div class="list-item-description">
                        <?php echo $gift['message'];?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php if (count($gifts) > 0 && !empty($more_url)): ?>
        <?php $this->Html->viewMore($more_url) ?>
    <?php endif; ?>
<?php else: ?> 
    <?php echo '<div class="clear" align="center">' . __d('gift', 'No more results found') . '</div>'; ?>
<?php endif; ?>
