<?php
$helper = MooCore::getInstance()->getHelper('Forum_Forum');
 ?>

<style>
    /* Rotating glyphicon when expanding/collapsing */
    .mo_breadcrumb .glyphicon {
        transition: .3s transform ease-in-out;
    }
    .mo_breadcrumb .collapsed .glyphicon {
        transform: rotate(-90deg);
    }
</style>
<div class="bar-content">
    <?php
    foreach ($cats as $cat):
        ?>
        <div class="content_center">
            <div class="mo_breadcrumb">

                <h2 class="panel-title">
                    <img src="<?php echo $helper->getIconForumCategory($cat);?>" alt="<?php echo $cat['ForumCategory']['name'];?>" title="">
                    <span style="font-weight: bold;"><?php echo $cat['ForumCategory']['name'];?></span>
                    <a href="#" data-toggle="collapse" data-target="#forum_<?php echo $cat['ForumCategory']['id']?>" class="pull-right ">
                        <?php if(Configure::read('Forum.forum_show_expand') == true): ?>
                            <span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span>
                        <?php endif;?>
                    </a>
                </h2>
            </div>
            <?php
            if(count($cat['Forums']) == 0):
                echo '<div class="row collapse in" id="forum_' .$cat['ForumCategory']['id'] .'">'. __d('forum','No Forum') .'</div>';
            else:
                ?>
                <div class="row collapse in" id="forum_<?php echo $cat['ForumCategory']['id']?>">
                    <div class="row">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-5"><p class="text-muted"><?php echo __d('forum','Forums');?></p></div>
                        <div class="col-sm-1"><p class="text-muted"><?php echo __d('forum','Topics');?></p></div>
                        <div class="col-sm-1"><p class="text-muted"><?php echo __d('forum','Replies');?></p></div>
                        <div class="col-sm-4"><p class="text-muted"><?php echo __d('forum','Latest Post');?></p></div>
                    </div>
                    <?php
                    foreach ($cat['Forums'] as $forum):
                        $forum_icon = $helper->getIconForum($forum);
                        $avt = "No Topic";
                        if(!empty($forum['last_topic']))
                            $avt = $this->Moo->getItemPhoto(array('User' => $forum['last_topic']['User']), array('prefix' => '50_square'), array('class' => 'img-circle','width' => '60%', 'height' => '60%'));
                        ?>
                        <div class="row">
                            <div class="col-sm-1"><a href="<?php echo $forum['Forum']['moo_href'];?>"><img src="<?php echo $forum_icon;?>" alt="<?php echo $forum['Forum']['name'];?>"></a></div>
                            <div class="col-sm-5">
                                <span style="font-weight: bold;font-size: 120%;"><a href="<?php echo $forum['Forum']['moo_href'];?>"><?php echo $forum['Forum']['name'];?></a> <?php echo $helper->getLockIcon($forum);?></span>
                                <?php echo $forum['Forum']['description'];?>
                                <?php
                                if(is_array($forum['subs'])):
                                    ?>
                                    <div class="row">
                                        <?php
                                        foreach ($forum['subs'] as $sub):
                                            ?>
                                            <div class="col-sm-6">
                                                <img src="<?php echo $helper->getIconForum($sub);?>" alt="<?php echo $sub['Forum']['name'];?>" width="20px" height="20px" title="">
                                                <span style="font-weight: bold;font-size: 90%;"><a href="<?php echo $sub['Forum']['moo_href'];?>"><?php echo $sub['Forum']['name'];?></a></span>
                                            </div>
                                            <?php
                                        endforeach;
                                        ?>
                                    </div>
                                    <?php
                                endif;
                                ?>
                            </div>
                            <div class="col-sm-1"><strong><?php echo number_format($forum['Forum']['count_topic']);?></strong></div>
                            <div class="col-sm-1"><strong><?php echo number_format($forum['Forum']['count_reply']);?></strong></div>
                            <div class="col-sm-4">
                                <div class="row">
                                    <div class="col-sm-3"><?php echo $avt;?></div>
                                    <div class="col-sm-9 row">
                                        <?php if(!empty($forum['last_topic'])): ?>
                                            <div class="col-sm-12">
                                                <div class="feed_time">
                                                    <span class="date"><?php echo $this->Moo->getTime($forum['last_topic']['ForumTopic']['created'], Configure::read('core.date_format'), $utz) ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <span style="font-weight: bold;font-size: 100%;"><a href="<?php echo $forum['last_topic']['ForumTopic']['moo_href'];?>"><?php echo $forum['last_topic']['ForumTopic']['title']; ?></a></span>
                                            </div>
                                        <?php endif;?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    <?php endforeach;?>
                </div>
            <?php endif;?>
        </div>
    <?php endforeach;?>

</div>