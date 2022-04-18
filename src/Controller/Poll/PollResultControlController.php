<?php

namespace Drupal\meeting\Controller\Poll;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Database\Database;


class PollResultControlController extends ControllerBase
{

  public static function update()
  {
    $id = \Drupal::request()->request->get('id');

    $conn = Database::getConnection();
    $query = $conn->select('poll', 'p')
      ->fields('p')
      ->condition('id', $id);
    $poll = $query->execute()->fetchAssoc();

    $poll_show_result = $poll['poll_show_results'] == 0 ? 1 : 0;

    $conn->update('poll')->fields(['poll_show_results' => $poll_show_result])->condition('id', $id)->execute();

    $response = new AjaxResponse();
    return $response;
  }
}
