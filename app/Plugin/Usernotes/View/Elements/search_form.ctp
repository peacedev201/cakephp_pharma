<?php if (empty($uid)): ?>
<?php else: ?>
    <div class="box2 search-friend note-search-form">
        <h3><?php echo __d('usernotes','Search') ?></h3>
        <div class="box_content">
            <form id="filters" method="">
                <ul class="list6">
                    <li><label></label><?php echo $this->Form->text('name',array('autocomplete'=>"off",'onkeypress'=>'return event.keyCode != 13;')); ?></li>
                    </li>
                    <li style="margin-top:20px"><input type="button" value="<?php echo __d('usernotes','Search') ?>" id="searchPeople" class="btn btn-action"></li>
                </ul>
            </form>
        </div>
    </div>
<?php endif; ?>