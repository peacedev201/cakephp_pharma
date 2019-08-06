<?php
     echo $this->Html->css(array(
        'Sticker.admin',
    ), null, array('inline' => false));
    echo $this->Html->script(array(
        'Sticker.admin'
    ), array('inline' => false));
?>
<?php
    $this->Html->addCrumb(__d('sticker',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('sticker',  "Manage Stickers"), array(
        'controller' => 'Stickers', 
        'action' => 'admin_index'
    ));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Sticker'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Sticker', __d('sticker', 'Stickers'));?>
<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo  __d('sticker', "Manage Stickers");?>
            <div class="pull-right">
                <div class="btn-group">
                    <button aria-expanded="false" type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
                        <?php echo __d('sticker', "Actions");?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a title="<?php echo __d('sticker', "Add New");?>" href="<?php echo $admin_link?>create">
                                <?php echo __d('sticker', "Add New");?>
                            </a>
                        </li>
                        <li>
                            <a title="<?php echo __d('sticker', "Ordering");?>" href="javascript:void(0)" onclick="jQuery.admin.saveAll('ordering')">
                                <?php echo __d('sticker', "Ordering");?>
                            </a>
                        </li>
                        <li>
                            <a title="<?php echo __d('sticker', "Enable");?>" href="javascript:void(0)" onclick="jQuery.admin.activeAll('enable')">
                                <?php echo __d('sticker', "Enable");?>
                            </a> 
                        </li>
                        <li>
                            <a title="<?php echo __d('sticker', "Disable");?>" href="javascript:void(0)" onclick="jQuery.admin.activeAll('disable')">
                                <?php echo __d('sticker', "Disable");?>
                            </a> 
                        </li>
                        <li>
                            <a title="<?php echo __d('sticker', "Delete");?>" href="javascript:void(0)" onclick="jQuery.admin.deleteAll('delete')">
                                <?php echo __d('sticker', "Delete");?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="panel-body">
            <form id="searchForm" method="get" action="<?php echo $admin_link?>">
                <div class="form-group">
                    <div class="col-md-5"></div>
                    <div class="col-md-6">
                        <?php echo $this->Form->input("keyword", array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'placeholder' => __d('sticker', 'Keyword'),
                            'name' => 'keyword',
                            'value' => !empty($search['keyword']) ? $search['keyword'] : ''
                        ));?>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary btn-lg" type="submit"><?php echo __d('sticker', "Search");?></button>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
            <?php if(!empty($stickers)):?>
            <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $admin_link;?>">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th style="width: 7px">
                                    <?php echo $this->Form->checkbox('', array(
                                        'hiddenField' => false,
                                        'div' => false,
                                        'label' => false,
                                        'onclick' => 'jQuery.admin.toggleCheckboxes(this)'
                                    ));?>
                                </th>      
                                <th colspan="2"><?php echo __d('sticker', 'Sticker');?></th>
                                <th style="width: 5%"><?php echo __d('sticker', 'Enable');?></th>
                                <th style="width: 5%"><?php echo __d('sticker', 'Ordering');?></th>
                                <th style="width: 10%;text-align:center;"><?php echo __d('sticker', 'Action');?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $count = 0;
                                foreach ($stickers as $sticker): 
                                    $sticker = $sticker['Sticker'];
                            ?>
                            <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                                <td style="text-align: center">
                                    <input type="checkbox" value="<?php echo $sticker['id']?>" class="multi_cb" id="cb<?php echo $sticker['id']?>" name="data[cid][]">
                                </td>
                                <td style="width: <?php echo STICKER_STICKER_ICON_WIDTH;?>px">
                                    <img style="width:<?php echo STICKER_STICKER_ICON_WIDTH;?>px;height:<?php echo STICKER_STICKER_ICON_HEIGHT;?>px;" src="<?php echo $this->Sticker->getStickerIcon($sticker); ?>" />
                                </td>
                                <td>
                                    <div class="sticker_sticker">
                                        <span><?php echo $sticker['name'];?></span>
                                    </div>
                                </td>
                                <td style="text-align: center">
                                    <?php if($sticker['enabled']):?>
                                    <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $sticker['id'];?>', 'disable')">
                                        <i class="fa fa fa-check" title="<?php echo __d('sticker', "Disable");?>"></i>
                                    </a>
                                        <?php else:?> 
                                    <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $sticker['id'];?>', 'enable')">
                                        <i class="fa fa fa-close" title="<?php echo __d('sticker', "Enable");?>"></i>
                                    </a>
                                    <?php endif;?>
                                </td>
                                <td>
                                    <?php echo $this->Form->text('ordering.', array(
                                        'class' => 'form-control',
                                        'div' => false,
                                        'label' => false,
                                        'value' => $sticker['ordering']
                                    ));?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo $admin_link.'create/'.$sticker['id'];?>">
                                            <?php echo __d('sticker', "Edit");?>
                                    </a>
                                    &#124;
                                    <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $sticker['id'];?>', 'delete')">
                                            <?php echo __d('sticker', "Delete");?>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </form>
            <?php echo $this->element('Sticker.admin_pagination');;?>
            <?php else:?>
                <?php echo __d('sticker', "No Stickers");?>
            <?php endif;?>
        </div>
    </div>
</div>