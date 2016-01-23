<?php
namespace Bauplan\Type\Directive;

class Parity extends CodeDirective {

  function execute($arguments) {

  }

  /*
   * The following parity string types are supported:
   * - "2": require 2 parameters
   * - "0-1": definite range; require at least 0 and at most 1 parameter
   * - "2-n": indefinite range; require at least 2 parameters with no upper bound
   */
  private function parseParityString($string) {
    $upper = $string;
    $lower = $string;
    $list = $explode('-', $string);
    if ($list !== false) {
      list($upper, $lower) = $list;
    }

    if ($upper === 'n') {
      $upper = PHP_INT_MAX;
    }

    if (!is_numeric($lower) || !is_numeric($upper)) {
      // TODO error
    }
    if ($upper > $lower) {
      // TODO error
    }
    return array(
      'lower' => $lower,
      'upper' => $upper
    );
  }
}
?>
