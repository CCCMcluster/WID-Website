<?php

namespace Drupal\wid_toolkit_project_design\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Toolkit Project Design' block.
 *
 * @Block(
 *   id = "toolkit_project_design_block",
 *   admin_label = @Translation("Toolkit Project Design block"),
 *   category = @Translation("Toolkit Project Design block")
 * )
 */
class ProjectDesignBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'wid_toolkit_project_design-block',
      '#attached' => [
        'library' => ['wid_toolkit_project_design/widtoolkitprojectdesign'],
      ],
      '#cache' => ['max-age' => 0],
    ];
    return $build;
  }

}
