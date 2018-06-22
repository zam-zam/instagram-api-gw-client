<?php

namespace Zamzam\Instagram;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Zamzam\Instagram\Exceptions\NoDataException;

/**
* 
*/
class ApiClient
{

    const ENDPOINTS = [
        'users' => [
            'info' => 'users/%s',
            'feed' => 'users/%s/feed/%s',
            'search' => 'users/search/%s'
        ],
        'places' => [
            'feed' => 'places/%s/feed/%s',
            'search' => 'places/search/%s/%s'
        ],
        'tags' => [
            'feed' => 'tags/%s/feed/%s',
            'search' => 'tags/search/%s'
        ],
        'media' => [
            'info' => 'media/%s',
            'comments' => 'media/%s/comments',
            'likers' => 'media/%s/likers'
        ]
    ];

    function __construct($server, $api_token)
    {
        $this->guzzleClient = new GuzzleClient([
            'base_uri' => sprintf('http://%s/api/v1/', $server)
        ]);

        $this->requestData = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ],
            'query' => [ 'api_token' => $api_token ]
        ];
    }

    /* Users */
    public function getUserInfoByName($name)
    {
        $uri_path = sprintf(
            self::ENDPOINTS['users']['info'],
            $name);
        return $this->makeRequest($uri_path);
    }

    public function getUserFeed($userId, $nextMaxId = null)
    {
        $uri_path = sprintf(
            self::ENDPOINTS['users']['feed'],
            $userId,
            $nextMaxId);
        return $this->makeRequest($uri_path);
    }

    public function searchUsers($queryString, $excludeList = [], $rankToken = null)
    {
        $uri_path = sprintf(
            self::ENDPOINTS['users']['search'],
            $queryString);
        return $this->makeRequest($uri_path);
    }

    /* Places */
    public function getPlaceFeed($placeId, $rankToken, $nextMaxId = null)
    {
        $uri_path = sprintf(
            self::ENDPOINTS['places']['feed'],
            $placeId,
            $nextMaxId);
        return $this->makeRequest($uri_path);
    }
    public function getPlaceByCoordinates($latitude, $longitude)
    {
        $uri_path = sprintf(
            self::ENDPOINTS['getBody']['search'],
            $latitude,
            $longitude);
        return $this->makeRequest($uri_path);
    }

    /* Hashtags */
    public function getTagFeed($hashtag, $rankToken, $nextMaxId = null)
    {
        $uri_path = sprintf(
            self::ENDPOINTS['tags']['feed'],
            $hashtag,
            $nextMaxId);
        return $this->makeRequest($uri_path);
    }
    public function searchTags($queryString, $excludeList = [], $rankToken = null)
    {
        $uri_path = sprintf(
            self::ENDPOINTS['tags']['search'],
            $queryString);
        return $this->makeRequest($uri_path);
    }

    /* Media */
    public function getMedia($mediaId)
    {
        $uri_path = sprintf(
            self::ENDPOINTS['media']['info'],
            $mediaId);
        return $this->makeRequest($uri_path);
    }

    public function getMediaComments($mediaId)
    {
        $uri_path = sprintf(
            self::ENDPOINTS['media']['comments'],
            $mediaId);
        return $this->makeRequest($uri_path);
    }

    public function getMediaLikers($mediaId)
    {
        $uri_path = sprintf(
            self::ENDPOINTS['media']['likers'],
            $mediaId);
        return $this->makeRequest($uri_path);
    }

    protected function makeRequest($uriPath)
    {
        $response = $this->guzzleClient->request('GET', $uriPath, $this->requestData);
        if ($response->getStatusCode() !== 200) {
            throw new NoDataException();
        }
        return json_decode($response->getBody()->getContents(), true);
    }

}