<?php

/**
 * dogecloud 对象存储
 * @author Seaton Jiang <hi@seatonjiang.com>
 * @license GPL-3.0 License
 * @version 2022.01.26
 */

if (!empty(kratos_option('g_cos_fieldset')['g_cos'])) {
    function dogcloud_upload($object, $file, $mime)
    {
        if (!@file_exists($file)) {
            return false;
        }
        if (@file_exists($file)) {
            $accessKey = kratos_option('g_cos_fieldset')['g_cos_accesskey'];
            $secretKey = kratos_option('g_cos_fieldset')['g_cos_secretkey'];
            $bucket = kratos_option('g_cos_fieldset')['g_cos_bucketname'];

            $filesize = fileSize($file);
            $file = fopen($file, 'rb');

            $signStr = "/oss/upload/put.json?bucket=$bucket&key=$object" . "\n" . "";
            $sign = hash_hmac('sha1', $signStr, $secretKey);
            $authorization = "TOKEN " . $accessKey . ":" . $sign;

            $url = "https://api.dogecloud.com/oss/upload/put.json?bucket=$bucket&key=$object";
            $headers = array("Host: api.dogecloud.com", "Content-Type: $mime", "Authorization: $authorization");

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_PUT => true,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_INFILE => $file,
                CURLOPT_INFILESIZE => $filesize,
                CURLOPT_HTTPHEADER => $headers,
            ));
            $response = curl_exec($curl);
            curl_close($curl);
        } else {
            return false;
        }
    }

    // 上传附件
    function dogecloud_upload_attachments($metadata)
    {
        if (get_option('upload_path') == '.') {
            $metadata['file'] = str_replace("./", '', $metadata['file']);
        }

        $object = str_replace("\\", '/', $metadata['file']);
        $object = str_replace(get_home_path(), '', $object);
        $file = get_home_path() . $object;
        $object = str_replace("wp-content/uploads/", '', $object);
        $mime = $metadata['type'];

        dogcloud_upload('/' . $object, $file, $mime);

        return $metadata;
    }

    if (substr_count($_SERVER['REQUEST_URI'], '/update.php') <= 0) {
        add_filter('wp_handle_upload', 'dogecloud_upload_attachments', 50);
    }

    // 上传缩略图
    function dogecloud_upload_thumbs($metadata)
    {
        if (isset($metadata['sizes']) && count($metadata['sizes']) > 0) {
            $wp_uploads = wp_upload_dir();
            $basedir = $wp_uploads['basedir'];
            $file_dir = $metadata['file'];
            $file_path = $basedir . '/' . dirname($file_dir) . '/';
            if (get_option('upload_path') == '.') {
                $file_path = str_replace("\\", '/', $file_path);
                $file_path = str_replace(get_home_path() . "./", '', $file_path);
            } else {
                $file_path = str_replace("\\", '/', $file_path);
            }
            $object_path = str_replace(get_home_path(), '', $file_path);
            foreach ($metadata['sizes'] as $val) {
                $object = '/' . $object_path . $val['file'];
                $object = str_replace("wp-content/uploads/", '', $object);
                $file = $file_path . $val['file'];
                $mime = $metadata['type'];

                dogcloud_upload('/' . $object, $file, $mime);
            }
        }
        return $metadata;
    }

    if (substr_count($_SERVER['REQUEST_URI'], '/update.php') <= 0) {
        add_filter('wp_generate_attachment_metadata', 'dogecloud_upload_thumbs', 100);
    }

    // 删除文件
    function dogecloud_delete_remote_file($file)
    {
        $accessKey = kratos_option('g_cos_fieldset')['g_cos_accesskey'];
        $secretKey = kratos_option('g_cos_fieldset')['g_cos_secretkey'];
        $bucket = kratos_option('g_cos_fieldset')['g_cos_bucketname'];

        $file = str_replace("\\", '/', $file);
        $file = str_replace(get_home_path(), '', $file);
        $del_file_path = str_replace("wp-content/uploads/", '', $file);
        $del_file_body = "[\"$del_file_path\"]";

        $signStr = "/oss/file/delete.json?bucket=$bucket" . "\n" . $del_file_body;
        $sign = hash_hmac('sha1', $signStr, $secretKey);
        $authorization = "TOKEN " . $accessKey . ":" . $sign;

        $url = "https://api.dogecloud.com/oss/file/delete.json?bucket=$bucket";
        $headers = array('Host' => 'api.dogecloud.com', 'Content-Type' => 'application/json', 'Authorization' => $authorization);

        $request = new WP_Http;
        $result = $request->request($url, array('method' => 'POST', 'body' => $del_file_body, 'headers' => $headers));

        return $file;
    }
    add_action('wp_delete_file', 'dogecloud_delete_remote_file', 100);

    // 修改图片地址
    function custom_upload_dir($uploads)
    {
        $upload_path = '';
        $upload_url_path = kratos_option('g_cos_fieldset')['g_cos_url'];

        if (empty($upload_path) || 'wp-content/uploads' == $upload_path) {
            $uploads['basedir'] = WP_CONTENT_DIR . '/uploads';
        } elseif (0 !== strpos($upload_path, ABSPATH)) {
            $uploads['basedir'] = path_join(ABSPATH, $upload_path);
        } else {
            $uploads['basedir'] = $upload_path;
        }

        $uploads['path'] = $uploads['basedir'] . $uploads['subdir'];

        if ($upload_url_path) {
            $uploads['baseurl'] = $upload_url_path;
            $uploads['url'] = $uploads['baseurl'] . $uploads['subdir'];
        }
        return $uploads;
    }
    add_filter('upload_dir', 'custom_upload_dir');
}
