
<div id="footer">
    <div class="footer_left">
           <div class="logo_footer">
               <img src="<?php echo $this->request->webroot ?>theme/mooInside/img/logo_footer.png" />
               
           </div>
        <div class="social_footer">
            <ul>
                <li><a href="#"><i class="ft_fb"></i></a></li>
                <li><a href="#"><i class="ft_twitter"></i></a></li>
                <li><a href="#"><i class="ft_google"></i></a></li>
                <li><a href="#"><i class="ft_instagram"></i></a></li>
                <li><a href="#"><i class="ft_linkedin"></i></a></li>
                <li><a href="#"><i class="ft_tumbr"></i></a></li>
                <li><a href="#"><i class="ft_utube"></i></a></li>
                <li><a href="#"><i class="ft_pin"></i></a></li>
                <li><a href="#"><i class="ft_rss"></i></a></li>
                <li><a href="#"><i class="ft_1"></i></a></li>
                 <li><a href="#"><i class="ft_2"></i></a></li>
            </ul>
        </div>
        <?php echo html_entity_decode( Configure::read('core.footer_code') )?><br />
        <?php if (Configure::read('core.show_credit')): ?>
        <span class="date"><?php echo __('Powered by')?> 
            <a href="http://www.moosocial.com" target="_blank">mooSocial <?php echo Configure::read('core.version')?></a>
        </span>
        <?php endif; ?>
        <?php if (Configure::read('core.select_language') || Configure::read('core.select_theme')): ?>
            <?php if (Configure::read('core.select_language')): ?>

            <?php if (Configure::read('core.show_credit')): ?>&nbsp;.&nbsp;<?php endif; ?>
            <a href="<?php echo  $this->request->base ?>/home/ajax_lang"
            data-target="#langModal" data-toggle="modal"
            title="<?php echo  __('Language') ?>">
                    <?php echo  (!empty($site_langs[Configure::read('Config.language')])) ? $site_langs[Configure::read('Config.language')] : __('Change') ?>
            </a>

            <?php endif; ?>




            <?php endif; ?>
    </div>
    <div class="footer_right">
        <div>
        <?php $this->doLoadingFooter();?>
        </div>
        <!--List of topic categories-->
        <?php
        $topicHelper = MooCore::getInstance()->getHelper('Topic_Topic');
        $categories = $this->requestAction(
                "mooinsides/getTopicCategories"
        );
        ?>
        <?php foreach ($categories as $key1=>$cat): //echo '<pre>'; print_r($cat); ?>
        <?php if($key1<=3): ?>
        <div>
            <ul>
                <?php if ($cat['Category']['header']): ?>
                    <li class="category_header">
                        <em><?php echo $cat['Category']['name'] ?></em>
                        <ul>
                            <?php foreach ($cat['children'] as $key=>$subcat): ?>
                                <?php if($key <=2): ?>
                                <li id="cat_<?php echo $subcat['Category']['id'] ?>" class="sub-cat <?php if (!empty($cat_id) && $cat_id == $subcat['Category']['id']) echo 'current'; ?>">
                                    <a data-url="<?php echo $this->request->base ?>/videos/browse/category/<?php echo $subcat['Category']['id'] ?>" <?php if (!empty($subcat['Category']['description'])): ?>class="tip" title="<?php echo nl2br($subcat['Category']['description']) ?>"<?php endif ?> href="<?php echo $this->request->base ?>/topics/index/<?php echo $subcat['Category']['id'] ?>/<?php echo seoUrl($subcat['Category']['name']) ?>"><?php echo $subcat['Category']['name'] ?> 
                                        </a>
                                </li>

                            <?php endif; ?>
                                 <?php if ($key == 3): ?>
                                    <a href="<?php echo $this->request->base ?>/topics"><?php echo  __('More option') ?></a>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                <?php else: ?>
                    <li id="cat_<?php echo $cat['Category']['id'] ?>" <?php if (!empty($cat_id) && $cat_id == $cat['Category']['id']) echo 'class="current"'; ?>>
                        <a data-url="<?php echo $this->request->base ?>/videos/browse/category/<?php echo $cat['Category']['id'] ?>" 
                            <?php if (!empty($cat['Category']['description'])): ?>class="tip" title="<?php echo nl2br($cat['Category']['description']) ?>"<?php endif ?> href="<?php echo $this->request->base ?>/topics/index/<?php echo $cat['Category']['id'] ?>/<?php echo seoUrl($cat['Category']['name']) ?>"><?php echo $cat['Category']['name'] ?> 
                         </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>
        <?php endforeach; ?>
    </div>
   
    
</div>