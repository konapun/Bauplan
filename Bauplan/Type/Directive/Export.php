<?php
namespace Bauplan\Type\Directive;
use Bauplan\Type\Diretive as Directive;
use Bauplan\ComplexType as Type;

/*
 * Exports a Code-type's ID as an instruction that acts on arguments
 */
class Export implements Directive {

  function registersAs() {
    return 'export';
  }

  function worksWith($type) {
    return $type == Type::CODE;
  }

  function register($code) {

  }

  function execute($args) {

  }
}
?>
