<?php

$request = new HttpRequest();
$request->setUrl('https://search.altares.fr/search');
$request->setMethod(HTTP_METH_GET);

$request->setQueryData(array(
  'searchChunk' => 'Altares'
));

$request->setHeaders(array(
  'x-api-key' => 'uwNPsInIeo59yK34sbnjB5R5dtsQzZSz9Jt7OZeN'
));

try {
  $response = $request->send();

  echo $response->getBody();
} catch (HttpException $ex) {
  echo $ex;
}

?>
