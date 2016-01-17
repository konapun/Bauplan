<?php
namespace Bauplan\Language\AST\IO;

/*
 * An interface for AST importing/exporting
 */
interface IO { //extends \Serializable {

  /*
   * Given a source string in the same format as the string returned by this \
   * implementor's export method, return the native AST represented by the
   * string.
   */
  function import($source);

  /*
   * Given a native AST, build a string representation
   */
  function export($tree);
}
?>
