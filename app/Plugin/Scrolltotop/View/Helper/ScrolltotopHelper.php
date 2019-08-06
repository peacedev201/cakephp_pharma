<?php
App::uses('AppHelper', 'View/Helper');
class ScrolltotopHelper extends AppHelper {
	function getCss()
	{
		$data = Configure::read('Scrolltotop');
		switch($data["scrolltotop_display_position"])
		{
			case "TL":
				$styleMargin = 'top:'.$data["scrolltotop_margin_top"].'px; left:'.$data["scrolltotop_margin_left"].'px;';
				break;
			case "TR":
				$styleMargin = 'top:'.$data["scrolltotop_margin_top"].'px; right:'.$data["scrolltotop_margin_right"].'px;';
				break;			
			case "BL":
				$styleMargin = 'bottom:'.$data["scrolltotop_margin_bottom"].'px; left:'.$data["scrolltotop_margin_left"].'px;';
				break;
			default:
				$styleMargin = 'bottom:'.$data["scrolltotop_margin_bottom"].'px; right:'.$data["scrolltotop_margin_right"].'px;';
				break;			
		}
		$request = Router::getRequest();
		
		$sPathImage = FULL_BASE_URL.$request->webroot.'scrolltotop/img/';
					
		return '.ScrollToTop_Global { position:fixed; '.$styleMargin.' display:none; cursor:pointer; z-index:1; }
				
				.ScrollToTop_Style_1 {
					background-color:#831608;
					background-image:linear-gradient(#BB413B, #831608);
					border:1px solid #831608;
					border-radius:5px 5px 5px 5px;
					box-shadow:0 1px 0 rgba(255, 255, 255, 0.3), 0 1px 0 rgba(0, 0, 0, 0.7), 0 2px 2px rgba(0, 0, 0, 0.5), 0 1px 0 rgba(255, 255, 255, 0.5) inset;
					padding:3px;
					text-shadow:0 -1px 0 rgba(0, 0, 0, 0.8);
				}		
					.ScrollToTop_Style_1 span {
						float:left;
						background-color:#BB413B;
						background-image:linear-gradient(#D4463C, #AA2618);
						border:1px dashed #EBA1A3;
						cursor:pointer;
						padding:4px 10px;
						font-size:12px;
						font-weight:bold;
						color:'.$data["scrolltotop_text_color"].';
					}
					.ScrollToTop_Style_1 span:hover {
						color:'.$data["scrolltotop_text_hover_color"].';
					}
					
				.ScrollToTop_Style_2 { background:url("'.$sPathImage.'Style_Real_2.png") no-repeat; width:40px; height:50px; }
					.ScrollToTop_Style_2 span { display:none; }
				
				.ScrollToTop_Style_3 { background:url("'.$sPathImage.'Style_Real_3.png") no-repeat; width:55px; height:30px; }
					.ScrollToTop_Style_3 span { display:none; }
					
				.ScrollToTop_Style_4 { background:url("'.$sPathImage.'Style_Real_4.gif") no-repeat; width:45px; height:31px; border-radius:5px; border:1px solid #ac9a75; }
					.ScrollToTop_Style_4 span { display:none; }
					
				.ScrollToTop_Style_5 { background:url("'.$sPathImage.'Style_Real_5.png") center center no-repeat; background-color:'.$data["scrolltotop_background_color"].'; width:40px; height:40px; border-radius:5px; border:1px solid #979797; }
				.ScrollToTop_Style_5:hover { background-color:'.$data["scrolltotop_background_hover_color"].'; }
					.ScrollToTop_Style_5 span { display:none; }
				
				.ScrollToTop_Style_6 { background:url("'.$sPathImage.'Style_Real_6.gif") no-repeat; width:47px; height:16px; border-radius:2px; }
					.ScrollToTop_Style_6 span { display:none; }
				
				.ScrollToTop_Style_7 { background:url("'.$sPathImage.'Style_Real_7.png") no-repeat; width:48px; height:48px; border-radius:5px; border:1px solid #92d400; background-color:'.$data["scrolltotop_background_color"].'; }
				.ScrollToTop_Style_7:hover { background-color:'.$data["scrolltotop_background_hover_color"].'; }
					.ScrollToTop_Style_7 span { display:none; }';
	}
	
	public function getJavascript()
	{
		$data = Configure::read('Scrolltotop');
		$html = '<div id="ScrollToTop" class="ScrollToTop_Global ScrollToTop_Style_'.$data["scrolltotop_your_style"].'"><span>'.$data["scrolltotop_text_display"].'</span></div>';
		$js = '$( document ).ready(function() {
					$("body" ).append($(\''.$html.'\'));
					jQuery(window).scroll(function()
					{
						if(jQuery(this).scrollTop() > '.$data["scrolltotop_show_after"].')
							jQuery("#ScrollToTop").fadeIn("slow");
						else
							jQuery("#ScrollToTop").fadeOut("slow");
	
						return false;
					});
		
					jQuery("#ScrollToTop").click(function (){
						jQuery("body, html").animate({scrollTop:0}, '.$data["scrolltotop_time_back"].', "linear");
					});
					
				});';
		
		return $js;
	}
}
