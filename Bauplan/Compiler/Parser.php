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
    $tokenStream->setFile($this->file);
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
    if ($this->stream->expect(Token::T_PREPROC_DECL)) {
      $this->stream->expect(Token::T_IDENTIFIER, 'Missing preprocessor declaration key');
      $this->parsePreprocVal();
    }
    else {
      return false;
    }
  }

  private function parsePreprocVal() { // TODO
    $this->parsePrimitiveType(); // optional
  }

  private function parseComplexType() {
    $complexNode = $this->parseTemplate() || $this->parseSection() || $this->parseVariable() || $this->parseCode() || $this->parseInstruction();
    if (!$complexNode) {
      throw new SyntaxError('Expected T_TEMPLATE, T_SECTION, T_VARIABLE, T_CODE, or T_INSTRUCTION');
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
    return $this->stream->expectOneOf(array(
      Token::T_BOOLEAN,
      Token::T_INTEGER,
      Token::T_DOUBLE,
      Token::T_QUOTED_STRING,
      Token::T_LITERAL,
      Token::T_BAREWORD
    ));
  }

  private function parseNumericType() {
    if (!$this->accept('T_INTEGER')) {
      if (!$this->accept('T_DOUBLE')) {
        $this->throwError('numeric type');
      }
    }
  }

  private function parseTemplate() {
    $template = $this->stream->expect(Token::T_TEMPLATE, 'Missing template');
    $this->parseTypedefWithBody();
    return $template;
  }

  private function parseSection() {
    $section = $this->stream->expect(Token::T_SECTION, 'Missing section');
    $this->parseTypedefWithBody();
    return $section;
  }

  private function parseVariable() {
    $variable = $this->stream->expect(Token::T_VARIABLE, 'Missing variable');
    $this->parseTypedefNoBody();
    return $variable;
  }

  private function parseCode() {
    $code = $this->stream->expect(Token::T_CODE, 'Missing code');
    $this->parseTypedefWithBody();
    return $code;
  }

  private function parseInstruction() {
    $instruction = $this->stream->expect(Token::T_INSTRUCTION, 'Missing instruction');
    $this->parseTypedefWithBody();
    return $instruction;
  }

  private function parseTypedefWithBody() {
    // FIXME: Build node - these should all be siblings (CST should be able to add siblings)
    $this->stream->expect(Token::T_TYPE_OPEN, 'Missing (');
    $this->stream->expect(Token::T_IDENTIFIER, 'Type declaration requires an identifier');
    $directiveBlockNode = $this->parseDirectiveBlock();
    $typeBodyNode = $this->parseBody();
    $this->stream->expect(Token::T_TYPE_CLOSE, 'Missing )');
  }

  private function parseBody() {
    $node = $this->parseType();
    if ($node) {
      $bodyNode = $this->parseBody();
      // TODO: Sibling? Child?
      return $node;
    }
    // optional
    return $node;
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
    $blockNode = $this->stream->expect(Token::T_DIRBLOCK_OPEN);
    if ($blockNode) {
      $this->parseDirectiveList();
      $this->stream->expect(Token::T_DIRBLOCK_CLOSE, 'Missing closing bracket for directive block');
    }
    // optional
    return $blockNode;
  }

  private function parseDirectiveList() {
    $this->stream->expect(Token::T_IDENTIFIER, 'Missing directive key');
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
    return true;
    $returnToken = $this->currentToken;
    if ($returnToken->getType() == $symbol) {
      $this->currentToken = array_shift($this->tokens);

      return $returnToken;
    }
    return false;
  }

}
?>
