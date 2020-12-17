<?php

namespace Drupal\cp_links\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Url;

/**
 * Provides social icon block with configurations.
 *
 * @Block(
 *   id = "cp_links",
 *   admin_label = @Translation("Links"),
 * )
 */
class LinksBlock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'item_list',
      '#items' => [
        [
          '#theme' => 'item_list',
          '#title' => $this->t('Profile'),
          '#items' => [
            [
              '#type' => 'link',
              '#url' => Url::fromUri('http://test.me/go/here', [
                'attributes' => [
                  'id' => 'account-overview',
                ],
              ]),
              '#title' => $this->t('Account overview'),
            ],
          ],
        ],
        [
          '#theme' => 'item_list',
          '#title' => $this->t('Services'),
          '#items' => [
            [
              '#type' => 'link',
              '#url' => Url::fromUri('http://test.me/go/here', [
                'attributes' => [
                  'id' => 'carwash-subscription',
                ],
              ]),
              '#title' => $this->t('Carwash subscription'),
            ],
            [
              '#type' => 'link',
              '#url' => Url::fromUri('http://test.me/go/here', [
                'attributes' => [
                  'id' => 'click-and-collect',
                ],
              ]),
              '#title' => $this->t('Click and collect'),
            ],
            [
              '#type' => 'link',
              '#url' => Url::fromUri('http://test.me/go/here', [
                'attributes' => [
                  'id' => 'fuel',
                ],
              ]),
              '#title' => $this->t('Fuel'),
            ],
            [
              '#type' => 'link',
              '#url' => Url::fromUri('http://test.me/go/here', [
                'attributes' => [
                  'id' => 'electric-vehicle',
                ],
              ]),
              '#title' => $this->t('Electric vehicle'),
            ],
          ],
        ],
        [
          '#theme' => 'item_list',
          '#title' => $this->t('Payment'),
          '#items' => [
            [
              '#type' => 'link',
              '#url' => Url::fromUri('http://test.me/go/here', [
                'attributes' => [
                  'id' => 'payment-methods',
                ],
              ]),
              '#title' => $this->t('Payment methods'),
            ],
            [
              '#type' => 'link',
              '#url' => Url::fromUri('http://test.me/go/here', [
                'attributes' => [
                  'id' => 'transactions',
                ],
              ]),
              '#title' => $this->t('Transactions'),
            ],
            [
              '#type' => 'link',
              '#url' => Url::fromUri('http://test.me/go/here', [
                'attributes' => [
                  'id' => 'invoice-data',
                ],
              ]),
              '#title' => $this->t('Invoice data'),
            ],
          ],
        ],
        [
          '#theme' => 'item_list',
          '#title' => $this->t('Extra club'),
          '#items' => [
            [
              '#type' => 'link',
              '#url' => Url::fromUri('http://test.me/go/here', [
                'attributes' => [
                  'id' => 'partner-agreements',
                ],
              ]),
              '#title' => $this->t('Partner agreements'),
            ],
            [
              '#type' => 'link',
              '#url' => Url::fromUri('http://test.me/go/here', [
                'attributes' => [
                  'id' => 'about-extraclub',
                ],
              ]),
              '#title' => //$this->t('About ExtraClub'), 
              [
                '#markup' => '<span>dupa</span>'
              ]
            ],
          ],
        ],
      ],
    ];

    //$build['#items'][3]['#items'][0]['#url']->setOption('attributes', ['pipa' => TRUE]);

    return $build;
  }

}
