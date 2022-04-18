<?php

namespace Drupal\meeting\Controller\Poll;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Database\Database;


class PollControlController extends ControllerBase
{

  public static function update()
  {
    $id = \Drupal::request()->request->get('id');

    $conn = Database::getConnection();
    $query = $conn->select('poll', 'p')
      ->fields('p')
      ->condition('id', $id);
    $poll = $query->execute()->fetchAssoc();

    $poll_activate_poll = $poll['poll_activate_poll'] == 0 ? 1 : 0;

    $conn->update('poll')->fields(['poll_activate_poll' => $poll_activate_poll])->condition('id', $id)->execute();

    $response = new AjaxResponse();
    return $response;
  }
}
