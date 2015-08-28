<h1><?php echo drupal_get_title(); ?></h1>
<?php if(!Subscriptions::canExtend()): ?>
  <p><?php echo helper::getText('CANNOT_PAY_2_YEARS_AHEAD'); ?></p>  
<?php elseif($subscribed): ?>
  <p><?php echo str_replace(array('%original_end_date%', '%new_end_date%'), array($original_end_date, $extend_new_end_date), helper::getText('PAYED_TEXT_SUBSCRIBED_PAGE')); ?></p>
  <?php 
  $form = drupal_get_form('gojira_to_ideal_page_extend_form');
  echo render($form); ?>
<?php else: ?>
  <p><?php echo str_replace('%new_end_date%', $extend_new_end_date, helper::getText('NOT_PAYED_TEXT_SUBSCRIBED_PAGE')) ?></p>
  <?php $form = drupal_get_form('gojira_to_ideal_page_form'); echo render($form); ?>
<?php endif; ?>  