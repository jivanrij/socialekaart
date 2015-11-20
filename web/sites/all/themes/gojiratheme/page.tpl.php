<?php
$overlay = '<div id="overlay_wait" class="overlay_wait"></div><div id="overlay_wait_txt" class="overlay_wait"></div>';
switch(Template::getView()){
  case Template::VIEWTYPE_AJAX:
    print render($page['content']);
    break;
  case Template::VIEWTYPE_FRONT:
    include(drupal_get_path('theme', 'gojiratheme') . '/_page_front.tpl.php');
    break;
  case Template::VIEWTYPE_CRUD:
    echo $overlay;
    include(drupal_get_path('theme', 'gojiratheme') . '/_page_crud.tpl.php');
    break;
  case Template::VIEWTYPE_CRUD_TITLE:
    echo $overlay;
    include(drupal_get_path('theme', 'gojiratheme') . '/_page_crud_title.tpl.php');
    break;
  case Template::VIEWTYPE_SEARCH:
    echo $overlay;
    include(drupal_get_path('theme', 'gojiratheme') . '/_page_search.tpl.php');
    break;
  case Template::VIEWTYPE_BIG:
    echo $overlay;
    include(drupal_get_path('theme', 'gojiratheme') . '/_page_big.tpl.php');
    break;
  case Template::VIEWTYPE_BIG_TITLE:
    echo $overlay;
    include(drupal_get_path('theme', 'gojiratheme') . '/_page_big_title.tpl.php');
    break;
  case Template::VIEWTYPE_LOCATIONSET:
    echo $overlay;
    include(drupal_get_path('theme', 'gojiratheme') . '/_page_locationset.tpl.php');
    break;
}
