<?php $helper = MooCore::getInstance()->getHelper('Contest_Contest'); ?>
<?php if (!empty($candidates) && count($candidates) > 0) : ?>
    <?php foreach ($candidates as $user): ?>
    <li class="user-list-index">
        <div class="list-content">
            <div class="user-idx-item">
                <a href="<?php echo $this->request->base ?>/<?php echo (!empty($user['User']['username'])) ? '-' . $user['User']['username'] : 'users/view/' . $user['User']['id'] ?>"><?php echo $this->Moo->getItemPhoto(array('User' => $user['User']), array('prefix' => '200_square')) ?></a>
            </div>
            <div class="user-list-info">
                <div class="user-name-info">
                    <?php echo $this->Moo->getName($user['User']) ?>
                </div>
            </div>
        </div>
        <?php $this->Html->rating($user['User']['id'], 'users'); ?>
    </li>
    <?php endforeach; ?>
    <?php if (isset($more_url) && $is_more_url): ?>
        <script> var searchParams = <?php echo (isset($params)) ? json_encode($params) : 'false'; ?>;</script>
        <?php $this->Html->viewMore($more_url, 'list-content') ?>
    <?php endif; ?>
<?php else: ?>
    <div class="clear text-center"><?php echo __d('contest', 'No results found'); ?></div>
<?php
endif;


