<?php
require('../../vendor/autoload.php');

use Volc\Service\Vod\Models\Request\VodApplyUploadInfoRequest;
use Volc\Service\Vod\Models\Response\VodApplyUploadInfoResponse;
use Volc\Service\Vod\Vod;

$client = Vod::getInstance();
$client->setAccessKey('your ak');
$client->setSecretKey('your sk');

$space = 'your space name';

$request = new VodApplyUploadInfoRequest();
$request->setSpaceName($space);

$response = new VodApplyUploadInfoResponse();
try {
    $response = $client->applyUploadInfo($request);
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

echo $response->getResult()->getData()->getUploadAddress()->getSessionKey(), "\n";
echo $response->getResult()->getData()->getUploadAddress()->getStoreInfos()[0]->getStoreUri(), "\n";
echo $response->getResult()->getData()->getUploadAddress()->getStoreInfos()[0]->getAuth(), "\n";
echo $response->getResult()->getData()->getUploadAddress()->getUploadHosts()[0], "\n";
