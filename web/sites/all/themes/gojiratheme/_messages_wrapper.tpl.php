<?php $message = Messages::getFormMessage(); ?>
<?php if($message): ?>
  <div id="crud_holder" class="rounded">
    <div class="errormessage"><?php echo $message; ?></div>
  </div>
<?php else: ?>
  <?php if(array_key_exists('status', $messages_list)): ?>
    <?php if(count($messages_list['status']) > 0): ?>
      <div id="crud_holder" class="rounded">
        <div class="statusmessage">
          <?php foreach($messages_list['status'] as $status): ?>
            <?php echo $status; ?><br />
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
<?php endif; ?>