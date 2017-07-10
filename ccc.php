<?php

$curl = curl_init();
$key = $_GET['key'];
//$s = $_GET['Company'];
$s = $_POST['Company'];


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

    //echo $response;
    //echo json_decode($response);

     echo '{
          "speech": "Voici les informations concernant '.$s.'",
          "displayText": "Voici les informations concernant '.$s.'",
          "source": "apiai-dirigeant-company-altares"
      }';


  }
}else {
  echo "Empty request";
}
?>
