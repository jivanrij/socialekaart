<?php $message = Messages::getFormMessage(); ?>
<?php if($message): ?>
  <div class="errormessage"><?php echo $message; ?></div>
<?php endif; ?>

<?php if(isset($messages_list) && is_array($messages_list) && array_key_exists('status', $messages_list)): ?>
  <?php if(count($messages_list['status']) > 0): ?>
    <div class="statusmessage">
      <?php foreach($messages_list['status'] as $status): ?>
        <?php echo $status; ?><br />
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php endif; ?>