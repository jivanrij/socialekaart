<div style="margin-top:5px;width:100%;height:500px;overflow-x:hidden; overflow-y: scroll;">
  <?php echo $output['txt']; ?>
</div>
<?php if(array_key_exists('form', $output)): ?>
  <?php echo render($output['form']); ?>
<?php endif; ?>

<p>
  <?php echo $output['button']; ?>
</p>