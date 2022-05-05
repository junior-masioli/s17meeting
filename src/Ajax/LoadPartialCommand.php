<?php

namespace Drupal\meeting\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Class ExampleCommand.
 */
class LoadPartialCommand implements CommandInterface
{

  protected $form;
  protected $div;
  protected $url;

  public function __construct($form = null, $div, $url)
  {
    $this->form = $form;
    $this->div = $div;
    $this->url = $url;
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
      'command' => 'load_partial',
      'form' => $this->form,
      'div' => $this->div,
      'url' => $this->url,
    ];
  }
}
