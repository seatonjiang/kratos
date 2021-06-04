<?php
require('../../vendor/autoload.php');

use Volc\Service\BusinessSecurity;
$client = BusinessSecurity::getInstance();

// call below method if you dont set ak and sk in ～/.volc/config
$client->setAccessKey("ak");
$client->setSecretKey("sk");

$response = $client->RiskDetect(1, "chat", "{\"uid\":123411, \"operate_time\":1609818934, \"chat_text\":\"a😊\"}");
echo $response;