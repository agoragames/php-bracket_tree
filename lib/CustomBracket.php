<?php
/**
 * Abstract class for building custom bracket types from BracketTree Templates.
 *
 * This class provides an interface for loading blank brackets with the nodes in
 * place for a given structure. This is generally favored over having to start with
 * an empty tree and add each node in the correct order.
 *
 * When inheriting, the subclass needs only implement getTemplateDir. This grants
 * access to creating new brackets based on BracketTree Templates via the 'by_size'
 * static method.
 *
 * Additionally, this provides delegation hooks for BracketTree_PositionalRelation
 * querying of the tree.
 *
 * @example custom bracket class
 *    class MyAwesomeBracket extends BracketTree_CustomBracket {
 *      protected static function getTemplateDir() {
 *        return dirname(__FILE__) . '/../templates/awesome_bracket';
 *      }
 *    }
 *
 *    $bracket = MyAwesomeBracket::by_size(4);
 *
 *
 * @see BracketTree_CustomBracket::by_size
 * @see BracketTree_PositionalRelation
 * @see https://github.com/agoragames/bracket_tree/wiki/BracketTree-Data-Specification
 *
 */
abstract class BracketTree_CustomBracket extends BracketTree_Bracket {
  abstract protected static function getTemplateDir();

  /**
   *
   * Returns an instance of bracket based on preset BracketTree template files,
   * with empty nodes at the given positions based on the template.
   *
   * @param $size number of players in template
   * @return $bracket instance of BracketTree_CustomBracket
   *
   */
  static function by_size($size) {
    $filename = static::getTemplateDir()."/".$size.".json";
    $handle = fopen($filename, 'r');
    $template = json_decode(fread($handle, filesize($filename)), true);
    $seats = $template['seats'];
    fclose($handle);

    $class = get_called_class();
    $bracket = new $class();

    foreach ($seats as $seat) {
      $bracket->add($seat['position'], array());
    }

    return $bracket;
  }

  /**
   * 
   * Delegate to PositionalRelation
   * 
   * @return BracketTree_PositionalRelation
   *
   */
  private function relation() {
    return new BracketTree_PositionalRelation($this);
  }

  /**
   *
   * Delegates `all` to a PositionalRelation.
   *
   * @return BracketTree_PositionalRelation
   * @see BracketTree_PositionalRelation#all 
   */
  public function all() {
    return $this->relation()->all();
  }

  /**
   *
   * Delegates `first` to a PositionalRelation.
   *
   * @return BracketTree_PositionalRelation
   * @see BracketTree_PositionalRelation#first 
   */
  public function first() {
    return $this->relation()->first();
  }

  /**
   *
   * Delegates `last` to a PositionalRelation.
   *
   * @return BracketTree_PositionalRelation
   * @see BracketTree_PositionalRelation#last 
   */
  public function last() {
    return $this->relation()->last();
  }

  /**
   *
   * Delegates `winners` to a PositionalRelation.
   *
   * @return BracketTree_PositionalRelation
   * @see BracketTree_PositionalRelation#winners 
   */
  public function winners() {
    return $this->relation()->winners();
  }
  /**
   *
   * Delegates `losers` to a PositionalRelation.
   *
   * @return BracketTree_PositionalRelation
   * @see BracketTree_PositionalRelation#losers 
   */
  public function losers() {
    return $this->relation()->losers();
  }

  /**
   *
   * Delegates `seat` to a PositionalRelation.
   *
   * @return BracketTree_PositionalRelation
   * @see BracketTree_PositionalRelation#seat 
   */
  public function seat($number) {
    return $this->relation()->seat($number);
  }

  /**
   *
   * Delegates `round` to a PositionalRelation.
   *
   * @return BracketTree_PositionalRelation
   * @see BracketTree_PositionalRelation#round 
   */
  public function round($number) {
    return $this->relation()->round($number);
  }
}
?>
