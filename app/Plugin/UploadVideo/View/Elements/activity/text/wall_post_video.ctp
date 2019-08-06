<?php
$subject = MooCore::getInstance()->getItemByType($activity['Activity']['type'], $activity['Activity']['target_id']);
$name = key($subject);
?>

<?php 
$bFeeling = false;
if (Configure::check('Feeling.feeling_enabled') && Configure::read('Feeling.feeling_enabled')): 
    $oFeelingActivityModel = MooCore::getInstance()->getModel('Feeling.FeelingActivity');
    $aFeeling = $oFeelingActivityModel->get_felling($activity['Activity']);
    if(!empty($aFeeling)):
        $oCategoryModel = MooCore::getInstance()->getModel('Feeling.FeelingCategory');
        $aCategory = $oCategoryModel->findById($aFeeling['Feeling']['category_id']);
        if (!empty($aCategory)) :
            $bFeeling = true;
        endif;
    endif; 
endif; 
?>

<?php if ($activity['Activity']['target_id']): ?>
    <?php $show_subject = MooCore::getInstance()->checkShowSubjectActivity($subject); ?>
    <?php if ($show_subject): ?>
        &rsaquo; <a href="<?php echo $subject[$name]['moo_href'] ?>"><?php echo h($subject[$name]['moo_title']) ?></a>
    <?php elseif (!$bFeeling): ?>
        <?php echo __('shared a new video'); ?>
    <?php endif; ?>
<?php elseif (!$bFeeling): ?>
    <?php echo __('shared a new video'); ?>
<?php endif; ?>

