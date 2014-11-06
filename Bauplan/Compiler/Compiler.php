<?php
namespace Bauplan\Compiler;
use Bauplan\Compiler\Lexer as Lexer;
use Bauplan\Compiler\Parser as Parser;
//use Bauplan\Compiler\Generator as Generator;
use Bauplan\Exception\IOException as IOException;

/*
 * Chain together lexer -> parser -> codegen
 */
class Compiler {
  private $lexer;
  private $parser;
  private $generator;

  function __construct() {
    $this->lexer = new Lexer();
    $this->parser = new Parser();
    //$this->Generator = new Generator();
  }

  function compile($filepath) {
    $source = file($filepath);
    if ($source === false) {
      throw new IOException("Can't locate source file $path for reading");
    }

    $this->parser->setFilename($filepath);
    return $this->compileSource($source);
  }

  function compileSource($source) {
    $tokenStream = $this->lexer->tokenize($source);
    $cst = $this->parser->parse($tokenStream);
    // TODO
  }
}
?>
