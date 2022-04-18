<?php

/**
 * @file
 * Contains \Drupal\meeting\Plugin\Block\Helper.
 */

namespace Drupal\meeting\Plugin\Helper;
use Drupal\Core\Render\Markup;

class Component
{
  public static function checkbox($id, $class, $wrapper, $checked, $data, $url)
  {
  $component = '<input type="checkbox" id="'.$id.'" class="'.$class. ' checkbox" ' .$checked.' data-id="'.$data.'" data-url="'.$url.'"/>
                <label for="'.$id.'" class="toggle">
                  <p>
                    <span>OFF</span>
                    <span>ON</span>
                  </p>
                </label>';
   return Markup::create('<div class="'.$wrapper.'">' . $component . '</div>');

  }
}
