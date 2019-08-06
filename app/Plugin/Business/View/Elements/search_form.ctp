<?php 
    $search_data = $this->requestAction(array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'search_data'
    ));
    $viewer = $search_data['viewer'];
    $params = $search_data['params'];
    $action_name = $search_data['action_name'];
    $search_keyword_location = $search_data['search_keyword_location'];
?>
<?php 
    echo $this->Html->css(array(
        'jquery-ui'
    ), array('block' => 'css', 'minify'=>false));
?>
<?php if(isset($mobile)):?>
    <?php if($this->request->is('ajax')):?>
        <script type="text/javascript">
            require(["jquery","mooBusiness","business_jqueryui"], function($, mooBusiness, business_jqueryui) {
                mooBusiness.initAdvancedSearchDialog();
            });
        </script>
    <?php else: ?>
        <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooBusiness', 'business_jqueryui'), 'object' => array('$', 'mooBusiness', 'business_jqueryui'))); ?>
            mooBusiness.initAdvancedSearchDialog();
        <?php $this->Html->scriptEnd(); ?>
    <?php endif; ?>
<?php endif; ?>
<div class="box2 search-friend">
    <h3><?php echo __d('business', 'Search')?></h3>
    <div class="box_content">
        <form method="get" id="formGlobalSearch" action="<?php echo $this->request->base; ?>/business_search">
            <ul class="list6">							
                <li>
                    <label><?php echo __d('business', 'Keyword');?></label>
                    <input type="text" class="global_search_category" name="keyword" placeholder="<?php echo __d('business', 'Find')?>" value="<?php echo !empty($keyword) ? $keyword : '';?>">
                </li>
                <li>
                    <div>
                        <label><?php echo __d('business', 'Near') ?></label>
                    </div>
                    <div class="col-md-12">
                        <input type="text" id="address<?php echo isset($mobile) ? '_mobile' : '';?>" class="global_search_location" name="keyword_location" placeholder="<?php echo __d('business', 'Search address')?>" value="<?php echo !empty($search_keyword_location) ? $search_keyword_location : '';?>">
                    </div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div>
                        <label><?php echo __d('business', 'Distance') ?></label>
                    </div>
                    <div class="col-md-12">
                        <div class='col-xs-9'>
                            <?php echo $this->Form->text('distance', array('value' => (isset($this->request->query['distance'])) ? $this->request->query['distance'] : ($this->request->params['action'] != 'search' ? Configure::read('Business.business_search_default_distance') : ''))); ?>
                        </div>
                        <div class='col-xs-3 '>
                            <div class="m_l_2 distance-search ">
                                <?php echo $this->Form->select('distance_type',array( 'miles' => __d('business', 'mile'), 'kms' => __d('business', 'km')), array('empty' => false, 'value' => (isset($params['distance_type'])) ? $params['distance_type'] : "")); ?>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </li>

                <li style="margin-top:20px">
                    <input type="button" class="btn btn-action" id="btn_global_search" value="<?php echo __d('business', 'Search');?>">
                </li>
            </ul>
        </form>
    </div>
</div>