<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Helper\CodeStore;

//----------------------------------------------------------------------------------------------------------------------
/**
 * An abstract helper class for automatically generating code.
 */
abstract class CodeStore
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Increment indentation before appending the line.
   */
  const C_INDENT_INCREMENT_BEFORE = 1;

  /**
   * Increment indentation after appending the line.
   */
  const C_INDENT_INCREMENT_AFTER = 2;

  /**
   * Decrement indentation before appending the line.
   */
  const C_INDENT_DECREMENT_BEFORE = 4;

  /**
   * Decrement indentation after appending the line.
   */
  const C_INDENT_DECREMENT_AFTER = 8;

  /**
   * The number of spaces per indentation level.
   *
   * @var int
   */
  private $indentation;

  /**
   * The source code. Each element is a line.
   *
   * @var string[]
   */
  private $lines;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param int $indentation The number of spaces per indentation level.
   *
   * @api
   */
  public function __construct($indentation = 2)
  {
    $this->indentation = $indentation;
    $this->lines       = [];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a line or lines of code.
   *
   * @param null|string|string[] $line The line or lines of code to be appended. Null values will be ignored.
   * @param bool                 $trim If true the line or lines of code will be trimmed before appending.
   *
   * @throws \InvalidArgumentException
   *
   * @api
   */
  public function append($line, $trim = true)
  {
    switch (true)
    {
      case is_string($line):
        $this->appendLine($line, $trim);
        break;

      case is_array($line):
        $this->appendLines($line, $trim);
        break;

      case is_null($line):
        // Nothing to do.
        break;

      default:
        throw new \InvalidArgumentException('Not a string nor an array.');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a part of code to the last line of code.
   *
   * @param string $part The part of code to be to the last line.
   *
   * @api
   */
  public function appendToLastLine($part)
  {
    $this->lines[count($this->lines) - 1] .= $part;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Removes all code from this code store.
   *
   * @api
   */
  public function clear()
  {
    $this->lines = [];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the generated code as a single string.
   *
   * @api
   */
  public function getCode()
  {
    $lines       = [];
    $indentLevel = 0;

    foreach ($this->lines as $line)
    {
      $mode = $this->indentationMode($line);

      // Increment or decrement indentation level.
      if ($mode & self::C_INDENT_INCREMENT_BEFORE)
      {
        $indentLevel++;
      }
      if ($mode & self::C_INDENT_DECREMENT_BEFORE)
      {
        $indentLevel = max(0, $indentLevel - 1);
      }

      // Append the line with indentation.
      $lines[] = $this->addIndentation($line, $indentLevel);

      // Increment or decrement indentation level.
      if ($mode & self::C_INDENT_INCREMENT_AFTER)
      {
        $indentLevel++;
      }
      if ($mode & self::C_INDENT_DECREMENT_AFTER)
      {
        $indentLevel = max(0, $indentLevel - 1);
      }
    }

    if (empty($lines))
    {
      return '';
    }

    return implode(PHP_EOL, $lines).PHP_EOL;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the indentation mode based on a line of code.
   *
   * The indentation mode can be any combination of the following flags (combined with the | bitwise operator).
   * <ul>
   * <li> self::C_INDENT_INCREMENT_BEFORE: The indentation must be incremented before appending the line of code.
   * <li> self::C_INDENT_INCREMENT_AFTER: The indentation must be incremented after appending the line of code.
   * <li> self::C_INDENT_DECREMENT_BEFORE: The indentation must be decremented before appending the line of code.
   * <li> self::C_INDENT_DECREMENT_AFTER: The indentation must be decremented after appending the line of code.
   * </ul>
   *
   * @param string $line The line of code.
   *
   * @return int
   *
   * @api
   */
  abstract protected function indentationMode($line);

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a line of code with the proper amount of indentationMode.
   *
   * @param string $line        The line of code.
   * @param int    $indentLevel The indentation level.
   *
   * @return string The indented line of code.
   */
  private function addIndentation($line, $indentLevel)
  {
    return str_repeat(' ', $this->indentation * $indentLevel).$line;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a line of code this this code.
   *
   * @param null|string $line The line of code to append. If null the line will be ignored.
   * @param bool        $trim If true the line of code will be trimmed before appending.
   */
  private function appendLine($line, $trim)
  {
    if ($line===null) return;

    if ($trim) $line = trim($line);

    $this->lines[] = $line;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends an array with lines of code this this code.
   *
   * @param string[] $lines The lines of code to append.
   * @param bool     $trim  If true the lines of code will be trimmed before appending.
   */
  private function appendLines($lines, $trim)
  {
    foreach ($lines as $line)
    {
      $this->appendLine($line, $trim);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
