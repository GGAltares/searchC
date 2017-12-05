<?php

$curl = curl_init();
$key = "uwNPsInIeo59yK34sbnjB5R5dtsQzZSz9Jt7OZeN";
$sP = $_POST['text'];

// GET DATA FROM API.AI CALL
$json = file_get_contents('php://input');
$request = json_decode($json, true);
$action = "search"; //$request["result"]["action"];

if ($sP!=null && $key !=null && $action!=null){
  //remove spaces in siren
  if (is_numeric(str_replace(" ","", $sP))) {
    $sP = str_replace(" ","", $sP);
  }

  if($action=="search") {
    search($sP);
  } else if($action=="dirigeant"){
    findDirigeant($sP);
  }
  } else {
    echo '{
         "speech": "Pas de données utilisables '.$sP.'-'.$request['replyData'].'--'.$request['reply'].'",
         "text": "Pas de données utilisables '.$sP.'",
         "source": "apiai-dirigeant-company-altares-'.$action.'"
     }';
 }

// HELPERS
function search($sP) {
  GLOBAL $key,$action;
  // CALL TO OUR LOCAL WS tO GET COMPANY DATA
  $curl = curl_init();
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


  // SET response Header type to JSON
  header('Content-Type: application/json');

  if ($err || $response==null) {
    echo '{
         "speech": "err '.$err.'",
         "text": "err '.$err.'",
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
              "text": "Voici les informations concernant '.$prez.'",
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
              "text": "'.$prez.'",
              "data":'.json_encode($data[0]).',
              "source": "apiai-dirigeant-company-altares-'.$action.'"
            }';
      }
    }else{
      echo '{
           "speech": "Pas de résultats utilisables '.$sP.'",
           "text": "Pas de résultats utilisables '.$sP.'",
           "source": "apiai-dirigeant-company-altares-'.$action.'"
       }';
    }
  }
}

function findDirigeant($sP) {
  GLOBAL $key,$action;
  // CALL TO OUR LOCAL WS tO GET COMPANY DATA
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://search.altares.fr/identity/".urlencode($sP),
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


  // SET response Header type to JSON
  header('Content-Type: application/json');

  if ($err || $response==null) {
    echo '{
         "speech": "err '.$err.'",
         "text": "err '.$err.'",
         "source": "apiai-dirigeant-company-altares-'.$action.'"
     }';
  } else {
    $json = json_decode($response);
    if(count($json)>0){
      if(count($json)==1){
        //print_r($json);
        //$data = array_map('convertD', $json);
        $prez = "Le dirigeant de la société ".$json->value." (".$json->siren.")
        est ".(($json->director->legalName!='')?$json->director->legalName:$json->director->firstName." ".$json->director->lastName)." en tant que ".$json->director->label. " depuis ".$json->director->startAt;
        echo '{
              "speech": "Voici : '.$prez.'",
              "text": "Voici le résultat: '.$prez.'",
              "data":'.json_encode($json).',
              "source": "apiai-dirigeant-company-altares-'.$action.'"
            }';
      } else {
        $data = array_map('convertD', $json);
        $prez="J'ai trouvé ".count($data)." entreprises avec les informations que vous m'avez donné...\n\r
                Laquelle vous intéresse ?\r\n";
        for($i=0;$i<count($data);$i++){
          $prez.= $data[$i]['value']." (".$data[$i]['siren'].") \n\r A l'adresse suivante : ".$data[$i]['rnvpL6'].". \n\r";
        }
        echo '{
              "speech": "'.$prez.'",
              "text": "'.$prez.'",
              "data":'.json_encode($data[0]).',
              "source": "apiai-dirigeant-company-altares-'.$action.'"
            }';
      }
    }else{
      echo '{
           "speech": "Pas de résultats utilisables '.$sP.'",
           "text": "Pas de résultats utilisables '.$sP.'",
           "source": "apiai-dirigeant-company-altares-'.$action.'"
       }';
    }
  }
}


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



?>
