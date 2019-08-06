<?php echo $this->Element('Products/sliders');?>
<div class="main-category col-md-3 col-md-pull-9">
    <div class="category_round navigation" id="thumbs">
        <h2 class="cate-name">
            <?php echo __d('store', 'Category');?>
            <a href="<?php echo STORE_URL;?>products">
                <?php echo __d('store', 'View all');?>
            </a>
        </h2>
        <?php echo $this->Element('main_category');?>
    </div>
</div>
<div class="clear"></div>

<?php echo $this->Element('Products/home_product');?>
<?php echo $this->Element('Products/highlight_category');?>



