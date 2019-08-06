<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness', 'business_timeentry', 'business_jqueryui'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initBusinessBranch();
<?php $this->Html->scriptEnd(); ?>
<?php
$businessHelper = MooCore::getInstance()->getHelper('Business_Business');
?>
<a href="<?php echo $url_dashboard;?>create_branch/<?php echo $business_id;?><?php echo $is_app ? "?back_and_refresh=1" : "";?>" class="button pull-right create_brach">
    <?php echo __d('business', 'Create a new sub page');?>                                
</a>
<div class="clear"></div>
<?php if ($branches != null): ?>
    <ul class="bus_branch_list dashboard_branch_list">
        <?php
        foreach ($branches as $branch):
            $branch = $branch['Business'];
            ?>
            <li class="full_content p_m_10">
                <a href="<?php echo $branch['moo_href']; ?>" class="title">
                    <?php echo $branch['name']; ?>                
                </a>
                <div class="extra_info">
                    <span><?php echo __d('business', 'Tel'); ?>: <?php echo $branch['phone']; ?></span>
                    <span><?php echo __d('business', 'Address'); ?>: <?php echo $branch['address']; ?></span>
                </div>
                 <div class="list_option">
                <div class="dropdown">
                    <button id="dropdown-edit" data-target="#" data-toggle="dropdown">
                        <i class="material-icons">more_vert</i>
                    </button>
                    <ul role="menu" class="dropdown-menu" aria-labelledby="dropdown-edit">
                        <li>
                            <a href="<?php echo $url_dashboard;?>create_branch/<?php echo $business_id;?>/<?php echo $branch['id'];?><?php echo $is_app ? "?back_and_refresh=1" : "";?>">
                                <?php echo __d('business', 'Edit');?>                        
                            </a>
                        </li>
                        <li>
                            <a class="delete_branch" href="javascript:void(0)" data-url="<?php echo $this->request->base;?>/business_branch/delete_branch/<?php echo $business_id;?>/<?php echo $branch['id'];?><?php echo $is_app ? "?app_no_tab=1" : "";?>"> 
                                <?php echo __d('business', 'Delete');?>                 
                            </a>
                        </li>
                    </ul>
                </div>
                </li>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else:?>
    <div class="clear" align="center">
        <?php echo __d('business', 'No more results found');?>
    </div>
<?php endif; ?>