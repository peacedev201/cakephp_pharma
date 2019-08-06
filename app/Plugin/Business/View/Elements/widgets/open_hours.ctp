<?php
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $time_zone_format = "";
    $tz_convert = $business['Business']['timezone'];
    if($tz_convert != "")
    {
        $dateTime = new DateTime();
        $dateTime->setTimeZone(new DateTimeZone($tz_convert));
        $time_zone  = $dateTime->format('P');
        $time_zone_format = substr($time_zone, 0, 1) . (int)substr($time_zone, 1, 2);
    }
?>
<div class="box2 filter_block bus_open_hrs">
    <h3>
        <?php echo __d('business', 'Business Hours');?> 
        <span style='font-weight: normal'>(<?php echo __d('business', 'UTC') . ' ' . $time_zone_format;?>)</span>
    </h3>
    <div class="box_content">
        <?php if($business['Business']['always_open'] || empty($business['BusinessTime'])):?>
            <?php echo __d('business', 'Open 24/7');?>
        <?php else:?>
            <ul class="list2 menu-list">
                <?php 
                    $array = array();
                    $date_day = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
                    foreach ($business['BusinessTime'] as $item) {
                        $array[$item['day']][] = $item;
                    }
                    uksort($array, function($key1, $key2) use ($date_day) {
                        return (array_search($key1, $date_day) > array_search($key2, $date_day));
                    });
                    $old_day = '';
                    $k = 0;
                    foreach ($array as $item): 
                    foreach($item as $time):
                        $k++;
                        $shift = false;
                        if($old_day != $time['day'])
                        {
                            $old_day = $time['day'];
                        }
                        else
                        {
                            $shift = true;
                        }
                ?>
                <li>
                    <div class="col-xs-2" <?php if(!empty($business['Business']['open_today']['day']) && $business['Business']['open_today']['day'] == $time['day']):?>style="font-weight: bold"<?php endif;?>>
                        <?php if(!$shift):?>
                            <?php echo $this->Business->parseDayName($time['day']);?>
                        <?php endif;?>
                    </div>
                    <div class="col-xs-10" <?php if(!empty($business['Business']['open_today']['id']) && $business['Business']['open_today']['id'] == $time['id']):?>style="font-weight: bold"<?php endif;?>>
                        <?php echo date('g:i a', strtotime($time['time_open']));?>  - <?php echo date('g:i a', strtotime($time['time_close']));?> <?php if($time['next_day']) echo __d('business', '(next day)'); ?>
                        <?php if(!empty($business['Business']['open_today']['id']) && $business['Business']['open_today']['id'] == $time['id']):?>
                        <div class="bu-open-now"> <?php echo __d('business', 'Open now');?></div>
                        <?php endif;?>
                    </div>
                    <div class="clear"></div>
                </li>
                <?php endforeach;?>
                <?php endforeach;?>
                <li>
                    <?php if(empty($business['Business']['now_open']) || !$business['Business']['now_open']):?>
                        <?php echo __d('business', 'Closed now');?>
                    <?php endif;?>
                </li>
            </ul>
        <?php endif;?>
    </div>
</div>