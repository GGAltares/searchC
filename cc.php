<?php

$client = new http\Client;
$request = new http\Client\Request;

$request->setRequestUrl('https://search.altares.fr/search');
$request->setRequestMethod('GET');
$request->setQuery(new http\QueryString(array(
  'searchChunk' => 'Altares'
)));

$request->setHeaders(array(
  'x-api-key' => 'uwNPsInIeo59yK34sbnjB5R5dtsQzZSz9Jt7OZeN'
));

$client->enqueue($request)->send();
$response = $client->getResponse();

echo $response->getBody();
?>
