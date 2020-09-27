<?php

namespace Drupal\wid_toolkit_inception_phase\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'toolkit inception phase' block.
 *
 * @Block(
 *   id = "toolkit_inception_phase_block",
 *   admin_label = @Translation("Toolkit inception phase block"),
 *   category = @Translation("Toolkit inception phase block")
 * )
 */
class InceptionPhaseBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'wid_toolkit_inception_phase-block',
      '#attached' => [
        'library' => ['wid_toolkit_inception_phase/widtoolkitinceptionphase'],
      ],
      '#cache' => ['max-age' => 0],
    ];
    return $build;
  }

}
