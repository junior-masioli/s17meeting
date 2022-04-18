<?php

namespace Drupal\meeting\Controller\Meeting;

use Drupal\Core\Controller\ControllerBase;


/**
 * Undocumented class
 */
class MeetingCreateController extends ControllerBase
{

  /**
   * @return array
   */
  public function index()
  {

    // render forms
    $form['container'] = [
      '#theme' => 'meeting_create_template',
      '#type'   => 'markup',
      '#meeting'   => $this->formBuilder()->getForm('Drupal\meeting\Form\Meeting\MeetingCreateForm', null),
    ];

    return $form;
  }
}
