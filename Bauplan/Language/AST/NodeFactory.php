<?php
namespace Bauplan\Language\AST;

/*
 * The node factory is responsible for building nodes of the requested type
 * which are nodes placed into the AST
 */
class NodeFactory {
  static function build($type) {
    $class = $type; // TODO
    if (!class_exists($class)) {
      throw new Exception("Can't find class for type \"$type\"");
    }
    return new $class;
  }
}
?>
