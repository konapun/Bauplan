<?php
namespace Bauplan; // FIXME move language core to Bauplan\Core namespace

use Bauplan\Core\DS\Hash as Hashmap;

/*
 * A simple key,value store. Bauplan uses template-level scoping so each
 * template has its own symbol table
 */
class SymbolTable {
  private $hash;

  function __construct($symbols=array()) {
    $this->hash = new Hashmap($symbols);
  }

  function get($symbol) {

  }

  function put($symbol) {
    
  }
}
?>
