<?php
    $business = $activity['Activity']['Target']['Business'];
?>
<?php echo __d('business', ' added a new business: ');?>
<a href="<?php echo $business['moo_href'];?>">
    <?php echo $business['name'];?>
</a>