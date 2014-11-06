<?php
namespace Bauplan\Compiler;
use Bauplan\Compiler\CST\Node as Node;
use Bauplan\Exception\SyntaxError as SyntaxError;

/*
 * A recursive descent parser which outputs a concrete syntax tree for other
 * transformations
 *
 * TODO: https://github.com/symfony/expression-language/blob/master/Parser.php
 */
class Parser {
  private $stream;
  private $file;

  function __construct() {
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

  function setFilename($name) {
    $this->file = $name;
  }

  function parse($tokenStream) {
    $this->stream = $tokenStream;
    return $this->parseBauplan();
  }

  /*** PRODUCTION RULES, BASED OFF doc/grammar.bnf ***/
  private function parseBauplan() {
    $this->parsePreprocDeclarations(); // TODO: should take place in a separate parse loop
    $node = $this->parseTemplate();
    if ($this->stream->hasNext()) { // shoule be end of stream
      $token = $this->stream->getCurrent();
      throw new SyntaxError(sprintf('Unexpected token "%s" of type "%s"', $token->getValue(), $token->getType()));
    }

    return $node;
  }

  private function parsePreprocDeclarations() {
    if ($this->parsePreprocDeclaration() !== false) $this->parsePreprocDeclarations();
  }

  private function parsePreprocDeclaration() {
    if ($this->stream->getCurrent()->test(Token::T_PREPROC_DECL)) {
      $this->stream->expect(Token::T_IDENTIFIER, '', 'Missing preprocessor declaration key');
    }
    else {
      return false;
    }
  }

  private function parsePreprocVal() { // TODO
    try {
      $this->parsePrimitiveType();
    }
    catch (SyntaxError $e) {
      // empty
    }
  }

  private function parseComplexType() {
    try {
      $this->parseTemplate();
    }
    catch (SyntaxError $e) {
      try {
        $this->parseSection();
      }
      catch (SyntaxError $e) {
        try {
          $this->parseVariable();
        }
        catch (SyntaxError $e) {
          try {
            $this->parseCode();
          }
          catch (SyntaxError $e) {
            try {
              $this->parseInstruction();
            }
            catch (SyntaxError $e) {
              $this->throwError('template, section, variable, code, or instruction');
            }
          }
        }
      }
    }
  }

  private function parseType() {
    try {
      $this->parseComplexType();
    }
    catch (SyntaxError $e) {
      try {
        $this->parsePrimitiveType();
      }
      catch (SyntaxError $e) {
        $this->throwError('type');
      }
    }
  }

  private function parsePrimitiveType() {
    if (!$this->accept('T_BOOLEAN')) {
      try {
        $this->parseNumericType();
      }
      catch (SyntaxError $e) {
        try {
          $this->parseString();
        }
        catch (SyntaxError $e) {
          $this->throwError('primitive type');
        }
      }
    }
  }

  private function parseNumericType() {
    if (!$this->accept('T_INTEGER')) {
      if (!$this->accept('T_DOUBLE')) {
        $this->throwError('numeric type');
      }
    }
  }

  private function parseTemplate() {
    if ($this->accept('T_TEMPLATE')) {
      $this->parseTypedefWithBody();
    }
    else {
      $this->throwError('T_TEMPLATE');
    }
  }

  private function parseSection() {
    if ($this->accept('T_SECTION')) {
      $this->parseTypedefWithBody();
    }
    else {
      $this->throwError('T_SECTION');
    }
  }

  private function parseVariable() {
    if ($this->accept('T_VARIABLE')) {
      $this->parseTypedefNoBody();
    }
    else {
      $this->throwError('T_VARIABLE');
    }
  }

  private function parseCode() {
    if ($this->accept('T_CODE')) {
      $this->parseTypedefNoBody();
    }
    else {
      $this->throwError('T_CODE');
    }
  }

  private function parseInstruction() {
    if ($this->accept('T_INSTRUCTION')) {
       $this->parseTypedefWithBody();
    }
    else {
      $this->throwError('T_INSTRUCTION');
    }
  }

  private function parseTypedefWithBody() {
    if ($this->accept('T_TYPE_OPEN')) {
      if ($this->accept('T_IDENTIFIER')) {
        $this->parseDirectiveBlock();
        $this->parseBody();
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

  private function parseBody() {
    try {
      $this->parseType();
      $this->parseBody();
    }
    catch (SyntaxError $e) {
      // empty
    }
  }

  private function parseTypedefNoBody() {
    if ($this->accept('T_TYPE_OPEN')) {
      if ($this->accept('T_IDENTIFIER')) {
        $this->parseDirectiveBlock();
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

  private function parseDirectiveBlock() {
    if ($this->accept('T_DIRBLOCK_OPEN')) {
      $this->parseDirectiveList();
      if (!$this->accept('T_DIRBLOCK_CLOSE')) {
        $this->throwError('T_DIRBLOCK_CLOSE');
      }
    }
    // empty
  }

  private function parseDirectiveList() {
    $this->parseIdentifier();
    $this->parseDirectiveValpart();
    $this->parseDirectiveListRest();
  }

  private function parseDirectiveListRest() {
    if ($this->accept('T_DIRECTIVE_SEP')) {
      $this->parseDirectiveList();
    }
    // empty
  }

  private function parseDirectiveValpart() {
    if ($this->accept('T_DIR_KEYVAL_SEP')) {
      $this->parseDirectiveValList();
    }
    // empty
  }

  private function parseDirectiveValList() {
    $this->parseType();
    $this->parseDirectiveValRest();
  }

  private function parseDirectiveValRest() {
    if ($this->accept('T_VAL_SEP')) {
      $this->parseDirectiveValList();
    }
    // empty
  }

  private function parseString() {
    if (!$this->accept('T_QUOTED_STRING')) {
      if (!$this->accept('T_LITERAL')) {
        if (!$this->accept('T_BAREWORD')) {
          $this->throwError("string");
        }
      }
    }
  }

  private function throwError($expected) {
    $currtok = $this->currentToken;
    throw new SyntaxError("Expected $expected, got \"" . $currtok->getValue() . "\" of type " . $currtok->getType() . "\n\tat line " . $currtok->getLine() . " in file " . $this->file);
  }

  //FIXME - REMOVE; handle through token and token stream
  private function accept($symbol) {
    $returnToken = $this->currentToken;
    if ($returnToken->getType() == $symbol) {
      $this->currentToken = array_shift($this->tokens);

      return $returnToken;
    }
    return false;
  }

}
?>
