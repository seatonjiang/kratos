<?php

namespace ExerciseBook\Flysystem\ImageX;

use Exception;
use ExerciseBook\Flysystem\ImageX\Exception\FileNotFoundException;
use ExerciseBook\Flysystem\ImageX\Exception\FilesystemException;
use GuzzleHttp\Client;
use Volc\Service\ImageX;

class ImageXAdapter
{

    /**
     * @var ImageX ImageX Client Instance
     */
    private $client;

    /**
     * @var ImageXConfig ImageX Client Settings
     */
    private $config;

    /**
     * @var string Resources uri Prefix
     */
    private $uriPrefix;

    /**
     * ImageXAdapter constructor.
     * @param array|ImageXConfig $config
     * @throws Exception
     */
    public function __construct($config)
    {
        if ($config instanceof ImageXConfig) {
            $this->client = ImageX::getInstance($config->region);
            $this->client->setAccessKey($config->accessKey);
            $this->client->setSecretKey($config->secretKey);

            $this->config = $config;
            $this->uriPrefix = $this->imageXBuildUriPrefix();
        } else if (is_array($config)) {
            $this->config = new ImageXConfig();

            $this->config->region = $config["region"];
            $this->config->accessKey = $config["access_key"];
            $this->config->secretKey = $config["secret_key"];
            $this->config->serviceId = $config["service_id"];
            $this->config->domain = $config["domain"];

            $this->client = ImageX::getInstance($this->config->region);
            $this->client->setAccessKey($this->config->accessKey);
            $this->client->setSecretKey($this->config->secretKey);

            $this->uriPrefix = $this->imageXBuildUriPrefix();
        } else throw new \InvalidArgumentException("Config not supported.");
    }

    /**
     * Generate the uri Prefix
     *
     * @return string
     * @throws Exception
     */
    function imageXBuildUriPrefix()
    {
        $prefix = '';
        switch ($this->config->region) {
            case 'cn-north-1':
                $prefix = 'tos-cn-i-';
                break;
            case 'us-east-1':
                $prefix = 'tos-us-i-';
                break;
            case 'ap-singapore-1':
                $prefix = 'tos-ap-i-';
                break;
            default:
                throw new Exception(sprintf("ImageX not support region, %s", $this->config->region));
        }
        return $prefix . $this->config->serviceId;
    }

    /**
     * ImageX Interface getImageUploadFiles
     *
     * @param string|null $fNamePrefix
     * @param int $offset
     * @param int $limit
     * @param int $marker
     * @return string
     */
    public function getImageUploadFiles(string $fNamePrefix = null, int $offset = 0, int $limit = 1, int $marker = 0)
    {
        $applyParams = [];
        $applyParams["Action"] = "GetImageUploadFiles";
        $applyParams["Version"] = "2018-08-01";
        $applyParams["ServiceId"] = $this->config->serviceId;

        if ($fNamePrefix != null) $applyParams["FnamePrefix"] = $fNamePrefix;
        $applyParams["Offset"] = $offset;
        $applyParams["Limit"] = $limit;
        $applyParams["Marker"] = $marker;

        $queryStr = http_build_query($applyParams);

        return $response = $this->client->requestImageX('GetImageUploadFiles', ['query' => $queryStr]);
    }

    /**
     * ImageX Interface getImageUploadFile
     *
     * @param string|null $storeUri
     * @return string
     */
    public function getImageUploadFile(string $storeUri = null)
    {
        $applyParams = [];
        $applyParams["Action"] = "GetImageUploadFile";
        $applyParams["Version"] = "2018-08-01";
        $applyParams["ServiceId"] = $this->config->serviceId;

        $applyParams["StoreUri"] = $storeUri;

        $queryStr = http_build_query($applyParams);

        return $response = $this->client->requestImageX('GetImageUploadFile', ['query' => $queryStr]);
    }


    public function write($path, $contents, Config $config)
    {
        // Sign
        $applyParams = [];
        $applyParams["Action"] = "ApplyImageUpload";
        $applyParams["Version"] = "2018-08-01";
        $applyParams["ServiceId"] = $this->config->serviceId;
        $applyParams["UploadNum"] = 1;
        $applyParams["StoreKeys"] = array();
        $queryStr = http_build_query($applyParams);

        $queryStr = $queryStr . "&StoreKeys=" . $path;
        $response = $this->client->applyUploadImage(['query' => $queryStr]);

        $applyResponse = json_decode($response, true);
        if (isset($applyResponse["ResponseMetadata"]["Error"])) {
            throw new FilesystemException(sprintf("uploadImages: request id %s error %s", $applyResponse["ResponseMetadata"]["RequestId"], $applyResponse["ResponseMetadata"]["Error"]["Message"]));
        }

        $uploadAddr = $applyResponse['Result']['UploadAddress'];
        if (count($uploadAddr['UploadHosts']) == 0) {
            throw new FilesystemException("uploadImages: no upload host found");
        }
        $uploadHost = $uploadAddr['UploadHosts'][0];
        if (count($uploadAddr['StoreInfos']) != 1) {
            throw new FilesystemException("uploadImages: store infos num != upload num");
        }

        // Upload
        $crc32 = dechex(crc32($contents));
        $tosClient = new Client([
            'base_uri' => "https://" . $uploadHost,
            'timeout' => 5.0,
        ]);
        $response = $tosClient->request('PUT',
            $uploadAddr['StoreInfos'][0]["StoreUri"],
            ["body" => $contents,
                "headers" =>
                    ['Authorization' => $uploadAddr['StoreInfos'][0]["Auth"],
                        'Content-CRC32' => $crc32]
            ]);
        $uploadResponse = json_decode((string)$response->getBody(), true);
        if (!isset($uploadResponse["success"]) || $uploadResponse["success"] != 0) {
            throw new FilesystemException("upload " . $path . " error");
        }

        // Commit
        $commitParams = [];
        $commitParams["ServiceId"] = $this->config->serviceId;
        $commitBody = [];
        $commitBody["SessionKey"] = $uploadAddr['SessionKey'];
        $commitReq = [
            "query" => $commitParams,
            "json" => $commitBody,
        ];

        $response = json_decode($this->client->commitUploadImage($commitReq), true);
        if (isset($response["ResponseMetadata"]["Error"])) {
            throw new FilesystemException(sprintf("uploadImages: request id %s error %s", $response["ResponseMetadata"]["RequestId"], $response["ResponseMetadata"]["Error"]["Message"]));
        }

        return $this->getMetadata($path);
    }

