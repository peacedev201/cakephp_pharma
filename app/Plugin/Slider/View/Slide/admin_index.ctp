<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->Html->addCrumb(__d('slider','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('slider', 'Slideshows Manager'), '/admin/slider/sliders');
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Slider'));
$this->end();
?>
<?php
echo $this->Html->script(array('Slider.colorpicker'), array('inline' => false));
echo $this->Html->css(array('Slider.colorpicker'), null, array('inline' => false));
?>
<?php echo $this->Moo->renderMenu('Slider', __d('slider','Slideshows'));?>

<div class="portlet-body">
    <?php if($sliderName):?>
    <div class="row">
        <div class="col-md-6">
            <h3><?php echo $sliderName;?> - <?php echo __d('slider', 'Manage Slides');?></h3>
        </div>
    </div>
    <?php endif;?>
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <a class="btn btn-gray" href="<?php echo  $this->request->base ?>/admin/slider/slide/create/0/<?php echo $id;?>">
                        <?php echo __d('slider','Add New Slide');?>
                    </a>
                </div>
                <!--<div class="btn-group">
                    <button class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo  $this->request->base ?>/admin/slider/slide/createText">
                        <?php echo __d('slider','Add Text');?>
                    </button>
                </div>-->
            </div>
            <div class="col-md-6">
                <div id="sample_1_filter" class="dataTables_filter"><label>
                        <form method="post" action="<?php echo  $this->request->base ?>/admin/slider/slide/index/<?php echo $id;?>">
                            <?php echo $this->Form->text('keyword', array('class' => 'form-control input-medium input-inline', 'placeholder' => __d('slider','Search by name')) ); ?>
                            <?php echo $this->Form->submit('', array('style' => 'display:none')); ?>
                        </form>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover" id="sample_1">
        <thead>
        <tr class="tbl_head">
            <th data-hide="phone"><?php echo __d('slider','Slide heading');?></th>
            <!--<th data-hide="phone"><?php //echo __d('slider','Slider Name');?></th>-->
            <th data-hide="phone"><?php echo __d('slider','Slide content');?></th>
            <th width="200px" data-hide="phone"><?php echo __d('slider','Actions');?></th>
        </tr>
        </thead>
        <tbody>
        <?php $count = 0;
        foreach ($slides as $slide):
            ?>
            <tr class="gradeX <?php ( ++$count % 2 ? "odd" : "even") ?>" id="<?php echo  $slide['Slide']['id'] ?>">
                <td class="reorder">
                    <?php echo htmlspecialchars($slide['Slide']['slide_name']);  ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($slide['Slide']['text']);  ?>
                </td>
                <td width="50px">
                    <a href="<?php echo  $this->request->base ?>/admin/slider/slide/create/<?php echo  $slide['Slide']['id'] ?>/<?php echo  $slide['Slide']['slider_id'] ?>" title="<?php echo __d('slider','edit') ?>"><i class="icon-edit icon-small"></i></a>
                    &nbsp;|&nbsp;
                    <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d("slider","Are you sure you want to delete this slide? This cannot be undone!");?>', '<?php echo  $this->request->base ?>/admin/slider/slide/delete/<?php echo  $slide['Slide']['id'] ?>')"><i class="icon-trash icon-small"></i></a>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="pagination pull-right">
            <?php echo $this->Paginator->prev('« '.__('Previous'), null, null, array('class' => 'disabled')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next(__('Next').' »', null, null, array('class' => 'disabled')); ?>
        </div>
    </div>
</div>