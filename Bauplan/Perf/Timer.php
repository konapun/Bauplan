<?php
namespace Bauplan\Perf;

/*
 * A little tool for generating and running benchmarks
 */
class Timer {
  private $start;
  private $currentTime;
  private $points;
  private $reporter;

  function __construct() {
    $this->points = array();
    $this->initialize();
  }

  function initialize() {
    $this->start = microtime(true);
    $this->currentTime = $this->start;
  }

  function createPoint($name) {
    $this->points[$name] = microtime(true) - $this->currentTime;
    $this->currentTime = microtime(true);
    return $name;
  }

  function getTimeSinceLastPoint() {
    $now = $this->createPoint('_internal_timer_point');
    $last = -1;
    foreach ($this->points as $name => $elapsed) {
      $time = $this->getTimeBetweenPoints($now, $name);
      if ($name != '_internal_timer_point' && ($last < 0 || $time < $last)) {
        $last = $time;
      }
    }

    return $last;
  }

  function getTimeBetweenPoints($point1, $point2) {
    if (!array_key_exists($point1, $this->points) || !array_key_exists($point2, $this->points)) {
      throw new InvalidArgumentException("Can't locate timeline point '$point1' or '$point2'");
    }
    return abs($this->points[$point1] - $this->points[$point2]);
  }

  function report($reporter=null) {
    if (is_null($reporter)) {
      $reporter = function($name, $interval) {
        echo "Timeline point '$name' done in $interval seconds<br>\n";
      };
    }

    foreach ($this->points as $name => $time) {
      $reporter($name, $time);
    }
  }
}
?>
