<?php
  //another one
  function sendSmsToClient($phone_number,$message,$apikey,$partnerID,$shortcodes){
    $partnerID = $partnerID;
    $apikey = $apikey;
    $shortcode = $shortcodes;
    
    $mobile = $phone_number; // Bulk messages can be comma separated
    $message = $message;
    
    $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
    //$finalURL = "https://quicksms.advantasms.com/api/services/sendsms/?message=".urlencode($message)."&mobile=".$phone_number."&shortcode=".urlencode($shortcodes)."&partnerID=".urlencode($partnerID)."&apikey=".urlencode($apikey)."";
    //$finalURL = "https://quicksms.advantasms.com/api/services/sendsms/?message=Test Message&mobile=0704241905&shortcode=JuaMobile&partnerID=3468&apikey=9dbd3d8b9ae3d183db6598e815d66f12";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $finalURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
  }
  function sendSmsToClient2($phone_number,$message,$apikey,$partnerID,$shortcodes){
    $partnerID = $partnerID;
    $apikey = $apikey;
    $shortcode = $shortcodes;
    
    $mobile = $phone_number; // Bulk messages can be comma separated
    $message = $message;
    $finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
    //$finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
    //$finalURL = "https://quicksms.advantasms.com/api/services/sendsms/?message=".urlencode($message)."&mobile=".$phone_number."&shortcode=".urlencode($shortcodes)."&partnerID=".urlencode($partnerID)."&apikey=".urlencode($apikey)."";
    //$finalURL = "https://quicksms.advantasms.com/api/services/sendsms/?message=Test Message&mobile=0704241905&shortcode=JuaMobile&partnerID=3468&apikey=9dbd3d8b9ae3d183db6598e815d66f12";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $finalURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
  }
  function checkDelivery($apikey,$partnerID,$message_id){
      $url = 'https://mysms.celcomafrica.com/api/services/getdlr/';

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json')); //setting custom header
    
    
      $curl_post_data = array(
              //Fill in the request parameters with valid values
            'partnerID' => $partnerID,
            'apikey' => $apikey,
            'messageID' => $message_id,
      );
    
      $data_string = json_encode($curl_post_data);
    
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    
      $curl_response = curl_exec($curl);
      return $curl_response;
  }
?>