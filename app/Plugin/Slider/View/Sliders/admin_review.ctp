<div class="modal-header">
    <?php echo __d('slider', 'Preview');?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
</div>
<?php 
/**
* Custom css
**/;
?>
<style type="text/css">
	body {
	/*overflow: hidden;*/
}

#content {
	margin: 0 auto 100px auto;
	max-width: 1200px;
	font-size: 16px;
}
	
	#content .syntaxhighlighter{ overflow-y: hidden !important; }
	#content .syntaxhighlighter > table > tbody > tr > td.code {
		padding: 10px !important;
	}
	
	#content .examples {
		line-height: 2.5em;
	}
	
	#content .photo-license-toggle {
		margin: 0 auto;
		padding: 10px 0px;
		max-width: 1024px;
		text-align: right;
	}
	
	#content .photo-license {
		display: none;
		margin: 0 auto;
		max-width: 1024px;
		font-size: 0.8em;
		border: 1px dashed #6F6F6F;
		border-radius: 6px;
		padding: 15px 10px;
		line-height: 1.6em;
	}
	
	#content .frontslider {
		box-shadow: 'rgba(0, 0, 0, 0.3) 0px 0px 10px 0px';	
	}

	#content figure{
		margin-top: 35px;
		text-align: center;
	}
		#content figure img{
			max-width: 100%;
		}
			
		#content figure figcaption{
			line-height: 1.8em;
		}
		
		
	#content table.configuration {
		margin: 0 auto;
		border: 1px solid #000;
		border-collapse: collapse;
	}
	
		#content table.configuration th,
		#content table.configuration td{
			padding: 5px 15px;
			border: 1px solid #000;
		}
		
		#content table.configuration th{
			vertical-align: middle;
			background-color: #CBEEF3;
			white-space:nowrap;
		}
		
		#content table.configuration td{
			vertical-align: top;
			
		}
		
/*
SLIDER EXAMPLE
*/

