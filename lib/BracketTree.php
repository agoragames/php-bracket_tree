<?php
  /**
   * BracketTree
   *
   * Tree-based Bracketing System
   *
   * @package BracketTree
   * @author Andrew Nordman <anordman@majorleaguegaming.com>
   * @copyright Andrew Nordman 2012
   * @link http://github.com/agoragames/php-bracket_tree
   */
  require_once 'PositionalRelation.php';
  require_once 'CustomBracket.php';
  require_once 'DoubleElimination.php';
  require_once 'SingleElimination.php';

  /**
   * Node contains the custom payload and binary tree pointers.  Uses left and
   * right properties for tree traversal and positioning.
   */
  class BracketTree_Node {
    public $left, $right, $payload, $position, $depth;

    public function __construct($position, $data) {
      $this->left = NULL;
      $this->right = NULL;
      $this->position = $position;
      $this->payload = $data;
      $this->depth = NULL;
    }
  }

  /**
   * The primary Bracket class. 
   */
  class BracketTree_Bracket {
    public $_root, $depth, $size;

    public function __construct($data = array()) {
      $this->_root = NULL;
      $this->depth = array('total' => 0, 'left' => 0, 'right' => 0);
      $this->size = 0;

      foreach($data as $node) {
        if ($node['position'] != NULL) {
          $this->add($node['position'], $node);
        }
      }
    }

    /** 
     * Adds the node to the tree on the given tree
     *
     * @param BracketTree_Node $position node position that guides the tree.
     * @param array $data associative array of data for the node
     * @return bool result of addition. Returns true if successfully added
     */
    public function add ($position, $data) {
      $current = NULL;
      $node = new BracketTree_Node($position, $data);

      if ($this->_root === NULL) {
        $this->_root = $node;
        $this->depth['total'] = 1;
        $this->depth['left'] = 1;
        $this->depth['right'] = 1;
        $this->size = 1;
        return true;
      } else {
        $current = $this->_root;
        $depth = 2;

        while(true) {
          if ($node->position < $current->position) {
            if ($current->left === NULL) {
              $node->depth = $depth;
              $current->left = $node;
              $this->size++;

              $this->depth_check($depth, $node->position);
              return true;
            } else {
              $current = $current->left;
              $depth++;
            }
          } elseif ($node->position > $current->position) {
            if ($current->right === NULL) {
              $node->depth = $depth;
              $current->right = $node;
              $this->size++;

              $this->depth_check($depth, $node->position);
              return true;
            } else {
              $current = $current->right;
              $depth++;
            }
          } else {
            break;
          }
        }

        return false;
      }
    }

    /**
     * Checks for new depths on total, left, and right sides of root. Sets if exeeds.
     *
     * @param int $depth Depth of the new node.
     * @param int $position Position of the new node.
     * @return void
     */
    public function depth_check($depth, $position) {
      $this->depth['total'] = max($depth, $this->depth['total']);

      if ($position < $this->_root->position) {
        $this->depth['left']  = max($depth, $this->depth['left']);
      } else if ($position > $this->_root->position) {
        $this->depth['right'] = max($depth, $this->depth['right']);
      }
    }

    /**
     *  Iterates across the tree in sequential order. For hierarchical traversal,
     *  see `top_down($iterator)`.
     *
     *  @param BracketTree_Node $node Node to be treated as root. If not passed, 
     *    this value becomes $this->_root;
     *  @param callback $iterator the lambda executed at each node point
     *  @return bool
     */
    public function in_order () {
      $args = func_get_args();

      if (func_num_args() == 2) {
        $root = $args[0];
        $iterator = $args[1];
      } else {
        $root = $this->_root;
        $iterator = $args[0];
      }

      $in_order = function ($node, $depth) use (&$in_order, $iterator) {
        if ($node != NULL) {
          if ($node->left != NULL) {
            $in_order($node->left, $depth+1);
          }

          $iterator($node);

          if ($node->right != NULL) {
            $in_order($node->right, $depth+1);
          }
        }
      };

      $in_order($root, 0);

      return true;
    }

    /**
     * Iterates from the root node down rather than in positional order
     *
     * @param BracketTree_Node $root The node to be treated as root. If not passed,
     *  this is set as $this->_root;
     * @param callback $iterator The lambda executed at each node point
     */
    public function top_down ($iterator) {
      $args = func_get_args();

      if (func_num_args() == 2) {
        $root = $args[0];
        $iterator = $args[1];
      } else {
        $root = $this->_root;
        $iterator = $args[0];
      }

      $td = function ($node) use (&$td, $iterator) {
        if ($node != NULL) {
          $iterator($node);

          if ($node->left != NULL) {
            $td($node->left);
          }

          if ($node->right != NULL) {
            $td($node->right);
          }
        }
      };

      $td($root);
    }

    /**
     * Finds node in the tree for the given position
     *
     * @param int $position the tree position to be returned
     * @return BracketTree_Node|null $node the node at the given position, or NULL if not found
     */
    public function at ($position) {
      $found = NULL;

      $current = $this->_root;

      while (true) {
        if ($position == $current->position) {
          $found = $current;
          break;
        } else if ($position < $current->position) {
          if ($current->left != NULL) {
            $current = $current->left;
          } else {
            break;
          }
          
        } else if ($position > $current->position) {
          if ($current->right != NULL) {
            $current = $current->right;
          } else {
            break;
          }
        } else {
          break;
        }
      }

      return $found;
    }

    /**
     * Converts tree into an array of BracketTree_Node's, in hierarchical order for
     * easy rebuild.
     *
     * @return BracketTree_Node[] $nodes BracketTree converted to an array
     */
    public function to_array() {
      $nodes = array();
      $this->top_down(function($node) use (&$nodes) {
        $nodes[] = $node;
      });

      return $nodes;
    }
  }
?>
