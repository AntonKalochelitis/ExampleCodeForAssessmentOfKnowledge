<?php

namespace system\core\api\wtrade;

use system\core\helpers\DebugHelper;

/**
 * Class ApiConnect
 *
 * @property string host
 * @property string apiName
 * @property string apiKey
 * @property string url
 * @property array params
 *
 * @package core\api
 */
class ApiConnect
{
    use \system\core\abstracts\traits\TraitSetGetForClass;

    /**
     * @return ApiConnect
     */
    public static function find():ApiConnect
    {
        return new ApiConnect;
    }

    /**
     * @param string $apiName
     *
     * @return ApiConnect
     */
    public function setApiName(string $apiName = ''):ApiConnect
    {
        $this->apiName = $apiName;

        return $this;
    }

    /**
     * @param string $apiKey
     *
     * @return ApiConnect
     */
    public function setApiKey(string $apiKey = ''):ApiConnect
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @param string $url
     *
     * @return ApiConnect
     */
    public function setUrl(string $url = ''):ApiConnect
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Формируем и отправляем запрос
     *
     * @return string
     *
     * @throws \Exception
     */
    public function sendRequest():string
    {
        // TODO: Решить вопрос с временем
        $time = date('Y:m:d H:i:s');

        // Проверяем на наличие обязательного параметра
        if (empty($this->apiName)) {
            exit('Empty apiName');
        }

        // Проверяем на наличие обязательного параметра
        if (empty($this->apiKey)) {
            exit('Empty apiKey');
        }

        // Проверяем на наличие обязательного параметра
        if (empty($this->url)) {
            exit('Empty url');
        }

        //
        $authParams = [
            'request' => [
                'apiName'   => $this->apiName,
                'apiSalt'   => $this->getSig($this->apiName, $this->apiKey, $time),
                'time'      => $time,
            ],
        ];

        $params['request'] =  array_merge($authParams['request'], $this->params);

        $result = $this->sendCurl(json_encode($params, JSON_UNESCAPED_UNICODE));

        return $result;
    }

    /**
     * Чистим список параметров
     *
     * @return $this
     */
    protected function cleanParams():ApiConnect
    {
        $this->params = [];

        return $this;
    }

    /**
     * Принимаем дополнительные параметры
     *
     * @param array $param
     * s
     *
     * @return ApiConnect
     */
    public function setParams(array $params = []):ApiConnect
    {
        $this->params = $this->params ?? [];
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    /**
     * Вычесляем подпись
     *
     * @param string $apiName
     * @param string $apiKey
     * @param $time
     *
     * @return string
     */
    protected function getSig(string $apiName, string $apiKey, $time):string
    {
        return md5( $apiName.$apiKey.$time );
    }

    /**
     * @param string $paramsJson
     *
     * @return string
     * @throws \Exception
     */
    protected function sendCurl(string $paramsJson):string
    {
        $curl = new \Curl\Curl;

        $response = $curl->post($this->url, $paramsJson);

        return $curl->response;
    }
}
