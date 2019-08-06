<div <?php if(!$this->request->is('ajax')): ?>class="content_center_home"<?php endif;?>>
    <div class="mo_breadcrumb">
        <h1><?php echo __d('credit', 'Transactions')?></h1>
    </div>
    <table class="table table-striped">
        <thead>
        <tr class="tbl_head">
            <th width="30%"><?php echo __d('credit','Action Date');?></th>
            <th><?php echo __d('credit','Action Type');?></th>
            <th width="50px" data-hide="phone"><?php echo __d('credit','Credits');?></th>
        </tr>
        </thead>
        <tbody id="list-content">
        <?php echo $this->element( 'list/my_credits', array( 'more_url' => '/credits/more_my_credits/page:2') ); ?>
        </tbody>
    </table>
    <?php if($this->Paginator->counter() != '1 of 1'):?>
    <?php echo $this->Paginator->prev('« '.__d('credit', 'Previous'), null, null, array('class' => 'disabled')); ?>
    <?php echo $this->Paginator->numbers(); ?>
    <?php echo $this->Paginator->next(__d('credit', 'Next').' »', null, null, array('class' => 'disabled')); ?>
    <?php endif;?>
</div>