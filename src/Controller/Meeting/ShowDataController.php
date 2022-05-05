<?php

namespace Drupal\meeting\Controller\Meeting;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;

/**
 * Class MeetingController
 * @package Drupal\meeting\Controller
 */
class ShowDataController extends ControllerBase
{

  /**
   * @return array
   */
  public function show($id)
  {

    $conn = Database::getConnection();

    $query = $conn->select('meeting', 'm')
      ->condition('id', $id)
      ->fields('m');
    $data = $query->execute()->fetchAssoc();
    $meeting_name = $data['meeting_name'];
    $meeting_description = $data['meeting_description'];
    $meeting_url_video = $data['meeting_url_video'];
    $meeting_status = $data['meeting_status'];

    if($data['fid']){
      $file = File::load($data['fid']);
      $picture = "<img src=$file->createFileUrl()' width='100' height='100' />";
    }else{
      $picture = '';
    }


    return [
      '#type' => 'markup',
      '#markup' => "<h1>$meeting_name</h1>
                    <p>$picture</p>
                    <p>$meeting_description</p>
                    <p>$meeting_url_video</p>
                    <p>$meeting_status</p>"
    ];
  }

}
