<ul class="list2 quiz-menu-list browseQuizzes">
    <li id="browse_all"<?php if (empty($cat_id)): ?> class="current"<?php endif; ?>>
        <a data-url="<?php echo $this->request->base . '/quizzes/browse/all'; ?>" href="<?php echo $this->request->base . '/quizzes'; ?>"><?php echo __d('quiz', 'All Quizzes'); ?></a>
    </li>
    <?php if (!empty($uid)): ?>
    <li id="my_quizzes"><a data-url="<?php echo $this->request->base . '/quizzes/browse/my'; ?>" href="<?php echo $this->request->base . '/quizzes'; ?>"><?php echo __d('quiz', 'My Quizzes'); ?></a></li>
    <li id="taken_quizzes"><a data-url="<?php echo $this->request->base . '/quizzes/browse/taken'; ?>" href="<?php echo $this->request->base . '/quizzes'; ?>"><?php echo __d('quiz', 'My Recent Taken Quizzes'); ?></a></li>
    <li id="friend_quizzes"><a data-url="<?php echo $this->request->base . '/quizzes/browse/friends'; ?>" href="<?php echo $this->request->base . '/quizzes'; ?>"><?php echo __d('quiz', "Friends' Quizzes"); ?></a></li>
    <?php endif; ?>
    <li class="separate"></li>
</ul>