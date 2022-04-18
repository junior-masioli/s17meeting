<?php

/**
 * @file
 * Contains \Drupal\meeting\Plugin\Block\ModalBlock.
 */

namespace Drupal\meeting\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Component\Serialization\Json;

/**
 * Provides a 'Modal' Block
 *
 * @Block(
 *   id = "modal_block",
 *   admin_label = @Translation("Modal block"),
 * )
 */
class ModalBlock extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $link_url = Url::fromRoute('meeting.modal');
    $link_url->setOptions([
      'attributes' => [
        'class' => ['use-ajax', 'button', 'button--small'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode(['width' => 400]),
      ]
    ]);

    return array(
      '#type' => 'markup',
      '#markup' => Link::fromTextAndUrl($this->t('Open modal'), $link_url)->toString(),
      '#attached' => ['library' => ['core/drupal.dialog.ajax']]
    );
  }
}
