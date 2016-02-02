<h1><?php echo drupal_get_title(); ?></h1>
<p>
    <?php echo t('Edit the notes you have for location: %title%. These notes are only visible for you.',array('%title%'=>$output['title'])); ?>
</p>
<?php echo render($output['form']); ?>