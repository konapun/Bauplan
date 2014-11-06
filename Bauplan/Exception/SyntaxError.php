<?php
namespace Bauplan\Exception;

class SyntaxError extends \LogicException {
  function __construt($message, $cursor=0, $file=null) {
    $err = $message;
    if ($cursor) {
      $err .= " around position $cursor";
    }
    if ($file) {
      $err .= " in file $file";
    }
    parent::__construct($err);
  }
}
?>
