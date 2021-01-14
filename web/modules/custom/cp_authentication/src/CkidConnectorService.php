<?php

namespace Drupal\cp_authentication;

use Drupal\Core\Url;
use GuzzleHttp\ClientInterface;
use Drupal\Core\Site\Settings;

/**
 * Circlek ID connector service.
 */
class CkidConnectorService {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * API base url.
   *
   * @var mixed
   */
  protected $apiUrl;

  /**
   * Client id.
   *
   * @var mixed
   */
  protected $clientId;

  /**
   * Client secret.
   *
   * @var mixed
   */
  protected $clientSecret;

  /**
   * Request service.
   *
   * @var mixed
   */
  protected $request;

  /**
   * {@inheritdoc}
   */
  public function __construct(ClientInterface $http_client) {
    $api_settings = Settings::get('master_profile');
    $this->apiUrl = $api_settings['api_url'];
    $this->clientId = $api_settings['client_id'];
    $this->clientSecret = $api_settings['client_secret'];
    $this->httpClient = $http_client;
  }

  /**
   * Gets CKID API Url.
   */
  public function getApiUrl() {
    return $this->apiUrl;
  }

  /**
   * Get authorize link.
   *
   * @return string
   *   String with request url.
   */
  public function getAuthorizeLink() {
    $uri = $this->getApiUrl() . '/api/v1/oauth/authorize';
    $option = [
      'query' => [
        'client_id' => $this->clientId,
        'response_type' => 'code',
        'scope' => 'USER',
        'redirect_uri' => Url::fromRoute('cp_authentication.get_token', [], ['absolute' => TRUE, 'https' => TRUE])->toString(),
      ],
    ];
    $request_url = Url::fromUri($uri, $option)->toString();

    return $request_url;
  }

  /**
   * Get token.
   *
   * @param string $code
   *   Code to exchange for token.
   *
   * @return mixed
   *   Return mixed.
   */
  public function getToken($code) {
    $uri = $this->getApiUrl() . '/api/v1/oauth/token';

    $response = $this->httpClient->post($uri, [
      'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/x-www-form-urlencoded',
        'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
      ],
      'form_params' => [
        'code' => $code,
        'grant_type' => 'authorization_code',
        'scope' => 'USER',
        'redirect_uri' => Url::fromRoute('cp_authentication.get_token', [], ['absolute' => TRUE, 'https' => TRUE])->toString(),
      ],
    ]);

    $body = json_decode($response->getBody()->getContents());

    if (!empty($body->access_token)) {
      user_cookie_save([
        'kid_token' => $body->access_token,
      ]);
    }

    if ($response->getStatusCode() == 200) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Introspect token.
   *
   * @param string $token
   *  Token.
   * @return bool
   *  Return true if token is active.
   */
  public function introspectToken($token) {
    $uri = $this->getApiUrl() . '/api/v1/oauth/token/introspect';

    $response = $this->httpClient->post($uri, [
      'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/x-www-form-urlencoded',
        'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
      ],
      'form_params' => [
        'token' => $token,
      ],
    ]);

    $body = json_decode($response->getBody()->getContents());

    if ($body->active) {
      return TRUE;
    }
    else {
      // refresh or login
      return FALSE;
    }

  }

  /**
   * Get user info.
   *
   * @param $token
   * @return mixed|null
   */
  public function getUserInfo($token) {
    $uri = $this->getApiUrl() . '/api/v2/oauth/userinfo';
    $response = $this->httpClient->get($uri, [
      'headers' => [
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $token,
      ],
    ]);

    $body = json_decode($response->getBody()->getContents());

    return $body;
  }

}
