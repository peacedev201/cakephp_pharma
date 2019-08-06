<form id="contactForm">
    <?php echo $this->Form->hidden('business_id', array(
        'value' => $business_id
    ));?>
    <div class="form-group">
        <div class="col-md-2">
            <label>
                <?php echo __d('business', 'Your name');?>:
            </label>
        </div>
        <div class="col-md-10">
            <?php echo $this->Form->text('name');?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label>
                <?php echo __d('business', 'Your email');?>:
            </label>
        </div>
        <div class="col-md-10">
            <?php echo $this->Form->text('email', array(
                'value' => !empty($cuser) ? $cuser['email'] : ''
            ));?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-2">
            <label>
                <?php echo __d('business', 'Message');?>:
            </label>
        </div>
        <div class="col-md-10">
            <?php echo $this->Form->textarea('message', array(
                'cols' => 70,
                'rows' => 10
            ));?>
        </div>
    </div>
    <?php if(Configure::read('core.recaptcha') && Configure::read('core.recaptcha_publickey') != null):?>
    <div class="form-group">
        <div class="col-md-2"></div>
        <div class="col-md-10">
            <div class="captcha_box">
                <script src='https://www.google.com/recaptcha/api.js?hl=en' async defer></script>
                <div id="recaptcha" class="g-recaptcha" data-sitekey="<?php echo Configure::read('core.recaptcha_publickey')?>"></div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <div class="form-group">
        <div class="col-md-2">
        </div>
        <div class="col-md-10">
            <a href="javascript:void(0)" class="btn btn-action" id="btnContact">
                <?php echo __d('business', 'Send');?>         
            </a>
            <div style="display:none;" class="error-message" id="contactMessage"></div>
        </div>
    </div>
    <div class="clear"></div>
</form>