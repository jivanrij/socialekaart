<?php
/**
 * Description of BIG
 *
 * @author jonathan
 */
class BIG {

  public static $instance = null;

  public static function getInstance() {
    if (is_null(self::$instance)) {
      self::$instance = new BIG();
    }
    
    return self::$instance;
  }
  
  /**
   * I will tell you if a BIG number is know in the cigb database.
   * 
   *  test big nr: 59063028901 from ECA van Rij
   * 
   * @param integer $big
   * @return boolean
   */
  public function verifyBIG($big){
    $client = new SoapClient("http://webservices.cibg.nl/Ribiz/OpenbaarV2.asmx?wsdl", array('trace' => 1));
    try{
      $result = $client->ListHcpApprox3(array('WebSite'=>'Ribiz','RegistrationNumber'=>$big));
    }catch(Exception $e){
      if($e->getCode() == 0){
        drupal_set_message(t("Cannot verify the BIG number (%big).", array('%big'=> $big)), 'error');
      }else{
        watchdog('soap', "SOAP Big check ({$big}) error - last request: ".$client->__getLastRequest());
        watchdog('soap', "SOAP Big check ({$big}) error - last response: ".$client->__getLastResponse());
        drupal_set_message(t('Cannot verify the BIG number. Please contact the administrator.'), 'status');
      }
      return false;
    }

    if(!isset($result->ListHcpApprox->ListHcpApprox3)){
      return false;
    }else{
      return true;
    }
  }

}
