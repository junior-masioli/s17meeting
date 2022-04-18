<?php

/**
 * @file
 * MeetingModalController class.
 */

namespace Drupal\meeting\Controller\Meeting;

use Drupal\Core\Controller\ControllerBase;

class MeetingAlertController extends ControllerBase
{

  public static function alert($type="alert-success", $text)
  {
    $btnClose = "<span class='closebtn'>&times;</span>";
    $alert = '<div class="alert ' . $type . '">'.$text. ' ' . $btnClose . '</div>';

    return $alert;
  }
}
