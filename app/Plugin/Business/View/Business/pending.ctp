<?php echo $this->Element('mobile_menu');?>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <p><?php echo __d('business', 'Your business page has been submitted to admin for approval. Click on "dashboard" button to go to dashboard to add more details for your business page');?></p>
        <a class="button" href="<?php echo $url_dashboard.'edit/'.$business_id;?><?php echo $is_app ? "?app_no_tab=1" : "";?>">
            <?php echo __d('business', 'Dashboard');?>                              
        </a>
    </div>
</div>