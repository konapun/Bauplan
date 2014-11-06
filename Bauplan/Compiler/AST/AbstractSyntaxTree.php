<?php
namespace Bauplan\Compiler\AST;

class AbstractSyntaxTree {
  private $cst;

  function __construct($concreteSyntaxTree) {
    $this->cst = $concreteSyntaxTree;
  }

  private function convertToAST($cst) {
    $cst->visit(function($node) {
      // TODO
    });
  }
}
?>
