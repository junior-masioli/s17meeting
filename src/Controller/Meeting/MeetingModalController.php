<?php

/**
 * @file
 * MeetingModalController class.
 */

namespace Drupal\meeting\Controller\Meeting;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;

class MeetingModalController extends ControllerBase
{

  public function modal()
  {
    $options = [
      'dialogClass' => 'popup-dialog-class',
      'width' => '50%',
    ];
    $response = new AjaxResponse();
    $response->addCommand(new OpenModalDialogCommand($this->t('Modal title'), $this->t('The modal text'), $options));

    return $response;
  }
}
