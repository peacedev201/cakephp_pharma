<div id="north" class="bar-content">
    <div class="box2">
        <div class="box_content">
            <div class="job_breadcrumb">
                <h1><?php echo __d('friend_inviter', 'Create Your Network') ?></h1>
                <ul class="">
                    <li class="current" id="browse_all">
                        <a href="<?php echo $this->request->base ?>/friend_inviters"><?php echo __('Home') ?></a>
                    </li>
                    <li>
                        <a href="<?php echo $this->request->base ?>/friend_inviter/friend_inviters/pending"><?php echo __d('friend_inviter', 'Pending Invites') ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="bar-content">
    <div class="content_center">

        <div class="mo_breadcrumb"></div>
        <div class="full_content p_m_10">
            <div class="page-des">
                <?php echo __d('friend_inviter', "You can use any of the tools on this page to find and connect with more friends."); ?>
            </div>

            <div id="id_show_networkcontacts" style="display:block;margin-top: 20px;"  class="suggestion_inviter">
                <div id="import_form">
                    <?php
                    if (isset($ers)) {
                        foreach ($ers as $key => $value) {
                            echo "<div><ul class='form-errors'><li><ul class='errors'><li>" . __d('friend_inviter', $value) . "</li></ul></li></ul></div>";
                        }
                    }
                    ?>
                    <div class="provider_list">
                        <?php foreach ($providers as $p => $provider): ?>
                            <div>

                                <a class="logo_item smoothbox usingapi <?php echo $provider['title'] ?>"  title="<?php echo $provider['title'] ?>" href="<?php echo $this->request->base ?>/friend_inviters/getcontacts?provider=<?php echo $p ?>">
                                    <img src='<?php echo $this->request->webroot ?>friend_inviter/img/<?php echo $provider['logo'] ?>.jpg'>
                                </a>
                                <span class="title"><?php echo __d('friend_inviter', $provider['title']) ?></span>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="sub-txt">
                    <?php echo __d('friend_inviter', "Click on one of the above services to search from your Web Account."); ?>
                    <br />
                    <i class="icon-lock"></i>
                    <?php echo __d('friend_inviter', "We will not store your account information."); ?>
                </div>
            </div>

            <div class="provider_list" style="min-height: 60px;margin-top: 25px;">
                <div>
                    <a class="" href="<?php echo $this->request->base ?>/friend_inviters/invite_status/3" id="do_it_later">
                        <?php echo __d('friend_inviter', 'Do it later!') ?>
                    </a>
                </div> 
                <div>
                    <a class="" href="<?php echo $this->request->base ?>/friend_inviters/invite_status/2" id="dont_show_page">
                        <?php echo __d('friend_inviter', 'Don’t show this page anymore!') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->Html->css(array('FriendInviter.fi'), array('block' => 'css')); ?>