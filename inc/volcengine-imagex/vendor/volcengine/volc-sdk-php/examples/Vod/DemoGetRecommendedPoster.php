<?php

use Volc\Service\Vod\Models\Request\VodGetRecommendedPosterRequest;
use Volc\Service\Vod\Models\Response\VodGetRecommendedPosterResponse;
use Volc\Service\Vod\Vod;

require('../../vendor/autoload.php');

$client = Vod::getInstance();
// call below method if you dont set ak and sk in ～/.vcloud/config
// $client->setAccessKey("");
// $client->setSecretKey("");

$vids = "vid1,vid2";

echo "\n获取候选封面\n";

$req = new VodGetRecommendedPosterRequest();
$req->setVids($vids);
$response = new VodGetRecommendedPosterResponse();
try {
    $response = $client->getRecommendedPoster($req);
} catch (Throwable $e) {
    print($e);
}

if ($response->getResponseMetadata()->getError() != null) {
    print_r($response->getResponseMetadata()->getError());
}

echo $response->serializeToJsonString();