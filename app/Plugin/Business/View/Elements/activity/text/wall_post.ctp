<?php if($this->request->params['plugin'] != 'Business'):?>
    <?php
        $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $business = $businessHelper->getOnlyBusiness($activity['Activity']['target_id']);
        $business = $business['Business'];
        
        $subject = MooCore::getInstance()->getItemByType($activity['Activity']['type'], $activity['Activity']['target_id']);
        $show_subject = MooCore::getInstance()->checkShowSubjectActivity($subject);
    ?>
    <?php if ($show_subject):?>
        &rsaquo; <a href="<?php echo $business['moo_href'] ?>"><?php echo $business['name'] ?></a>
    <?php endif;?>
<?php endif;?>