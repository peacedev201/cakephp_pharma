<?php echo $this->Html->css(array('/popup/css/main'), null, array('inline' => false)); ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>

	$.fn.imagefit = function(options) {
		this.each(function(){
				var container = this;
				var imgs = $('img', container).not($("table img"));
				imgs.each(function(){
					$(this).css('max-width', '100%');
				});
			});
		return this;
	};
});</script>

<div class="modal fade" id="mooPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
    <h4 class="modal-title" id="myModalLabel"><?php echo $popup['Popup']['title']; ?></h4>
    </div>
    <div class="modal-body content_popup">
    <?php echo $popup['Popup']['body']; ?>
    </div>

    <div style="text-align: left;" class="modal-footer">
    <?php
    if($popup['Popup']['popup_option'] == 1):
    ?>
    <label class="checkbox-inline"><input type="checkbox" name="showPopupAgain" id="showPopupAgain" ><?php echo __d('popup','I got it, do not show it again.');?></label>
    <?php endif;?>
    <button type="button" class="btn btn-default pull-right" data-dismiss="modal"><?php echo __d('popup','Close');?></button>
    </div>

    </div>
    </div>
    </div>
    <script type="text/javascript">
    require(["jquery","bootstrap"], function($) {
    $('#mooPopup').insertBefore($('#footer'));
    $('#showPopupAgain').click(function() {
        if ($('#showPopupAgain').is(":checked")){
            var data = {idpopup: <?=$popup['Popup']['id'];?>, valuepopup: 0}
            $.post('<?php echo $this->base;?>/popups/write_session_popup',data);
        }
        else{
            var data = {idpopup: <?=$popup['Popup']['id'];?>, valuepopup: 1}
            $.post('<?php echo $this->base;?>/popups/write_session_popup',data);
        }
    });
	$(document).ready(function(){
		$('#mooPopup').modal();
		console.log('dkm');
		$('#mooPopup').imagefit();
	});
<?php $this->Html->scriptEnd(); ?>