<h1><?php echo drupal_get_title(); ?></h1>
<p>
  <?php echo $output['txt']; ?>
</p>
<?php if(array_key_exists('form', $output)): ?>
  <?php echo render($output['form']); ?>
<?php endif; ?>

<p>
  <?php echo $output['button']; ?>
</p>