<?php
	$ids = explode(',',$activity['Activity']['items']);
	$userModel = MooCore::getInstance()->getModel('User');	
	$users = $userModel->find( 'all', array( 'conditions' => array( 'User.id' => $ids ), 'limit'=>10
													 ) ); 
	$userModel->cacheQueries = false;
?>

<ul class="users_list" id="list-content">
    <?php if($this->request->is('ajax')): ?>
        <script type="text/javascript">
            require(["jquery","mooUser"], function($, mooUser) {
                mooUser.initOnUserList();

                $('.cancel_request').unbind('click');
                $('.cancel_request').click(function(e){
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: $(this).attr('href'),
                        success: function (data) {
                            location.reload();
                        }
                    });
                });
            });
        </script>
    <?php else: ?>
        <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooUser'), 'object' => array('$', 'mooUser'))); ?>
        mooUser.initOnUserList();

        $('.cancel_request').unbind('click');
        $('.cancel_request').click(function(e){
        e.preventDefault();
        $.ajax({
        type: 'POST',
        url: $(this).attr('href'),
        success: function (data) {
        location.reload();
        }
        });
        });
        <?php $this->Html->scriptEnd(); ?>
    <?php endif; ?>

    <?php
    echo $this->element('lists/users_list_bit', array('users' => $users, 'more_url' => '/users/ajax_browse/all/page:2'));
    ?>
</ul>