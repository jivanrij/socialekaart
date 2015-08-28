<?php
/**
 * Ajax action to switch the users only_show_favorites field
 *
 * @global type $user
 */
function switchfavorites() {
  if ($_GET['turn'] == 'on') {
    Favorite::getInstance()->turnFavoriteFilter(true);
  } else {
    Favorite::getInstance()->turnFavoriteFilter(false);
  }
}