    public function writeStream($path, $resource, Config $config)
    {
        return $this->write($path, stream_get_contents($resource), $config);
    }

    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    public function updateStream($path, $resource, Config $config)
    {
        return $this->write($path, stream_get_contents($resource), $config);
    }

    public function rename($path, $newpath)
    {
        if (!$this->copy($path, $newpath)) {
            return false;
        }

        return $this->delete($path);
    }

    public function copy($path, $newpath)
    {
        return false;
    }

    public function delete($path)
    {
        $path = $this->uriPrefix . '/' . $path;
        $response = json_decode($this->client->deleteImages($this->config->serviceId, [$path]), true);
        if (isset($response["ResponseMetadata"]["Error"])) {
            throw new FileNotFoundException($path);
        }
        return true;
    }

    public function deleteDir($dirname)
    {
        $len = strlen($dirname);
        if ($len > 0) {
            if ($dirname[$len - 1] != '/' && $dirname[$len - 1] != '\\') {
                $dirname .= '/';
            }
        } else $dirname = '/';


        return $this->delete($dirname);
    }

    public function createDir($dirname, Config $config)
    {
        $len = strlen($dirname);
        if ($len > 0) {
            if ($dirname[$len - 1] != '/' && $dirname[$len - 1] != '\\') {
                $dirname .= '/';
            }
        } else $dirname = '/';

        return $this->write($dirname, '', $config);
    }

    public function setVisibility($path, $visibility)
    {
        // ImageX did not support visibility
        return true;
    }

    public function has($path)
    {
        try {
            $response = $this->getMetadata($path);
        } catch (FileNotFoundException $e) {
            return false;
        }
        return true;
    }

    public function read($path)
    {
        if (!$this->has($path)) {
            return false;
        }

        $httpClient = new Client();
        $url = $this->config->domain . '/' . $this->uriPrefix . '/' . $path;
        return [
            'type' => 'file',
            'path' => $path,
            'contents' => $httpClient->get($url)->getBody()->getContents()
        ];
    }

    public function readStream($path)
    {
        if (!$this->has($path)) {
            return false;
        }

        $httpClient = new Client();
        $url = $this->config->domain . '/' . $this->uriPrefix . '/' . $path;
        return [
            'type' => 'file',
            'path' => $path,
            'stream' => $httpClient->get($url)->getBody()->detach()
        ];
    }

    public function listContents($directory = '', $recursive = false)
    {
        $ret = [];
        $path = trim($directory, '/\\');

        $continue = true;
        $offset = 0;
        while ($continue) {
            $response = json_decode($this->getImageUploadFiles($path, $offset, 100, 0), true);

            if (isset($response["ResponseMetadata"]["Error"])) {
                break;
            }

            $result = $response['Result'];

            $fileObjects = $result['FileObjects'];
            foreach ($fileObjects as $data) {
                $data['LastModified'] = strtotime($data['LastModified']);
                $data['timestamp'] = $data['LastModified'];

                $data['type'] = 'file';
                $data['size'] = $data['FileSize'];
                $data['path'] = $data['FileName'];
                array_push($ret, $data);
            }

            $offset += 100;
            $continue = $result['hasMore'];
        }

        return $ret;
    }

    public function getMetadata($path)
    {
        $path = $this->uriPrefix . '/' . $path;
        $response = json_decode($this->getImageUploadFile($path), true);

        if (isset($response["ResponseMetadata"]["Error"])) {
            $error = $response["ResponseMetadata"]["Error"];
            if ($error['CodeN'] == 604010) {
                throw new FileNotFoundException($path);
            } else {
                throw new FilesystemException(sprintf(
                    "getMetadata: request id %s, path %s, error %s",
                    $response["ResponseMetadata"]["RequestId"], $path, $response["ResponseMetadata"]["Error"]["Message"]
                ));
            }
        }

        $data = $response['Result'];
        $data['LastModified'] = strtotime($data['LastModified']);
        $data['timestamp'] = $data['LastModified'];

        $data['type'] = 'file';
        $data['size'] = $data['FileSize'];
        $data['path'] = $data['FileName'];
        return $data;
    }

    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    public function getMimetype($path)
    {
        return $this->getMetadata($path);
    }

    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    public function getVisibility($path)
    {
        return $this->getMetadata($path);
    }

    public function getUploadAuthToken($path)
    {
        return $this->client->getUploadAuth([$this->config->serviceId], 3600, $path);
    }

}
