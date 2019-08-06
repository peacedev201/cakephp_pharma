<form class="form_search_store">
    <ul class="list6">							
        <li>
            <label><?php echo __d('store', 'Store name');?></label>
            <?php echo $this->Form->text('keyword', array(
                'class' => 'orderby',
                'label' => false,
                'div' => false,
            ));?>
        </li>
        <li>
            <button class="btn btn-action padding-button" id="btn_search_store">
                <?php echo __d('store', 'Search');?>
            </button>
        </li>
    </ul>
</form>