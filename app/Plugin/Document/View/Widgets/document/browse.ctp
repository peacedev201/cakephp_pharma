<div class="bar-content">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1><?php echo __d('document','Documents');?></h1>
            <?php if ($uid):?>
            	<a href="<?php echo $this->request->base?>/documents/create" class="button button-action topButton button-mobi-top"><?php echo __d('document','Create New Document');?></a>
            <?php endif;?>
         </div>
		<ul id="list-content" class="document-content-list">
			<?php if (count($documents)):?>
				<?php echo $this->element('lists/documents', array('is_view_more'=>$is_view_more,'url_more'=>$url_more,'documents' =>$documents), array('plugin'=>'Document')); ?>
			<?php else:?>		
				<li class="clear text-center"><?php echo __d('document','No more results found');?></li>
			<?php endif;?>
		</ul>
    </div>
</div>