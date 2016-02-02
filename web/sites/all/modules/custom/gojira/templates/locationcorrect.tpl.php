<h1><?php echo drupal_get_title(); ?></h1>
<?php echo render($output['form']); ?>
<p>
    <a href="/inform?nid=<?php echo $output['nid']; ?>" title="Send us your improvement">Send us your improvement</a>
</p>