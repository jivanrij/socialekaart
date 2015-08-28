<?php
function linkhaweb() {
    
    global $user;
    $oUser = user_load($user);
    
    if($iHawebUser){
        Haweb::linkAccount($oUser);
    }
    
    return theme('linkhaweb');
}