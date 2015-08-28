<h1><?php echo drupal_get_title(); ?></h1>
<p><?php echo helper::getText('SUGGEST_LOCATION_THANKS'); ?></p>
<p>
	<a href="\?loc=<?php echo $oNewLocation->nid; ?>" title="<?php echo t('Look at location'); ?>">
		<?php echo t('To take a look at the just added location click here.'); ?>
	</a>
</p>