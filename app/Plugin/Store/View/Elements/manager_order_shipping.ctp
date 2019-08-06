<?php if($shippings != null):?>
    <?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        var currency_symbol = '<?php echo Configure::read('store.currency_symbol');?>';
        var currency_position = '<?php echo Configure::read('Store.currency_position');?>';
        require(["jquery","store_manager"], function($, store_manager) {
            store_manager.calculateShippingPrice();
        });
    </script>
    <?php endif; ?>
    <?php foreach($shippings as $shipping):?>
        <div>
            <label for="store_shipping_id_<?php echo $shipping['id'];?>">
                <input type="radio" id="store_shipping_id_<?php echo $shipping['id'];?>" value="<?php echo $shipping['id'];?>" name="store_shipping_id" <?php if($select_id == $shipping['id']):?>checked="checked"<?php endif;?> />
                <?php echo $shipping['name'];?> - <span class="shipping_price" data-price="<?php echo $shipping['price'];?>" data-weight="<?php echo $shipping['weight'];?>" data-key="<?php echo $shipping['key_name'];?>"><?php echo $this->Store->formatMoney($shipping['price']); ?></span>
            </label>
        </div>
    <?php endforeach;?>
<?php else: ?>
    <?php echo __d('store', 'No shippings');?>
<?php endif; ?>
