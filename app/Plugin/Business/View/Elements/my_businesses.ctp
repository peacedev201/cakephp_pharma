<div class="mo_breadcrumb">
    <h1 class="bu-full-width">
        <?php echo __d('business', 'My Businesses');?>
        <!--mobile-->
        <?php echo $this->Element('mobile_my_menu');?>
        <!--end mobile-->
    </h1>
</div>
<ul id="list-content" class="bussiness-list">
    <?php echo $this->Element('Business.lists/my_business_list');?>
</ul>