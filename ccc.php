<?php

$curl = curl_init();
$key = $_GET['key'];
$s = $_GET['searchChunk'];

if ($s!=null && $key !=null){
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://search.altares.fr/search?searchChunk=".$s,
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

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  header('Content-Type: application/json');
  echo $response;
  //echo json_decode($response);
  /*
   return {
        "speech": speech,
        "displayText": speech,
        #"data": {},
        # "contextOut": [],
        "source": "apiai-onlinestore-shipping"
    }  
  */
  
}
}else {
  echo "Empty request";
}
?>
