<?php

namespace Drupal\meeting\Controller\Meeting;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Database\Database;


class MeetingActivateModuleController extends ControllerBase
{

  public static function activate()
  {
    $meeting_id = \Drupal::request()->request->get('meeting_id');
    $module     = \Drupal::request()->request->get('module');
    $order      = \Drupal::request()->request->get('order');

    $conn = Database::getConnection();
    $query = $conn->select('meeting_module', 'm')
      ->fields('m')
      ->condition('meeting_id', $meeting_id)
      ->condition('module', $module);
    $meeting_module = $query->execute()->fetchAssoc();

    if($meeting_module){
      $conn->delete('meeting_module')->condition('meeting_id', $meeting_id)->condition('module', $module)->execute();
    }else{
      $data = [
        "meeting_id" => $meeting_id,
        "module"     => $module,
        "order"      => $order,
      ];
      $conn->insert('meeting_module')->fields($data)->execute();
    }



    $response = new AjaxResponse();
    return $response;
  }
}
