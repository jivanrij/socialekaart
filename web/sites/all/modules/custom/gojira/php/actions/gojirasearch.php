<?php
// search route handler
function gojirasearch() {
  global $user;
  $user = user_load($user->uid);
  
  $user_is_admin = false;
  if (in_array('administrator', array_values($user->roles))) {
    $user_is_admin = true;
  }
  
  return theme('gojirasearch');
}