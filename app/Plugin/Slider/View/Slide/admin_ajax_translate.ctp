<script>
    $(document).ready(function() {

        $('#tCreateButton').click(function() {

            disableButton('tCreateButton');
            $.post("<?php echo  $this->request->base ?>/admin/slider/slide/ajax_translate_save", $("#tCreateForm").serialize(), function(data) {
                enableButton('tCreateButton');
                var json = $.parseJSON(data);

                if (json.result == 1)
                    location.reload();
                else
                {
                    $(".error-message").show();
                    $(".error-message").html('<strong>Error!</strong>' + json.message);
                }
            });

            return false;
        });


    });

</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo  __d('slider', 'Translation') ?></h4>
</div>
<div class="modal-body">
    <form id="tCreateForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $slide['Slide']['id'])); ?>
        <?php foreach ($languages as $key => $language) : ?>

            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-4 control-label"><?php echo __d('slider','Slide caption heading');?> (<?php echo $language; ?>)</label>
                    <div class="col-md-8">
                        <?php foreach ($slide['nameTranslation'] as $translation) : ?>
                            <?php if ($translation['locale'] == $key) : ?>
                                <?php echo $this->Form->text('name.' . $key, array('placeholder' => __d('slider', 'Enter text'), 'class' => 'form-control', 'value' => $translation['content'])); ?>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>

            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-4 control-label"><?php echo __d('slider','Caption content');?> (<?php echo $language; ?>)</label>
                    <div class="col-md-8">
                        <?php foreach ($slide['textTranslation'] as $translation) : ?>
                            <?php if ($translation['locale'] == $key) : ?>
                                <?php echo $this->Form->textarea('text.' . $key, array('style' => 'height:100px', 'class' => 'form-control', 'value' => $translation['content'])); ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
<?php endforeach; ?>

    </form>

    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">

    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal"><?php echo  __d('slider', 'Close') ?></button>
    <a href="#" id="tCreateButton" class="btn btn-action"><?php echo  __d('slider', 'Save Change') ?></a>

</div>