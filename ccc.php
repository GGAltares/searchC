<?php

$curl = curl_init();
$key = $_GET['key'];

// GET DATA FROM API.AI CALL
$json = file_get_contents('php://input');
$request = json_decode($json, true);
$action = $request["result"]["action"];
$parameters = $request["result"]["parameters"];
$sP = $parameters['Company'];

// get term by get if not via api.ai
if($sP == ""){
  $sP = $_GET['Company'];
}

if ($sP!=null && $key !=null){
  //remove spaces in siren
  if (is_numeric(str_replace(" ","", $sP))) {
    $sP = str_replace(" ","", $sP);
  }

  // CALL TO OUR LOCAL WS tO GET COMPANY DATA
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://search.altares.fr/search?searchChunk=".urlencode($sP)."&isActiv=true",
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
  if ($err || $response==null) {
    echo '{
         "speech": "err '.$err.'",
         "displayText": "err '.$err.'",
         "source": "apiai-dirigeant-company-altares-'.$action.'"
     }';
  } else {
    $json = json_decode($response);
    if(count($json)>0){
      if(count($json)==1){
        $data = array_map('convert', $json);
        $prez = $data[0]['value']." (".$data[0]['siren'].") \n\r A l'adresse suivante : ".$data[0]['venue']."\n\r".$data[0]['activity']."";
        echo '{
              "speech": "Voici les informations concernant '.$prez.'",
              "displayText": "Voici les informations concernant '.$prez.'",
              "data":'.json_encode($data[0]).',
              "source": "apiai-dirigeant-company-altares-'.$action.'"
            }';
      } else {
        $data = array_map('convert', $json);
        $prez="J'ai trouvé ".count($data)." entreprises avec les informations que vous m'avez donné...\n\r
                Laquelle vous intéresse ?\r\n";
        for($i=0;$i<count($data);$i++){
          $prez.= $data[$i]['value']." (".$data[$i]['siren'].") \n\r A l'adresse suivante : ".$data[$i]['rnvpL6'].". \n\r";
        }
        echo '{
              "speech": "'.$prez.'",
              "displayText": "'.$prez.'",
              "data":'.json_encode($data[0]).',
              "source": "apiai-dirigeant-company-altares-'.$action.'"
            }';
      }
    }else{
      echo '{
           "speech": "Pas de résultats utilisables '.$sP.'",
           "displayText": "Pas de résultats utilisables '.$sP.'",
           "source": "apiai-dirigeant-company-altares-'.$action.'"
       }';
    }
  }
} else {
/*  echo '{
    "status": {
    "code": 400,
    "errorType": "Empty request"
    }
  }';*/
  echo '{
       "speech": "Pas de données utilisables '.$sP.'",
       "displayText": "Pas de données utilisables '.$sP.'",
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
  $res['rnvpL4'] = $item->address->rnvpL4;
  $res['rnvpL6'] = $item->address->rnvpL6;
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
