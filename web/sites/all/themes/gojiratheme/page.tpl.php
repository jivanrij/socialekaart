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
    include(drupal_get_path('theme', 'gojiratheme') . '/_add.tpl.php');
    echo $sLowResWarning;
    break;
  case Template::VIEWTYPE_CRUD_TITLE:
    echo $overlay;
    include(drupal_get_path('theme', 'gojiratheme') . '/_page_crud_title.tpl.php');
    include(drupal_get_path('theme', 'gojiratheme') . '/_add.tpl.php');
    echo $sLowResWarning;
    break;
  case Template::VIEWTYPE_SEARCH:
    echo $overlay;
    include(drupal_get_path('theme', 'gojiratheme') . '/_page_content.tpl.php');
    include(drupal_get_path('theme', 'gojiratheme') . '/_add.tpl.php');
    echo $sLowResWarning;
    break;
  case Template::VIEWTYPE_BIG:
    echo $overlay;
    include(drupal_get_path('theme', 'gojiratheme') . '/_page_big.tpl.php');
    include(drupal_get_path('theme', 'gojiratheme') . '/_add.tpl.php');
    echo $sLowResWarning;
    break;
  case Template::VIEWTYPE_BIG_TITLE:
    echo $overlay;
    include(drupal_get_path('theme', 'gojiratheme') . '/_page_big_title.tpl.php');
    include(drupal_get_path('theme', 'gojiratheme') . '/_add.tpl.php');
    echo $sLowResWarning;
    break;
}
