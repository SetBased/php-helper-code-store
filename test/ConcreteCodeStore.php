<?php
declare(strict_types=1);

namespace SetBased\Helper\CodeStore\Test;

use SetBased\Helper\CodeStore\CodeStore;

/**
 * A concrete implementation of class CodeStore.
 */
class ConcreteCodeStore extends CodeStore
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * If true the current lines are part of a heredoc.
   *
   * @var bool
   */
  private bool $isHeredoc = false;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function __construct(int $indentation = 2, int $width = 15)
  {
    parent::__construct($indentation, $width);

    $this->separator = '#'.str_repeat('-', $width - 1);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  protected function indentationMode(string $line): int
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

      case 'end-end':
        return self::C_INDENT_DECREMENT_BEFORE_DOUBLE;

      case 'heredoc-start':
        $this->isHeredoc = true;

        return 0;

      case 'heredoc-end':
        $this->isHeredoc = false;

        return self::C_INDENT_HEREDOC;

      default:
        return ($this->isHeredoc===false) ? 0 : self::C_INDENT_HEREDOC;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
