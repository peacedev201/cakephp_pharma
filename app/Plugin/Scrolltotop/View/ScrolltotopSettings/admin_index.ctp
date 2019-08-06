<?php
    echo $this->Html->css(array('jquery-ui','Scrolltotop.admin','Scrolltotop.jquery.miniColors', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui','Scrolltotop.jquery.miniColors.min', 'footable'), array('inline' => false));
    $this->Html->addCrumb(__('Plugins Manager'), '/admin/plugins');
	$this->Html->addCrumb(__d('scrolltotop','Scroll To Top Setting'), array('controller' => 'scrolltotop_settings', 'action' => 'admin_index'));
	
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Scroll To Top'));
    $this->end();
    
    $helper = MooCore::getInstance()->getHelper('Scrolltotop_Scrolltotop');
    __d('scrolltotop','Scroll To Top');
?>
<style>
	<?php echo $helper->getCss();?>
</style>
<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo$this->Moo->renderMenu('Scrolltotop', __d('scrolltotop','Settings'));?>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">                           
                           <form class="form-horizontal intergration-setting" method="post" enctype="multipart/form-data">                           	   
							   <div class="form-body">
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Enable Scroll To Top');?>                          
							            </label>
							            <div class="col-md-7">
                                            <?php
                                            	echo $this->Form->input('enable', array(
			                                    'type' => 'checkbox', 
			                                    'checked' => Configure::read('Scrolltotop.scrolltotop_enable'),
			                                    'label' => '',                                    
			                                )); 
                                            ?>                                                                                    
                                        </div>								            
							        </div>
							        <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Display Position');?>                          
							            </label>
							            <div class="col-md-7">
							            	<div class="position">
								             	<input class="position_radio" type="radio" name="data[display_position]" id="display_position1" <?php if (Configure::read('Scrolltotop.scrolltotop_display_position') == 'TL'): ?>checked<?php endif;?> value="TL" />
												<label for="display_position1"><?php echo __d('scrolltotop','Top Left');?></label>
												<img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'scrolltotop/img/ScrollToTop_Location_TL.jpg';?>">
											</div>
											<div class="position">
												<input class="position_radio" type="radio" name="data[display_position]" id="display_position2" <?php if (Configure::read('Scrolltotop.scrolltotop_display_position') == 'TR'): ?>checked<?php endif;?> value="TR" />
												<label for="display_position2"><?php echo __d('scrolltotop','Top Right');?></label>
												<img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'scrolltotop/img/ScrollToTop_Location_TR.jpg';?>">
											</div>
											<div class="position">
												<input class="position_radio" type="radio" name="data[display_position]" id="display_position3" <?php if (Configure::read('Scrolltotop.scrolltotop_display_position') == 'BL'): ?>checked<?php endif;?> value="BL" />
												<label for="display_position3"><?php echo __d('scrolltotop','Bottom Left');?></label>
												<img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'scrolltotop/img/ScrollToTop_Location_BL.jpg';?>">
											</div>
											<div class="position">
												<input class="position_radio" type="radio" name="data[display_position]" id="display_position4" <?php if (Configure::read('Scrolltotop.scrolltotop_display_position') == 'BR'): ?>checked<?php endif;?> value="BR" />
												<label for="display_position4"><?php echo __d('scrolltotop','Bottom Right');?></label>
												<img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'scrolltotop/img/ScrollToTop_Location_BR.jpg';?>">
											</div>                                                                                 
	                                     </div>	 						           
								    </div>
								    <div id="margin_top" class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Margin Top');?>                          
							            </label>
							            <div class="col-md-7">
							            	<div class="col-md-7">
							            		<input class="form-control px" name="data[margin_top]" value="<?php echo Configure::read('Scrolltotop.scrolltotop_margin_top');?>" class="form-control" type="text">&nbsppx		                                            
	                                        </div>	
							           	</div>
                                     </div>
                                     <div id="margin_left" class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Margin Left');?>                          
							            </label>
							            <div class="col-md-7">
							            	<div class="col-md-7">
							            		<input class="form-control px" name="data[margin_left]" value="<?php echo Configure::read('Scrolltotop.scrolltotop_margin_left');?>" class="form-control" type="text">&nbsppx		                                            
	                                        </div>	
							           	</div>
                                     </div>
								    <div id="margin_bottom" class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Margin Bottom');?>                          
							            </label>
							            <div class="col-md-7">
							            	<div class="col-md-7">
							            		<input class="form-control px" name="data[margin_bottom]" value="<?php echo Configure::read('Scrolltotop.scrolltotop_margin_bottom');?>" class="form-control" type="text">&nbsppx		                                            
	                                        </div>	
							           	</div>
                                     </div>	
                                     <div id="margin_right" class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Margin Right');?>                          
							            </label>
							            <div class="col-md-7">
							            	<div class="col-md-7">
							            		<input class="form-control px" name="data[margin_right]" value="<?php echo Configure::read('Scrolltotop.scrolltotop_margin_right');?>" class="form-control" type="text">&nbsppx		                                            
	                                        </div>	
							           	</div>
                                     </div>
                                     <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Time Back to Top');?>                          
							            </label>
							            <div class="col-md-7">
							            	<div class="col-md-7">
							            		<input class="form-control px" name="data[time_back]" value="<?php echo Configure::read('Scrolltotop.scrolltotop_time_back');?>" class="form-control" type="text">&nbspmillisecond		                                            
	                                        </div>	
							           	</div>
                                     </div>		
                                     <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Show After');?>                          
							            </label>
							            <div class="col-md-7">
							            	<div class="col-md-7">
							            		<input class="form-control px" name="data[show_after]" value="<?php echo Configure::read('Scrolltotop.scrolltotop_show_after');?>" class="form-control" type="text">&nbsppx		                                            
	                                        </div>	
							           	</div>
                                     </div>	
                                     <div class="form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Choose Your Style');?>                          
							            </label>
							            <div class="col-md-7">
							            	<div class="style">
								             	<input class="style_radio" type="radio" name="data[your_style]" <?php if (Configure::read('Scrolltotop.scrolltotop_your_style') == '1'): ?>checked<?php endif;?> value="1" />
												<div><img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'scrolltotop/img/Style_Demo_1.png';?>"></div>
											</div> 
											<div class="style">
								             	<input class="style_radio"  type="radio" name="data[your_style]" <?php if (Configure::read('Scrolltotop.scrolltotop_your_style') == '5'): ?>checked<?php endif;?> value="5" />
												<div><img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'scrolltotop/img/Style_Real_5.png';?>"></div>
											</div> 
											<div class="style">
								             	<input class="style_radio"  type="radio" name="data[your_style]" <?php if (Configure::read('Scrolltotop.scrolltotop_your_style') == '7'): ?>checked<?php endif;?> value="7" />
												<div><img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'scrolltotop/img/Style_Real_7.png';?>"></div>
											</div>  
											<div class="style">
								             	<input class="style_radio"  type="radio" name="data[your_style]" <?php if (Configure::read('Scrolltotop.scrolltotop_your_style') == '2'): ?>checked<?php endif;?> value="2" />
												<div><img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'scrolltotop/img/Style_Demo_2.png';?>"></div>
											</div> 
											<div class="style">
								             	<input class="style_radio"  type="radio" name="data[your_style]" <?php if (Configure::read('Scrolltotop.scrolltotop_your_style') == '3'): ?>checked<?php endif;?> value="3" />
												<div><img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'scrolltotop/img/Style_Demo_3.png';?>"></div>
											</div> 
											<div class="style">
								             	<input class="style_radio"  type="radio" name="data[your_style]" <?php if (Configure::read('Scrolltotop.scrolltotop_your_style') == '4'): ?>checked<?php endif;?> value="4" />
												<div><img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'scrolltotop/img/Style_Real_4.gif';?>"></div>
											</div> 
											<div class="style">
								             	<input class="style_radio"  type="radio" name="data[your_style]" <?php if (Configure::read('Scrolltotop.scrolltotop_your_style') == '6'): ?>checked<?php endif;?> value="6" />
												<div><img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'scrolltotop/img/Style_Real_6.gif';?>"></div>
											</div>                                                                                  
	                                     </div>	 						           
								     </div>	
								     <div class="background form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Background Color');?>                          
							            </label>
							            <div class="col-md-7">
							            	<div class="col-md-7">
							            		<input class="color-picker form-control" name="data[background_color]" value="<?php echo Configure::read('Scrolltotop.scrolltotop_background_color');?>" class="form-control" type="text">	                                            
	                                        </div>	
							           	</div>
                                     </div>	
                                     <div class="background form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Background Hover Color');?>                          
							            </label>
							            <div class="col-md-7">
							            	<div class="col-md-7">
							            		<input class="color-picker form-control" name="data[background_hover_color]" value="<?php echo Configure::read('Scrolltotop.scrolltotop_background_hover_color');?>" class="form-control" type="text">	                                            
	                                        </div>	
							           	</div>
                                     </div>	
                                     <div class="text form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Text Display');?>                          
							            </label>
							            <div class="col-md-7">
							            	<div class="col-md-7">
							            		<input class="input_text form-control" name="data[text_display]" value="<?php echo Configure::read('Scrolltotop.scrolltotop_text_display');?>" class="form-control" type="text">	                                            
	                                        </div>	
							           	</div>
                                     </div>	
                                     <div class="text form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Text Color');?>                          
							            </label>
							            <div class="col-md-7">
							            	<div class="col-md-7">
							            		<input class="color-picker form-control" name="data[text_color]" value="<?php echo Configure::read('Scrolltotop.scrolltotop_text_color');?>" class="form-control" type="text">	                                            
	                                        </div>	
							           	</div>
                                     </div>	
                                     <div class="text form-group">
							            <label class="col-md-3 control-label">
							                <?php echo __d('scrolltotop','Text Hover Color');?>                          
							            </label>
							            <div class="col-md-7">
							            	<div class="col-md-7">
							            		<input class="color-picker form-control" name="data[text_hover_color]" value="<?php echo Configure::read('Scrolltotop.scrolltotop_text_hover_color');?>" class="form-control" type="text">	                                            
	                                        </div>	
							           	</div>
                                     </div>		
								     <div class="form-actions">
								        <div class="row">
								            <div class="col-md-offset-3 col-md-9">
								                <input type="submit" class="btn btn-circle btn-action" value="<?php echo __d('scrolltotop','Save Settings');?>">
								            </div>
								        </div>
								     </div>
							    </div>
						    </form>
						    <h3><?php echo __d('scrolltotop','Test area');?></h3>
						    <div>
								<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt leo et metus elementum nec commodo velit malesuada. Suspendisse laoreet enim sodales metus tempor blandit at a dolor. Vivamus urna dolor, semper nec tristique eu, gravida eu urna. Vestibulum eu hendrerit quam. Mauris pharetra aliquet magna, eleifend sollicitudin nibh fermentum ut. Maecenas vitae diam et massa porttitor lobortis quis sed elit. Duis enim tortor, suscipit eget blandit id, fringilla non urna. Sed placerat purus nec diam dignissim sagittis. Sed facilisis auctor nisi. Nulla feugiat ornare tincidunt. Curabitur vel mi nibh, sed blandit ligula. Nam sit amet velit a ligula egestas posuere. Duis sollicitudin urna eget eros sagittis iaculis. Curabitur lacus ante, ullamcorper eget porta nec, tincidunt id ante. </p>
								<br />
								<p> Cras posuere consectetur feugiat. Curabitur tincidunt, arcu eu euismod mattis, nunc metus varius ante, non elementum ligula est quis justo. Phasellus nec orci et diam dictum aliquet ut vel sem. Integer vel massa massa, sit amet sagittis enim. Nullam interdum, lacus eget molestie aliquam, enim leo consequat purus, ut porttitor sapien purus nec neque. In sit amet orci est. Nulla facilisi. Quisque eu odio mi, nec mollis purus. Nullam sollicitudin porttitor enim, non dignissim nunc feugiat in. Sed non elit arcu. Quisque et elit dui. Ut at mauris massa. Quisque sit amet felis mauris, sit amet fermentum orci. Maecenas luctus euismod neque, quis semper sapien rhoncus eget. </p>
								<br />
								<p> Nam et arcu risus. Fusce eu magna ac orci bibendum porta. Cras malesuada cursus tellus, eget ultricies augue venenatis sit amet. Ut volutpat justo id ligula ullamcorper id rutrum diam rhoncus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Vestibulum sed lectus vel ipsum euismod placerat. Nullam sodales mauris eget felis ullamcorper at porttitor risus rutrum. Nam sit amet sapien eget eros vulputate sollicitudin. Nam varius purus quam. </p>
								<br />
								<p> Donec non sem nisl, quis lacinia augue. Quisque vel eros eget nulla ornare imperdiet. Quisque eu diam ipsum, eget dapibus nulla. Suspendisse non mauris nunc. Praesent et varius ipsum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam nisi mi, placerat ac tristique at, venenatis vitae diam. Mauris auctor mattis arcu, vel elementum neque ornare bibendum. </p>
								<br />
								<p> Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. In tincidunt, metus et lacinia feugiat, nulla urna laoreet nisl, quis sagittis lorem leo eget diam. Proin viverra eleifend nisi in varius. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Duis nisl augue, varius vitae consequat in, bibendum in justo. Nullam quis ligula risus, facilisis hendrerit libero. Curabitur non odio arcu. Phasellus auctor posuere dapibus. Proin consectetur, nunc sed eleifend egestas, est metus facilisis dolor, nec aliquam felis justo eget velit. Pellentesque ut tincidunt nisi. </p>
								<br />
								<p> Vestibulum pellentesque sodales nunc, eget pulvinar erat rhoncus a. Nullam id diam at velit rhoncus ultricies id id tortor. Aenean auctor sollicitudin eros sed mattis. Suspendisse odio massa, interdum in luctus quis, tristique quis purus. Nulla eget massa non sapien posuere vehicula ut quis lorem. Aenean urna eros, rutrum vitae congue sit amet, auctor sed est. In vel ipsum urna. Nulla consequat consequat justo ac lacinia. Suspendisse a porttitor nulla. Nulla tincidunt ante mollis felis accumsan at ullamcorper magna consequat. Ut at massa in tellus molestie pellentesque in in risus. Sed euismod, nibh ut sodales vehicula, lectus enim tincidunt metus, eu adipiscing leo magna non tortor. Mauris condimentum tincidunt mi, a mollis dolor dignissim eu. Donec sit amet turpis augue, non fringilla dui. </p>
								<br />
								<p> Mauris arcu tellus, dictum at hendrerit nec, lobortis in orci. Cras ultrices cursus ligula, nec rhoncus neque suscipit ut. Ut ornare tellus quis diam vestibulum consectetur. In semper euismod dui eget aliquet. Pellentesque imperdiet mollis nisi. Mauris non sem quis sem viverra hendrerit. Vestibulum iaculis lobortis erat, vitae gravida dui commodo in. Donec tristique venenatis erat, vel porta massa aliquet sit amet. Mauris pulvinar interdum dolor, vel vestibulum elit fringilla at. </p>
								<br />
								<p> Morbi sagittis mi sed tortor elementum viverra vel sed nunc. Quisque magna nibh, consequat et vulputate sed, dictum ac mi. Aliquam sit amet turpis lorem. Suspendisse id hendrerit augue. Nam elementum purus quis turpis luctus sit amet volutpat odio dignissim. Maecenas nibh purus, iaculis et tempus non, convallis a tortor. Phasellus facilisis, ante eget fringilla rhoncus, sem leo dapibus enim, nec dictum lorem nisi sed lectus. Nulla facilisi. Morbi ultrices euismod sem, quis lacinia metus placerat ut. </p>
								<br />
								<p> Proin viverra ante faucibus nisi suscipit tincidunt. Sed imperdiet elit ut mi hendrerit congue. Curabitur varius scelerisque ipsum non tempus. Phasellus posuere vulputate venenatis. Duis mollis metus et libero tristique pretium iaculis velit gravida. In mollis mauris sit amet lacus pulvinar sit amet semper dolor rhoncus. Aliquam sit amet eros vitae massa tristique elementum. Nulla in est ante, et malesuada tellus. Aliquam ut sem lectus. Nam vulputate congue leo, sit amet blandit eros rutrum ut. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas dolor felis, tempus sed auctor et, faucibus et felis. Aliquam quam lorem, tincidunt nec tempor eget, ornare sit amet leo. </p>
								<br />
								<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean porta tellus ac nunc tempus lacinia ut ac velit. Cras dapibus erat eget justo aliquet sit amet ultrices magna vestibulum. Donec sit amet tellus et purus convallis tincidunt. Curabitur ullamcorper malesuada augue sit amet lacinia. Integer mi mi, venenatis eu ullamcorper ut, congue in velit. Ut ut ultricies ante. Phasellus non dapibus odio. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Morbi ante leo, hendrerit at porttitor quis, semper sed mi. Vestibulum vitae nunc metus. Praesent tortor nulla, accumsan non auctor sed, congue ut enim. Etiam eros turpis, sagittis in gravida ut, lacinia at leo. Curabitur vitae nulla elit. Fusce ante justo, mattis in dictum sed, blandit ut sem. Nullam a velit eu velit volutpat aliquet eu et neque. </p>
								<br />
								<p> Vivamus ullamcorper, risus et cursus dapibus, augue lacus eleifend turpis, eget lacinia urna lectus non dui. Sed augue dolor, tincidunt nec sagittis sit amet, semper eu tortor. Cras quis neque orci, tincidunt ornare dolor. Cras eget ante sapien, at egestas est. Integer scelerisque scelerisque diam. Aliquam eu nulla est, at pretium leo. Aenean ac neque nec urna molestie luctus. Praesent mattis ullamcorper sem quis sodales. Aenean laoreet congue mi, et commodo massa aliquam eu. Donec tincidunt viverra vestibulum. Aliquam sollicitudin metus lacus. Fusce ut mi neque. Mauris nec augue id libero pretium facilisis. Ut vehicula semper suscipit. </p>
								<br />
								<p> Pellentesque orci metus, posuere at mollis a, blandit in orci. Duis fringilla rhoncus diam in rutrum. Vestibulum quis lorem eu nisi dapibus tempus. Quisque accumsan sem et dolor tincidunt fermentum. Sed bibendum aliquet magna, et feugiat purus iaculis consequat. Vestibulum quam mauris, consectetur eget hendrerit sit amet, posuere eget neque. Mauris nec lorem nisl, et euismod urna. Vestibulum posuere felis vel massa facilisis mollis. Proin volutpat, metus quis varius hendrerit, tortor lorem viverra purus, ut aliquam lectus lectus vitae diam. </p>
								<br />
								<p> Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse non ipsum dolor, a malesuada massa. Vivamus facilisis nunc at dolor egestas in sollicitudin massa pharetra. Aenean fermentum magna quis nisi iaculis tincidunt nec sed nisl. Mauris metus quam, tincidunt vitae luctus non, bibendum nec dolor. Donec blandit sollicitudin porttitor. Etiam auctor, ligula eget placerat posuere, metus mauris blandit dolor, in feugiat ipsum lacus non nisi. </p>
								<br />
								<p> Donec tristique ante augue, sed semper orci. Aliquam in nisi vel magna luctus tempus vel tristique risus. Nunc ac accumsan diam. Vestibulum ultrices augue non lacus laoreet at congue velit viverra. Cras consequat ornare diam ac commodo. Phasellus tellus purus, tempus vel vestibulum nec, elementum sed lectus. Vestibulum aliquam, lorem at ultrices mollis, tortor orci cursus augue, sed rhoncus nulla mauris cursus neque. Maecenas eu justo turpis, eget consequat sem. Nam fringilla vulputate turpis id tristique. In bibendum, mi a porta lacinia, orci libero vulputate metus, non ornare turpis quam pretium lectus. In hac habitasse platea dictumst. Donec euismod semper lorem, sed adipiscing dui vestibulum id. </p>
								<br />
								<p> Maecenas aliquam mollis rutrum. Aliquam quam quam, placerat id bibendum eu, dapibus at nisi. Suspendisse nec nibh justo. Cras at magna non nulla sollicitudin commodo dignissim sed eros. Aliquam erat volutpat. Donec quis felis in ipsum congue fermentum. Fusce auctor, mauris id gravida pellentesque, tortor ipsum ultrices erat, sed ultrices erat ante vitae sapien. Cras ligula nibh, elementum et luctus feugiat, consectetur eget elit. </p>
								<br />
								<p> Integer ut metus accumsan eros ornare pellentesque et a eros. Fusce at mauris sapien, at interdum justo. Integer venenatis commodo ipsum, fermentum rhoncus massa facilisis eget. Donec luctus, risus sit amet aliquet ornare, eros est commodo metus, pretium dictum nulla sem eget dolor. Donec luctus mauris sit amet metus commodo sed congue ligula feugiat. Curabitur sit amet nisi metus. In hac habitasse platea dictumst. Nunc sit amet magna tempor est rutrum congue nec nec nibh. Suspendisse arcu nisl, feugiat non condimentum eu, malesuada vel lectus. Nam vel metus odio, in molestie nibh. Nam arcu lectus, lacinia sed semper eu, dictum sed metus. </p>
								<br />
								<p> Phasellus consectetur, lectus nec viverra sodales, dolor dolor interdum nunc, vitae mollis elit sem sed nisi. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque mauris mi, euismod sed sollicitudin ac, tincidunt at velit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Etiam accumsan, turpis ac vehicula tempor, quam metus feugiat leo, vel molestie sapien orci vel velit. Morbi ut diam non justo sagittis iaculis dignissim non mauris. Donec consequat auctor tincidunt. Maecenas iaculis adipiscing bibendum. Morbi ut tellus eros. </p>
								<br />
								<p> Nunc eget eleifend nisl. Ut quis velit non eros molestie vulputate vulputate feugiat justo. Duis eu ligula purus, ut pharetra mi. Donec ut diam quis eros rutrum consequat non sit amet ante. Etiam sollicitudin varius augue non sagittis. Praesent congue imperdiet suscipit. Sed mollis nisi id augue rhoncus rhoncus. Integer vitae tortor quis ligula interdum tempor non ut nisi. Donec dignissim lacus vitae lacus porta viverra. </p>
								<br />
								<p> Proin orci lectus, tristique a ornare sit amet, mollis sit amet ipsum. Morbi auctor arcu eu nisi interdum iaculis. Donec a tortor id velit sagittis pretium. Maecenas a urna eu nunc tristique blandit. Vestibulum mi velit, lobortis id malesuada sit amet, dignissim non sem. Fusce ut eros eros. Morbi ut est non nisi interdum aliquet at ut quam. </p>
								<br />
								<p> Nam lorem turpis, tincidunt sit amet pulvinar sed, tempor non leo. Nulla pharetra lorem ac elit interdum mollis. Morbi varius vulputate dignissim. Vivamus libero augue, mollis ultrices tincidunt vel, luctus vel dolor. Etiam in ante eu sem consectetur aliquam eget ut lectus. Duis leo mauris, convallis eget rutrum sed, fermentum in leo. Aliquam erat volutpat. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse ultrices dui sit amet ligula ultrices sed rhoncus arcu eleifend. In id fermentum elit. Aliquam vel magna id massa vehicula pharetra. Cras vel neque lacus. Proin sollicitudin purus ut mi placerat scelerisque. Pellentesque sit amet est mauris. Nulla et turpis volutpat tellus varius commodo eu et massa. </p>
								<br />
								<p> Quisque id orci lorem. Curabitur non nibh leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Quisque neque lacus, interdum sit amet volutpat nec, imperdiet eget neque. Aenean vulputate urna nec nibh rhoncus vitae porttitor sem suscipit. Phasellus eu orci lacus. Nunc interdum justo quis augue luctus non porttitor enim hendrerit. Vestibulum enim odio, pretium sed volutpat eget, faucibus vel tortor. Sed eu tellus et nunc placerat hendrerit a eu ante. Phasellus egestas, diam vulputate tempor vulputate, leo est rutrum diam, at pulvinar risus elit ac massa. In hac habitasse platea dictumst. In ante turpis, pellentesque in tincidunt in, vestibulum nec est. Praesent feugiat urna quis velit sagittis ac luctus diam tristique. Integer vulputate, orci eu convallis luctus, metus tellus feugiat metus, sed porta massa urna a sapien. </p>
								<br />
								<p> Sed leo libero, gravida in bibendum a, pulvinar quis dui. Duis sit amet metus non justo fringilla laoreet. Donec et dapibus mauris. Nullam eu pulvinar velit. Nullam congue, leo nec pharetra scelerisque, est elit pulvinar diam, quis blandit sapien tortor in lectus. Sed at nunc at elit rhoncus vehicula vitae at turpis. Suspendisse potenti. Suspendisse elementum malesuada est, mattis ullamcorper purus rhoncus at. Phasellus ut turpis quis nunc sollicitudin aliquet eu id erat. Proin scelerisque lacinia nisi ac aliquet. Suspendisse sagittis est at justo tincidunt pharetra. Nulla facilisi. Duis et cursus diam. Nullam posuere venenatis erat. In ac elit massa. Fusce lobortis commodo massa a pretium. </p>
								<br />
								<p> Praesent malesuada laoreet libero in aliquam. Praesent lobortis ipsum ut risus porta hendrerit. Suspendisse venenatis gravida nisi et lobortis. Etiam fringilla leo vitae sem elementum adipiscing. Vivamus egestas dapibus magna, quis sollicitudin ligula varius in. Duis velit nunc, euismod sit amet porttitor vitae, feugiat nec enim. Quisque adipiscing mauris non enim volutpat a lobortis orci hendrerit. Vestibulum varius lobortis massa, eget pellentesque eros scelerisque a. Ut eu enim felis, quis eleifend ipsum. Sed eu erat non ligula eleifend blandit. </p>
								<br />
								<p> Aliquam congue scelerisque sapien, molestie feugiat nibh pharetra congue. Donec nec quam leo. Curabitur massa nibh, dapibus et eleifend quis, auctor id turpis. Etiam interdum ipsum non orci gravida et viverra enim malesuada. Quisque vehicula tincidunt velit, volutpat pharetra purus pretium et. Duis at est sapien. Nullam in dui ac ipsum elementum hendrerit ac et mauris. Ut commodo blandit libero placerat iaculis. In vel ullamcorper mauris. Aenean tempor, eros sit amet tristique sagittis, lacus tellus rutrum diam, ac cursus eros dui nec massa. Etiam pharetra, tortor non imperdiet accumsan, sem nibh luctus neque, at convallis diam ante in lacus. Suspendisse adipiscing nibh quam, vel aliquet massa. Nunc nulla risus, malesuada vel dictum sed, rutrum eu turpis. Morbi adipiscing vestibulum eros, non pharetra sem semper sit amet. Sed ligula felis, eleifend vel lobortis sit amet, rhoncus eleifend ante. </p>
								<br />
								<p> Morbi odio libero, varius quis dignissim nec, volutpat sit amet purus. Suspendisse vel leo et lectus consequat mollis. Proin nisl nibh, pretium vel consequat vel, faucibus vel lorem. Nulla facilisi. Praesent nunc ipsum, lacinia quis pellentesque non, mattis at purus. Vivamus a ante ante. Nulla pharetra auctor commodo. Cras mattis venenatis mi sit amet feugiat. Nullam eros tellus, ullamcorper id porttitor vel, vestibulum non leo. Suspendisse potenti. Nullam et augue felis, id viverra est. Curabitur in leo purus. Sed sed est eget libero ultrices posuere non eu libero. Nulla sit amet mauris in massa bibendum semper. </p>
								<br />
								<p> Pellentesque vitae lorem sed augue bibendum elementum. Etiam consectetur tincidunt feugiat. Vestibulum id gravida erat. Mauris tempus tempor sollicitudin. Nullam sed neque massa. Maecenas id tellus libero, vel aliquet ante. Integer et dui nec nibh lacinia commodo. Morbi id eros metus, eu bibendum massa. Nunc turpis dui, ornare eget posuere pharetra, tincidunt sed magna. Aliquam in massa eu eros vehicula ornare eget non odio. Donec a lobortis mauris. Nulla facilisi. Fusce auctor turpis eros, quis aliquet lorem. Nam a ipsum erat. </p>
								<br />
								<p> Sed vitae eros urna. Vivamus pretium tellus non tortor auctor eleifend vel id nisl. Nunc viverra est nec metus suscipit vehicula. Proin iaculis mauris at quam rutrum vel tincidunt neque faucibus. Morbi quam arcu, sodales eget dapibus quis, sollicitudin commodo dolor. Cras viverra congue quam tristique fringilla. Maecenas ultrices luctus mauris, quis auctor ipsum viverra in. Cras dictum consequat urna sed suscipit. Sed nec nunc tellus, id eleifend orci. Aliquam interdum laoreet nibh, non sodales metus blandit quis. Aliquam erat volutpat. Integer in ultricies tortor. Nunc auctor, massa eu consectetur dapibus, arcu magna tempor augue, et tincidunt ipsum lorem non metus. Donec at tortor ac leo laoreet gravida. </p>
								<br />
								<p> Sed ac lacus sed quam bibendum cursus. Nunc facilisis, massa sed molestie porta, risus dui sagittis felis, ut sodales augue velit id elit. Morbi quis mauris mi, non malesuada leo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed velit felis, lacinia a dictum quis, ultrices a enim. Curabitur non nulla non elit ornare aliquam. Phasellus fermentum scelerisque luctus. Praesent turpis nisi, mollis vitae ultricies et, egestas vel urna. Aenean feugiat, dolor sed volutpat semper, justo tortor porta eros, et fringilla metus est eget urna. Fusce sodales erat nec arcu aliquet fringilla. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse potenti. Etiam non tempus ligula. Nunc quis dui nec nibh adipiscing dapibus. Vestibulum iaculis nulla at odio bibendum facilisis. </p>
								<br />
								<p> Donec sed tellus vitae augue sagittis placerat. Suspendisse consequat fermentum lectus, eu faucibus dolor sagittis vel. Duis gravida, ipsum non volutpat pulvinar, ante nisl suscipit neque, sed lobortis magna erat ac tortor. Vestibulum viverra sodales sapien, in auctor massa porttitor in. Aliquam egestas pellentesque orci, ac commodo tellus porttitor at. Ut luctus risus eu quam ullamcorper tristique. Nullam a dui felis. </p>
								<br />
								<p> Nullam dignissim, ante vel aliquet blandit, velit erat dictum purus, eu mollis arcu massa non massa. Nullam ornare nisi et urna ultrices fringilla at eget ipsum. Mauris imperdiet velit nec lacus porttitor eu malesuada nisl condimentum. Nam eu ultricies erat. Mauris a lorem ipsum, in semper diam. Morbi sollicitudin ornare sodales. Mauris eu sem erat. </p>
								<br />
								<p> Proin eget tortor id magna blandit interdum vel ut metus. Proin cursus porta posuere. Mauris mattis tincidunt velit, non convallis lacus feugiat ut. Aliquam erat volutpat. Vivamus nunc orci, pulvinar vitae ornare ut, ullamcorper eu nibh. Duis nisi leo, faucibus a lobortis ut, congue ac nisi. Donec ornare interdum nisi nec viverra. </p>
								<br />
								<p> Duis vitae lorem at magna scelerisque dictum mollis bibendum tellus. Praesent dignissim elementum arcu, ut facilisis tellus vestibulum eu. Nullam id nibh leo, nec pretium purus. Mauris vitae arcu eu nunc dapibus accumsan. Ut erat odio, consectetur non rhoncus eget, bibendum ut ligula. Pellentesque sed dui sed risus porta pulvinar eget at nisi. Sed malesuada porta mauris, in tristique lorem elementum at. Maecenas quis libero id tortor mattis viverra. Ut malesuada erat tincidunt velit egestas at fringilla neque lobortis. Aliquam erat volutpat. Nunc hendrerit, metus ut dignissim tincidunt, lectus eros adipiscing erat, a mattis ipsum odio at sapien. Donec vitae eros ut erat feugiat vulputate. Aenean dapibus cursus mauris, sed vestibulum urna vestibulum non. Nam at est vitae ante malesuada cursus. Morbi vitae tempus sem. Cras vestibulum, sapien vel tincidunt tempor, mauris diam sagittis orci, non ultricies sem leo vel sem. </p>
								<br />
								<p> Praesent vitae nulla lectus, vitae aliquam turpis. Nulla facilisi. Sed ultrices, felis nec molestie adipiscing, eros velit posuere leo, eu placerat est lorem vitae metus. Aenean vestibulum accumsan ante, at pellentesque arcu elementum et. Integer ut justo vitae dui elementum varius. Cras pulvinar ullamcorper hendrerit. Sed mattis interdum est, et convallis risus vulputate et. In erat metus, semper quis lobortis ac, aliquam nec risus. </p>
								<br />
								<p> Ut molestie lacus a lorem pulvinar non rutrum purus tempus. Sed a libero vel libero pretium ullamcorper. Sed quis nulla dui, suscipit convallis nulla. Praesent porttitor justo elementum ligula mattis consequat convallis ligula mollis. Sed vestibulum rhoncus sapien egestas laoreet. Vestibulum nec libero magna. Aliquam porttitor condimentum faucibus. Nulla nec augue eros, ut malesuada odio. Aenean aliquam interdum felis, volutpat ornare sapien auctor eget. Curabitur ut risus arcu. Fusce ut velit vel turpis porttitor cursus suscipit venenatis elit. </p>
								<br />
								<p> Pellentesque enim magna, mattis nec interdum non, lacinia vel nulla. Aenean viverra pulvinar scelerisque. Mauris ac elit sit amet dui pharetra ultricies. Sed nec libero leo. Cras augue ligula, scelerisque sit amet placerat ac, congue tincidunt risus. Proin elementum nunc magna. Aliquam sed ipsum erat. Sed auctor, massa vitae consequat placerat, tellus tellus rutrum ligula, feugiat condimentum magna velit quis lorem. Maecenas at eros malesuada risus pretium iaculis. Cras eget ligula tortor, eu blandit libero. Sed suscipit vestibulum elit, ac dapibus nisl dapibus ac. Curabitur et justo augue. Sed magna massa, condimentum eget viverra ac, luctus nec diam. </p>
								<br />
								<p> Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Quisque cursus lorem in odio tristique rutrum. Maecenas et felis ac ipsum pharetra eleifend. Pellentesque et massa a arcu bibendum egestas. Aliquam pellentesque commodo ultrices. Ut iaculis, massa et vulputate tempus, orci dui dictum erat, eu fermentum nunc massa ut lacus. Morbi bibendum eros non quam aliquet viverra. Curabitur consectetur lacinia tellus a sagittis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean sollicitudin cursus lectus. Cras at nibh sem, quis semper augue. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur et lectus nunc, at luctus orci. Nunc mattis urna at turpis tincidunt aliquam. Nullam sed urna eu mauris pretium feugiat. Fusce nisl urna, lobortis auctor aliquet a, porta non augue. </p>
								<br />
								<p> Aliquam cursus sem in purus molestie nec dignissim nibh tristique. Nulla pharetra, massa eget venenatis imperdiet, libero dolor ornare eros, quis ultricies dolor ante ac tellus. Fusce viverra auctor ipsum ut lobortis. Sed interdum odio varius libero hendrerit vitae laoreet felis vehicula. Vivamus dui nunc, varius blandit sagittis sed, consequat id metus. Proin fringilla nisl ac magna volutpat et scelerisque augue porta. Quisque aliquet aliquet condimentum. Nulla facilisi. Nulla metus elit, volutpat eget ullamcorper id, porta blandit nulla. Suspendisse euismod porta viverra. Mauris placerat ullamcorper lorem vitae pharetra. Nunc sagittis ante dictum libero pharetra vitae rutrum diam aliquet. Cras adipiscing mollis sem, iaculis tempor leo aliquet sed. Pellentesque velit elit, commodo sit amet adipiscing in, bibendum eget nunc. </p>
								<br />
								<p> Nulla sed nibh felis, vel auctor diam. Vestibulum lorem eros, eleifend quis consequat sit amet, adipiscing feugiat magna. Duis a ante vel tellus hendrerit laoreet ultrices ut sem. Ut porta lectus in turpis ornare congue. In sodales, quam nec sagittis lobortis, leo lorem bibendum purus, non porttitor eros massa sodales elit. Aenean semper ipsum sit amet lacus tincidunt et suscipit elit venenatis. Nullam enim lorem, tristique quis consequat ut, mollis in elit. In massa metus, aliquam sed euismod a, commodo quis velit. Proin sit amet tellus egestas est pulvinar aliquet at at mauris. Sed scelerisque sapien at arcu aliquet porttitor. Pellentesque adipiscing diam et felis consectetur blandit. Suspendisse condimentum pellentesque leo quis vehicula. In porttitor nunc ac dui facilisis aliquet ut a nunc. </p>
								<br />
								<p> Nunc vitae tellus sollicitudin turpis facilisis ornare et feugiat est. Etiam id felis a risus tristique tincidunt nec at sem. Curabitur quis sapien ac augue molestie molestie. Donec mattis nisi sit amet elit accumsan vitae malesuada libero pellentesque. Donec blandit varius fermentum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ligula sapien, ultricies eu viverra non, commodo non lacus. Etiam tempor metus at tellus ornare rhoncus. Cras lacus elit, commodo et feugiat eget, malesuada ac tortor. Aliquam ullamcorper egestas dui dignissim iaculis. Donec et turpis ut leo sodales lobortis. Nullam et lorem quis enim euismod tempus non eu purus. Sed auctor, mauris non porta vehicula, lectus odio euismod lacus, vel molestie augue eros eu nisl. </p>
								<br />
								<p> Vivamus dictum elit ut urna scelerisque mattis. Aenean suscipit, massa nec rutrum dictum, sem turpis tempor magna, ac vestibulum metus orci eu augue. Vivamus ipsum nisl, porttitor sed pharetra dictum, porta eu quam. Nam non ipsum non odio hendrerit ullamcorper. Proin nibh velit, tincidunt a pellentesque at, fringilla in nulla. Aliquam at sem vitae nisi adipiscing cursus at vitae lacus. Aliquam aliquet justo sed tortor semper rutrum. Suspendisse ultricies condimentum iaculis. Nullam vehicula ante vel lorem blandit in convallis augue hendrerit. Integer condimentum leo vitae quam feugiat commodo. Quisque ut nibh id purus pellentesque blandit quis eget mi. Vivamus sed metus vitae ante vestibulum placerat. Aliquam eu turpis at justo faucibus pellentesque ut sed libero. </p>
								<br />
								<p> Mauris et neque nec nisl viverra iaculis a eget felis. Quisque et sapien lacus. Etiam et consequat risus. In venenatis neque in purus imperdiet elementum. Aenean nec diam odio, ut tempus sem. Duis mollis placerat lacus, eget ornare elit dapibus ac. Pellentesque pharetra iaculis imperdiet. Fusce et augue leo, id rhoncus risus. Nulla rutrum elementum purus, et posuere augue vulputate et. Ut ligula libero, luctus in aliquet eu, faucibus sed dolor. In et magna velit. Vivamus quis massa et neque ullamcorper sagittis vel et augue. Curabitur ullamcorper facilisis ante, et venenatis odio congue a. </p>
								<br />
								<p> Integer et sapien ut nisi luctus interdum in eu augue. Morbi quis sapien nibh. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum vitae arcu ac urna porta laoreet non consequat neque. Suspendisse diam tellus, consequat eget gravida ac, semper ut sem. Integer urna orci, semper in pulvinar consequat, tempor eget sem. Quisque vel mauris nec tortor accumsan rutrum. Maecenas urna ipsum, feugiat eu faucibus at, lobortis at quam. Morbi fringilla metus ac ante tempor non tincidunt tellus posuere. Praesent luctus pretium urna molestie fringilla. Donec quam justo, dictum quis iaculis non, pulvinar id nibh. Donec molestie tellus eget arcu elementum lobortis. Quisque lacus ante, vehicula quis gravida ac, pharetra a diam. Vestibulum laoreet tempor vehicula. Phasellus interdum, velit et lacinia sodales, neque nunc egestas velit, ut feugiat mauris quam eu nisi. Morbi sit amet consequat massa. </p>
								<br />
								<p> Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed bibendum lorem nulla. Curabitur pretium congue magna vel sollicitudin. Pellentesque vestibulum gravida erat vitae sollicitudin. Vivamus adipiscing nisi in sapien bibendum et tempor felis malesuada. Curabitur blandit, velit a accumsan porta, massa urna accumsan risus, id adipiscing felis purus quis nibh. Sed bibendum, nisi in posuere lobortis, ipsum metus sagittis lectus, vitae adipiscing quam odio vel diam. Aliquam ultrices porttitor ante non pellentesque. Quisque justo sapien, auctor non ornare eget, tincidunt sit amet sapien. Suspendisse et velit ut elit commodo adipiscing quis commodo nisi. Morbi est eros, porttitor et pellentesque nec, pellentesque vel magna. Aenean eu turpis mauris, ut pulvinar purus. </p>
								<br />
								<p> Cras nec turpis urna, quis aliquet odio. Vivamus tristique odio ac metus euismod ut ornare justo bibendum. Curabitur lacinia arcu sed erat iaculis aliquet. Pellentesque pharetra turpis at justo malesuada venenatis. Aliquam faucibus rutrum risus, sed imperdiet sapien eleifend ut. Morbi posuere pharetra tellus nec aliquet. Phasellus eu lorem eget lorem consectetur dignissim eu nec mauris. Maecenas diam nibh, euismod nec consectetur ut, posuere ac lorem. Sed lobortis convallis sem ac ultricies. Etiam et blandit augue. Ut viverra tristique massa, sed bibendum leo luctus vel. Quisque pharetra pretium justo, non tincidunt nulla sodales non. Praesent ut odio nulla. Aenean aliquet, nisl nec fermentum blandit, libero nulla consectetur est, non imperdiet massa diam ut elit. </p>
								<br />
								<p> Nulla eleifend ligula vel tortor ultrices commodo. Fusce varius odio quis magna porttitor ut ornare urna gravida. Phasellus malesuada rutrum elit, in scelerisque nunc blandit vitae. Mauris quis ipsum nisl. Proin vitae feugiat augue. Curabitur non orci quam. Aliquam sagittis, ligula vel vulputate mollis, tortor nunc dapibus dui, sed aliquam leo tellus eu risus. </p>
								<br />
								<p> Praesent eu tortor nisl, in scelerisque tortor. Pellentesque ligula elit, viverra id aliquam sit amet, cursus posuere lacus. Mauris ligula urna, ornare id lacinia sit amet, sagittis porttitor magna. Cras consectetur, urna at vulputate porttitor, lectus dolor rhoncus nulla, et pellentesque sapien enim vitae dolor. Donec nisi augue, adipiscing ac mattis ac, varius vulputate erat. Sed vitae neque augue, elementum vehicula tellus. Donec sed erat nisl. Maecenas aliquam elementum leo, quis convallis mi pretium nec. Aliquam vulputate dolor pulvinar nunc tempus mattis. Donec gravida, eros id iaculis porta, quam lacus eleifend elit, non egestas felis lacus sed risus. In eget aliquam neque. Proin convallis purus non diam porta tristique. Nulla turpis dolor, cursus sed pulvinar non, iaculis sit amet neque. Ut pellentesque interdum nibh vitae commodo. In blandit, velit vel fringilla luctus, nibh metus lobortis erat, et varius elit mi in sem. </p>
								<br />
								<p> Aenean scelerisque cursus leo at rutrum. Cras molestie nulla a purus bibendum tempus. Fusce id sem orci. Nulla facilisi. In in leo diam. Sed id lectus at erat vehicula rutrum id rhoncus justo. Praesent iaculis, ligula sed venenatis dapibus, nulla justo adipiscing sapien, in aliquam mi quam sed lorem. Nulla tempor consequat porttitor. </p>
								<br />
								<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean sit amet massa a leo vestibulum auctor eget at risus. Vestibulum eu diam faucibus arcu facilisis vulputate. Nam massa ipsum, pretium ac volutpat eu, rutrum non nisl. Donec tortor enim, tristique ac congue et, molestie et tortor. Duis elit nisi, facilisis vitae tincidunt ut, viverra non lacus. Integer auctor consequat porttitor. Etiam porta venenatis felis sed pretium. Cras et mauris tellus, at mattis risus. Etiam vitae purus in lorem aliquam facilisis vel a felis. Proin quis ligula ut arcu rhoncus sollicitudin a eget elit. </p>
								<br />
								<p> Praesent a nisl metus. Cras quis aliquet justo. In hac habitasse platea dictumst. Curabitur elementum velit vitae metus sodales eget ornare est tincidunt. Nulla nec ipsum ac est bibendum molestie. Nunc sit amet risus nunc, a dictum justo. Curabitur ullamcorper, massa vitae volutpat fringilla, lectus urna euismod dolor, id adipiscing tortor diam quis augue. Suspendisse tempus varius eros, eu sodales sem sagittis sed. Phasellus eget sem quis nisl euismod sollicitudin vel at justo. Donec volutpat metus sit amet lectus fermentum placerat. Aliquam adipiscing eros in purus feugiat tempor. Maecenas adipiscing aliquam ante. </p>
								<br />
								<p> Donec sagittis, nisl in porta volutpat, arcu nunc porttitor tellus, id egestas magna nunc et orci. In condimentum libero quis tellus lobortis aliquam. Vivamus non lectus mauris, at venenatis dui. Suspendisse non aliquet tortor. Sed ultricies mauris condimentum arcu sodales blandit condimentum orci ullamcorper. Maecenas pharetra ultrices dolor eleifend lacinia. Proin eget accumsan elit. </p>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
jQuery('.color-picker').miniColors({
	change:function(hex, rgb){
		jQuery('#console').prepend('change: ' + hex + ', rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')<br>');
	},
	open:function(hex, rgb) {
		jQuery('#console').prepend('open: ' + hex + ', rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')<br>');
	},
	close:function(hex, rgb) {
		jQuery('#console').prepend('close: ' + hex + ', rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')<br>');
	}
});

function choosePosition()
{
	switch ($('.position_radio:checked').attr('value'))
	{
		case "TL":
			jQuery('#margin_top, #margin_left').show();
			jQuery('#margin_bottom, #margin_right').hide();
			break;
		case "TR":
			jQuery('#margin_top, #margin_right').show();
			jQuery('#margin_bottom, #margin_left').hide();
			break;
		case "BL":
			jQuery('#margin_bottom, #margin_left').show();
			jQuery('#margin_top, #margin_right').hide();
			break;
		default:
			jQuery('#margin_bottom, #margin_right').show();
			jQuery('#margin_top, #margin_left').hide();
			break;
	}
}
function chooseStyle()
{
	switch ($('.style_radio:checked').attr('value'))
	{
		case '1':
			$('.text').show();
			$('.background').hide();
			break;
		case '5':
		case '7':
			$('.text').hide();
			$('.background').show();
			break;
		default:
			$('.text').hide();
			$('.background').hide();
			break;
	}
}

$( document ).ready(function() {
	choosePosition();
	$('.position_radio').change(function(){
		choosePosition();
	});
	
	chooseStyle();
	$('.style_radio').change(function(){
		chooseStyle();
	});
});

<?php echo $helper->getJavascript();?>

<?php $this->Html->scriptEnd(); ?>