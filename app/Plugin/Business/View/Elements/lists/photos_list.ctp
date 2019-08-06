<?php
    $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
?>
<?php if ($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery", "mooBehavior", "mooBusiness"], function($, mooBehavior, mooBusiness) {
        mooBehavior.initMoreResults();
    });
</script>
<?php endif?>
<?php if($photos != null):?>
	<?php foreach ($photos as $k => $photo):
        $photo = !empty($photo['BusinessPhoto']) ? $photo['BusinessPhoto'] : $photo;
    ?>
        <li class="photoItem" >
            <div class="p_2">
                <a class="layer_square photoModal" title="<?php echo $photo['caption'];?>" href="<?php echo $this->request->base?>/photos/view/<?php echo $photo['id']?>">
                    <img src="<?php echo $photoHelper->getImage(array('Photo' => $photo), array('prefix' => '150_square')); ?>" alt="" />
                </a>
            </div>
        </li>
    <?php endforeach; ?>
    <?php if(!empty($more_photo_url) && count($photos) == Configure::read('Business.business_photo_per_page')):?>
        <?php $this->Html->viewMore($more_photo_url, 'photos-content') ?>
    <?php endif;?>
<?php else:?>
	<?php echo '<div class="clear text-center">' . __d('business', 'No more results found') . '</div>';?>
<?php endif;?>