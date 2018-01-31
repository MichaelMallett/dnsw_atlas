<?php

namespace Drupal\dnsw_atlas;

use GuzzleHttp\Client;
use Masterminds\HTML5\Exception;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DnswAtlasApi.
 */
class DnswAtlasApi {

  private $client;

  private $apiKey;

  private $baseUrl;

  /**
   * DnswAtlasApi constructor.
   *
   * @param \GuzzleHttp\Client $client
   *   Guzzle client.
   * @param string $baseUrl
   *   Atlas API base url.
   */
  public function __construct(Client $client, $baseUrl) {
    $this->client = $client;
    $this->baseUrl = $baseUrl;
  }

  /**
   * Set api key.
   */
  public function setApiKey($apiKey) {
    $this->apiKey = $apiKey;
  }

  /**
   * Check for api key.
   */
  private function checkApiKey() {
    if (!$this->apiKey) {
      throw new \Exception('Api Key not supplied.');
    }
  }

  /**
   * Make the request to Atlas.
   *
   * @param string $method
   *   Request method to use.
   * @param array $query
   *   The query parameters.
   * @param array $headers
   *   Additional headers.
   *
   * @return array|bool
   *   Array containing parsed response or FALSE.
   */
  public function request($method, array $query = [], array $headers = []) {

    try {
      $this->checkApiKey();
    }
    catch (Exception $e) {
      watchdog_exception('atlas_api_error', $e);
    }

    $options['query'] = array_merge([
      'out' => 'json',
      'key' => $this->apiKey,
    ], $query);

    try {
      $response = $this->client->request($method, $this->baseUrl, $options);
      $code = $response->getStatusCode();
      if ($code == 200) {
        $parsed_response = $this->parseResponse($response);
        return $parsed_response;
      }
    }
    catch (RequestException $e) {
      watchdog_exception('atlas_api_error', $e);
    }
    return FALSE;
  }

  /**
   * Parse the response from Guzzle.
   *
   * @param \Psr\Http\Message\ResponseInterface $response
   *   Response object.
   *
   * @return array
   *   Parsed array of response values.
   */
  private function parseResponse(ResponseInterface $response) {
    $contents = (string) $response->getBody();
    // Convert the evil characters out.
    $result = iconv($in_charset = 'UTF-16LE', $out_charset = 'UTF-8', $contents);
    $json = \GuzzleHttp\json_decode($result, TRUE);
    return [
      'code' => $response->getStatusCode(),
      'headers' => $response->getHeaders(),
      'body' => $json,
    ];
  }

}