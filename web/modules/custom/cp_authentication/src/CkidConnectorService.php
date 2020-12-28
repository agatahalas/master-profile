<?php


namespace Drupal\cp_authentication;

use Drupal\Core\Config\ConfigFactoryInterface;

class CkidConnectorService {
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
   * HttpClient service.
   *
   * @var mixed
   */
  protected $httpClient;

  /**
   * Config.
   *
   * @var mixed
   */
  protected $config;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->get('cp_authentication.settings');

    $this->apiUrl = $this->config->get('apiUrl');
    $this->clientId = $this->config->get('clientId');
    $this->clientSecret = $this->config->get('clientSecret');

    $this->httpClient = \Drupal::httpClient();
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
    return $this->getApiUrl() . '/api/v1/oauth/authorize?client_id=' . $this->clientId . '&response_type=code&scope=USER&redirect_uri=https://master-profile.lndo.site/customer/get-token';
  }

}