.front-demo {
	visibility: hidden;
	width: 1024px;
	height: 400px;
	z-index:1;
	overflow:hidden;
}

	.front-demo .inner.devrama-slider .projector{
		box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.298039);
	}
	
		.front-demo .inner.devrama-slider .projector .slide1 h3 {
			display: inline-block;
			margin: 0;
			color: #2B6BA7;
			text-shadow: 3px 2px 4px rgb(0, 0, 0);
			font-size: 56px;
			background-color: rgba(0, 0, 0, 0.5);
			padding: 10px 94px 10px 10px;
			-webkit-transform: skew(25deg, -10deg);
			   -moz-transform: skew(25deg, -10deg);
			     -o-transform: skew(25deg, -10deg);
			        transform: skew(25deg, -10deg);
		}
		
		.front-demo .inner.devrama-slider .projector .slide1 .description {
			display: inline-block;
			margin: 0;
			color: #FFF;
			text-shadow: 3px 2px 4px rgb(0, 0, 0);
			font-size: 26px;
			background-color: rgba(0, 0, 0, 0.5);
			padding: 10px 10px 10px 10px;
			
		}
		
		.front-demo .inner.devrama-slider .projector .slide2 h3 {
			display: inline-block;
			margin: 0;
			padding: 0;
			color: #FFF;
			text-shadow: 3px 2px 4px rgb(0, 0, 0);
			font-size: 72px;
		}
		
		.front-demo .inner.devrama-slider .projector .slide2 .description {
			display: inline-block;
			margin: 0;
			color: #FFF;
			font-size: 16px;
			padding: 7px 20px;
			
		}
		
		.front-demo .inner.devrama-slider .projector .slide3 {
			background-color: #27446F;
		}
		
		.front-demo .inner.devrama-slider .projector .slide3 h3 {
			display: block;
			margin: 30px 0 20px 0;
			padding: 0;
			color: #FFF;
			text-shadow: 3px 2px 4px rgb(0, 0, 0);
			font-size: 49px;
			text-align: center;
		}
		
		.front-demo .inner.devrama-slider .projector .slide3 .table {
		}
		
		.front-demo .inner.devrama-slider .projector .slide3 .table table{
			width: 100%;
			border: 1px solid #fff;
		}
		
		.front-demo .inner.devrama-slider .projector .slide3 .table table th,
		.front-demo .inner.devrama-slider .projector .slide3 .table table td{
			padding: 5px 15px;
			border: 1px solid #fff;
		}
		
		.front-demo .inner.devrama-slider .projector .slide3 .table table td{
			height: 200px;
			vertical-align: middle;
			text-align: center;
		}
		
		.front-demo .inner.devrama-slider .projector .slide4{
			
		}
		.front-demo .inner.devrama-slider .projector .slide4 h3 {
			display: block;
			margin: 0;
			padding: 0;
			color: #973A2B;
			text-shadow: 3px 2px 4px rgb(0, 0, 0);
			font-size: 34px;
		}
		
		.front-demo .inner.devrama-slider .projector .slide4 .square {
			width: 800px;
			height: 700px;
			-webkit-transform: translate(-20%, -47%) rotate(-45deg);
			   -moz-transform: translate(-20%, -47%) rotate(-45deg);
			    -ms-transform: translate(-20%, -47%) rotate(-45deg);
			     -o-transform: translate(-20%, -47%) rotate(-45deg);
			        transform: translate(-20%, -47%) rotate(-45deg);
			background-color: rgba(255, 255, 255, 0.25);

		}
		
		.front-demo .inner.devrama-slider .projector .slide4 i.slide-star1,
		.front-demo .inner.devrama-slider .projector .slide4 i.slide-star2,
		.front-demo .inner.devrama-slider .projector .slide4 i.slide-star3{
			color: #ff0;
			color: rgba(255, 255, 0, 0.28);
		}
		
		.front-demo .inner.devrama-slider .projector .slide4 i.slide-star1 {
			font-size: 183px;
		} 
		
		.front-demo .inner.devrama-slider .projector .slide4 i.slide-star2 {
			font-size: 115px;
		}
		
		.front-demo .inner.devrama-slider .projector .slide4 i.slide-star3 {
			font-size: 68px;
		}
		
		
		.front-demo .inner.devrama-slider .projector .slide5 h3 {
			display: inline-block;
			margin: 0;
			color: #EE5133;
			text-shadow: 3px 2px 4px rgb(0, 0, 0);
			font-size: 56px;
			background-color: rgba(0, 0, 0, 0.5);
			padding: 10px 28px;
			width: 100%;
		}
		
		.front-demo .inner.devrama-slider .projector .slide5 .description {
			display: inline-block;
			margin: 0;
			color: #FFF;
			text-shadow: 3px 2px 4px rgb(0, 0, 0);
			font-size: 26px;
			background-color: rgba(0, 0, 0, 0.5);
			padding: 5px 25px;
			
		}
		
		.front-demo .inner.devrama-slider .projector .slide5 .description.a{
			-webkit-transform: rotate(-8deg);
			   -moz-transform: rotate(-8deg);
			    -ms-transform: rotate(-8deg);
			     -o-transform: rotate(-8deg);
			        transform: rotate(-8deg);
		}
		.front-demo .inner.devrama-slider .projector .slide5 .description.b{
			-webkit-transform: rotate(-8deg);
			   -moz-transform: rotate(-8deg);
			    -ms-transform: rotate(-8deg);
			     -o-transform: rotate(-8deg);
			        transform: rotate(-8deg);
		}
		.front-demo .inner.devrama-slider .projector .slide5 .description.c{
			-webkit-transform: rotate(4deg);
			   -moz-transform: rotate(4deg);
			    -ms-transform: rotate(4deg);
			     -o-transform: rotate(4deg);
			        transform: rotate(4deg);
		}
		.front-demo .inner.devrama-slider .projector .slide5 .description.d{
			-webkit-transform: rotate(-8deg);
			   -moz-transform: rotate(-8deg);
			    -ms-transform: rotate(-8deg);
			     -o-transform: rotate(-8deg);
			        transform: rotate(-8deg);
		}
		.front-demo .inner.devrama-slider .projector .slide5 .description.e{
			-webkit-transform: rotate(4deg);
			   -moz-transform: rotate(4deg);
			    -ms-transform: rotate(4deg);
			     -o-transform: rotate(4deg);
			        transform: rotate(4deg);
		}

    .bg_text_slide
    {
        left: 0;
        position: absolute;
        bottom: 0;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        padding: 10px;
        opacity: <?php echo $slider['Slider']['opacity'];?>;
    }

