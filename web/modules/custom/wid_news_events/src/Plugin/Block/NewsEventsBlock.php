<?php

namespace Drupal\wid_news_events\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'events & news' block.
 *
 * @Block(
 *   id = "news_events_block",
 *   admin_label = @Translation("Events & News block"),
 *   category = @Translation("Events & News block")
 * )
 */
class NewsEventsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'wid_news_events-block',
      '#attached' => [
        'library' => ['wid_news_events/widnewsevents'],
      ],
      '#cache' => ['max-age' => 0],
    ];
    return $build;
  }

}
