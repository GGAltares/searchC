<?php

$curl = curl_init();
$key = $_GET['key'];
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
    CURLOPT_URL => "https://data.opendatasoft.com/api/records/1.0/search/?dataset=sirene%40public&facet=depet&facet=libcom&facet=siege&facet=saisonat&facet=libnj&facet=libapen&facet=libtefen&facet=categorie&facet=proden&facet=vmaj1&facet=vmaj2&facet=vmaj3&facet=liborigine&facet=libtca&facet=libreg_new&q=".urlencode($sP),
    //CURLOPT_URL => "https://search.altares.fr/search?searchChunk=".urlencode($sP)."&isActiv=true",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET"
    /*CURLOPT_HTTPHEADER => array(
      "x-api-key: ".$key
    ),*/
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
    $json= $json->records;
    if(count($json)>0){
      if(count($json)==1){
        $data = array_map('convertOpen', $json);
        $prez = "Nous avons trouvé l'entreprise que vous cherchez (".$sP.")";
        echo '{
              "speech": "'.$prez.'",
              "text": "'.$prez.'",
              "attachments":['.json_encode($data[0]).'],
              "source": "apiai-dirigeant-company-altares-'.$action.'"
            }';
      } else {
        $data = array_map('convertOpen', $json);
        $prez="J'ai trouvé ".count($data)." entreprises avec les informations que vous m'avez donné...\n\rLaquelle vous intéresse ?\r\n";
        for($i=0;$i<count($data);$i++){
          //$prez.= $data[$i]['value']." (".$data[$i]['siren'].") \n\r A l'adresse suivante : ".$data[$i]['rnvpL6'].". \n\r";
        }
        echo '{
              "speech": "'.$prez.'",
              "text": "'.$prez.'",
              "attachments":'.json_encode($data).',
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
  $res['title']= $item->legalName ." (".$item->siren.")";
  $res['title_link']= "https://www.manageo.fr/entreprises/".$item->siren.".html?utm_source=slack&utm_medium=referral&utm_campaign=slack";
  $res['text'] = $item->nafDescription."\n\r :office: ".$item->address->rnvpL4.', '.$item->address->rnvpL6;
  $res["color"]= "#42C1C6";

  return $res;
}
function convertOpen($item) {
  $res = array();
  $res['title']= $item->fields->nomen_long ." (".$item->fields->siren.")";
  $res['title_link']= "https://www.manageo.fr/entreprises/".$item->fields->siren.".html?utm_source=slack&utm_medium=referral&utm_campaign=slack";
  $res['text'] = $item->fields->libmonoact."\n\r :office: ".$item->fields->l4_normalisee.', '.$item->fields->l6_normalisee;
  $res["color"]= "#42C1C6";
  /*
  $res['value'] = $item->fields->nomen_long;
  $res['name'] = $item->fields->nomen_long;
  $res['siren'] = $item->fields->siren;
  $res['activity'] = $item->fields->libmonoact;
  $res['venue'] = $item->fields->l4_normalisee.', '.$item->fields->l6_normalisee;
  $res['fields'] = $item->fields;
  */
  return $res;
}



?>
