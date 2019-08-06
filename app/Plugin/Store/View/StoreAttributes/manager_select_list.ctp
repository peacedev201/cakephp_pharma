<div class="title-modal">
    <?php echo __d('store',  'Attributes')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <?php if($attributes != null):?> 
        <form class="form-horizontal" id="formSelect">
            <?php echo $this->Form->hidden('buy', array(
                'value' => $buy
            ));?>
            <a href="javascript:void(0)" class="btn btn-primary" id="btnAddAttr">
                <?php echo __d('store', 'Add');?>                    
            </a>
            <br/><br/>
            <div id="attributes_content" ></div>
        </form>
    <?php endif;?>
    <div class="pull-right">
        <a id="btnAttrSave" type="button" class="btn btn-primary" href="javascript:void(0)" data-buy="<?php echo $buy;?>">
            <?php echo __d('store', 'Select');?>
        </a>
        <a id="btnAttrCancel" type="button" class="btn btn-primary" data-dismiss="modal" href="javascript:void(0)">
            <?php echo __d('store', 'Close');?>
        </a>
    </div>
    <div class="clear"></div>
    <div class="error-message" style="display:none;margin-top: 5px;" id="attributeMessage"></div>
</div>

<script type="text/template" id="attributeDataTemplate">
    <div class="form-group">
        <div class="col-md-6">
            <?php echo $this->form->select('attribute_id.', $attributes, array(
                'empty' => false,
                'class' => 'form-control attribute_id',
                'id' => ''
            ));?>
        </div>
        <div class="col-md-2">
            <?php echo $this->Form->select('plus.', array(
                '1' => '+',
                '0' => '-'
            ), array(
                'empty' => false,
                'class' => 'form-control plus',
                'id' => ''
            ));?>
        </div>
        <div class="col-md-3">
            <?php echo $this->Form->text('attribute_price.', array(
                'placeholder' => __d('store', 'Price'),
                'class' => 'form-control attribute_price',
                'value' => 0,
                'id' => ''
            ));?>
        </div>
        <div class="col-md-1">
            <a href="javascript:void(0)" class="btn-big-height btn btn-primary btn-lg remove_attribute">
                <?php echo __d('store', 'Remove');?>                    
            </a>
        </div>
    </div>
</script>