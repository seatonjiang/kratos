<?php

namespace Volc\Service\Vod;

const ResourceSpaceFormat = "trn:vod:%s:*:space/%s";
const ResourceVideoFormat = "trn:vod::*:video_id/%s";
const ResourceStreamTypeFormat = "trn:vod:::stream_type/%s";
const ResourceWatermarkFormat = "trn:vod::*:watermark/%s";
const ActionGetPlayInfo = "vod:GetPlayInfo";
const ActionApplyUpload = "vod:ApplyUploadInfo";
const ActionCommitUpload = "vod:CommitUploadInfo";
const Statement = "Statement";
const Star = "*";


class VodOption
{
    public static $VOD_TPL_OBJ = 'tplv-vod-obj';
    public static $VOD_TPL_NOOP = 'tplv-vod-noop';
    public static $VOD_TPL_RESIZE = 'tplv-vod-rs';
    public static $VOD_TPL_CENTER_CROP = 'tplv-vod-cc';
    public static $VOD_TPL_SMART_CROP = 'tplv-vod-cs';
    public static $VOD_TPL_SIG = 'tplv-bd-sig';

    public static $FORMAT_JPEG = 'jpeg';
    public static $FORMAT_PNG = 'png';
    public static $FORMAT_WEBP = 'webp';
    public static $FORMAT_AWEBP = 'awebp';
    public static $FORMAT_GIF = 'gif';
    public static $FORMAT_HEIC = 'heic';
    public static $FORMAT_ORIGINAL = 'image';

    public static $HTTP = 'http';
    public static $HTTPS = 'https';

    public $isHttps;
    public $format;
    public $sigKey;
    public $tpl;
    public $w;
    public $h;
    public $kv;

    public function setHttps(bool $isHttps)
    {
        $this->isHttps = $isHttps;
    }

    public function getHttps()
    {
        return $this->isHttps;
    }

    public function setFormat(string $format)
    {
        $this->format = $format;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setSig(string $sig)
    {
        $this->sigKey = $sig;
    }

    public function getSig()
    {
        return $this->sigKey;
    }

    public function setKV(array $kv)
    {
        $this->kv = $kv;
    }

    public function getKV()
    {
        return $this->kv;
    }

    public function setVodTplObj()
    {
        $this->tpl = VodOption::$VOD_TPL_OBJ;
    }

    public function setVodNoop()
    {
        $this->tpl = VodOption::$VOD_TPL_NOOP;
    }

    public function setVodSig()
    {
        $this->tpl = VodOption::$VOD_TPL_SIG;
    }

    public function setVodTplCenterCrop(int $weight, int $height)
    {
        $this->tpl = VodOption::$VOD_TPL_CENTER_CROP;
        $this->w = $weight;
        $this->h = $height;
    }

    public function setVodTplSmartCrop(int $weight, int $height)
    {
        $this->tpl = VodOption::$VOD_TPL_SMART_CROP;
        $this->w = $weight;
        $this->h = $height;
    }

    public function setVodTplResize(int $weight, int $height)
    {
        $this->tpl = VodOption::$VOD_TPL_RESIZE;
        $this->w = $weight;
        $this->h = $height;
    }

    public function getTpl()
    {
        return $this->tpl;
    }

    public function getW()
    {
        return $this->w;
    }

    public function getH()
    {
        return $this->h;
    }

    public static $apiList = [
        'GetPlayInfo' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'GetPlayInfo',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'ApplyUploadInfo' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'ApplyUploadInfo',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'CommitUploadInfo' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'timeout' => 8.0,
                'query' => [
                    'Action' => 'CommitUploadInfo',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'UploadMediaByUrl' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'UploadMediaByUrl',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'QueryUploadTaskInfo' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'QueryUploadTaskInfo',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'StartWorkflow' => [
            'url' => '/',
            'method' => 'post',
            'config' => [
                'query' => [
                    'Action' => 'StartWorkflow',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'UpdateMediaInfo' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'UpdateMediaInfo',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'UpdateMediaPublishStatus' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'UpdateMediaPublishStatus',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'GetMediaInfos' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'GetMediaInfos',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'GetRecommendedPoster' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'GetRecommendedPoster',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'DeleteMedia' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'DeleteMedia',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'DeleteTranscodes' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'DeleteTranscodes',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'GetMediaList' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'GetMediaList',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'GetHlsDecryptionKey' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'GetHlsDecryptionKey',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
        'GetPrivateDrmPlayAuth' => [
            'url' => '/',
            'method' => 'get',
            'config' => [
                'query' => [
                    'Action' => 'GetPrivateDrmPlayAuth',
                    'Version' => '2020-08-01',
                ],
            ]
        ],
    ];

    public static function getConfig(string $region = '')
    {
        switch ($region) {
            case 'cn-north-1':
                $config = [
                    'host' => 'https://vod.volcengineapi.com',
                    'config' => [
                        'timeout' => 5.0,
                        'headers' => [
                            'Accept' => 'application/json',
                        ],
                        'v4_credentials' => [
                            'region' => 'cn-north-1',
                            'service' => 'vod',
                        ],
                    ],
                ];
                break;
            case 'ap-singapore-1':
                $config = [
                    'host' => 'https://vod.ap-singapore-1.volcengineapi.com',
                    'config' => [
                        'timeout' => 5.0,
                        'headers' => [
                            'Accept' => 'application/json'
                        ],
                        'v4_credentials' => [
                            'region' => 'ap-singapore-1',
                            'service' => 'vod',
                        ],
                    ],
                ];
                break;
            case 'us-east-1':
                $config = [
                    'host' => 'https://vod.us-east-1.volcengineapi.com',
                    'config' => [
                        'timeout' => 5.0,
                        'headers' => [
                            'Accept' => 'application/json'
                        ],
                        'v4_credentials' => [
                            'region' => 'us-east-1',
                            'service' => 'vod',
                        ],
                    ],
                ];
                break;
            default:
                throw new \Exception("Cant find the region, please check it carefully");
        }
        return $config;
    }
}
