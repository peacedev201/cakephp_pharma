<?php $this->setCurrentStyle(4);?>
<?php
$eventHelper = MooCore::getInstance()->getHelper('Event_Event');
?>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true,'requires'=>array('jquery','mooEvent'), 'object' => array('$', 'mooEvent'))); ?>
mooEvent.initOnCreate();
<?php $this->Html->scriptEnd(); ?>
<div class="create_form">
<div class="bar-content">
    <div class="content_center">
        <form id="createForm">
        <?php
        if (!empty($event['Event']['id'])){
            echo $this->Form->hidden('id', array('value' => $event['Event']['id']));
            echo $this->Form->hidden('photo', array('value' => $event['Event']['photo']));
        }else{
            echo $this->Form->hidden('photo', array('value' => ''));
        }
        ?>	

        <div class="box3">	
            <div class="mo_breadcrumb">
                <h1><?php if (empty($event['Event']['id'])) echo __( 'Add New Event'); else echo __( 'Edit Event');?></h1>
            </div>

            <div class="full_content p_m_10">
                <div class="form_content new_form">
                <div class="col-md-7">
                    <div class="input_container">
                        <label><?php echo __( 'Event Title')?></label>
                        <div>
                            <?php echo $this->Form->text('title', array('value' => $event['Event']['title'])); ?>
                        </div>
                    </div>
                    <div class="input_container">
                        <label><?php echo __( 'Information')?></label>
                        <div>
                            <?php echo $this->Form->tinyMCE('description', array('id' => 'editor' ,'escape'=>false,'value' => $event['Event']['description'])); ?>
                        </div>
                    </div>

                </div>
                <div class="col-md-5">
                    <div class="input_container">
                        <label><?php echo __( 'Category')?></label>
                        <div>
                            <?php echo $this->Form->select( 'category_id', $categories, array( 'value' => $event['Event']['category_id'] ) ); ?>
                        </div>
                    </div>
                    <div class="input_container">
                        <label><?php echo __( 'Location')?><a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __( 'e.g. Aluminum Hall, Carleton University')?>">(?)</a></label>
                        <div>
                            <?php echo $this->Form->text('location', array('value' => $event['Event']['location'])); ?>
                        </div>
                    </div>
                    <div class="input_container">
                        <label><?php echo __( 'Address')?><a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __( 'Enter the full address (including city, state, country) of the location.<br />This will render a Google map on your event page (optional)')?>">(?)</a></label>
                        <div>
                             <?php echo $this->Form->text('address', array('value' => $event['Event']['address'])); ?>
                        </div>
                    </div>
                    <div class="input_container">
                        <label><?php echo __( 'From')?></label>
                        <div>
                             <div class='col-xs-6'>
                                    <?php
                                    echo $this->Form->text('from', array('class' => 'datepicker', 'value' => $event['Event']['from'])); ?>
                                </div>
                                <div class='col-xs-6'>
                                    <div class="m_l_2">
                                        <?php

                                        echo $this->Form->text('from_time', array('value' => $event['Event']['from_time'], 'class' => 'timepicker'));
                                        ?>
                                    </div>
                                </div>
                                <div class="clear"></div>
                        </div>
                    </div>
                    <div class="input_container">
                        <label><?php echo __( 'To')?></label>
                        <div>
                            <div class='col-xs-6'>
                                    <?php
                                    echo $this->Form->text('to', array('class' => 'datepicker', 'value' => $event['Event']['to']));  ?>
                                </div>
                                <div class='col-xs-6'>
                                    <div class="m_l_2">
                                    <?php

                                    echo $this->Form->text('to_time', array('value' => $event['Event']['to_time'], 'class' => 'timepicker'));

                                    ?>
                                        </div>
                                </div>
                                <div class="clear"></div>
                        </div>
                    </div>
                    <div class="input_container">
                        <label><?php echo __( 'Event Type')?><a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __( 'Public: anyone can view and RSVP<br />Private: only invited guests can view and RSVP')?>">(?)</a></label>
                        <div>
                             <?php 
                                echo $this->Form->select('type', array( PRIVACY_PUBLIC  => __( 'Public'), 
                                                                                                                PRIVACY_PRIVATE => __( 'Private')
                                                                                                        ), 
                                                                                                 array( 'value' => $event['Event']['type'], 'empty' => false ) 
                                                                                ); 
                                ?>
                        </div>
                    </div>
                    <div class="input_container">
                        <label><?php echo __( 'Photo')?></label>
                        <div>
                            <div id="select-0" style="margin: 10px 0 0 0px;"></div>
                                <?php if (!empty($event['Event']['photo'])): ?>
                                <img width="150" id="item-avatar" class="img_wrapper" src="<?php echo  $eventHelper->getImage($event, array('prefix' => '150_square')) ?>" />
                                <?php else: ?>
                                    <img width="150" id="item-avatar" class="img_wrapper" style="display: none;" src="" />
                                <?php endif; ?>
                        </div>
                    </div>
                    <div class="input_container">
                        <label></label>
                        <div>
                        </div>
                    </div>
                </div>
                <div class="clear" style="padding: 0;"></div>
                <div style="padding:0 15px;">
                    <button type='button' class='btn btn-action' id="saveBtn"><?php echo __( 'Save')?></button>

                     <?php if ( !empty( $event['Event']['id'] ) ): ?>
                       <a href="<?php echo $this->request->base?>/events/view/<?php echo $event['Event']['id']?>" class="button"><?php echo __( 'Cancel')?></a>
                      <?php endif; ?>
                      <?php if ( ($event['Event']['user_id'] == $uid ) || ( !empty( $event['Event']['id'] ) && !empty($cuser['Role']['is_admin']) ) ): ?>
                                    <a href="javascript:void(0)" data-id="<?php echo $event['Event']['id']?>" class="button deleteEvent"><?php echo __( 'Delete')?></a>
                      <?php endif; ?>
                    <div class="error-message" style="display:none;"></div>
                </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
</div>