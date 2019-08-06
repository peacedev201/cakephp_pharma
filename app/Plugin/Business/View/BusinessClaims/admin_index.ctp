<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php
    echo $this->Html->css(array('Business.business-admin.css'), null, array('inline' => false));
    $this->Html->addCrumb(__d('business', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('business', 'Claim Requests'), array('controller' => 'business_claims', 'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Business'));
    $this->end();
?>
<?php $this->Paginator->options(array('url' => $this->passedArgs)); ?>
<?php echo $this->Moo->renderMenu('Business', __d('business', 'Claim Requests')); ?>

<div class="portlet-body form">
    <div class="portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed ">
            <div class="table-toolbar">
                <form id="searchForm" method="get" action="">
                    <div class="">
                        <div class="col-md-8"></div>
                        <div class="col-md-3">
                            <?php echo $this->Form->input("keyword", array(
                                'div' => false,
                                'label' => false,
                                'class' => 'form-control',
                                'placeholder' => __d('business', 'Search by name'),
                                'name' => 'keyword',
                                'value' => $keyword
                            ));?>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-gray" type="submit"><?php echo __d('business', "Search");?></button>
                        </div>
                        <div class="clear"></div>
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr class="tbl_head">
                                            <th width="30"><?php echo $this->Paginator->sort('id', __d('business', 'ID')); ?></th>
                                            <th><?php echo $this->Paginator->sort('User.name', __d('business', 'Claimed By'));?></th>
                                            <th><?php echo $this->Paginator->sort('created', __d('business', 'Claimed Date'));?></th>
                                            <th><?php echo $this->Paginator->sort('Business.name', __d('business', 'Business Name'));?></th>
                                            <th width="215"><?php echo __d('business', 'Actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 0; ?>
                                        <?php foreach ($aBusinesses as $aBusiness): ?>
                                        <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                                            <td><?php echo $aBusiness['Business']['id']; ?></td>
                                            <td>
                                                <a href="<?php echo $aBusiness['User']['moo_href']; ?>" target="_blank"><?php echo $aBusiness['User']['moo_title']; ?></a>
                                            </td>
                                            <td><?php echo $this->Time->niceShort($aBusiness['Business']['created']); ?></td>
                                            <td>
                                                <a href="<?php echo $aBusiness['Business']['moo_href']; ?>" target="_blank"><?php echo $this->Text->truncate(h($aBusiness['Business']['name']), 100, array('eclipse' => '...')); ?></a>
                                            </td>
                                            <td>
                                                <a href="<?php echo $this->request->base . '/businesses/dashboard/edit/' . $aBusiness['Business']['id']; ?>" target="_blank"><?php echo __d('business', 'View') ?></a>
                                            </td>
                                        </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                </div>
                <div class="col-sm-9">
                    <div id="dataTables-example_paginate" class="dataTables_paginate paging_simple_numbers">
                        <ul class="pagination">
                            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('business', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('business', 'Previous'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                            <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
                            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('business', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('business', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>