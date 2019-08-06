<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "mooBehavior"], function($, mooBehavior) {
            mooBehavior.initMoreResults();
        });
    </script>
<?php endif;?>
<div class="bar-content">
    <div class="content_center">
        <h3 class="header_green">
            <?php echo __d('business', 'My reviews');?>
        </h3>
        <ul class="bus_my_review" id="my_review_content">
            <?php echo $this->element('Business.lists/my_review_list', array(
                'reviews' => $reviews,
                'more_url' => $more_url
            ));?>
        </ul>
    </div>
</div>
