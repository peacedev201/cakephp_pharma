<?php if($reports != null):?>
    <?php foreach($reports as $report):
        $report_value = $report[0];
        $report = $report['AdsReport'];
    ?> 
        <?php if($report['type'] == 'view'):?>
            <p><?php echo __d('ads', 'Number of views: ').$ads_campaign['view_count'];?></p>
        <?php else:?>
            <p><?php echo __d('ads', 'Number of clicks ').$ads_campaign['click_count'];?></p>
        <?php endif;?>
        <p><?php echo $report_value['male'];?>: <?php echo __d('ads', 'male');?></p>
        <p><?php echo $report_value['famale'];?>: <?php echo __d('ads', 'female');?></p>
        <?php if($roles != null):?>
            <?php foreach($roles as $role_id => $role_name):?> 
                <p><?php echo !empty($report_value['role_id'][$role_id]) ? $report_value['role_id'][$role_id] : 0;?>: <?php echo __d('ads', 'user role ').$role_name;?></p>
            <?php endforeach;?>
        <?php endif;?>
        <p><?php echo $report_value['under_20'];?>: <?php echo __d('ads', 'under 20');?></p>
        <p><?php echo $report_value['20_to_50'];?>: <?php echo __d('ads', 'from 20 - 50');?></p>
        <p><?php echo $report_value['above_50'];?>: <?php echo __d('ads', 'above 50');?></p>
    <?php endforeach;?>
<?php else:?> 
    <?php echo __d('ads', 'No reports');?>
<?php endif;?>
