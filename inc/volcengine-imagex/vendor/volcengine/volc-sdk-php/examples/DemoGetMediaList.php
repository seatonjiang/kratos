<?php

use Volc\Models\Vod\Request\VodGetMediaListRequest;
use Volc\Models\Vod\Response\VodGetMediaListResponse;
use Volc\Service\Vod\Vod;

require('../vendor/autoload.php');

$client = Vod::getInstance();
// call below method if you dont set ak and sk in ～/.vcloud/config
// $client->setAccessKey("");
// $client->setSecretKey("");

$spaceName = "your space";
$vid = "";
$status = "Published"; //Published,Unpublished
$order = "Desc";
$tags = "tag1,tag2,tag3";
$startTime = "1999-01-01T00:00:00Z";
$endTime = "2021-04-01T00:00:00Z";
$offset = "0";
$pageSize = "10";

echo "\n获取视频列表\n";

$req = new VodGetMediaListRequest();
$req->setSpaceName($spaceName);
$req->setVid($vid);
$req->setStatus($status);
$req->setOrder($order);
$req->setTags($tags);
$req->setStartTime($startTime);
$req->setEndTime($endTime);
$req->setOffset($offset);
$req->setPageSize($pageSize);

$response = new VodGetMediaListResponse();
try {
    $response = $client->getMediaList($req);
} catch (Throwable $e) {
    print($e);
}

if ($response->getResponseMetadata()->getError() != null) {
    print_r($response->getResponseMetadata()->getError());
}

echo $response->serializeToJsonString();
