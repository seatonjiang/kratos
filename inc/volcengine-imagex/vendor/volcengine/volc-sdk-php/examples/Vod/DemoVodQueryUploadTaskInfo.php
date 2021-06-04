<?php
require('../../vendor/autoload.php');

use Volc\Service\Vod\Models\Business\VodURLSet;
use Volc\Service\Vod\Models\Request\VodQueryUploadTaskInfoRequest;
use Volc\Service\Vod\Models\Response\VodQueryUploadTaskInfoResponse;
use Volc\Service\Vod\Vod;

$client = Vod::getInstance();
$client->setAccessKey('your ak');
$client->setSecretKey('your sk');

$jobId = 'url jobId';
$jobIds = [$jobId];


$request = new VodQueryUploadTaskInfoRequest();
$request->setJobIds(implode(",", $jobIds));

$response = new VodQueryUploadTaskInfoResponse();
try {
    $response = $client->queryUploadTaskInfo($request);
} catch (Exception $e) {
    echo $e, "\n";
} catch (Throwable $e) {
    echo $e, "\n";
}
if ($response->getResponseMetadata()->getError() != null) {
    print_r($response->getResponseMetadata()->getError());
}
echo $response->serializeToJsonString();
echo "\n";

$videoInfo = new VodURLSet();
$videoInfo = $response->getResult()->getData()->getMediaInfoList()[0];
echo $videoInfo->getRequestId(), "\n";
echo $videoInfo->getState(), "\n";
