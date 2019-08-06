<?php
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->Html->addCrumb(__d('slider','Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('slider', 'Slideshows Manager'), '/admin/slider/sliders');
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Slider'));
    $this->end();
	echo $this->Html->script(array('Slider.jquerydevramaslider'), array('inline' => false));
?>
<?php echo$this->Moo->renderMenu('Slider', __d('slider','Slideshows'));?>

<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <a class="btn btn-gray" href="<?php echo  $this->request->base ?>/admin/slider/sliders/create">
                        <?php echo __d('slider','Create New Slideshow');?>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div id="sample_1_filter" class="dataTables_filter"><label>
                        <form method="post" action="<?php echo  $this->request->base ?>/admin/slider/sliders/">
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
            <th data-hide="phone"><?php echo __d('slider','Name');?></th>
            <th width="200px" data-hide="phone"><?php echo __d('slider','Actions');?></th>
        </tr>
        </thead>
        <tbody>
        <?php $count = 0;
        foreach ($sliders as $slider):
            ?>
            <tr class="gradeX <?php ( ++$count % 2 ? "odd" : "even") ?>" id="<?php echo  $slider['Slider']['id'] ?>">
                <td class="reorder"><a href="<?php echo  $this->request->base ?>/admin/slider/slide/index/<?php echo  $slider['Slider']['id'] ?>" title="<?php echo  htmlspecialchars($slider['Slider']['slider_name']); ?>"><?php echo htmlspecialchars($slider['Slider']['slider_name']);  ?></a></td>
                <td width="50px">
				<a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d("slider","Are you sure you want to delete this slider? All the items within it will also be deleted. This cannot be undone!");?>', '<?php echo  $this->request->base ?>/admin/slider/sliders/delete/<?php echo  $slider['Slider']['id'] ?>')"><i class="icon-trash icon-small"></i></a>
				&nbsp;|&nbsp;
                    <a href="<?php echo  $this->request->base ?>/admin/slider/sliders/create/<?php echo  $slider['Slider']['id'] ?>" title="<?php echo __d('slider','edit') ?>"><i class="icon-edit icon-small"></i></a>
                &nbsp;|&nbsp;
				<a data-toggle="modal" data-target="#ajax" href="<?php echo  $this->request->base ?>/admin/slider/sliders/review/<?php echo  $slider['Slider']['id'] ?>"><?php echo  __d('slider','Preview') ?></a>
				</td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="pagination pull-right">
            <?php echo $this->Paginator->prev('« '.__d('slider','Previous'), null, null, array('class' => 'disabled')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next(__d('slider','Next').' »', null, null, array('class' => 'disabled')); ?>
        </div>
    </div>
</div>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).on('loaded.bs.modal', function (e) {
    Metronic.init();
    });
    $(document).on('hidden.bs.modal', function (e) {
    $(e.target).removeData('bs.modal');
    });
<?php $this->Html->scriptEnd(); ?>