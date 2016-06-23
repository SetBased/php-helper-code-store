<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Helper\CodeStore\Test;

use SetBased\Helper\CodeStore\CodeStore;

//----------------------------------------------------------------------------------------------------------------------
class ConcreteCodeStore extends CodeStore
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function indentationMode($line)
  {
    switch (trim($line))
    {
      case 'BEGIN':
        return self::C_INDENT_INCREMENT_BEFORE;

      case 'begin':
        return self::C_INDENT_INCREMENT_AFTER;

      case 'end':
        return self::C_INDENT_DECREMENT_BEFORE;

      case 'END':
        return self::C_INDENT_DECREMENT_AFTER;

      case 'middle':
        return self::C_INDENT_INCREMENT_AFTER | self::C_INDENT_DECREMENT_BEFORE;

      case 'MIDDLE':
        return self::C_INDENT_INCREMENT_BEFORE | self::C_INDENT_DECREMENT_AFTER;

      default:
        return 0;
    }
  }
  
  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
