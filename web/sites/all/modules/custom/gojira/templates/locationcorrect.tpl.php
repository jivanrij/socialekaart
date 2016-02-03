<h1><?php echo drupal_get_title(); ?></h1>
<?php echo render($output['form']); ?>
<div class="inform">
    <i class="fa fa-envelope-o"></i> <a href="/inform?nid=<?php echo $output['nid']; ?>" title="Informeer ons">Informeer ons</a> als er iets niet klopt aan deze zorgverlener.
</div>


