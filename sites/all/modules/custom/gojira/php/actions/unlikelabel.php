<?php
function unlikelabel(){
  Labels::unlike($_GET['tid'], $_GET['nid']);
  Search::getInstance()->updateSearchIndex($_GET['nid']);
}