<?php
// implements the return page described in http://www.easy-ideal.com/api-implementeren/
function reportdouble() {
    if(isset($_GET['nids'])){
        Mailer::informAdminAboutDoubleLocations($_GET['nids']);
    }
    exit;
}
