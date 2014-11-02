<?php
namespace Bauplan\Compiler;
use Bauplan\Compiler\Lexer as Lexer;
use Bauplan\Compiler\SyntaxTree as SyntaxTree;
use Bauplan\Exception\IOException as IOException;
use Bauplan\Exception\ParseException as ParseException;

/*
 * A recursive descent parser which outputs an abstract syntax tree
 */
class Parser {
  private $currentToken;
  private $tokens;
  private $lexer;
  private $file;
  private $syntaxTree;

  function __construct($lexer=null) {
    if ($lexer == null) {
      $lexer = new Lexer();
    }

    $this->syntaxTree = new SyntaxTree(new Token("Bauplan", "ROOT"));
    $this->lexer = $lexer;
    $this->file = "[plain source]";
  }

  /*** PUBLIC API ***/
  function parseFile($path) {
    $source = file($path);
    if ($source === false) {
      throw new IOException("Can't locate source file $path for reading");
    }

    $this->file = $path;
    return $this->parse($source);
  }

  function parse($source) {
    $tokens = $this->lexer->tokenize($source);
    return $this->parseTokens($tokens);
  }

  function parseTokens($tokens) {
    $this->tokens = $tokens;
    $this->currentToken = array_shift($this->tokens);
    $this->bauplan();

    return $this->syntaxTree;
  }

  /*** PRODUCTION RULES, BASED OFF doc/grammar.bnf ***/
  private function bauplan() {
    $this->preprocDeclarations();
    $this->template();
  }

  private function preprocDeclarations() {
    if ($this->preprocDeclaration() !== false) $this->preprocDeclarations();
  }

  private function preprocDeclaration() {
    if ($this->accept('T_PREPROC_DECL')) {
      if ($this->accept('T_IDENTIFIER')) {
        $this->preprocVal();
      }
      else {
        $this->throwError('T_IDENTIFIER');
      }
    }
    else {
      // empty
      return false; // Fix for infinite recursion on production rule
    }
  }

  private function preprocVal() {
    try {
      $this->primitiveType();
    }
    catch (ParseException $e) {
      // empty
    }
  }

  private function type() {
    try {
      $this->template();
    }
    catch (ParseException $e) {
      try {
        $this->section();
      }
      catch (ParseException $e) {
        try {
          $this->variable();
        }
        catch (ParseException $e) {
          try {
            $this->code();
          }
          catch (ParseException $e) {
            try {
              $this->instruction();
            }
            catch (ParseException $e) {
              $this->throwError('template, section, variable, code, or instruction');
            }
          }
        }
      }
    }
  }

  private function complexType() {
    try {
      $this->type();
    }
    catch (ParseException $e) {
      try {
        $this->primitiveType();
      }
      catch (ParseException $e) {
        // empty
      }
    }
  }

  private function primitiveType() {
    if (!$this->accept('T_BOOLEAN')) {
      try {
        $this->numericType();
      }
      catch (ParseException $e) {
        try {
          $this->string();
        }
        catch (ParseException $e) {
          $this->throwError('primitive type');
        }
      }
    }
  }

  private function numericType() {
    if (!$this->accept('T_INTEGER')) {
      if (!$this->accept('T_DOUBLE')) {
        $this->throwError('numeric type');
      }
    }
  }

  private function template() {
    if ($this->accept('T_TEMPLATE')) {
      $this->typedefWithBody();
    }
    else {
      $this->throwError('T_TEMPLATE');
    }
  }

  private function section() {
    if ($this->accept('T_SECTION')) {
      $this->typedefWithBody();
    }
    else {
      $this->throwError('T_SECTION');
    }
  }

  private function variable() {
    if ($this->accept('T_VARIABLE')) {
      $this->typedefNoBody();
    }
    else {
      $this->throwError('T_VARIABLE');
    }
  }

  private function code() {
    if ($this->accept('T_CODE')) {
      $this->typedefNoBody();
    }
    else {
      $this->throwError('T_CODE');
    }
  }

  private function instruction() {
    if ($this->accept('T_INSTRUCTION')) {
       $this->typedefWithBody();
    }
    else {
      $this->throwError('T_INSTRUCTION');
    }
  }

  private function typedefWithBody() {
    if ($this->accept('T_TYPE_OPEN')) {
      if ($this->accept('T_IDENTIFIER')) {
        $this->directiveBlock();
        $this->complexType();
        if (!$this->accept('T_TYPE_CLOSE')) {
          $this->throwError('T_TYPE_CLOSE');
        }
      }
      else {
        $this->throwError('T_IDENTIFIER');
      }
    }
    else {
      $this->throwError('T_TYPE_OPEN');
    }
  }

  private function typedefNoBody() {
    if ($this->accept('T_TYPE_OPEN')) {
      if ($this->accept('T_IDENTIFIER')) {
        $this->directiveBlock();
        if (!$this->accept('T_TYPE_CLOSE')) {
          $this->throwError('T_TYPE_CLOSE');
        }
      }
      else {
        $this->throwError('T_IDENTIFIER');
      }
    }
    else {
      $this->throwError('T_TYPE_OPEN');
    }
  }

  private function directiveBlock() {
    if ($this->accept('T_DIRBLOCK_OPEN')) {
      $this->directiveList();
      if (!$this->accept('T_DIRBLOCK_CLOSE')) {
        $this->throwError('T_DIRBLOCK_CLOSE');
      }
    }
    // empty
  }

  private function directiveList() {
    $this->directiveKeypart();
    $this->directiveValpart();
    $this->directiveListRest();
  }

  private function directiveListRest() {
    if ($this->accept('T_DIRECTIVE_SEP')) {
      $this->directiveList();
    }
    // empty
  }

  private function directiveKeypart() {
    if (!$this->accept('T_IDENTIFIER')) {
      $this->throwError('T_IDENTIFIER');
    }
  }

  private function directiveValpart() {
    if ($this->accept('T_DIR_KEYVAL_SEP')) {
      $this->directiveValList();
    }
    // empty
  }

  private function directiveValList() {
    $this->primitiveType();
    $this->directiveValRest();
  }

  private function directiveValRest() {
    if ($this->accept('T_VAL_SEP')) {
      $this->directiveValList();
    }
    // empty
  }

  private function string() {
    if (!$this->accept('T_QUOTED_STRING')) {
      if (!$this->accept('T_BAREWORD')) {
        $this->throwError("string");
      }
    }
  }

  private function throwError($expected) {
    $currtok = $this->currentToken;
    throw new ParseException("Expected $expected, got \"" . $currtok->getValue() . "\" of type " . $currtok->getType() . "\n\tat line " . $currtok->getLine() . " in file " . $this->file);
  }

  /*** HELPERS ***/
  private function accept($symbol) {
    $returnToken = $this->currentToken;
    if ($returnToken->getType() == $symbol) {
      $this->currentToken = array_shift($this->tokens);

      $this->syntaxTree->addChild($returnToken);
      return $returnToken;
    }
    return false;
  }

}
?>
