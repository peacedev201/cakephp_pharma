<?php
__d('credit','Blog');
__d('credit','Core');
__d('credit','Credit');
__d('credit','Event');
__d('credit','Group');
__d('credit','Topic');
__d('credit','User');
__d('credit','Video');
__d('credit','Activity');
__d('credit','Photo');
__d('credit','Post news feed');
__d('credit','Friend Inviter');

__d('credit','Write new entry');
__d('credit','Like');
__d('credit','Comment');
__d('credit','Share');
__d('credit','Comment status');
__d('credit','Sending credits');
__d('credit','Receiving credits');
__d('credit','Admin giving you credits');
__d('credit','Admin set your credits');
__d('credit','Buying credits');
__d('credit','Admin approved your withdrawal request');
__d('credit','Pay item with credit');
__d('credit','Admin refunding you credits');
__d('credit','Create Event');
__d('credit','Create Group');
__d('credit','Join Group');
__d('credit','Upload Photo');
__d('credit','Create Topic');
__d('credit','Add friends');
__d('credit','Sign Up');
__d('credit','Post Video');

echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable', 'jquery.validate.min'), array('inline' => false));
$this->Html->addCrumb(__d('credit', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('credit', 'Credit Settings'), array('plugin' => 'credit', 'controller' => 'credit_settings', 'action' => 'settings'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Credit'));
$this->end();
?>
<?php echo $this->Moo->renderMenu('Credit', __d('credit','Credit settings')); ?>

<div class="portlet-body">
        <div>
            <p><?php echo __d('credit','You can edit how many credits member can earn for each action within a specific period'); ?></p>

            <p><?php echo __d('credit','Credit: number of credits can earn by doing this activity. "0" means that no credit will be be earned by doing this activity.');?></p>

            <p><?php echo __d('credit','Max: maximum credits member can earn by doing this activity within rollover period.');?></p>

            <p><?php echo __d('credit',"Rollover period: is a period within max number of credits restriction is applied. '0' means that you want to apply Max number of credits to forever, can't earn more credit if the max amount is reached.");?></p>
        </div>
    <form method="post" id="frmCreditSetting" class="form-horizontal"
          action="<?php echo $this->request->base ?>/admin/credit/credit_settings/save">
        <table class="table table-striped table-bordered table-hover" id="sample_1">
            <thead>
            <tr class="tbl_head">
                <th width="40%"><?php echo __d('credit', 'Action type'); ?></th>
                <th><?php echo __d('credit', 'Credit'); ?></th>
                <th><?php echo __d('credit', 'Max Credit'); ?></th>
                <th><?php echo __d('credit', 'Rollover Period'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($group_actions)): ?>
                <?php $count = 0;
                $temp = array();
                foreach ($group_actions as $key => $items):
                    $plugin = Inflector::underscore($header[$key]);
                    ?>
                    <tr>
                        <td style="text-align: center;text-transform: capitalize;background: #D7E7F0;font-weight: bold;"
                            colspan="5"><?php echo __d($plugin, $key); ?></td>
                    </tr>
                    <?php foreach ($items as $action):
                    $action = $action['CreditActiontypes'];
                    $temp[] = $action['id'];
                    ?>
                    <tr class="gradeX" id="<?php echo $action['id'] ?>">
                        <td><?php echo __d($plugin, $action['action_type_name']); ?></td>
                        <td>
                            <input class="form-control" data-id="<?php echo $action['id']; ?>" type="text"
                                   value="<?php echo $action['credit'] ?>"
                                   name="credit[<?php echo $action['id'] ?>]"/>
                        </td>
                        <td>
                            <input class="form-control" type="text" value="<?php echo $action['max_credit'] ?>"
                                   name="max_credit_<?php echo $action['id'] ?>"/>
                        </td>
                        <td>
                            <input class="form-control" type="text" value="<?php echo $action['rollover_period'] ?>"
                                   name="rollover_period-<?php echo $action['id'] ?>"/>
                            <?php echo __d('credit', 'day(s)'); ?>
                        </td>
                    </tr>
                    <?php
                endforeach;
                endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">
                        <?php echo __d('credit', 'No item found'); ?>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <div class="form-actions">
            <div class="row">
                <div class="col-md-12">
                    <input type="submit" id="btnCreditSetting" value="<?php echo __d('credit', 'Save'); ?>"
                           class="btn btn-circle btn-action">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
echo $this->Html->scriptStart(array('inline' => false));
?>

var temp = <?php echo json_encode($temp); ?>,
i;

$(document).ready(function(){

$('#frmCreditSetting').validate({
errorClass: "error-message",
errorElement: "div"
});

$.validator.addMethod('compare',function(value, element, params){

return (parseInt(value) <= parseInt($("input[name='max_credit_" + params + "']").val())) ? true : false;
},"<?php echo __d('credit', 'Credit must less than Max Credit') ?>");

$("#btnCreditSetting").on('click',function(){
jQuery("input[name^='credit']").each(function(){

$(this).rules("add",{
required: true,
compare : $(this).attr('data-id')
});
});
});

});

<?php
echo $this->Html->scriptEnd();
?>
