<?php
namespace Bauplan\Type\Directive;
use Bauplan\Type\Diretive as Directive;
use Bauplan\ComplexType as Type;

/*
 * A directive that allows loading a javascript file into the HTML driver
 */
class IncludeJS implements Directive {

  function registersAs() {
    return 'include-js';
  }

  function worksWith($type) {
    return $type == Type::TEMPLATE;
  }

  function register($template) {
    $template->getRoot()->get('scripts')->append('<script src="FIXME"></script>');
  }

  function execute($args) {

  }
}
?>
