<?php if ($has_locations): ?>

    <div id="ownlist_menu">
        <h2><?php echo t('Filter by category'); ?></h2>
        <ul>
            <li><a class="ownlist_cat active show_all"><?php echo t('Show all'); ?></a></li>
            <?php foreach ($ordered_categorys as $category_name => $category_info): ?>
                <li><a class="ownlist_cat" rel="<?php echo $category_info->category_nid; ?>"><?php echo t($category_name); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div id="ownlist_content">
        <?php
        echo '<ul>';
        foreach ($ordered_locations as $location_node) {
            $location = Location::getLocationObjectOfNode($location_node->nid);
            // we move the longitude a bit because we show the page with a bug crud and don't have the center
            echo '<li class="ownlist_cat_' . $location_node->category_nid . '"><a class="location_category_list" href="#' . $location_node->nid . '">' . $location_node->title . '</a></li>';
        }
        echo '</ul>';
        ?>
    </div>
<?php else: ?>
<p><?php echo helper::getText('OWNLIST_NO_FAVORITES_FOUND_TEXT'); ?></p>
<?php endif; ?>