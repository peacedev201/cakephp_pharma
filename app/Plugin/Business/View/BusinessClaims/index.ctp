<?php $this->setCurrentStyle(4); ?>
  <div class="bar-content">
        <div class="content_center createaddon_content">
<div class="create_form">
  
            <div class="box3">
                <div class="mo_breadcrumb">
                    <h1><?php echo __d('business', "Please confirm you're the business owner") ?></h1>
                </div>
                <div class="full_content p_m_10">
                    <div class="form_content">
                       
                        <p><?php echo __d('business', "By clicking on the button below, you agree that:"); ?></p>
                        <p><?php echo __d('business', "1. You are the business owner or have permission to manage and update this business listing."); ?></p>
                        <p><?php echo __d('business', "2. Your business listing and any amendments you make to it are subject to site's Terms and conditions. Please read them carefully."); ?></p>
                        <p><?php echo __d('business', "3. You are authorized to provide, and grant us permission to display. all information and content you provide."); ?></p>
                        <p><?php echo __d('business', "You will be taken to the dashboard page where you can change or add more information for your business. Your changes will appear on site within 24 hours."); ?></p>
                        <p>
                            <a class="btn btn-action" href="<?php echo $this->request->base; ?>/businesses/claims/create/<?php echo $id; ?><?php echo $is_app ? "?app_no_tab=1" : "";?>"><?php echo __d('business', 'Claim this Business'); ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>