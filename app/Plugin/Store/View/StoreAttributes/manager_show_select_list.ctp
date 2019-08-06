<script type="text/template" id="attributeData">
    <?php echo json_encode($attributes);?>
</script>
<?php if($attributes != null):?> 
    <?php /*if($buy == 1):?> 
        <?php echo $this->Form->hidden('attribute_buy', array(
            'value' => $attribute_id
        ));?>
    <?php else:?> 
        <?php echo $this->Form->hidden('attribute_id', array(
            'value' => $attribute_id
        ));?>
    <?php endif;*/?>
    <?php foreach($attributes as $attribute):
    ?> 
        <div class="form-group">
            <div class="col-md-2">
                <?php echo $this->Form->hidden('attribute_id.', array(
                    'value' => $attribute['attribute_id']
                ));?>
                <?php echo $attribute['name'];?>
            </div>
            <div class="col-md-2">
                <?php echo $this->Form->select('plus.', array(
                    '1' => '+',
                    '0' => '-'
                ), array(
                    'empty' => false,
                    'class' => 'form-control',
                    'value' => $attribute['plus']
                ));?>
            </div>
            <div class="col-md-3">
                <?php echo $this->Form->text('attribute_price.', array(
                    'placeholder' => __d('store', 'Price'),
                    'class' => 'form-control',
                    'value' => $attribute['attribute_price']
                ));?>
            </div>
            <div class="col-md-1">
                <a href="javascript:void(0)" class="btn-big-height btn btn-primary btn-lg remove_added_attribute" data-id="<?php echo $attribute['attribute_id'];?>">
                    <?php echo __d('store', 'Remove');?>                    
                </a>
            </div>
        </div>
    <?php endforeach;?>
<?php endif;?>
