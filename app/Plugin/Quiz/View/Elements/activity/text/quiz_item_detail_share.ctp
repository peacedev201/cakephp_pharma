<?php 
if (isset($activity['Activity']['parent_id']) && $activity['Activity']['parent_id']):
    $oQuizModel = MooCore::getInstance()->getModel('Quiz.Quiz');
    $aQuiz = $oQuizModel->findById($activity['Activity']['parent_id']);
    echo __d('quiz', "shared %s's <a href='%s'>quiz</a>", "<a href=" . $aQuiz['User']['moo_href'] . "> ". $aQuiz['User']['name'] ."</a>", $aQuiz['Quiz']['moo_href']);
endif; 
?>
    
<?php if ($activity['Activity']['target_id']): ?>
    <?php
    $subject = MooCore::getInstance()->getItemByType($activity['Activity']['type'], $activity['Activity']['target_id']);
    list($plugin, $name) = mooPluginSplit($activity['Activity']['type']);
    $show_subject = MooCore::getInstance()->checkShowSubjectActivity($subject);
    ?>
    <?php if ($show_subject): ?>
    &rsaquo; <a href="<?php echo $subject[$name]['moo_href']; ?>"><?php echo h($subject[$name]['moo_title']); ?></a>
    <?php endif; ?>
<?php endif; ?>