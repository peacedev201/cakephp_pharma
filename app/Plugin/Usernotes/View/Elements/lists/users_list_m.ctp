<div class="bar-content">
    <div class="content_center full_content p_m_10">
        
        <div class="mo_breadcrumb">
            <h1><?php echo __d('usernotes','My notes') ?></h1>
        </div>

        <ul class="users_list" id="list-content">
            <?php
            if (!empty($values) || !empty($online_filter))
                echo __d('usernotes','Loading...');
            else
                echo $this->element('Usernotes.lists/users_list', array('more_url' => '/usernotess/ajax_browse/all/page:2'));
            ?>
        </ul>
        <div class="clear"></div>
    </div>
</div>