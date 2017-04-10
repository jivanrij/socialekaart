<h1><?php echo drupal_get_title(); ?></h1>
<?php if($oNewLocation): ?>
    <p><?php echo helper::getText('SUGGEST_LOCATION_THANKS'); ?></p>
    <p>
            <a href="/?loc=<?php echo $oNewLocation->nid; ?>" title="<?php echo t('Look at location'); ?>">
                    <?php echo t('To take a look at the just added location click here.'); ?>
            </a>
    </p>
<?php else: ?>
    <p>
        <?php echo t('Location information successfully stored. But we could not find the coordinates. We will also give it a try, for now the location is inactive.'); ?>
    </p>
<?php endif; ?>