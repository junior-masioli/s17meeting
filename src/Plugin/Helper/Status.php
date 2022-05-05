<?php

/**
 * @file
 * Contains \Drupal\meeting\Plugin\Block\Helper.
 */

namespace Drupal\meeting\Plugin\Helper;

define('DEFAULT_STATUS', [
  'Published',
  'Approved',
  'Archived',
  'Deleted',
]);

class Status
{
  public static function question_status($status)
  {
    return DEFAULT_STATUS[$status];
  }
}
