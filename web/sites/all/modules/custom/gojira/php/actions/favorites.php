<?php
// let the favorites have it's own route
function favorites(){
  
  $self = Location::getCurrentLocationNodeObjectOfUser();
  $self->self = true;
  $self->score = 0;
  $self->distance = 0;
  $self->title = 'own location';
  
  $location = Search::getInstance()->getCenterMap($check_city);
  
  drupal_add_js(array('gojira' => array('page' => 'favorites')), 'setting');
  
  return theme('favorites');
}