<?php
namespace Bauplan\Language\Directive;

use Bauplan\Language\Token as Token;

class DirectiveToken extends Token {
  const T_COLON = 'T_COLON';
  const T_PIPE = 'T_PIPE';
  const T_COMMA = 'T_COMMA';
  const T_STRING = 'T_STRING';
  const T_KEY = 'T_KEY';
  const T_NUMBER = 'T_NUMBER';
  const T_TRUE = 'T_TRUE';
  const T_FALSE = 'T_FALSE';
}
?>
