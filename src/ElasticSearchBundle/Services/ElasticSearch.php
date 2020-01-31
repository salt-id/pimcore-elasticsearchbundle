<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 30/01/2020
 * Time: 10:51
 */

namespace SaltId\ElasticSearchBundle\Services;

use Elasticsearch\ClientBuilder;

class ElasticSearch
{
    /** @var \Elasticsearch\Client $client */
    private $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setBasicAuthentication('admin', 'admin')
            ->setHosts(
                [
                    'host' => 'http://elastic.staging7.salt.id:80'
                ]
            )
            ->build();
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