</style>
<?php
$slideHelper = MooCore::getInstance()->getHelper('Slider_Slide');
?>
<div class="front-demo front-demo-<?php echo $slider['Slider']['id']?>">
    <?php foreach($slides as $slide):?>
        <a href="<?php echo $slide['Slide']['link'];?>" class="slide1" data-lazy-background="<?php echo $slideHelper->getImage($slide, array('prefix' => '850'))?>" <?php if($slide['Slide']['new_tab']):?>target="_blank"<?php endif;?>>
            <div class="bg_text_slide" <?php if($slider['Slider']['background_caption']):?> style="background-color: <?php echo $slider['Slider']['background_caption']?>" <?php endif;?>>
                <?php if($slide['Slide']['slide_name']):?>
                    <div style="font-weight: bold; font-size: <?php echo ($slide['Slide']['caption_font_size']) ? $slide['Slide']['caption_font_size'] : '12';?>px; color: <?php if($slide['Slide']['caption_color']){echo $slide['Slide']['caption_color'];}else{ echo '#000';}?>">
                        <?php echo $slide['Slide']['slide_name'];?>
                    </div>
                <?php endif;?>
                <?php if($slide['Slide']['text']):?>
                    <div style="color:<?php if($slide['Slide']['color']){echo $slide['Slide']['color'];}else{ echo '#000';}?>;font-size: <?php echo ($slide['Slide']['font_size']) ? $slide['Slide']['font_size'] : '12';?>px;"
                         data-pos="" data-duration="" data-effect="move">
                        <?php echo $slide['Slide']['text'];?>
                    </div>
                <?php endif;?>
            </div>
        </a>
    <?php endforeach;?>    
     
</div>
<script>

    $(document).ready(function(){
        $('.front-demo-<?php echo $slider['Slider']['id']?>').DrSlider({
            height: <?php echo $slider['Slider']['height']?>,
            width: <?php echo $slider['Slider']['width']?>,
			navigationType: '<?php echo $slider['Slider']['navigation_type']?>',
			duration: <?php echo $slider['Slider']['duration']?>,
			transitionSpeed: <?php echo $slider['Slider']['transition_speed']?>,
			showNavigation: '<?php echo $slider['Slider']['show_navigation']?>',
			classNavigation: undefined,
		    navigationColor: '<?php echo $slider['Slider']['navigation_color']?>',
		    navigationHoverColor: '<?php echo $slider['Slider']['navigation_hover_color']?>',
		    navigationHighlightColor: '<?php echo $slider['Slider']['navigation_hightlight_color']?>',
		    navigationNumberColor: '#000000',
		    positionNavigation: '<?php echo $slider['Slider']['position_navigation']?>',
		    navigationType: '<?php echo $slider['Slider']['navigation_type']?>',
		    showControl: <?php if($slider['Slider']['show_control']):?>true<?php else:?>false<?php endif;?>,
		    classButtonNext: undefined,
		    classButtonPrevious: undefined,
		    controlColor: '<?php echo $slider['Slider']['control_color']?>',
		    controlBackgroundColor: '<?php echo $slider['Slider']['control_background_color']?>',
		    positionControl: '<?php echo $slider['Slider']['position_control']?>',
		    transition: '<?php echo $slider['Slider']['transition_effect']?>',
		    showProgress: <?php echo $slider['Slider']['show_progress']?>,
		    progressColor: '<?php echo $slider['Slider']['progress_color']?>',
		    pauseOnHover: <?php if($slider['Slider']['pause_on_hover']):?>true<?php else:?>false<?php endif;?>,
        }); 
    });
</script>