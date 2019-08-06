<div>
    <p><?php echo __d('credit','You can edit how many credits member can earn for each action within a specific period'); ?></p>

    <p><?php echo __d('credit',"Credit: number of credits can earn by doing this activity. '0' means that no credit will be be earned by doing this activity.");?></p>

    <p><?php echo __d('credit','Max: maximum credits member can earn by doing this activity within rollover period.');?></p>

    <p><?php echo __d('credit',"Rollover period: is a period within max number of credits restriction is applied. '0' means that you want to apply Max number of credits to forever, can't earn more credit if the max amount is reached.");?></p>
</div>

<table class="table table-striped table-bordered credit-info-table">
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
                <td class="td_header" colspan="5"><?php echo __d($plugin, $key); ?></td>
            </tr>
            <?php foreach ($items as $action):
            $action = $action['CreditActiontypes'];
            $temp[] = $action['id'];
            ?>
            <tr class="gradeX" id="<?php echo $action['id'] ?>">
                <td><?php echo __d($plugin, $action['action_type_name']); ?></td>
                <td>
                    <?php echo round($action['credit'],2); ?>
                </td>
                <td>
                    <?php echo $action['max_credit'] ?>
                </td>
                <td>
                    <?php echo $action['rollover_period'] ?>
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