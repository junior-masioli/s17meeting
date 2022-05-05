<?php

/**
 * @file
 * Contains \Drupal\meeting\Plugin\Block\IconBlock.
 */

namespace Drupal\meeting\Plugin\Block;

use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'Icon' Block
 *
 * @Block(
 *   id = "icon_block",
 *   admin_label = @Translation("Icon block"),
 * )
 */
class IconBlock extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    return array(
      '#theme' => 'meeting_edit_template',
      '#type'   => 'markup',
      '#icon' => $this->t('Hello, World!'),
    );
  }
}
