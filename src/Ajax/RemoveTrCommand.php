<?php

namespace Drupal\meeting\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Class ExampleCommand.
 */
class RemoveTrCommand implements CommandInterface
{

  protected $id;

  public function __construct($id)
  {
    $this->id = $id;
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
      'command' => 'RemoveTr',
      'id' => $this->id,
    ];
  }
}
