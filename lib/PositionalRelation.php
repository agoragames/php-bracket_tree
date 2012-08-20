<?php
/*
 * BracketTree_PositionalRelation
 *
 * This class is a relational object used to constructing chainable queries on the 
 * tree for times when you do not know the exact position value of the BracketTree_Node
 * you are looking for.  It uses two types of methods: relation condition methods and
 * relation access methods.
 *
 * ## Relation Condition Methods
 *
 * Relation condition methods take th original BracketTree_PositionalRelation object
 * and add conditions, returning itself for easy chaining of relation conditions. Actual
 * traversal does not happen with these methods:
 *
 *    $bracket = BracketTree_DoubleElimination.by_size(4);
 *    $relation = new BracketTree_PositionalRelation($bracket);
 *    $relation->winners()->round(4);
 * 
 * Once you call a Relation Access Method, it will build based on the relation conditions
 * you have passed to it. Typically you will not directly instance this class due to
 * delegation methods inside BracketTree_CustomBracket. All of the methods are accessible
 * from that object.
 *
 *    $bracket = BracketTree_DoubleElimination.by_size(4);
 *    $node = $bracket->winners()->round(4)->seat(3); // BracketTree_Node
 *
 *
 * ## Relation Access Methods
 *
 * Relation access methods take the complete set of relation conditions and query the
 * tree based on them, collecting all applicable BracketTree_Node's that meet the
 * criteria. No relations conditions are required to call a relation access method.
 */
class BracketTree_PositionalRelation {
  public $bracket, $side, $round;

  public function __construct($bracket) {
    $this->bracket = $bracket;
    $this->side = NULL;
    $this->round = NULL;
  }

  /**
   *
   * Sets the side to winner's bracket
   * @return self
   *
   */
  public function winners() {
    $this->side = 'left';
    return $this;
  }

  /**
   * Sets the side to loser's bracket in the current query condition.
   * @return self
   *
   */
  public function losers() {
    $this->side = 'right';
    return $this;
  }

  /** 
   * Sets the round in the current query condition. Translates the round based on
   * the total depth of the side minus the round passed.
   *
   * @param int $number Round number
   * @return self
   */
  public function round($number) {
    $this->round = $number;
    return $this;
  }

  /**
   * Retrieves the seat in $number position based on the current relation conditions
   *
   * @param int $number Seat number
   * @return BracketTree_Node|null $node Seat requested
   */
  public function seat($number) {
    $seats = $this->all();
    if ($number <= count($seats)) {
      return $seats[$number - 1];
    } else {
      return NULL;
    }
  }

  /**
   * Retrieves the first seat based on the current relation conditions
   *
   * @return BracketTree_Node|null
   */
  public function first() {
    $seats = $this->all();
    return $seats[0];
  }

  /**
   * Retrieves the last seat based on the current relation conditions
   *
   * @return BracketTree_Node|null
   */
  public function last() {
    $seats = $this->all();
    $seat = end($seats);

    return end($seats);
  }

  /**
   * Retrieves all seats (top-down) based on the current relation conditions
   *
   * @return BracketTree_Node[]
   */
  public function all() {
    if ($this->side != NULL) {
      if ($this->round != NULL) {
        $seats = $this->by_round($this->round, $this->side);
      } else {
        if ($this->side == 'left') {
          $side_root = $this->bracket->_root->left;
        } else {
          $side_root = $this->bracket->_root->right;
        }
        $seats = array();

        $this->bracket->top_down($side_root, function($node) use (&$seats) {
          $seats[] = $node;
        });
      }
    } else {
      if ($this->round != NULL) {
        $seats = array_merge(
          $this->by_round($this->round, 'left'),
          $this->by_round($this->round, 'right')
        );
      } else {
        $seats = array();
        $this->bracket->top_down($this->bracket->_root, function($node) use (&$seats) {
          $seats[] = $node;
        });
      }
    }

    return $seats;
  }

  /** 
   * Traverses a side of the bracket and retrieves seats for a given depth
   *
   * @param int $round Round to retrieve
   * @param string $side Side of the bracket ('left', or 'right')
   * @return BracketTree_Node[] All nodes in the round for given side
   */
  private function by_round($round, $side) {
    $depth = $this->bracket->depth[$side] - ($round - 1);
    $seats = array();

    if ($side == 'left') {
      $side_root = $this->bracket->_root->left;
    } else {
      $side_root = $this->bracket->_root->right;
    }

    $this->bracket->top_down($side_root, function($node) use (&$seats, $depth) {
      if ($node->depth == $depth) {
        $seats[] = $node;
      }
    });

    return $seats;
  }
}
?>
