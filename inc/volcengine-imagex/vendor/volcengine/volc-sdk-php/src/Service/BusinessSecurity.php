<?php


namespace Volc\Service;
use Volc\Base\V4Curl;

class BusinessSecurity extends V4Curl
{
    protected function getConfig(string $region)
    {
        switch ($region) {
            case 'cn-north-1':
                $config = [
                    'host' => 'https://open.volcengineapi.com',
                    'config' => [
                        'timeout' => 5.0,
                        'headers' => [
                            'Accept' => 'application/json'
                        ],
                        'v4_credentials' => [
                            'region' => 'cn-north-1',
                            'service' => 'BusinessSecurity',
                        ],
                    ],
                ];
                break;
            default:
                throw new \Exception(sprintf("AdBlocker not support region, %s", $region));
        }
        return $config;
    }

    protected $apiList = [
        'RiskDetection' => [
            'url' => '/',
            'method' => 'post',
            'config' => [
                'query' => [
                    'Action' => 'RiskDetection',
                    'Version' => '2021-02-02',
                ],
            ]
        ],
        'AsyncRiskDetection' => [
            'url' => '/',
            'method' => 'post',
            'config' => [
                'query' => [
                    'Action' => 'AsyncRiskDetection',
                    'Version' => '2021-02-25',
                ],
            ]
        ],
        'RiskResult' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'RiskResult',
                    'Version' => '2021-03-10',
                ],
            ]
        ],
    ];

    protected function requestWithRetry(string $api, array $configs): string
    {
        try {
            $response = $this->request($api, $configs);
            return (string)$response->getBody();
        }
        catch (\Exception $e)
        {
            $response = $this->request($api, $configs);
            return (string)$response->getBody();
        }
    }

    public function RiskDetect(int $appId, string $service, string $parameters): string
    {
        $commitBody = array();
        $commitBody["AppId"] = $appId;
        $commitBody["Service"] = $service;
        $commitBody["Parameters"] = $parameters;
        $commitReq = [
            "json" => $commitBody
        ];
        return $this->requestWithRetry("RiskDetection", $commitReq);
    }

    public function AsyncRiskDetect(int $appId, string  $service, string $parameters): string
    {
        $commitBody = array();
        $commitBody["AppId"] = $appId;
        $commitBody["Service"] = $service;
        $commitBody["Parameters"] = $parameters;
        $commitReq = [
            "json" => $commitBody
        ];
        return $this->requestWithRetry("AsyncRiskDetection", $commitReq);
    }

    public function RiskResult(int $appId, string $service, int $startTime, int $endTime, int $pageSize, int $pageNum): string
    {
        $commitBody = array();
        $commitBody["AppId"] = $appId;
        $commitBody["Service"] = $service;
        $commitBody["StartTime"] = $startTime;
        $commitBody["EndTime"] = $endTime;
        $commitBody["PageSize"] = $pageSize;
        $commitBody["PageNum"] = $pageNum;
        $commitReq = [
            "query" => $commitBody
        ];
        return $this->requestWithRetry("RiskResult", $commitReq);
    }
}