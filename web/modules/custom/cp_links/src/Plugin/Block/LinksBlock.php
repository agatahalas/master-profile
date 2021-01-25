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
      '#attributes' => [
        'class' => [
          'cp-links-block'
        ],
      ],
      '#items' => [
        [
          '#theme' => 'item_list',
          '#title' => $this->t('Profile'),
          '#items' => [
            [
              '#type' => 'link',
              '#url' => Url::fromRoute('cp_account.dashboard', [], [
                'attributes' => [
                  'id' => 'profile',
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
                  'id' => 'car-wash',
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
                  'id' => 'fuel',
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
                  'id' => 'credit-card',
                ],
              ]),
              '#title' => $this->t('Payment methods'),
            ],
            [
              '#type' => 'link',
              '#url' => Url::fromUri('http://test.me/go/here', [
                'attributes' => [
                  'id' => 'document',
                ],
              ]),
              '#title' => $this->t('Transactions'),
            ],
            [
              '#type' => 'link',
              '#url' => Url::fromUri('http://test.me/go/here', [
                'attributes' => [
                  'id' => 'document',
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
                  'id' => 'document',
                ],
              ]),
              '#title' => $this->t('Partner agreements'),
            ],
            [
              '#type' => 'link',
              '#url' => Url::fromUri('http://test.me/go/here', [
                'attributes' => [
                  'id' => 'about-extra-club',
                ],
              ]),
              '#title' => $this->t('About Extra Club'),
            ],
          ],
        ],
      ],
    ];

    return $build;
  }

}
