<div class="sticker_search_container">
    <i class="material-icons sticker_search_icon">search</i>
    <label>
        <input type="text" id="sticker_search" placeholder="<?php echo __d('sticker', 'Search stickers');?>" value="">
    </label>
    <i class="material-icons sticker_search_cancel" style="display: none">cancel</i>
</div>
<div class="sticker_category_container">
    <?php if($categories != null):?>
    <ul class="sticker_category_list">
        <?php foreach($categories as $category):
            $category = $category['StickerCategory'];
        ?>
        <li>
            <div class="sticker_category" style="background-color: #<?php echo $category['background_color'];?>;" data-key="<?php echo trim($category['name']);?>">
                <img src="<?php echo $this->Sticker->getCategoryIcon($category);?>" />
                <span><?php echo $category['name'];?></span>
            </div>
        </li>
        <?php endforeach;?>
    </ul>
    <?php endif;?>
</div>
<div id="sticker_search_content"></div>