<?php

namespace Drupal\cp_authentication\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Locale\CountryManager;
use Drupal\Core\Url;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;

/**
 * An example controller.
 */
class UserInfo extends ControllerBase {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * Construct.
   */
  public function __construct(RequestStack $request_stack, ClientInterface $http_client) {
    $this->requestStack = $request_stack;
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('http_client')
    );
  }

  /**
   * Returns a user info.
   */
  public function content() {
    $build = [
      '#markup' => $this->t('Hello from user info!'),
    ];

    // Cookie value.
    $value = $this->requestStack->getCurrentRequest()->cookies->get('Drupal_visitor_kid_token');

    if ($value) {
      try {
        $uri = 'https://test-circlekid-core-stable.test.gneis.io/api/v2/oauth/userinfo';
        $response = $this->httpClient->get($uri, [
          'headers' => [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $value,
          ],
        ]);

        $body = json_decode($response->getBody()->getContents());
        $user_info = $this->prepareUserInfo($body);

        $build = [
          '#theme' => 'table',
          '#caption' => $this->t('My details'),
        ];
        foreach ($user_info as $title => $value) {
          $build['#rows'][] = [
            'data' => [
              $this->t($title),
              $value,
              Link::fromTextAndUrl($this->t('Edit'), Url::fromRoute('<none>')),
            ]
          ];
        }
      }
      catch (RequestException $e) {
        $build['#markup'] = $this->t('Exception: ' . $e->getMessage());
      }
    }
    return $build;
  }

  /**
   * Prepare user data to display.
   *
   * @param $data
   * @return array
   */
  private function prepareUserInfo($data) {
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
