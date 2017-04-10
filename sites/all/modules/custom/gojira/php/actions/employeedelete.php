<?php
/**
 * Deletes a given employee of the current loggen in manager
 *
 * @return string
 */
function employeedelete() {
    
  global $user;
  $deleteUserId = $_GET['id'];
  $deleteUser = user_load($deleteUserId);

  if ($deleteUserId == 1 || $deleteUserId == $user -> uid) {
    drupal_set_message(t('User @name unsuccessfully removed, you cannot remove yourself.', array('@name' => $deleteUser -> name)), 'status');
    drupal_goto('employee/list');
    return;
  }

  $employees = Group::getEmployees();
  foreach ($employees as $employee) {
    if ($employee -> uid == $deleteUserId) {
      user_delete($deleteUserId);
      drupal_set_message(t('User @name successfully removed.', array('@name' => $deleteUser -> name)), 'status');
      drupal_goto('employee/list');
      return;
    }
  }

  drupal_set_message(t('User @name not removed, please contact administrator if this problem re-occurs.', array('@name' => $deleteUser -> name)), 'error');
  watchdog(WATCHDOG_ERROR, 'Failed to remove employer user (' . $deleteUserId . ') by employee user (' . $user -> uid . ').');
  drupal_goto('employee/list');
  return;
}