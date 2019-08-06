<style type="text/css">
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
<div class="row">
    <div class="col-sm-3">
        <?php echo $this->Paginator->counter(array(
            'separator' => __d('feed_list', ' of a total of ')
        ));?>
    </div>
    <div class="col-sm-9">
        <div id="dataTables-example_paginate" class="dataTables_paginate paging_simple_numbers">
            <ul class="pagination">
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('feed_list', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('feed_list', 'Previous'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('feed_list', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('feed_list', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
            </ul>
        </div>
    </div>
</div>