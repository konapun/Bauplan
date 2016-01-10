<?php

$source = file_get_contents('t/bauplan1-comparison.bau');
$source2 = ";;(
  This is a block comment. It can span multiple lines and gets removed by the lexer.
  Demonstrating another linez.
;;)";

if (preg_match('/^;;\((.*?)(?=(;;\)))/s', $source2, $match)) {
  var_dump($match);
}
if (preg_match('/^;;\((.*?)(?=(;;\)))/s', $source2, $match)) {
  //var_dump($match);
}
if (preg_match('/<<<(.*?)(?=(>>>))/s', $source, $match)) {
  //var_dump($match);
}
if (preg_match('/<<<([^>>>]*)>>>/', $source2, $match)) {
  //var_dump($match);
}
 ?>
