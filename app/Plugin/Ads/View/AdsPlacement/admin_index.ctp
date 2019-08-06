<?php
    $this->Html->addCrumb(__d('ads','Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('ads','Manage Placements'),'/admin/ads/ads_placement' );
    echo $this->Html->css(array('jquery-ui', 'footable.core.min','/commercial/css/commercial-admin.css'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Ads'));
    $this->end();
?>
<style type="text/css">
    .table > tbody > tr > td {
     vertical-align: middle;
}

    .pagination > li.current.paginate_button ,
    .pagination > li.disabled {
        position: relative;
        float: left;
        padding: 6px 12px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #428bca;
        text-decoration: none;
        background-color: #eee;
        border: 1px solid #ddd;
    }
</style>
<?php  $this->Html->scriptStart(array('inline' => false));   ?>
    jQuery(document).ready(function(){
       $("[id^=enable_]").change(function(){
           value = $(this).val();
           name_id = this.id;
           
            jQuery.ajax({
                url: '<?php echo  $this->request->base ?>/admin/ads/ads_placement/change_status/'+ value+ "/"+ name_id,
                type: 'POST',
                success: function(data){
                    var json = $.parseJSON(data);
                    if(json.result == 1) {
                        location.reload();
                    }
                }
            });
          
       });
    });

<?php $this->Html->scriptEnd();  ?>

<?php echo$this->Moo->renderMenu('Ads', __d('ads','Manage Placements'));?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-gray" id="sample_editable_1_new" onclick="confirmSubmitForm('<?php echo __d('ads','Are you sure you want to delete ad placements?') ?>', 'deleteForm')">
                        <?php echo __d('ads','Delete Selected')?>
                    </button>
                </div>
            </div>
           <form id="searchForm" method="get" action="<?php echo $admin_url.'ads_placement';?>">
            
            <div class="col-md-3">
                
                    <?php echo $this->Form->input("keyword", array(
                        'div' => false,
                        'label' => false,
                        'class' => 'form-control',
                        'placeholder' => __d('ads', 'Keyword'),
                        'name' => 'keyword',
                        'value' => $keyword
                    ));?>
                
                
            </div>
            <div class="col-md-1">
                    <button class="btn btn-gray" type="submit"><?php echo __d('ads', "Search");?></button>
                </div>
               </form>
            <div class="col-md-2">
                <?php echo $this->html->link(__d('ads','Create Placement'),array(
                    'plugin'=>'ads',
                    'controller'=>'ads_placement',
                    'action'=>'create',
                ), array(
                    'class' => 'btn btn-gray',
                    'div' => false,
                    'label' => false,
                ));?>
            </div>
        </div>
    </div>
    
    <form method="post" action="<?php echo  $this->request->base ?>/admin/ads/ads_placement/delete" id="deleteForm">
        <?php echo  $this->Form->hidden('category'); ?>
        <table class="table table-striped table-bordered " id="sample_1">
            <thead>
                <tr>
                    <?php if ($cuser['Role']['is_super']): ?>
                        <th width="30"><input type="checkbox" onclick="toggleCheckboxes2(this)"></th>
                    <?php endif; ?>
                    <th><?php echo $this->Paginator->sort('name',__d('ads','Placement')); ?></th>
                    <th><?php echo $this->Paginator->sort('price',__d('ads','Price')); ?>(<?php echo Configure::read('Ads.currency_symbol');?>)</th>
                    <th data-hide="phone"><?php echo $this->Paginator->sort('view_limit', __d('ads','Views Limit')); ?></th>
                    <th data-hide="phone"><?php echo  $this->Paginator->sort('click_limit',__d('ads','Clicks Limit')); ?></th>
                    <th data-hide="phone"><?php echo $this->Paginator->sort('number_of_ads',__d('ads','No of Ads')); ?></th>
                    <th data-hide="phone"><?php echo $this->Paginator->sort('total_ads',__d('ads','Ads appear')); ?></th>
                    <th data-hide="phone"><?php echo __d('ads','Active ads'); ?></th>
                    <th data-hide="phone"><?php echo __d('ads','Status'); ?></th>
                    <th data-hide="phone"><?php echo __d('ads','Active'); ?></th>

                </tr>
            </thead>
            <tbody>
                <?php $count = 0;
                $option_select = array('1'=>__d('ads','Enable'),'0'=>__d('ads','Disable'));
                foreach ($ads_placements as $place):
                    $enable = ($place['AdsPlacement']['enable'])?1:0;
                    $day = $place['AdsPlacement']['period']<2?__d('ads','day'):__d('ads','days');
                    ?>
                    <tr class="gradeX <?php ( ++$count % 2 ? "odd" : "even") ?>">
                        <?php if ($cuser['Role']['is_super']): ?>
                            <td><input type="checkbox" name="ads_place[]" value="<?php echo  $place['AdsPlacement']['id'] ?>" class="check"></td>
                     <?php endif; ?>
                            <td><a href="<?php echo $admin_url.'ads_placement/create/'.$place['AdsPlacement']['id'] ?>"><?php echo h($place['AdsPlacement']['name']);  ?></a></td>
                        <td><?php echo $place['AdsPlacement']['price'].' '.'/'.$place['AdsPlacement']['period'].' '.$day;  ?></td>
                        <td><?php echo $place['AdsPlacement']['view_limit'];   ?></td>
                        <td><?php echo $place['AdsPlacement']['click_limit'];  ?></td>
                        <td><?php echo $place['AdsPlacement']['number_of_ads']; ?></td>
                        <td><?php echo $place['AdsPlacement']['total_ads']; ?></td>
                        <td>
                            <a href="<?php echo $this->request->base;?>/admin/ads/?placement=<?php echo $place['AdsPlacement']['id'];?>">
                                <?php echo __d('ads', 'View');?>
                            </a>
                        </td>
                        <td><span id="status"><?php echo ($place['AdsPlacement']['enable'])?__d('ads',"Active"):__d('ads',"Disable"); ?></span></td>
                        <td><?php echo $this->Form->select('enable_'.$place['AdsPlacement']['id'],$option_select,array('class' => 'form-control', 'value' => $enable, 'empty' => false)); ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </form>
            <div class="row">
            <div class="col-sm-3">
            </div>
            <div class="col-sm-9">
                <div id="dataTables-example_paginate" class="dataTables_paginate paging_simple_numbers">
                    <ul class="pagination">
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first('First', array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev('Previous', array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next('Next', array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last('Last', array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    </ul>
                </div>
            </div>
        </div>
    
</div>