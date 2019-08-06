<div class="content_center_home">
    <div class="mo_breadcrumb">
         <h1><?php echo __d('contest','Contests');?></h1>
        <?php if($isProfileMe): ?>
        <a href="<?php echo $this->request->base?>/contests/create" class="button button-action topButton button-mobi-top"><?php echo __d('job','Create a new contest');?></a>
        <?php endif; ?>
    </div>
    <ul id="contest-content">
        <?php echo $this->element('lists/contests', array('contests' => $contests, 'more_url' => $more_url, 'is_more_url' => $is_more_url, 'params' => $params)); ?>
    </ul>
</div>