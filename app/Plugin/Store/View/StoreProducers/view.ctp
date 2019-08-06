
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).on('loaded.bs.modal', function (e) {
    Metronic.init();
});
$(document).on('hidden.bs.modal', function (e) {
    $(e.target).removeData('bs.modal');
});

function save_order()
{
    var list={};
    $('input[name="data[ordering]"]').each(function(index,value){
        list[$(value).data('id')] = $(value).val();
    })
    //console.log(list);
    jQuery.post("<?php echo $this->request->base?>/stores/producers/save_order/",{cats:list},function(data){
        window.location = data;
    });
}
<?php $this->Html->scriptEnd(); ?>

<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <a class="btn btn-gray" href="<?php echo $this->request->base?>/stores/producers/create">
                        <?php echo __d('store', 'Add New');?>
                    </a>
                    <a style="margin-left: 10px" onclick="save_order()" class="btn btn-gray" >
                        <?php echo __d('store', 'Save order');?>
                    </a>
                </div>
            </div>
            <div class="col-md-6">

            </div>
        </div>
    </div>


    <table class="table table-striped table-bordered table-hover" id="sample_1">
        <thead>
        <tr class="tbl_head">
            <th><?php echo __d('store', 'Name');?></th>
            <th><?php echo __d('store', 'Phone');?></th>
            <th><?php echo __d('store', 'Email');?></th>
            <th><?php echo __d('store', 'Address');?></th>
            <th width="50px"><?php echo __d('store', 'StoreOrder');?></th>
            <th width="50px"><?php echo __d('store', 'Publish');?></th>            
            <th width="50px" data-hide="phone"><?php echo __d('store', 'Actions');?></th>
        </tr>
        </thead>
        <tbody>

        <?php $count = 0;
        foreach ($producers as $producer): ?>
            <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>" id="<?php echo $producer['StoreProducer']['id']?>">              
                <td class="reorder">
                    <?php
//      $this->MooPopup->tag(array(
//             'href'=>$this->Html->url(array("controller" => "products",
//                                            "action" => "create",
//                                           "plugin" => 'Store',
//                                            $category['StoreProducer']['id']                                            
//                                        )),
//             'title' => $category['StoreProducer']['name'],
//             'innerHtml'=> "<strong>" . $category['StoreProducer']['name'] . "</strong>",              
//     ));
echo $this->Html->link('<strong>'.$producer['StoreProducer']['name'].'<strong>',array('controller' => 'producers','action' => 'create','plugin' => 'Store',$producer['StoreProducer']['id']),array('title' => $producer['StoreProducer']['name'], 'escape' => false));
 ?> 
                   </td>
				<td class='reorder'>
					<?php echo $producer['StoreProducer']['phone']; ?>
				</td>  
				<td class='reorder'>
					<?php echo $producer['StoreProducer']['email']; ?>
				</td>
				<td class='reorder'>
					<?php echo $producer['StoreProducer']['address']; ?>
				</td>								
                <td width="50px" class="reorder"><input data-id="<?php echo $producer['StoreProducer']['id']?>" style="width:50px" type="text" name="data[ordering]" value="<?php echo $producer['StoreProducer']['ordering']?>" /> </td>
				
                <td class="reorder">
					<?php if ( $producer['StoreProducer']['publish'] ): ?>
						<a href="<?php echo $this->request->base?>/stores/producers/publish_producer/<?php echo $producer['StoreProducer']['id']?>"><i class="fa fa-check-square-o " title="<?php echo __d('store', 'Unpublished');?>"></i></a>&nbsp;
					<?php else: ?>
						<a href="<?php echo $this->request->base?>/stores/producers/publish_producer/<?php echo $producer['StoreProducer']['id']?>/1"><i class="fa fa-times-circle" title="<?php echo __d('store', 'Published');?>"></i></a>&nbsp;
					<?php endif; ?>
                </td>			
                
                
                <td width="50px"><a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('store', 'Are you sure you want to delete this producer?');?>', '<?php echo $this->request->base?>/stores/producers/delete/<?php echo $producer['StoreProducer']['id']?>')"><i class="icon-trash icon-small"></i></a></td>
            </tr>
        <?php endforeach ?>

        </tbody>
    </table>

</div>
