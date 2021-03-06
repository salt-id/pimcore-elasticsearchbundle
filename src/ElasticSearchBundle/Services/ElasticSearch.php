<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 30/01/2020
 * Time: 10:51
 */

namespace SaltId\ElasticSearchBundle\Services;

use Elasticsearch\ClientBuilder;
use SaltId\ElasticSearchBundle\Model\Config;

class ElasticSearch
{
    /** @var \Elasticsearch\Client $client */
    private $client;

    public function __construct()
    {
        $config = Config::getById(1);
        $client = ClientBuilder::create();
        if ($config->getHttpBasicAuthUser() && $config->getHttpBasicAuthPassword()) {
            $client->setBasicAuthentication($config->getHttpBasicAuthUser(), $config->getHttpBasicAuthPassword());
        }
        $client->setHosts(
                [
                    'host' => $config->getHostorip() . ':' . $config->getPort()
                ]
            );
        $this->client = $client->build();
    }

    public function createIndex(string $index, array $bodyData)
    {
        $params = [
            'index' => $index,
            'body'  => $bodyData
        ];

        try {
            return $this->client->indices()->create($params);
        } catch (\Exception $exception) {
            return $this->getLastRequestInfo();
        }
    }

    public function deleteIndex(string $index)
    {
        $params = [
            'index' => $index
        ];

        try {
            return $this->client->indices()->delete($params);
        } catch (\Exception $exception) {
            return $this->getLastRequestInfo();
        }
    }

    public function createDocument(string $index, string $type, string $id, array $bodyData)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'id' => $id,
            'body' => $bodyData
        ];

        try {
            return $this->client->index($params);
        } catch (\Exception $exception) {
            return $this->getLastRequestInfo();
        }
    }

    public function deleteByQuery(string $index, string $type, array $bodyData)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'body' => $bodyData
        ];

        try {
            return $this->client->deleteByQuery($params);
        } catch (\Exception $exception) {
            return $this->getLastRequestInfo();
        }
    }

    public function deleteDocument(string $index, string $id)
    {
        $params = [
            'index' => $index,
            'id'    => $id
        ];

        try {
            return $this->client->delete($params);
        } catch (\Exception $exception) {
            return $this->getLastRequestInfo();
        }
    }

    public function searchDocument($params)
    {
        try {
            return $this->client->search($params);
        } catch (\Exception $exception) {
            return $this->getLastRequestInfo();
        }
    }

    public function getDocument(string $index, string $id)
    {
        $params = [
            'index' => $index,
            'id' => $id
        ];

        try {
            return $this->client->get($params);
        } catch (\Exception $exception) {
            return $this->getLastRequestInfo();
        }
    }

    private function getLastRequestInfo()
    {
        return $this->client->transport->getLastConnection()->getLastRequestInfo();
    }
}