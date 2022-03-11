<?php
/**
 * Project nhanhvn-lib
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 11/03/2022
 * Time: 10:41
 */

namespace nguyenanhung\Services\NhanhVN;

/**
 * Class NhanhVN - Kết nối tới hệ thống Nhanh.vn
 *
 * @package   nguyenanhung\Services\NhanhVN\Helper
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class NhanhVNServices
{
    const URI_GET_PRODUCT_SEARCH = '/api/product/search';
    const URI_GET_PRODUCT_DETAIL = '/api/product/detail';
    const URI_SHIPPING_LOCATION  = '/api/shipping/location';
    const URI_ADD_ORDER          = '/api/order/add';
    const URI_GET_ORDER          = '/api/order/index';
    const URI_UPDATE_ORDER       = '/api/order/update';
    const URI_GET_CUSTOMER       = '/api/customer/search';
    const URI_GET_CATEGORY       = '/api/product/category';

    /**
     * The server will use this parameter to process your request
     */
    const SERVICE_VERSION = '1.0'; // please DO NOT change or remove this value

    /**
     * the server address.
     * testing: https://dev.nhanh.vn
     * production: https://graph.nhanh.vn
     *
     * @var string
     */
    //protected $server = "https://dev.nhanh.vn";
    protected $server = "https://graph.nhanh.vn";

    /**
     * apiUsername
     *
     * @var string
     */
    protected $apiUsername = "xxx";

    /**
     * secretKey
     *
     * @var string
     */
    protected $secretKey = "xxx";

    /**
     * Function getServer
     *
     * @return string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 11/03/2022 43:02
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Function setServer
     *
     * @param $server
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 11/03/2022 42:39
     */
    public function setServer($server)
    {
        $this->server = $server;
    }

    /**
     * Function getApiUsername
     *
     * @return string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 11/03/2022 42:44
     */
    public function getApiUsername()
    {
        return $this->apiUsername;
    }

    /**
     * Function setApiUsername
     *
     * @param $apiUsername
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 11/03/2022 42:51
     */
    public function setApiUsername($apiUsername)
    {
        $this->apiUsername = $apiUsername;
    }

    /**
     * Function getSecretKey
     *
     * @return string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 11/03/2022 42:53
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * Function setSecretKey
     *
     * @param $secretKey
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 11/03/2022 42:56
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * Function createChecksum
     *
     * @param string|array $data
     *
     * @return string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 11/03/2022 43:07
     */
    public function createChecksum($data)
    {
        if (is_array($data)) {
            $dataString = json_encode($data);
        } else {
            $dataString = $data;
        }

        return md5(md5($this->getSecretKey() . $dataString) . $dataString);
    }

    /**
     * validate the checksum of data
     *
     * @param string|array $data
     * @param string       $checksum
     *
     * @return bool
     */
    public function isValidChecksum($checksum, $data)
    {
        return $checksum == $this->createChecksum($data);
    }

    /**
     * Function sendRequest - Send request tới hệ thống nhanh.vn
     *
     * @param string          $requestUri
     * @param array           $data
     * @param string|int|null $storeId
     *
     * @return mixed
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 11/03/2022 43:46
     */
    public function sendRequest($requestUri, $data = array(), $storeId = null)
    {
        $dataString = json_encode($data);
        $postFields = [
            "version"     => self::SERVICE_VERSION,
            "apiUsername" => $this->getApiUsername(),
            "storeId"     => $storeId,
            "data"        => $dataString,
            "checksum"    => $this->createChecksum($dataString)
        ];

        $curl = curl_init($this->getServer() . $requestUri);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $curlResult = curl_exec($curl);

        return json_decode($curlResult);
    }
}
