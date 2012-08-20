<?php

class PositionalRelationTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    $this->bracket = BracketTree_DoubleElimination::by_size(4);
    $this->relation = new BracketTree_PositionalRelation($this->bracket);
  }

  public function testWinnersSetsSideToLeft() {
    $this->relation->winners();
    $this->assertEquals('left', $this->relation->side);
  }

  public function testWinnersReturnsRelation() {
    $this->assertInstanceOf('BracketTree_PositionalRelation', $this->relation->winners());
  }
   
  public function testLosersSetsSideToRight() {
    $this->relation->losers();
    $this->assertEquals('right', $this->relation->side);
  }

  public function testLosersReturnsRelation() {
    $this->assertInstanceOf('BracketTree_PositionalRelation', $this->relation->losers());
  }

  public function testRoundSetsRound() {
    $this->relation->round(1);
    $this->assertEquals(1, $this->relation->round);
  }

  public function testRoundReturnsRelation() {
    $this->assertInstanceOf('BracketTree_PositionalRelation', $this->relation->round(1));
  }

  public function testSeatReturnsNode() {
    $node = $this->relation->winners()->round(1)->seat(3);
    $this->assertInstanceOf('BracketTree_Node', $node);
  }

  public function testSeatReturnsCorrectNode() {
    $node = $this->relation->winners()->round(1)->seat(3);
    $this->assertEquals(5, $node->position);
  }

  public function testSeatReturnsNullIfExceedingSeats() {
    $node = $this->relation->winners()->round(1)->seat(9);
    $this->assertNull($node);
  }

  public function testAllReturnsArrayOfNodes() {
    $nodes = $this->relation->winners()->round(1)->all();
    $this->assertCount(4, $nodes);
    foreach($nodes as $node) {
      $this->assertInstanceOf('BracketTree_Node', $node);
    }
  }

  public function testAllReturnsAllSidesIfSideNotPicked() {
    $this->assertCount(6, $this->relation->round(1)->all());
  }

  public function testAllReturnsAllRoundsIfRoundNotPicked() {
    $this->assertCount(7, $this->relation->winners()->all());
  }

  public function testAllReturnsAllNodesIfRoundNorSidePicked() {
    $this->assertCount(13, $this->relation->all());
  }

  public function testFirstReturnsFirstSeat() {
    $node = $this->relation->round(1)->first();
    $this->assertInstanceOf('BracketTree_Node', $node);
    $this->assertEquals(1, $node->position);
  }

  public function testLastReturnsLastSeat() {
    $node = $this->relation->round(1)->last();
    $this->assertInstanceOf('BracketTree_Node', $node);
    $this->assertEquals(13, $node->position);
  }

  public function testModificationsPersist() {
    $before_node = $this->relation->round(1)->first();
    $before_node->payload['name'] = 'Foo';
    $after_node = $this->relation->round(1)->first();
    $this->assertEquals('Foo', $after_node->payload['name']);
  }
}
?>
