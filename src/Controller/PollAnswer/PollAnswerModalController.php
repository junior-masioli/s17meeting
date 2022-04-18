<?php

namespace Drupal\meeting\Controller\PollAnswer;

use Drupal\Core\Controller\ControllerBase;
use Drupal\meeting\Controller\PollAnswer\PollAnswerController;

/**
 * Undocumented class
 */
class PollAnswerModalController extends ControllerBase
{

  /**
   * @return array
   */
  public function index($id)
  {

    // render forms
    $form['container'] = [
      '#theme'     => 'answer_template',
      '#type'      => 'markup',
      '#form'      => $this->formBuilder()->getForm('Drupal\meeting\Form\PollAnswer\MeetingPollAnswerForm', $id),
      '#answers'   => PollAnswerController::get($id, false)
    ];
    return $form;
  }
}
