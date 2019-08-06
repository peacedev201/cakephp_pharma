<?php
$this->Html->addCrumb(__d('credit', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('credit', 'FAQ Manager'), array('controller' => 'faqs', 'action' => 'admin_index'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Page"));
$this->end();
 echo $this->Moo->renderMenu('Credit', __d('credit', 'Manage FAQ'));
echo $this->Html->script(array('tinymce/tinymce.min'), array('inline' => false));
?>
<div class="portlet box">
    <div class="portlet-title">
    <h3 style="color: #000"><?php echo ($bIsEdit) ? __d('credit','Edit Faqs') : __d('credit','Add Faqs') ?></h3>
    </div>
    <div class="portlet-body form">
        <form id="createForm" class="form-horizontal" role="form">
            <?php echo $this->Form->hidden('id', array('value' => $faq['CreditFaq']['id'])); ?>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('credit', 'Question'); ?></label>
                    <div class="col-md-9">
                        <?php echo $this->Form->textarea('question', array('class' => 'form-control', 'value' => $faq['CreditFaq']['question'])); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('credit', 'Answer'); ?></label>
                    <div class="col-md-9">
                        <?php echo $this->Form->textarea('answer', array('class' => 'form-control', 'value' => $faq['CreditFaq']['answer'],'id' => 'answer')); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('credit', 'Active'); ?></label>
                    <div class="col-md-9">
                        <div class="checkbox-list">
                            <label class="checkbox-inline">
                                <?php echo $this->Form->checkbox('active', array('checked' => $faq['CreditFaq']['active'])); ?>

                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-danger error-message" style="display:none;margin-top:10px;"></div>
            <div class="modal-footer">
                <a href="#" id="createButton" class="btn btn-action"><?php echo __d('credit', 'Save') ?></a>
                <a type="button" onclick="window.history.go(-1); return false;" href="javascript:void(0)" class="btn default"  ?><?php echo __d('credit', 'Close') ?></a>
            </div>
        </form>
    </div>
</div>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).ready(function () {
        tinymce.init({
            selector: "#answer",
            theme: "modern",
            skin: 'light',
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor"
            ],
            toolbar1: "styleselect | bold italic | bullist numlist outdent indent | forecolor backcolor emoticons | link unlink anchor image media | preview fullscreen code",
            image_advtab: true,
            height: 200
        });
        $('#createButton').click(function () {
            $('#answer').val(tinyMCE.activeEditor.getContent());
            disableButton('createButton');
            $.post("<?php echo $this->request->base ?>/admin/credit/credit_faqs/save", $("#createForm").serialize(), function (data) {
                enableButton('createButton');
                var json = $.parseJSON(data);

                if (json.result == 1)
                    window.location.href = "<?php echo $this->request->base; ?>/admin/credit/credit_faqs";
                else {
                    $(".error-message").show();
                    $(".error-message").html('<strong><?php echo __d('credit','Error') ?>! </strong>' + json.message);
                }
            });

            return false;
        });
        function toggleField() {
            $('.opt_field').toggle();
        }
    });
<?php $this->Html->scriptEnd(); ?>
