<div <?php if(!$this->request->is('ajax')): ?>class="content_center_home"<?php endif;?>>
    <div class="mo_breadcrumb">
        <h1><?php echo __d('credit', 'Credit Ranks')?></h1>
    </div>
    <?php echo $this->element( 'list/credit_ranks', array( 'more_url' => '/credit/ranks/browse/page:2' ) ); ?>

</div>
