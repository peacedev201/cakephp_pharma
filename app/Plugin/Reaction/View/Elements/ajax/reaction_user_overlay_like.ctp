<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>
    <div class="title-modal-reaction">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
            <li class="<?php if ($active_reaction == REACTION_ALL): ?>active<?php endif; ?><?php if (!$showTabAll): ?> hidden<?php endif; ?>"><a class="reaction-tab react-active-all<?php if($active_reaction == -1): ?> noajax<?php endif; ?>" data-ajax="<?php echo $this->request->base.'/reactions/ajax_show_more/'.$type.'/'.$id.'/'.REACTION_ALL; ?>" data-page="1" data-reaction="all" href="#tabAllLike" data-toggle="tab" data-title="<?php echo __d('reaction', 'All'); ?>"><?php echo $reaction['total_count']; ?></a></li>
            <li class="<?php if ($active_reaction == REACTION_LIKE): ?>active<?php endif; ?><?php if ($reaction['like_count'] == 0): ?> hidden<?php endif; ?>"><a class="reaction-tab react-active-like<?php if ($active_reaction == REACTION_LIKE): ?> noajax<?php endif; ?>" data-reaction="like" data-ajax="<?php echo $this->request->base.'/reactions/ajax_show_more/'.$type.'/'.$id.'/'.REACTION_LIKE; ?>" data-page="1" href="#tabLike" data-toggle="tab" data-title="<?php echo __d('reaction', 'Like'); ?>"><?php echo $reaction['like_count']; ?></a></li>
            <li class="<?php if ($active_reaction == REACTION_LOVE): ?>active<?php endif; ?><?php if ($reaction['love_count'] == 0): ?> hidden<?php endif; ?>"><a class="reaction-tab react-active-love<?php if ($active_reaction == REACTION_LOVE): ?> noajax<?php endif; ?>" data-reaction="love" data-ajax="<?php echo $this->request->base.'/reactions/ajax_show_more/'.$type.'/'.$id.'/'.REACTION_LOVE; ?>" data-page="1" href="#tabLove" data-toggle="tab" data-title="<?php echo __d('reaction', 'Love'); ?>"><?php echo $reaction['love_count']; ?></a></li>
            <li class="<?php if ($active_reaction == REACTION_HAHA): ?>active<?php endif; ?><?php if ($reaction['haha_count'] == 0): ?> hidden<?php endif; ?>"><a class="reaction-tab react-active-haha<?php if ($active_reaction == REACTION_HAHA): ?> noajax<?php endif; ?>" data-reaction="haha" data-ajax="<?php echo $this->request->base.'/reactions/ajax_show_more/'.$type.'/'.$id.'/'.REACTION_HAHA; ?>" data-page="1" href="#tabHaha" data-toggle="tab" data-title="<?php echo __d('reaction', 'Haha'); ?>"><?php echo $reaction['haha_count']; ?></a></li>
            <li class="<?php if ($active_reaction == REACTION_WOW): ?>active<?php endif; ?><?php if ($reaction['wow_count'] == 0): ?> hidden<?php endif; ?>"><a class="reaction-tab react-active-wow<?php if ($active_reaction == REACTION_WOW): ?> noajax<?php endif; ?>" data-reaction="wow" data-ajax="<?php echo $this->request->base.'/reactions/ajax_show_more/'.$type.'/'.$id.'/'.REACTION_WOW; ?>" data-page="1" href="#tabWow" data-toggle="tab" data-title="<?php echo __d('reaction', 'Wow'); ?>"><?php echo $reaction['wow_count']; ?></a></li>
            <li class="<?php if ($active_reaction == REACTION_COOL): ?>active<?php endif; ?><?php if ($reaction['cool_count'] == 0): ?> hidden<?php endif; ?>"><a class="reaction-tab react-active-cool<?php if ($active_reaction == REACTION_COOL): ?> noajax<?php endif; ?>" data-reaction="cool" data-ajax="<?php echo $this->request->base.'/reactions/ajax_show_more/'.$type.'/'.$id.'/'.REACTION_COOL; ?>" data-page="1" href="#tabCool" data-toggle="tab" data-title="<?php echo __d('reaction', 'Cool'); ?>"><?php echo $reaction['cool_count']; ?></a></li>
            <li class="<?php if ($active_reaction == REACTION_CONFUSED): ?>active<?php endif; ?><?php if ($reaction['confused_count'] == 0): ?> hidden<?php endif; ?>"><a class="reaction-tab react-active-confused<?php if ($active_reaction == REACTION_CONFUSED): ?> noajax<?php endif; ?>" data-reaction="confused" data-ajax="<?php echo $this->request->base.'/reactions/ajax_show_more/'.$type.'/'.$id.'/'.REACTION_CONFUSED; ?>" data-page="1" href="#tabConfused" data-toggle="tab" data-title="<?php echo __d('reaction', 'Confused'); ?>"><?php echo $reaction['confused_count']; ?></a></li>
            <li class="<?php if ($active_reaction == REACTION_SAD): ?>active<?php endif; ?><?php if ($reaction['sad_count'] == 0): ?> hidden<?php endif; ?>"><a class="reaction-tab react-active-sad<?php if ($active_reaction == REACTION_SAD): ?> noajax<?php endif; ?>" data-reaction="sad" data-ajax="<?php echo $this->request->base.'/reactions/ajax_show_more/'.$type.'/'.$id.'/'.REACTION_SAD; ?>" data-page="1" href="#tabSad" data-toggle="tab" data-title="<?php echo __d('reaction', 'Sad'); ?>"><?php echo $reaction['sad_count']; ?></a></li>
            <li class="<?php if ($active_reaction == REACTION_ANGRY): ?>active<?php endif; ?><?php if ($reaction['angry_count'] == 0): ?> hidden<?php endif; ?>"><a class="reaction-tab react-active-angry<?php if ($active_reaction == REACTION_ANGRY): ?> noajax<?php endif; ?>" data-reaction="angry" data-ajax="<?php echo $this->request->base.'/reactions/ajax_show_more/'.$type.'/'.$id.'/'.REACTION_ANGRY; ?>" data-page="1" href="#tabAngry" data-toggle="tab" data-title="<?php echo __d('reaction', 'Angry'); ?>"><?php echo $reaction['angry_count']; ?></a></li>
        </ul>
        <?php if(!$isApp): ?>
        <button type="button" class="close" data-dismiss="modal" style="display: block;"><span aria-hidden="true">&times;</span></button>
        <?php endif; ?>
    </div>
