<?php 

require 'BracketTree.php';

class BracketTest extends PHPUnit_Framework_TestCase {
  protected function setUp() {
    $this->nodes = array(
      array('position' => 2, 'name' => 'Foo'),
      array('position' => 1, 'name' => 'Bar'),
      array('position' => 3, 'name' => 'Baz') 
    );

    $this->bracket = new BracketTree_Bracket($this->nodes);
  }

  public function testRootIsNull() {
    $bracket = new BracketTree_Bracket();
    $this->assertNull($bracket->_root);
  }

  public function testAddsNodeToRoot() {
    $bracket = new BracketTree_Bracket();

    $result = $bracket->add(2, array('name' => 'Bob'));
    $this->assertTrue($result);
    $this->assertInstanceOf('BracketTree_Node', $bracket->_root);
    $this->assertEquals('Bob', $bracket->_root->payload['name']);
  }

  public function testAddExistingPositionReturnsFalse() {
    $bracket = new BracketTree_Bracket();

    $bracket->add(2, array('name' => 'Foo'));
    $result = $bracket->add(2, array('name' => 'Bar'));
    $this->assertFalse($result);
  }

  public function testAddSmallerToLeftOfRoot() {
    $bracket = new BracketTree_Bracket();

    $payload1 = array('name' => 'Foo');
    $payload2 = array('name' => 'Bar');
    $bracket->add(2, $payload2);
    $bracket->add(1, $payload1);

    $this->assertEquals(2, $bracket->_root->position);
    $this->assertEquals($payload2, $bracket->_root->payload);
    $this->assertEquals(1, $bracket->_root->left->position);
    $this->assertEquals($payload1, $bracket->_root->left->payload);
  }

  public function testAddLargerToRightOfRoot() {
    $bracket = new BracketTree_Bracket();

    $payload3 = array('name' => 'Foo');
    $payload2 = array('name' => 'Bar');
    $bracket->add(2, $payload2);
    $bracket->add(3, $payload3);

    $this->assertEquals(2, $bracket->_root->position);
    $this->assertEquals($payload2, $bracket->_root->payload);
    $this->assertEquals(3, $bracket->_root->right->position);
    $this->assertEquals($payload3, $bracket->_root->right->payload);
  }

  public function testArrayAsConstructorParameter() {
    $this->assertEquals(2, $this->bracket->_root->position);
    $this->assertEquals($this->nodes[0], $this->bracket->_root->payload);

    $this->assertEquals(1, $this->bracket->_root->left->position);
    $this->assertEquals($this->nodes[1], $this->bracket->_root->left->payload);

    $this->assertEquals(3, $this->bracket->_root->right->position);
    $this->assertEquals($this->nodes[2], $this->bracket->_root->right->payload);
  }

  public function testInOrderExistingNodes() {
    $test = $this;
    $this->bracket->in_order(function($node) use ($test) {
      $test->assertInstanceOf('BracketTree_Node', $node);
    });
  }

  public function testInOrderReturnsInSequentialOrder() {
    $positions = array();
    $this->bracket->in_order(function($node) use (&$positions) {
      $positions[] = $node->position;
    });

    $this->assertEquals(array(1,2,3), $positions);
  }

  public function testTopDownReturnsInHierarchicalOrder() {
    $positions = array();
    $this->bracket->top_down(function($node) use (&$positions) {
      $positions[] = $node->position;
    });

    $this->assertEquals(array(2,1,3), $positions);
  }
  
  public function testAtReturnsSpecifiedNode() {
    $node = $this->bracket->at(3);

    $this->assertInstanceOf('BracketTree_Node', $node);
    $this->assertEquals(3, $node->position);
    $this->assertEquals('Baz', $node->payload['name']);
  }

  public function testAtReturnsNullIfNotPresent() {
    $node = $this->bracket->at(4);

    $this->assertNull($node);
  }

  public function testToArrayReturnsArrayOfNodes() {
    $node_array = $this->bracket->to_array();

    $this->assertCount(3, $node_array);
    $this->assertEquals('Foo', $node_array[0]->payload['name']);
    $this->assertEquals('Bar', $node_array[1]->payload['name']);
    $this->assertEquals('Baz', $node_array[2]->payload['name']);
  }
   
  public function testDepthIsZeroWhenEmpty() {
    $empty_bracket = new BracketTree_Bracket();
    $this->assertEquals(0, $empty_bracket->depth);
  }

  public function testDepthIsIncreasedByEachTierAdded() {
    $this->assertEquals(2, $this->bracket->depth);
  }

  public function testAddIncreasesDepthWhenDeeper() {
    $bracket = new BracketTree_Bracket();
    $bracket->add(1, array('name' => 'Foo'));

    $this->assertEquals(1, $bracket->depth);
  }

  public function testAddIncreasesSize() {
    $bracket = new BracketTree_Bracket();
    $this->assertEquals(0, $bracket->size);

    $bracket->add(1, array('name' => 'Foo'));
    $this->assertEquals(1, $bracket->size);

    $this->assertEquals(3, $this->bracket->size);
  }
}
?>
