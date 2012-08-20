<?php

class TestBracket extends BracketTree_CustomBracket {
  public static function getTemplateDir() {
    return dirname(__FILE__) . '/../templates/double_elimination';
  }
}

class CustomBracketTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    $this->bracket = TestBracket::by_size(4);
  }

  public function testWinnersReturnsRelation() {
    $this->assertInstanceOf('BracketTree_PositionalRelation', $this->bracket->winners());
  }
  public function testRelationCallsAreRemembered() {
    $this->bracket->winners();
    $relation = $this->bracket->round(1);
    $this->assertEquals(1, $relation->round);
    $this->assertNull($relation->side);
  }
}
?>
