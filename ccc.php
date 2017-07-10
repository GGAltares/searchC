<?php

$curl = curl_init();
$key = $_GET['key'];
$sP = $_POST['Company'];
// GET DATA FROM API.AI CALL
$json = file_get_contents('php://input');
$request = json_decode($json, true);
$action = $request["result"]["action"];
$parameters = $request["result"]["parameters"];
$sP = $parameters['Company'];


if ($sP!=null && $key !=null){

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://search.altares.fr/search?searchChunk=".$sP,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "x-api-key: ".$key
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);
  header('Content-Type: application/json');

  if ($err) {
    echo '{
         "speech": "err '.$err.'",
         "displayText": "err '.$err.'",
         "source": "apiai-dirigeant-company-altares"
     }';
    /*echo '{
      "status": {
      "code": 500,
      "errorType": "ERR-'.$err.'"
      }
    }';
    */
  } else {

    //echo $response;
    //echo json_decode($response);
    $response
     echo '{
          "speech": "Voici les informations concernant '.$sP.'",
          "displayText": "Voici les informations concernant '.explode(", ",$response).'",
          "source": "apiai-dirigeant-company-altares"
      }';


  }
} else {
/*  echo '{
    "status": {
    "code": 400,
    "errorType": "Empty request"
    }
  }';*/
  echo '{
       "speech": "EMPTY ",
       "displayText": "EMPTY",
       "source": "apiai-dirigeant-company-altares"
   }';
}
?>
