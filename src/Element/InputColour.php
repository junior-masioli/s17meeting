<?php

namespace Drupal\meeting\Element;

use Drupal\Core\Render\Element;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Color as ColorUtility;

/**
 * Provides a form element for choosing a color.
 *
 * Properties:
 * - #default_value: Default value, in a format like #ffffff.
 *
 * Example usage:
 * @code
 * $form['inputcolour'] = array(
 *   '#type' => 'inputcolour',
 *   '#title' => $this->t('Color'),
 *   '#default_value' => '#ffffff',
 * );
 * @endcode
 *
 * @FormElement("inputcolour")
 */
class InputColour extends Element\FormElement
{

  public function getInfo()
  {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#process' => [
        [$class, 'processAjaxForm'],
      ],
      '#element_validate' => [
        [$class, 'validateInputColour'],
      ],
      '#pre_render' => [
        [$class, 'preRenderInputColour'],
      ],
      '#theme' => 'inputcolour_form',
      '#theme_wrappers' => ['form_element'],
    ];
  }



  /**
   * Form element validation handler for #type 'color'.
   */
  public static function validateInputColour(&$element, FormStateInterface $form_state, &$complete_form)
  {
    $value = trim($element['#value']);

    // Default to black if no value is given.
    // @see http://www.w3.org/TR/html5/number-state.html#color-state
    if ($value === '') {
      $form_state->setValueForElement($element, '#000000');
    } else {
      // Try to parse the value and normalize it.
      try {
        $form_state->setValueForElement($element, ColorUtility::rgbToHex(ColorUtility::hexToRgb($value)));
      } catch (\InvalidArgumentException $e) {
        $form_state->setError($element, t('%name must be a valid color.', ['%name' => empty($element['#title']) ? $element['#parents'][0] : $element['#title']]));
      }
    }
  }

  /**
   * Prepares a #type 'color' render element for input.html.twig.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   *   Properties used: #title, #value, #description, #attributes.
   *
   * @return array
   *   The $element with prepared variables ready for input.html.twig.
   */
  public static function preRenderInputColour($element)
  {
    $element['#attributes']['type'] = 'color';
    Element::setAttributes($element, ['id', 'name', 'value']);
    static::setAttributes($element, ['form-colors']);

    return $element;
  }




}
