<?php
/**
 * Welcome page
 *
 * @return string
 */
function welcome() {
  global $user;
  if ($user->uid) {
    // if you are logged in, go to the search
    return gojirasearch();
  } else {
    return theme('welcome');
  }
}