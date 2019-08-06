<?php if ($this->request->is('ajax')) $this->setCurrentStyle(4) ?>


<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","webChat"], function($, webChat) {
            webChat.markAllMessagesAsRead();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('webChat'), 'object' => array( 'webChat'))); ?>
        webChat.markAllMessagesAsRead();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<div class="content_center_home">
    <div class="mo_breadcrumb">
        <h1><?php echo __('Conversations') ?></h1>
        <?php
        echo $this->Html->link(__('Mark All As Read'),
            array("controller" => "conversations",
                "action" => "mark_all_read",
                "plugin" => false,
                "?"=>"app_no_tab=1"
            ),
            array(
                'class' => 'topButton button button-action button-mobi-top btn_mark_all_read',
                "data-user_id" => MooCore::getInstance()->getViewer(true)
            )
        );
        ?>
    </div>
    <ul class="list6 comment_wrapper conversation_list" id="list-content">
        <?php echo $this->element( 'Chat.messages_list', array( 'more_url' => '/conversations/ajax_browse/page:2' ) ); ?>

    </ul>
</div>