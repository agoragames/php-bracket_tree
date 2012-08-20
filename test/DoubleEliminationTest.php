<?php

class DoubleEliminationTest extends PHPUnit_Framework_TestCase {
  public function testBySizeCreatesBracket() {
    $bracket = BracketTree_DoubleElimination::by_size(4);
    $this->assertInstanceOf('BracketTree_DoubleElimination', $bracket);
  }

  public function testBySizeHasProperNodeCount() {
    $bracket = BracketTree_DoubleElimination::by_size(4);
    $this->assertEquals(13, $bracket->size);
  }

}
?>