<div class="modal-body">

    <!-- Tab panes -->
    <div class="tab-content">

        <div class="tab-pane<?php if ($active_reaction == REACTION_ALL): ?> active<?php endif; ?>" id="tabAllLike">

            <ul class="list1 users_list user-like" id="list-reaction-all">
                <?php if ($active_reaction == -1): ?>
                    <?php echo $this->element('lists/users_list_bit'); ?>
                    <?php if (count($users) + ($page - 1) * $limit < $count):?>
                        <?php $this->Html->viewMore($more_url,'list-reaction-all') ?>
                        <script>
                            require(["mooBehavior"], function(mooBehavior) {
                                mooBehavior.initMoreResultsPopup();
                            });
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>

        <div class="tab-pane<?php if ($active_reaction == REACTION_LIKE): ?> active<?php endif; ?>" id="tabLike">
            <ul class="list1 users_list user-like" id="list-reaction-<?php echo REACTION_LIKE; ?>">
                <?php if ($active_reaction == REACTION_LIKE): ?>
                    <?php echo $this->element('lists/users_list_bit'); ?>
                    <?php if (count($users) + ($page - 1) * RESULTS_LIMIT < $count):?>
                        <?php $this->Html->viewMore($more_url,'list-reaction-'.REACTION_LIKE) ?>
                        <script>
                            require(["mooBehavior"], function(mooBehavior) {
                                mooBehavior.initMoreResultsPopup();
                            });
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="tab-pane<?php if ($active_reaction == REACTION_LOVE): ?> active<?php endif; ?>" id="tabLove">
            <ul class="list1 users_list user-like" id="list-reaction-<?php echo REACTION_LOVE ?>">
                <?php if ($active_reaction == REACTION_LOVE): ?>
                    <?php echo $this->element('lists/users_list_bit'); ?>
                    <?php if (count($users) + ($page - 1) * RESULTS_LIMIT < $count):?>
                        <?php $this->Html->viewMore($more_url,'list-reaction-'.REACTION_LOVE) ?>
                        <script>
                            require(["mooBehavior"], function(mooBehavior) {
                                mooBehavior.initMoreResultsPopup();
                            });
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="tab-pane<?php if ($active_reaction == REACTION_HAHA): ?> active<?php endif; ?>" id="tabHaha">
            <ul class="list1 users_list user-like" id="list-reaction-<?php echo REACTION_HAHA ?>">
                <?php if ($active_reaction == REACTION_HAHA): ?>
                    <?php echo $this->element('lists/users_list_bit'); ?>
                    <?php if (count($users) + ($page - 1) * RESULTS_LIMIT < $count):?>
                        <?php $this->Html->viewMore($more_url,'list-reaction-'.REACTION_HAHA) ?>
                        <script>
                            require(["mooBehavior"], function(mooBehavior) {
                                mooBehavior.initMoreResultsPopup();
                            });
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="tab-pane<?php if ($active_reaction == REACTION_WOW): ?> active<?php endif; ?>" id="tabWow">
            <ul class="list1 users_list user-like" id="list-reaction-<?php echo REACTION_WOW ?>">
                <?php if ($active_reaction == REACTION_WOW): ?>
                    <?php echo $this->element('lists/users_list_bit'); ?>
                    <?php if (count($users) + ($page - 1) * RESULTS_LIMIT < $count):?>
                        <?php $this->Html->viewMore($more_url,'list-reaction-'.REACTION_WOW) ?>
                        <script>
                            require(["mooBehavior"], function(mooBehavior) {
                                mooBehavior.initMoreResultsPopup();
                            });
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="tab-pane<?php if ($active_reaction == REACTION_COOL): ?> active<?php endif; ?>" id="tabCool">
            <ul class="list1 users_list user-like" id="list-reaction-<?php echo REACTION_COOL ?>">
                <?php if ($active_reaction == REACTION_COOL): ?>
                    <?php echo $this->element('lists/users_list_bit'); ?>
                    <?php if (count($users) + ($page - 1) * RESULTS_LIMIT < $count):?>
                        <?php $this->Html->viewMore($more_url,'list-reaction-'.REACTION_COOL) ?>
                        <script>
                            require(["mooBehavior"], function(mooBehavior) {
                                mooBehavior.initMoreResultsPopup();
                            });
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="tab-pane<?php if ($active_reaction == REACTION_CONFUSED): ?> active<?php endif; ?>" id="tabConfused">
            <ul class="list1 users_list user-like" id="list-reaction-<?php echo REACTION_CONFUSED ?>">
                <?php if ($active_reaction == REACTION_CONFUSED): ?>
                    <?php echo $this->element('lists/users_list_bit'); ?>
                    <?php if (count($users) + ($page - 1) * RESULTS_LIMIT < $count):?>
                        <?php $this->Html->viewMore($more_url,'list-reaction-'.REACTION_CONFUSED) ?>
                        <script>
                            require(["mooBehavior"], function(mooBehavior) {
                                mooBehavior.initMoreResultsPopup();
                            });
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="tab-pane<?php if ($active_reaction == REACTION_SAD): ?> active<?php endif; ?>" id="tabSad">
            <ul class="list1 users_list user-like" id="list-reaction-<?php echo REACTION_SAD ?>">
                <?php if ($active_reaction == REACTION_SAD): ?>
                    <?php echo $this->element('lists/users_list_bit'); ?>
                    <?php if (count($users) + ($page - 1) * RESULTS_LIMIT < $count):?>
                        <?php $this->Html->viewMore($more_url,'list-reaction-'.REACTION_SAD) ?>
                        <script>
                            require(["mooBehavior"], function(mooBehavior) {
                                mooBehavior.initMoreResultsPopup();
                            });
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="tab-pane<?php if ($active_reaction == REACTION_ANGRY): ?> active<?php endif; ?>" id="tabAngry">
            <ul class="list1 users_list user-like" id="list-reaction-<?php echo REACTION_ANGRY ?>">
                <?php if ($active_reaction == REACTION_ANGRY): ?>
                    <?php echo $this->element('lists/users_list_bit'); ?>
                    <?php if (count($users) + ($page - 1) * RESULTS_LIMIT < $count):?>
                        <?php $this->Html->viewMore($more_url,'list-reaction-'.REACTION_ANGRY) ?>
                        <?php if($this->request->is('ajax')): ?>
                        <script>
                            require(["mooBehavior"], function(mooBehavior) {
                                mooBehavior.initMoreResultsPopup();
                            });
                        </script>
                        <?php else: ?>
                            <?php $this->Html->scriptStart(array('inline' => false,'requires'=>array('mooBehavior'),'object'=>array('mooBehavior'))); ?>

                            <?php $this->Html->scriptEnd(); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    $('a.reaction-tab[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var element = $(e.target);
        var data = element.data();
        var url = data.ajax;
        var contentId = element.attr('href');

        if(!element.hasClass('noajax')){
            $.get(mooConfig.url.full + url, { noCache: 1 }, function(dataHtml){
                element.addClass('noajax');
                console.log('contentId', contentId);
                $(contentId).find('ul.users_list').html(dataHtml);
            }, 'html');
        }
    });
</script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false,'requires'=>array('jquery'),'object'=>array('$'))); ?>
    $('a.reaction-tab[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var element = $(e.target);
        var data = element.data();
        var url = data.ajax;
        var contentId = element.attr('href');

        if(!element.hasClass('noajax')){
            $.get(mooConfig.url.full + url, { noCache: 1 }, function(dataHtml){
                element.addClass('noajax');
                console.log('contentId', contentId);
                $(contentId).find('ul.users_list').html(dataHtml);
            }, 'html');
        }
    });
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>