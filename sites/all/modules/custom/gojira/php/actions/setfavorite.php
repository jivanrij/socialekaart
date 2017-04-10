<?php
/**
 * Ajax action to switch the favorite boolean for a user
 *
 * @global type $user
 */
function setfavorite() {
  if ($_GET['turn'] == 'off') {
    Favorite::getInstance()->removeFromFavorite($_GET['nid']);
  } else {
    Favorite::getInstance()->setFavorite($_GET['nid']);
  }
  echo 'success';
  exit;
}
