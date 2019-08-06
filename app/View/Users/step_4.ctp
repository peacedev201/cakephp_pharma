<div class="bar-content">
    <div class="content_center full_content p_m_10">
        <div class="mo_breadcrumb">
            <h1><?php echo __('Welcome to Pharmatalk!!')?></h1>
        </div>
        <p><?php echo __('Welcome to be a Member of Pharmtalk !');?></p>
        <p>
            <?php echo __('Pharmatalk is a Online Community for Pharmacists and people engaged in Pharmaceutical business.');?>
        </p>
        <p>
            <?php echo __('Our Pharmatalk team is here to support communication among Pharma-world members, and, hope to be pharmaceutical communication & information platform in generating good and reliable health information database to help people.');?>
        </p>
        <p><?php echo __('Thanks');?></p>

        <?php if(!$cuser['confirmed']):?>
            <div class="">
                <h3><?php echo __('Email address confirmation');?></h3>
                <p><?php echo __('Your email address has not been confirmed! At least one email address should be confirmed to move into main page.');?></p>
            </div>
        <?php endif;?>

        <?php if(Configure::read('ProfileCompletion.profile_completion_enabled')):?>
        <div class="">
            <h3><?php echo __('Profile Completeness');?></h3>
            <p><?php echo __('All the required field must be filled-in. When Profile not completed,  but, “not completed” banner will be shown on first page, and Several function will not be activated until completion.');?></p>
        </div>
        <?php endif;?>

        <div class="">
            <div><?php echo __('Email address confirmation');?></div>
            <?php echo __('Login email: '). ($cuser['confirmed'] ? __('Confirmed') : __('Not yet'));?><br/>
            <?php echo __('Sub email: '). ($cuser['submail_confirmed'] ? __('Confirmed') : __('Not yet'));?>
        </div>
        <?php

        if( (Configure::read('ProfileCompletion.profile_completion_enabled') && !empty($cuser) && $total_per == 100)):?>
            <div class="p_10">
                <?php echo sprintf(__d('profile_completion', '%s Profile Completeness'), $total_percent.'%'); ?>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $total_percent;?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $total_percent;?>%">
                        <span class="sr-only"><?php echo $total_percent.__d('profile_completion', '% Complete');?></span>
                    </div>
                </div>
            </div>
        <?php endif;?>
        <div class="form-group">
            <a class="btn btn-action" href="<?php echo $this->request->base.'/users/step_3';?>"><?php echo __('Previous');?></a>
            <a class="btn btn-action" href="<?php echo $this->request->base.'/users/do_logout';?>"><?php echo __('Logout');?></a>
            <a class="btn btn-action" href="<?php echo $this->request->base.'/';?>"><?php echo __('Start');?></a>
        </div>

    </div>
</div>

<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","mooUser"], function($,mooUser) {
            mooUser.initRegStep4();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooUser'), 'object' => array('$','mooUser'))); ?>
    mooUser.initRegStep4();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>