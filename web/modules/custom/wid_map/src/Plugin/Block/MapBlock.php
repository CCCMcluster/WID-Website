<?php

namespace Drupal\wid_map\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\wid_map\Controller\MapController;

/**
 * Provides a 'map' block.
 *
 * @Block(
 *   id = "map_block",
 *   admin_label = @Translation("Map block"),
 *   category = @Translation("Map block")
 * )
 */
class MapBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $items = [];
    $mapData = MapController::getData();
    return [
      '#items' => $items,
      '#theme' => 'wid_map-map',
      '#attached' => [
        'drupalSettings' => [
          'mapData' => $mapData,
        ],
        'library' => ['wid_map/d3js', 'wid_map/widmap'],
      ],
    ];
  }

}
