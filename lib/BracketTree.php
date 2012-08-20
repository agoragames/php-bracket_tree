<?php
  /**
   *
   * BracketTree
   *
   * Tree-based Bracketing System
   *
   * @author Andrew Nordman <anordman@majorleaguegaming.com>
   * @copyright Andrew Nordman 2012
   * @link http://github.com/agoragames/php-bracket_tree
   *
   */
  /**
   * class BracketTree_Node
   *
   * Node contains the custom payload and binary tree pointers.  Uses left and
   * right properties for tree traversal and positioning.
   */

  require_once 'CustomBracket.php';
  require_once 'DoubleElimination.php';
  require_once 'SingleElimination.php';

  class BracketTree_Node {
    public $left, $right, $payload, $position;

    public function __construct($position, $data) {
      $this->left = NULL;
      $this->right = NULL;
      $this->position = $position;
      $this->payload = $data;
    }
  }

  /**
   * class BracketTree_Bracket
   *
   * The primary Bracket class. 
   */
  class BracketTree_Bracket {
    public $_root, $depth, $size;

    public function __construct($data = array()) {
      $this->_root = NULL;
      $this->depth = 0;
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
     * @param $position node position that guides the tree.
     * @param $data associative array of data for the node
     * @return Boolean result of addition. Returns true if successfully added
     */
    public function add ($position, $data) {
      $current = NULL;
      $node = new BracketTree_Node($position, $data);

      if ($this->_root === NULL) {
        $this->_root = $node;
        $this->depth = 1;
        $this->size = 1;
        return true;
      } else {
        $current = $this->_root;
        $depth = 2;

        while(true) {
          if ($node->position < $current->position) {
            if ($current->left === NULL) {
              $current->left = $node;
              $this->size++;

              if ($depth > $this->depth) {
                $this->depth = $depth;
              }

              return true;
            } else {
              $current = $current->left;
              $depth++;
            }
          } elseif ($node->position > $current->position) {
            if ($current->right === NULL) {
              $current->right = $node;
              $this->size++;

              if ($depth > $this->depth) {
                $this->depth = $depth;
              }

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
     *
     *  Iterates across the tree in sequential order. For hierarchical traversal,
     *  see `top_down($iterator)`.
     *
     *  @param $iterator the lambda executed at each node point
     */
    public function in_order ($iterator) {
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

      $in_order($this->_root, 0);

      return true;
    }

    /**
     *
     * Iterates from the root node down rather than in positional order
     *
     * @param $iterator the lambda executed at each node point
     *
     */
    public function top_down ($iterator) {
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

      $td($this->_root);
    }


    /**
     *
     * Finds node in the tree for the given position
     *
     * @param $position the tree position to be returned
     * @return $node the node at the given position, or NULL if not found
     *
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
     *
     * Converts tree into an array of BracketTree_Node's, in hierarchical order for
     * easy rebuild.
     *
     * @return $nodes Array of Nodes
     *
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
