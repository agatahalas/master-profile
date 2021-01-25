<?php

namespace Drupal\cp_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Locale\CountryManager;
use Drupal\Core\Url;
use Drupal\cp_authentication\CkidConnectorService;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use Drupal\cp_ckid_basic_data\CkidBasicData;

/**
 * An example controller.
 */
class Dashboard extends ControllerBase {

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
   * User basic data service.
   *
   * @var \Drupal\cp_user_basic_data\CkidBasicData
   */
   protected $ckidBasicData;

  /**
   * Construct.
   */
  public function __construct(CkidConnectorService $ckid_connector_service, RequestStack $request_stack, ClientInterface $http_client, CkidBasicData $ckid_basic_data) {
    $this->ckidConnectorService = $ckid_connector_service;
    $this->requestStack = $request_stack;
    $this->httpClient = $http_client;
    $this->ckidBasicData = $ckid_basic_data;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('cp_authentication.ckid_connector'),
      $container->get('request_stack'),
      $container->get('http_client'),
      $container->get('cp_ckid_basic_data.ckid_basic_data')
    );
  }

  /**
   * Returns a user info.
   */
  public function content() {
    $build = [
      '#markup' => $this->t('Hello from user info!'),
    ];

    // Check if user is logged in.
    if ($this->ckidConnectorService->loggedIn()) {
      try {
        $kid_session = \Drupal::service('tempstore.private')->get('kid_session');
        $user_info = $this->ckidBasicData->getData($kid_session->get('kid_access_token_value'), TRUE);

        $build = [
          '#theme' => 'cp_user_info_list',
          '#user_name' => $user_info['Name'],
          '#user_ckid' => $user_info['Circle K ID'],
        ];
        foreach ($user_info as $title => $value) {
          $items[] = [
            'title' => $this->t($title),
            'value' => $value,
            'link' => Link::fromTextAndUrl($this->t('Edit'), Url::fromRoute('<none>')),
          ];
        }

        $build['#items'][] = [
          'title' => $this->t('My details'),
          'icon' => 'profile',
          'rows' => $items,
          '#attributes' => [
            'class' => [
              'user-info-table',
              'my-details',
            ],
          ],
        ];

        $build['#items'][] = [
          'title' => $this->t('My cars'),
          'icon' => 'car',
          'rows' => $items,
          '#attributes' => [
            'class' => [
              'user-info-table',
              'my-details',
            ],
          ],
        ];

        $build['#items'][] = [
          'title' => $this->t('Communication'),
          'icon' => 'email',
          'rows' => $items,
          '#attributes' => [
            'class' => [
              'user-info-table',
              'my-details',
            ],
          ],
        ];

        $build['#items'][] = [
          'title' => $this->t('Password & Security'),
          'icon' => '',
          'rows' => $items,
          '#attributes' => [
            'class' => [
              'user-info-table',
              'my-details',
            ],
          ],
        ];

        $build['#items'][] = [
          'title' => $this->t('Terms & Conditions'),
          'icon' => 'tac',
          'rows' => $items,
          '#attributes' => [
            'class' => [
              'user-info-table',
              'my-details',
            ],
          ],
        ];
      }
      catch (RequestException $e) {
        $build['#markup'] = $this->t('Exception: @message', ['@message' => $e->getMessage()]);
      }
    }
    // User is not logged in, redirect to login page.
    else {
      $this->ckidConnectorService->redirectToAuthenticatePage();
    }
    return $build;
  }

}
