<?php
/**
 * The get tags suggentions via ajax
 */
function locationtags() {
  echo json_encode(helper::getAvailableTerms($_GET['term']));
  exit;
}