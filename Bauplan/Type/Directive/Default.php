<?php
namespace Bauplan\Type\Directive;
use Bauplan\Type\Diretive as Directive;
use Bauplan\Type as Type;

class Value implements Directive {
  
  function registersAs() {
    return 'value';
  }
  
  function worksWith($type) {
    return $type == Type::VARIABLE;
  }
  
  function register($variable) {
    
  }
  
  function execute($args) {
  
  }
}
?>
