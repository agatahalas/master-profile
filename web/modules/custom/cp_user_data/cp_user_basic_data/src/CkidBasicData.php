<?php

namespace Drupal\cp_user_basic_data;

use GuzzleHttp\ClientInterface;
use Drupal\cp_authentication\CkidConnectorService;
use Drupal\Core\Locale\CountryManager;

/**
 * Circlek ID connector service.
 */
class CkidBasicData {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * CircleK ID connector.
   *
   * @var \Drupal\cp_authentication\CkidConnectorService
   */
   protected $ckidConnector;

  /**
   * {@inheritdoc}
   */
  public function __construct(CkidConnectorService $ckid_connector, ClientInterface $http_client) {
    $this->ckidConnector = $ckid_connector;
    $this->httpClient = $http_client;
  }

  public function getData($token) {
    $uri = $this->ckidConnector->getApiUrl() . '/api/v2/oauth/userinfo';
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
   * Prepare user data to display.
   *
   * @param object $data
   *   User data.
   *
   * @return array
   *   Processed user data.
   */
   public function prepareUserInfo($data) {
    if (isset($data->country_code)) {
      $phone = isset($data->phone_number) ? '+' . $data->country_code . ' ' . $data->phone_number : '';
    }
    else {
      $phone = isset($data->phone_number) ? $data->phone_number : '';
    }

    $countries = CountryManager::getStandardList();
    if (!empty($countries) && isset($data->ckidTcCountryCode)) {
      $country = $countries[strtoupper($data->ckidTcCountryCode)];
    }

    return [
      'Name' => isset($data->name) ? $data->name : '',
      'Circle K ID' => isset($data->email) ? $data->email : '',
      'Connected accounts' => isset($data->email) ? $data->email : '',
      'Phone' => $phone,
      'Birthday' => isset($data->birthdate) ? $data->birthdate : '',
      'Address' => '',
      'Zip code' => isset($data->zip_code) ? $data->zip_code : '',
      'Country' => isset($country) ? $country : '',
      'Gender' => isset($data->gender) ? $data->gender : '',
    ];
  }

}
