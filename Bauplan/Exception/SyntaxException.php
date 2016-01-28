<?php
namespace Bauplan\Exception;

class SyntaxException extends \LogicException {
  //private $line;
  private $offset;
  //private $file;

  function __construct($message, $line, $offset, $file=null) {
    //$this->line = $line;
    $this->offset = $offset;
    //$this->file = $file;

    $err = $message;
    if ($cursor) {
      $err .= " around position $cursor";
    }
    if ($file) {
      $err .= " in file $file";
    }
    parent::__construct($err);
  }

/*
  function getFile() {
    // TODO
    return "TODO";
  }
*/
}
?>
