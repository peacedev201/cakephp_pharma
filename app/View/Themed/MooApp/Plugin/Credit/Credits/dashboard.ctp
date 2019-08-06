<script>
function doRefesh()
{
    location.reload();
}
</script>
<?php 
	echo $this->Html->css(array('jquery-ui', 'footable.core.min','token-input'), null, array('inline' => false));
	$creditHelper = MooCore::getInstance()->getHelper('Credit_Credit'); 
?>

<?php if(Configure::read("Credit.member_can_send_credit") == 'send_friend_only'): ?>

    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooCredit'), 'object' => array('mooCredit'))); ?>
        mooCredit.initCreditSendToFriend();
    <?php $this->Html->scriptEnd(); ?>

<?php else: ?>

    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooCredit'), 'object' => array('mooCredit'))); ?>
        mooCredit.initCreditSendToMember();
    <?php $this->Html->scriptEnd(); ?>

<?php endif; ?>

<div class="bar-content">
    <div class="content_center">
    	<div id="list-content">

			<div class="send_credit">
    			<ul class="credit-content-list">
    				<li class="full_content p_m_10">	    			
						<div class="mo_breadcrumb">
		    				<h3 style="margin: 0;"><?php echo __d('credit', 'Credit Rank'); ?></h3>
		    			</div>	    	
					    <div class="box_content credit_badge" style="width: 100%;display: inline-block;">
				            <div class="ranks-image">
				                <img width="60" src="<?php echo $creditHelper->getImageRank($now_rank, array('prefix' => '150_square'))?>" id="item-avatar" class="img_wrapper">
				            </div>
				            <div class="badge-info">
				                <div class="badge-rank">
				                    <div class="badge-rank-now" style="width: <?php echo $width_rank;?>%;">
				                        <?php echo round($item['CreditBalances']['current_credit'],2);?>
				                    </div>
				                </div>
				                <?php if(!empty($next_rank)):?>
				                <div class="badge-rank-next">
				                        <?php echo round($next_rank['CreditRanks']['credit'],2);?>
				                </div>
				                <?php endif;?>
				            </div>
				            <?php if(!empty($next_rank)):?>
				            <div class="badge-info-next-rank">
				                <?php echo __d('credit','Next Rank');?> :
				                <span><?php echo htmlspecialchars($next_rank['CreditRanks']['name']);?></span>
				            </div>
				            <?php endif;?>
				        </div>
    				</li>
    			</ul>    			
			</div>

    		<div class="send_credit">   
    			<ul class="credit-content-list">
    				<li class="full_content p_m_10">	 			
						<div class="mo_breadcrumb">
		    				<h3 style="margin: 0;"><?php echo __d('credit', 'Credit Statistics'); ?></h3>
		    			</div>	    	
					    <div class="box_content">
					        <div class="ranking-content">
					        </div>
					        <div class="rank">
					            <p style='font-size: 38px; text-align: center; font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;'>
					                <b>
					                    <?php echo (isset($item['CreditBalances']['current_credit'])) ? round($item['CreditBalances']['current_credit'],2) : "0";?>
					                </b>
					            </p>
					        </div>
					        <div class="rank-info">
					            <div>
					                <i class="current-balance"></i>
					                <p>   <?php echo __d('credit','Current Balance');?> <em><?php echo (isset($item['CreditBalances']['current_credit'])) ? round($item['CreditBalances']['current_credit'],2) : "0";?></em>  </p>
					            </div>
					            <div>
					                <i class="total-earn-credit"></i>
					                <p><?php echo __d('credit','Total Earned Credits');?> <em><?php echo (isset($item['CreditBalances']['earned_credit'])) ? round($item['CreditBalances']['earned_credit'],2) : "0";?></em></p>
					            </div>
					            <div>
					                <i class="total-spent-credit"></i>
					                <p><?php echo __d('credit','Total Spent Credits');?> <em><?php echo (isset($item['CreditBalances']['spent_credit'])) ? round($item['CreditBalances']['spent_credit'],2) : "0";?></em></p>
					            </div>
					        </div>
					    </div>
    				</li>
    			</ul>    			
			</div>

			<div class="send_credit">
    			<ul class="credit-content-list">
    				<li class="full_content p_m_10">	    			
						<div class="mo_breadcrumb">
		    				<h3 style="margin: 0;"><?php echo __d('credit', 'Buy Credits'); ?></h3>
		    			</div>	    	
					    <div class="box_content ">
						   <div class="credit_buy_desc">
					           <?php echo __d('credit', 'You can buy credits using your PayPal account, just click on Buy Credits button');?>
					       </div>
					       <div class="btt_buy_credit">
					            <a class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" href="<?php echo $this->request->base?>/credits/ajax_sell" class="" title="<?php echo __d('credit', 'Buy Credits')?>"><?php echo __d('credit', 'Buy Credits')?>! </a>
					       </div>
						</div>
    				</li>
    			</ul>    			
			</div>

			<div class="send_credit">
    			<ul class="credit-content-list">
    				<li class="full_content p_m_10">	    			
						<div class="mo_breadcrumb">
		    				<h3 style="margin: 0;"><?php echo __d('credit', 'Send Credits'); ?></h3>
		    			</div>	    	
					    <div class="box_content ">
				            <div class="create_form">
				                <form id="sendCredits">
				                    <ul class="list6 list6sm2" style="position:relative">
				                        <li>
				                            <div class="col-sm-12">
				                                <label style="width: 100%;text-align: left;">
				                                    <?php 
				                                        if(Configure::read("Credit.member_can_send_credit") == 'send_friend_only'){
				                                            echo __d('credit','Your Friend Name');
				                                        }else{
				                                            echo __d('credit','Member Name');
				                                        }
				                                    ?>
				                                </label>
				                            </div>
				                            <div class="col-sm-12 credit_suggest_friend">
				                                <?php 
				                                    if(Configure::read("Credit.member_can_send_credit") == 'send_friend_only'){
				                                        echo $this->Form->friendSuggestion();
				                                    }else{
				                                        echo $creditHelper->memberSuggestion();
				                                    }
				                                ?>
				                            </div>
				                            <div class="clear"></div>
				                        </li>
				                        <li>
				                            <div class="col-sm-2">
				                                <label><?php echo __d('credit', 'Credit')?></label>
				                            </div>
				                            <div class="col-sm-12">
				                                <?php echo $this->Form->text('credit'); ?>
				                            </div>
				                            <div class="clear"></div>
				                        </li>
				                        <li>
				                            <div class="col-sm-1">
				                                <a href="#" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" id="sendButton"><?php echo __d('credit','Send Credits')?>
				                                </a>
				                            </div>
				                            <div class="clear"></div>
				                        </li>
				                    </ul>
				                </form>
				            </div>
				            <div class="error-message" style="display:none;"></div>
				            <div class="alert-success" style="display:none;"></div>
				        </div>
    				</li>
    			</ul>    			
			</div>

    	</div>		
    </div>
</div>