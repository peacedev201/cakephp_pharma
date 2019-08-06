<?php if ($this->Moo->socialIntegrationEnable('linkedin') || $this->Moo->socialIntegrationEnable('twitter')):    ?>   
<?php  
    $provider_count = 0;
    $arr_provider = array('facebook','google','linkedin','twitter');
    foreach ($arr_provider as $pr) {
        if($this->Moo->socialIntegrationEnable($pr)){
            $provider_count++;
        }
    }   
?>
 <style>
        <?php if($provider_count >= 3): ?>                            
                @media (max-width: 480px){
                     .user_register_holder #twitterSignInWrapper{
                        margin-top: 10px;
                    }
                    .user_register_holder .sociallogin-providers{
                        clear: both;
                    }
                    .user_register_holder #twitterSignInWrapper {
                        margin-left: 0;
                    }
                }
                
                @media (max-width:1025px) and (min-width:993px) {
                    .user_register_holder #linkedinSignInWrapper{
                        margin-top: 10px;
                         margin-left: 137px;
                    }
                    
                    .user_register_holder #twitterSignInWrapper{
                        margin-left: 137px;
                    }
                }
                
                @media (min-width:993px){
                     .user_register_holder #twitterSignInWrapper{
                        margin-top: 10px;
                    }
                }
                
            <?php if($this->Moo->socialIntegrationEnable('facebook') && $this->Moo->socialIntegrationEnable('google')): ?>
                @media (max-width: 480px){
                    .user_register_holder #linkedinSignInWrapper {
                        margin-left: 0;
                    }
                }
            <?php endif; ?> 
        <?php endif; ?>
        <?php if($this->Moo->socialIntegrationEnable('facebook') && $this->Moo->socialIntegrationEnable('google')): ?>
                    .user_register_holder #linkedinSignInWrapper, .user_register_holder #twitterSignInWrapper{
                        margin-top: 10px;
                    }
               
                
                @media (max-width: 992px) and (min-width: 768px) {
                .user_register_holder #linkedinSignInWrapper, .user_register_holder #twitterSignInWrapper {
                    margin-top: 0;
                }
        <?php endif; ?>               
 </style>
    <?php if ($this->Moo->socialIntegrationEnable('linkedin')): ?>
        <div id="linkedinSignInWrapper">
            <a href="<?php echo  $this->Html->url(array('plugin' => 'social_login', 'controller' => 'social_logins', 'action' => 'login', 'linkedin')) ?>">
                <div class="sociallogin-button">
                    <span class="icon"></span>
                    <span class="buttonText"><?php echo  __d('social_login','Linkedin') ?></span>
                </div>
            </a>
        </div>
    <?php endif; ?>
    <?php if ($this->Moo->socialIntegrationEnable('twitter')): ?>
        <div id="twitterSignInWrapper">
            <a href="<?php echo  $this->Html->url(array('plugin' => 'social_login', 'controller' => 'social_logins', 'action' => 'login', 'twitter')) ?>">
               <div class="sociallogin-button">
                    <span class="icon"></span>
                    <span class="buttonText"><?php echo  __d('social_login','Twitter') ?></span>
                </div>
            </a>
        </div>
    <?php endif; ?>
<?php endif; ?>
