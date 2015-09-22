
<div id="conditions_holder">
  <?php echo $output['txt']; ?>
</div>
<?php if(array_key_exists('form', $output)): ?>
  <?php echo render($output['form']); ?>
<?php endif; ?>
<p>
  <?php echo $output['button']; ?>
</p>