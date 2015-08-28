<h1><?php echo drupal_get_title(); ?></h1>
<?php if(helper::getSystemnameRole() == helper::ROLE_EMPLOYEE): ?>
    <p><?php echo helper::getText('SETTINGS_SAVED_EMPLOYEE_TEXT'); ?></p>
<?php else: ?>
    <p><?php echo helper::getText('SETTINGS_SAVED_DEFAULT_TEXT'); ?></p>
<?php endif; ?>
