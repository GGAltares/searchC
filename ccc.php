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

    $json = json_decode($response);
    $data = array_map('convert', $json);
    //echo $response;
    //echo json_decode($response);
     echo '{
          "speech": "Voici les informations concernant '.$sP.'",
          "displayText": "Voici les informations concernant '.explode(", ",$response).'",
          "data":"'.json_encode($data).'",
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

// HELPERS

function convert($item) {
  $res = array();
  $res['value'] = $item->legalName;
  $res['name'] = $item->legalName;
  $res['siren'] = $item->siren;
  $res['activity'] = $item->nafDescription;
  $res['venue'] = $item->address->rnvpL4.', '.$item->address->rnvpL6;
  $res['url'] = '--'.$item->siren.'?utm_source=altares.fr&utm_medium=referral&utm_campaign=searchhome';
  return $res;
}

/*
function as_get_search($request) {
  $data = [];

  if ( isset( $request['term'] ) ) {
    $term = $request['term'];
    if( strlen( $term ) > 1 ) {
      $opts = array('http' => array('header' => array('x-api-key: uwNPsInIeo59yK34sbnjB5R5dtsQzZSz9Jt7OZeN')));
      $context = stream_context_create($opts);
      $response = file_get_contents('https://search.altares.fr/search?searchChunk=' . $term . '&isActiv=true', false, $context);
      $json = json_decode($response);
      $data = array_map('convert', $json);
    }
  }

    return rest_ensure_response( $data );
  }
  */
?>
