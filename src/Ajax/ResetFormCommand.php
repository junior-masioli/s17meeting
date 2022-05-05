<?php

namespace Drupal\meeting\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Class ResetFormCommand.
 */
class ResetFormCommand implements CommandInterface
{

  protected $form;

  public function __construct($form = null)
  {
    $this->form = $form;
  }

  /**
   * Render custom ajax command.
   *
   * @return ajax
   *   Command function.
   */
  public function render()
  {
    return [
      'command' => 'resetFormCommand',
      'form' => $this->form
    ];
  }
}
