<div id="filters" class="margin_top_content">
    <?php if (!Configure::read('core.guest_search') && empty($uid)): ?>
    <?php else: ?>
        <?php echo $this->Form->text('keyword', array('placeholder' => __d('quiz', 'Enter keyword to search'), 'rel' => 'quizzes', 'class' => 'json-view')); ?>
    <?php endif; ?>
</div>