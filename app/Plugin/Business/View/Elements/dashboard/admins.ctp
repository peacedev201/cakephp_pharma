<?php
echo $this->Html->css(array(
    'Business.business', 'Business.autocomplete'), array('block' => 'css', 'minify'=>false));
?>

<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness', 'business_jqueryui'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initBusinessAdmin(<?php echo $business_id;?>);
<?php $this->Html->scriptEnd(); ?>
<div class="extra_info_text">
    <?php echo __d('business', 'Below you can see all the users who can administer your page. You can add any registered users as admins of this page and remove existing ones.
    Please note, that users selected by you for this page will get almost authority (similar to yours) to manage this page. Thus you should be careful while choosing your admins.
    ');?>
    <br/>
    <br/>
    <?php echo sprintf(__d('business', 'Click %s to manage permission of your page admin.'), '<a href="'.$this->request->base.'/businesses/dashboard/permissions/'.$business_id.'">'.__d('business', 'here').'</a>'); ?>
    <br/><br/>
</div>
<form id="adminForm">
    <?php echo $this->Form->hidden('business_id', array(
        'value' => $business_id
    ));?>
    <?php echo $this->Form->hidden('user_id', array(
        'id' => 'business_admin_id'
    ));?>
    <?php echo $this->Form->input('name', array(
        'id' => 'suggest_admin'
    ));?>
    <a id="addAdminButton" class="button" href="javascript:void(0)">
        <?php echo __d('business', 'Add as admin');?>                                
    </a>
    <br/><br/>
    <div style="display: none; margin-top: 10px" class="error-message" id="adminMessage"></div>
</form>

<ul id="admin_content" class="list6 comment_wrapper comment_list"></ul>