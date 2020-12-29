<?php


namespace Drupal\cp_authentication;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Url;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\RequestStack;
use GuzzleHttp\ClientInterface;

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

  // /**
  //  * HttpClient service.
  //  *
  //  * @var mixed
  //  */
  // protected $httpClient;

  /**
   * Config.
   *
   * @var mixed
   */
  protected $config;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, ClientInterface $http_client) {
    $this->config = $config_factory->get('cp_authentication.settings');

    $this->apiUrl = $this->config->get('apiUrl');
    $this->clientId = $this->config->get('clientId');
    $this->clientSecret = $this->config->get('clientSecret');

    //$this->httpClient = \Drupal::httpClient();
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
   */
  public function getAuthorizeLink() {
    //dd($this->requestStack->getCurrentRequest());
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
   * @param $code
   * @return mixed
   */
  public function getToken($code) {
    $uri = $this->getApiUrl() . '/api/v1/oauth/token';
    //dd($this->requestStack);
    // $current_request = $this->httpClient->getCurrentRequest();
    // dd($current_request);
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

}
