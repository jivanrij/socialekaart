<?php
/**
 * check is address exists via ajax
 */
function showtutorial() {
  global $user;
  $user = user_load($user->uid);
  $master = false;
  if(in_array(helper::ROLE_EMPLOYER_MASTER, $user->roles) || in_array('administrator', $user->roles)){
    $master = true;
  }
    
  $iStep = 1;
  if(isset($_GET['step'])){
    $iStep = $_GET['step'];
  }
  
  // setting the defaults for the buttons
  $back_button = '<a class="gbutton rounded noshadow" ref="%back_ref%" id="back_tutorial_button" title="'.t('Step back').'"><span>'.t('Step back').'</span></a>';
  $close_button = '<a class="add_margin_left gbutton rounded noshadow left" id="close_tutorial_button" ref="quit" title="'.t('Stop tutorial').'"><span>'.t('Stop tutorial').'</span></a>';
  $forward_button = '<a class="gbutton rounded noshadow right" id="forward_tutorial_button" ref="%forward_ref%" title="'.t('Step forward').'"><span>'.t('Step forward').'</span></a>';
  $back_ref = '1';
  $forward_ref = '2';
  // changing the defaults where needed for the buttons
  switch($iStep){
    case '1':
      $back_button = '';
      $close_button = '<a class="gbutton rounded noshadow left" id="close_tutorial_button" ref="quit" title="'.t('Stop tutorial').'"><span>'.t('Stop tutorial').'</span></a>';
      $close_button = '';
      break;
    case '2':
      $back_ref = '1';
      $forward_ref = '3';
      $close_button = '';
      break;
    case '3':
      $back_ref = '2';
      $forward_ref = '4';
      $close_button = '';
      break;
    case '4':
      $back_ref = '3';
      if($master){
        // only a master user needs to see step 5, this step is ment to tell him something about the users
        $forward_ref = '5';
        $close_button = '';
      }else{
        $forward_ref = 'quit'; // ends the tutorial
        $close_button = '';
        $forward_button = '<a class="gbutton rounded noshadow right" id="forward_tutorial_button" ref="%forward_ref%" title="'.t('Start working').'"><span>'.t('Start working').'</span></a>';
      }
      break;
    case '5':
      $back_ref = '4';
      $forward_ref = 'quit'; // ends the tutorial
      $close_button = '';
      $forward_button = '<a class="gbutton rounded noshadow right" id="forward_tutorial_button" ref="%forward_ref%" title="'.t('Start working').'"><span>'.t('Start working').'</span></a>';
      break;
    case 'quit':
      // ends the tutorial
      global $user;
      $user = user_load($user->uid);
      $fieldName = GojiraSettings::CONTENT_TYPE_TUTORIAL_FIELD;
      $user->$fieldName = array(LANGUAGE_NONE => array(0 => array('value' => 1)));
      user_save($user);
      exit;
  }
  // combine the ref's on the defaults
  $back_button = str_replace('%back_ref%', $back_ref, $back_button);
  $forward_button = str_replace('%forward_ref%', $forward_ref, $forward_button);
  
  //$text = helper::getText('TUTORIAL_TEXT_STEP_'.strtoupper($iStep));
  
  //include getcwd().'/sites/all/modules/custom/gojira/templates/questiontopics/'.$sCurrentUrl.'.html';

  $text = file_get_contents ( getcwd().'/sites/all/modules/custom/gojira/templates/tutorial/step_'.$iStep.'.html', true);
  
  $text .= '<div class="gbutton_wrapper">'.$back_button.$close_button.$forward_button.'</div>';
          
  echo $text;
  exit;
}