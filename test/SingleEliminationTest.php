<?php

require_once 'BracketTree.php';

class SingleEliminationTest extends PHPUnit_Framework_TestCase {
  public function testBySizeCreatesBracket() {
    $bracket = BracketTree_SingleElimination::by_size(4);
    $this->assertInstanceOf('BracketTree_SingleElimination', $bracket);
  }

  public function testBySizeHasProperNodeCount() {
    $bracket = BracketTree_SingleElimination::by_size(4);
    $this->assertEquals(7, $bracket->size);
  }
}
?>

