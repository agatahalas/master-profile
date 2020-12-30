<?php

namespace Drupal\cp_authentication\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Locale\CountryManager;
use Drupal\Core\Url;
use Drupal\cp_authentication\CkidConnectorService;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;

/**
 * An example controller.
 */
class UserInfo extends ControllerBase {

  /**
   * The CKID Connector service.
   *
   * @var \Drupal\cp_authentication\CkidConnectorService
   */
  protected $ckidConnectorService;

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
  public function __construct(CkidConnectorService $ckid_connector_service, RequestStack $request_stack, ClientInterface $http_client) {
    $this->ckidConnectorService = $ckid_connector_service;
    $this->requestStack = $request_stack;
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('cp_authentication.ckid_connector'),
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
    $token = $this->requestStack->getCurrentRequest()->cookies->get('Drupal_visitor_kid_token');

    if ($token) {
      try {
        $body = $this->ckidConnectorService->getUserInfo($token);
        $user_info = $this->prepareUserInfo($body);

        $build = [
          '#theme' => 'cp_user_info_list',
        ];
        foreach ($user_info as $title => $value) {
          $items[] = [
            'title' => $this->t($title),
            'value' => $value,
            'link' =>  Link::fromTextAndUrl($this->t('Edit'), Url::fromRoute('<none>')),
          ];
        }

        $build['#items'][] = [
          'title' => $this->t('My details'),
          'rows' => $items,
          'attributes' => [
            'class' => [
              'user-info-table',
              'my-details'
            ],
          ],
        ];

        $build['#items'][] = [
          'title' => $this->t('My cars'),
          'rows' => $items,
          'attributes' => [
            'class' => [
              'user-info-table',
              'my-details'
            ],
          ],
        ];

        $build['#items'][] = [
          'title' => $this->t('Communication'),
          'rows' => $items,
          'attributes' => [
            'class' => [
              'user-info-table',
              'my-details'
            ],
          ],
        ];
      }
      catch (RequestException $e) {
        $build['#markup'] = $this->t('Exception: @message', ['@message' => $e->getMessage()]);
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
