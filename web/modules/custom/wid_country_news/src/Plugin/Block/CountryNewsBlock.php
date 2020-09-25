<?php

namespace Drupal\wid_country_news\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\wid_country_news\Controller\CountryNewsController;

/**
 * Provides a 'country news' block.
 *
 * @Block(
 *   id = "country_news_block",
 *   admin_label = @Translation("Country news block"),
 *   category = @Translation("Country news block")
 * )
 */
class CountryNewsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data = CountryNewsController::getCountryNews();
    $build = [
      '#theme' => 'wid_country_news-block',
      '#attached' => [
        'drupalSettings' => [
          'news' => $data,
        ],
        'library' => ['wid_country_news/widcountrynews'],
      ],
      '#cache' => ['max-age' => 0],
    ];
    return $build;
  }

}
