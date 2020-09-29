<?php

namespace Drupal\wid_latest_news_features\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Latest News & Features' block.
 *
 * @Block(
 *   id = "wid_latest_news_features",
 *   admin_label = @Translation("Latest News & Features"),
 *   category = @Translation("Latest News & Features")
 * )
 */
class LatestNewsFeaturesBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'wid_latest_news_features-block',
      '#attached' => [
        'library' => ['wid_latest_news_features/widlatestnewsfeatures'],
      ],
      '#cache' => ['max-age' => 0]
    ];
    return $build;
  }
}
