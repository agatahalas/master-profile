<?php

namespace Drupal\cp_authentication;

use Drupal\Core\Url;
use GuzzleHttp\ClientInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
   * Temp store private - kid_session.
   *
   * @var mixed
   */
  protected $kidSession;

  /**
   * {@inheritdoc}
   */
  public function __construct(ClientInterface $http_client) {
    $api_settings = Settings::get('master_profile');
    $this->apiUrl = $api_settings['api_url'];
    $this->clientId = $api_settings['client_id'];
    $this->clientSecret = $api_settings['client_secret'];
    $this->httpClient = $http_client;
    $this->kidSession = \Drupal::service('tempstore.private')->get('kid_session');
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
      $this->kidSession->set('kid_access_token_value', $body->access_token);
      $this->kidSession->set('kid_refresh_token_value', $body->refresh_token);
      $this->kidSession->set('kid_token_expire', time() + $body->expires_in);

      $data = $this->introspectToken($body->access_token);
      $this->kidSession->set('kid_client_id', $data->client_id);
    }
  }

  /**
   * Introspect token.
   *
   * @param string $token
   *   Token.
   *
   * @return object
   *   Returns token instrospect object.
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

    return $body;
  }

  /**
   * Refresh token.
   *
   * @return bool
   *   Returns true if token has been refreshed or false if not.
   */
  public function refreshToken($client_id) {
    $uri = $this->getApiUrl() . '/api/v2/oauth/token/refresh';
    $client_id = $this->kidSession->get('kid_client_id');

    $refresh_token = $this->kidSession->get('kid_refresh_token_value');

    $response = $this->httpClient->post($uri, [
      'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/x-www-form-urlencoded',
        'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
      ],
      'form_params' => [
        'client_id' => $client_id,
        'grant_type' => 'refresh_token',
        'refresh_token' => $refresh_token,
        'scope' => 'USER',
      ],
    ]);

    $body = json_decode($response->getBody()->getContents());

    if ($body->access_token) {
      $this->kidSession->set('kid_access_token_value', $body->access_token);
      $this->kidSession->set('kid_token_expire', time() + $body->expires_in);
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Get user info.
   *
   * @param string $token
   *   Valid token.
   *
   * @return mixed|null
   *   Body object.
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

  /**
   * Check if user is logged in.
   *
   * @return bool
   *   Return true if user is logged in or false.
   */
  public function loggedIn() {
    if ($this->kidSession->get('kid_token_expire') < time()) {
      $client_id = $this->kidSession->get('kid_client_id');
      if (empty($client_id)) {
        return FALSE;
      }
      else {
        $token_refreshed = $this->refreshToken($client_id);
        if ($token_refreshed) {
          return TRUE;
        }
        else {
          return FALSE;
        }
      }
    }
    else {
      return TRUE;
    }
  }

  /**
   * Redirect to user dashboard.
   */
  public function redirectToUserPage() {
    $url = Url::fromRoute('cp_authentication.user_info');
    $response = new RedirectResponse($url->toString());
    $response->send();
  }

  /**
   * Redirect to user login page.
   */
  public function redirectToAuthenticatePage() {
    $url = $this->getAuthorizeLink();
    $response = new RedirectResponse($url);
    $response->send();
  }

}
