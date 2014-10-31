<?php
namespace Bauplan;
use Bauplan\Role\Renderable as Renderable;
use Bauplan\Role\Cloneable as Cloneable;

/*
 * Base operations for all types
 */
abstract class Type implements \Serializable, Renderable, Cloneable {
  /* public enumeration */
  const TEMPLATE = 0;
  const SECTION = 1;
  const VARIABLE = 2;
  const CODE = 3;
  const INSTRUCTION = 4;
  
  private $id;
  
  function __construct($id) {
    $this->id = $id;
  }
}
?>
