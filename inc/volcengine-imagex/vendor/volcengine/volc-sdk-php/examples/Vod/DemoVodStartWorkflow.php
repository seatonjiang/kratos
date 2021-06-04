<?php
require('../../vendor/autoload.php');

use Volc\Service\Vod\Models\Business\WorkflowParams;
use Volc\Service\Vod\Models\Request\VodStartWorkflowRequest;
use Volc\Service\Vod\Models\Response\VodStartWorkflowResponse;
use Volc\Service\Vod\Vod;

$client = Vod::getInstance();
$client->setAccessKey('your ak');
$client->setSecretKey('your sk');

$input_params = new WorkflowParams();
$request = new VodStartWorkflowRequest();
$request->setVid("your vid");
$request->setTemplateId("your template_id");
$request->setInput($input_params);
$request->setPriority(0);
$request->setCallbackArgs("your callback_args");

$response = new VodStartWorkflowResponse();
try {
    $response = $client->startWorkflow($request);
} catch (Exception $e) {
    echo $e, "\n";
} catch (Throwable $e) {
    echo $e, "\n";
}
if ($response->getResponseMetadata()->getError() != null) {
    echo $response->getResponseMetadata()->getError()->serializeToJsonString();
    return;
}
echo $response->getResult()->getRunId(), "\n";


