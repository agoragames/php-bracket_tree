<?php

abstract class BracketTree_CustomBracket extends BracketTree_Bracket {
  abstract protected static function getTemplateDir();

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
}
?>
