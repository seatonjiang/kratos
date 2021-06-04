<?php
require('../../vendor/autoload.php');

use Volc\Service\Vod\Models\Request\VodCommitUploadInfoRequest;
use Volc\Service\Vod\Models\Response\VodCommitUploadInfoResponse;
use Volc\Service\Vod\Upload\Functions;
use Volc\Service\Vod\Vod;

$client = Vod::getInstance();
$client->setAccessKey('your ak');
$client->setSecretKey('your sk');

$space = 'your space';
$session = "";
$callbackArgs = 'my callback';

Functions::addGetMetaFunc();
Functions::addSnapshotTimeFunc(2.1);
$functions = Functions::getFunctionsString();

$request = new VodCommitUploadInfoRequest();
$request->setSpaceName($space);
$request->setFunctions($functions);
$request->setSessionKey($session);
$request->setCallbackArgs($callbackArgs);

$response = new VodCommitUploadInfoResponse();
try {
    $response = $client->commitUploadInfo($request);
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

echo $response->getResult()->getData()->getVid(), "\n";
echo $response->getResult()->getData()->getPosterUri(), "\n";
echo $response->getResult()->getData()->getSourceInfo()->getWidth(), "\n";
echo $response->getResult()->getData()->getSourceInfo()->getHeight(), "\n";
