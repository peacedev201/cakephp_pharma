<?php
    $request_base = Router::getRequest()->base;
?>

<div id="<?php echo $element_prefix.'reaction_result_'. $element_id ?>" class="reaction-review <?php echo $class ?>">
<?php if(!empty($myReaction)): ?>
    <?php //if(Configure::read('Reaction.reaction_like_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_LIKE ?>" id="<?php echo $element_prefix . 'react-result-like-' . $element_id ?>" class="react-review react-active-like <?php echo (($myReaction['Reaction']['like_count'] == 0) ? ' react-see-hide' : '') ?>" data-target="#themeModal" data-toggle="modal" data-title="<?php echo __d('reaction', 'Like') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count"><?php echo $myReaction['Reaction']['like_count'] ?></span></a>
    <?php //endif; ?>
    <?php if(Configure::read('Reaction.reaction_love_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_LOVE ?>" id="<?php echo $element_prefix . 'react-result-love-' . $element_id ?>" class="react-review react-active-love<?php echo (($myReaction['Reaction']['love_count'] == 0) ? ' react-see-hide' : '') ?>" data-target="#themeModal" data-toggle="modal" data-title="<?php echo __d('reaction', 'Love') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count"><?php echo $myReaction['Reaction']['love_count'] ?></span></a>
    <?php endif; ?>
    <?php if(Configure::read('Reaction.reaction_haha_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_HAHA ?>" id="<?php echo $element_prefix . 'react-result-haha-' . $element_id ?>" class="react-review react-active-haha<?php echo (($myReaction['Reaction']['haha_count'] == 0) ? ' react-see-hide' : '') ?>" data-target="#themeModal" data-toggle="modal" data-title="<?php echo __d('reaction', 'Haha') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count"><?php echo $myReaction['Reaction']['haha_count'] ?></span></a>
    <?php endif; ?>
    <?php if(Configure::read('Reaction.reaction_wow_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_WOW ?>" id="<?php echo $element_prefix . 'react-result-wow-' . $element_id ?>" class="react-review react-active-wow<?php echo (($myReaction['Reaction']['wow_count'] == 0) ? ' react-see-hide' : '') ?>" data-target="#themeModal" data-toggle="modal" data-title="<?php echo __d('reaction', 'Wow') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count"><?php echo $myReaction['Reaction']['wow_count'] ?></span></a>
    <?php endif; ?>
    <?php if(Configure::read('Reaction.reaction_cool_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_COOL ?>" id="<?php echo $element_prefix . 'react-result-cool-' . $element_id ?>" class="react-review react-active-cool<?php echo (($myReaction['Reaction']['cool_count'] == 0) ? ' react-see-hide' : '') ?>" data-target="#themeModal" data-toggle="modal" data-title="<?php echo __d('reaction', 'Cool') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count"><?php echo $myReaction['Reaction']['cool_count'] ?></span></a>
    <?php endif; ?>
    <?php if(Configure::read('Reaction.reaction_confused_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_CONFUSED ?>" id="<?php echo $element_prefix . 'react-result-confused-' . $element_id ?>" class="react-review react-active-confused<?php echo (($myReaction['Reaction']['confused_count'] == 0) ? ' react-see-hide' : '') ?>" data-target="#themeModal" data-toggle="modal" data-title="'<?php echo __d('reaction', 'Confused') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count"><?php echo $myReaction['Reaction']['confused_count'] ?></span></a>
    <?php endif; ?>
    <?php if(Configure::read('Reaction.reaction_sad_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_SAD ?>" id="<?php echo $element_prefix . 'react-result-sad-' . $element_id ?>" class="react-review react-active-sad<?php echo (($myReaction['Reaction']['sad_count'] == 0) ? ' react-see-hide' : '') ?>" data-target="#themeModal" data-toggle="modal" data-title="sad" data-dismiss="modal" data-backdrop="true"><span class="react-result-count"><?php echo $myReaction['Reaction']['sad_count'] ?></span></a>
    <?php endif; ?>
    <?php if(Configure::read('Reaction.reaction_angry_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_ANGRY ?>" id="<?php echo $element_prefix . 'react-result-angry-' . $element_id ?>" class="react-review react-active-angry<?php echo (($myReaction['Reaction']['angry_count'] == 0) ? ' react-see-hide' : '') ?>" data-target="#themeModal" data-toggle="modal" data-title="angry" data-dismiss="modal" data-backdrop="true"><span class="react-result-count"><?php echo $myReaction['Reaction']['angry_count'] ?></span></a>
    <?php endif; ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/'.$data_type.'/'.$data_id.'/'.REACTION_ALL ?>" id="<?php echo $element_prefix.'react-result-total-'.$element_id ?>" class="react-count-all<?php echo (($myReaction['Reaction']['total_count'] == 0) ? ' react-see-hide': '') ?>" data-target="#themeModal" data-toggle="modal" data-title="'. __d('reaction', 'All Likes') .'" data-dismiss="modal" data-backdrop="true"><?php echo $myReaction['Reaction']['total_count'] ?></a>
<?php else: ?>
    <?php //if(Configure::read('Reaction.reaction_like_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_LIKE ?>" id="<?php echo $element_prefix . 'react-result-like-' . $element_id ?>" class="react-review react-active-like<?php echo (($myReaction['Reaction']['total_count'] == 0) ? ' react-see-hide' : '') ?>" data-target="#themeModal" data-toggle="modal" data-title="<?php echo __d('reaction', 'Like') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count"><?php echo $myReaction['Reaction']['total_count'] ?></span></a>
    <?php //endif; ?>
    <?php if(Configure::read('Reaction.reaction_love_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_LOVE ?>" id="<?php echo $element_prefix . 'react-result-love-' . $element_id ?>" class="react-review react-active-love react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="<?php echo __d('reaction', 'Love') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>
    <?php endif; ?>
    <?php if(Configure::read('Reaction.reaction_haha_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_HAHA ?>" id="<?php echo $element_prefix . 'react-result-haha-' . $element_id ?>" class="react-review react-active-haha react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="<?php echo __d('reaction', 'Haha') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>
    <?php endif; ?>
    <?php if(Configure::read('Reaction.reaction_wow_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_WOW ?>" id="<?php echo $element_prefix . 'react-result-wow-' . $element_id ?>" class="react-review react-active-wow react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="<?php echo __d('reaction', 'Wow') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>
    <?php endif; ?>
    <?php if(Configure::read('Reaction.reaction_cool_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_COOL ?>" id="<?php echo $element_prefix . 'react-result-cool-' . $element_id ?>" class="react-review react-active-cool react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="<?php echo __d('reaction', 'Cool') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>
    <?php endif; ?>
    <?php if(Configure::read('Reaction.reaction_confused_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_CONFUSED ?>" id="<?php echo $element_prefix . 'react-result-confused-' . $element_id ?>" class="react-review react-active-confused react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="<?php echo __d('reaction', 'Confused') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>
    <?php endif; ?>
    <?php if(Configure::read('Reaction.reaction_sad_enabled')):?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_SAD ?>" id="<?php echo $element_prefix . 'react-result-sad-' . $element_id ?>" class="react-review react-active-sad react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="<?php echo __d('reaction', 'Sad') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>
    <?php endif; ?>
    <?php if(Configure::read('Reaction.reaction_angry_enabled')): ?>
    <a href="<?php echo $request_base . '/reactions/ajax_show/' . $data_type . '/' . $data_id . '/' . REACTION_ANGRY ?>" id="<?php echo $element_prefix . 'react-result-angry-' . $element_id ?>" class="react-review react-active-angry react-see-hide" data-target="#themeModal" data-toggle="modal" data-title="<?php echo __d('reaction', 'Angry') ?>" data-dismiss="modal" data-backdrop="true"><span class="react-result-count">0</span></a>
    <?php endif; ?>
    <a href="<?php echo $request_base.'/reactions/ajax_show/'.$data_type.'/'.$data_id.'/'.REACTION_ALL?>" id="<?php echo $element_prefix.'react-result-total-'.$element_id ?>" class="react-count-all<?php echo (($myReaction['Reaction']['total_count'] == 0) ? ' react-see-hide': '') ?>" data-target="#themeModal" data-toggle="modal" data-title="<?php echo  __d('reaction', 'All Likes') ?>" data-dismiss="modal" data-backdrop="true"><?php echo $myReaction['Reaction']['total_count'] ?></a>
<?php endif; ?>
</div>