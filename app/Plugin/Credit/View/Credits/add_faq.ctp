<script type="text/javascript">
    require(["jquery","mooCredit"], function($,mooCredit) {
        $(document).ready(function () {
            $('#createButton').click(function () {
                mooCredit.creditFaqCreate();
            });
        });
    });
</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">
        <?php echo __d('credit','Add new FAQs')?>
    </h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-2"><?php echo __d('credit', 'Question');?></label>
                <div class="col-md-10">
                    <?php echo $this->Form->textarea('question', array('class' => 'form-control', 'value' => '')); ?>
                </div>
            </div>
        </div>
        <div class="alert alert-danger error-message" style="display:none;margin-top:10px;"></div>
        <div class="modal-footer">
            <button type="button" class="btn default" data-dismiss="modal"><?php echo  __d('credit','Close') ?></button>
            <a href="javascript:void(0)" id="createButton" class="btn btn-action"><?php echo  __d('credit','Save') ?></a>
        </div>
    </form>
</div>

<script type="text/javascript">
//    $(document).ready(function () {
//        $('#createButton').click(function () {
//            //mooCredit.creditFaqCreate();
//            disableButton('createButton');
//            $.post("<?php //echo  $this->request->base ?>///credits/faq_save", $("#createForm").serialize(), function (data) {
//                enableButton('createButton');
//                var json = $.parseJSON(data);
//
//                if (json.result == 1)
//                    location.reload();
//                else
//                {
//                    $(".error-message").show();
//                    $(".error-message").html('<strong>Error! </strong>' + json.message);
//                }
//            });
//
//            return false;
//        });
//        function toggleField()
//        {
//            $('.opt_field').toggle();
//        }
//    });
</script>
