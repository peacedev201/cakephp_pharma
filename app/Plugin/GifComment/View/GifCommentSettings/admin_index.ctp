<?php
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    $this->Html->addCrumb(__d('gif_comment','Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('gif_comment','Settings'), array('controller' => 'gif_comment_settings', 'action' => 'admin_index'));
    echo $this->element('admin/adminnav', array('cmenu' => 'Gif Comment'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('GifComment',  __d('gif_comment', 'General'));?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">
                            <?php //echo  $this->element('admin/setting'); ?>
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">                           	   
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
							                <?php echo __d('gif_comment','Enable Gif Comment');?>                          
                                        </label>
                                        <div class="col-md-7">
							            	<?php
                                                                        
								            	echo $this->Form->input('enabled', array(
				                                    'type' => 'checkbox', 
				                                    'checked' => Configure::read('GifComment.gif_comment_enabled'),
				                                    'class' => 'form-control',
				                                    'label' => ''));
							            	?>
                                        </div>								            
                                    </div>
                                </div>


                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-9">
                                            <input type="submit" class="btn btn-circle btn-action" value="<?php echo __d('gif_comment','Save Settings');?>">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>