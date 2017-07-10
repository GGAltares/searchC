<?php

$curl = curl_init();
$s = $_GET['seachChunk'];

if ($s!=null){
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://search.altares.fr/search?searchChunk=".$s,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "postman-token: 373e9142-a90a-75f0-bab4-b31a7a5e809c",
    "x-api-key: uwNPsInIeo59yK34sbnjB5R5dtsQzZSz9Jt7OZeN"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
}else {
  echo "Empty request";
}
?>
