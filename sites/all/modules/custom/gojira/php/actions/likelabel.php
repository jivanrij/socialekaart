<?php
function likelabel(){
  Labels::like($_GET['tid'], $_GET['nid']);
  Search::getInstance()->updateSearchIndex($_GET['nid']);
}