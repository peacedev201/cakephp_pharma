<?php

echo $this->Html->css(array(
    'fineuploader',
    '/commercial/css/jquery-ui.css'), null, array('inline' => false));
echo $this->Html->css(array( 'fineuploader','/commercial/css/commercial.css' ));
$ads_campaign = $ads_campaign['AdsCampaign'];
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true,
    'requires' => array('jquery', 'ads_main','ads_jquery-ui'),
    'object' => array('$', 'ads_main'))); ?>	
ads_main.initReport();
<?php $this->Html->scriptEnd(); ?>
<div class="bar-content">
    <div class="content_center">
        <div class="box3">
            <div class="mo_breadcrumb">
                <p>
            <?php if(!empty($from_date) && !empty($to_date)):?>
                <?php echo sprintf(__d('ads', 'Here is detailed report of %s from %s to %s'), $ads_campaign['name'], $from_date, $to_date);?>
            <?php else:?> 
                <?php echo sprintf(__d('ads', 'Here is detailed report of %s'), $ads_campaign['name']);?>
            <?php endif;?>
                </p>
            </div>

            <div class="full_content p_m_10">
                <form id="formReport" class="col-md-6">
                    <div class="form-group">
                    <?php echo $this->Form->hidden('ads_campaign_id', array(
                        'value' => $ads_campaign['id']
                    ));?>
                        <div class="col-md-1 ads_report_from">
                        <?php echo __d('ads', 'From');?>
                        </div>
                        <div class="col-md-5">
                        <?php echo $this->Form->input("from_date", array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'value' => !empty($from_date) ? str_replace('-', '/', $from_date) : ''
                        ));?>
                        </div>
                        <div class="col-md-1 ads_report_to">
                        <?php echo __d('ads', 'to');?>
                        </div>
                        <div class="col-md-5">
                        <?php echo $this->Form->input("to_date", array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'value' => !empty($to_date) ? str_replace('-', '/', $to_date) : ''
                        ));?>
                        </div>
                        <div class="clear"></div>
                    </div>
                </form>
                <div class="col-md-3">
                    <input type="button" id="getReport" class="btn-gray ads-report-search" value="<?php echo __d('ads', 'Search');?>" />
                </div>
            </div>
            <div class="clear"></div>
            <div class="full_content" id="reportDetail"></div>
        </div>
    </div>
</div>
