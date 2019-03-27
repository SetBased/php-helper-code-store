<?php
declare(strict_types=1);

namespace SetBased\Helper\CodeStore;

/**
 * An abstract helper class for automatically generating code.
 */
abstract class CodeStore
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Increment indentation before appending the line.
   *
   * @since 1.0.0
   * @api
   */
  const C_INDENT_INCREMENT_BEFORE = 1;

  /**
   * Increment indentation after appending the line.
   *
   * @since 1.0.0
   * @api
   */
  const C_INDENT_INCREMENT_AFTER = 2;

  /**
   * Decrement indentation before appending the line.
   *
   * @since 1.0.0
   * @api
   */
  const C_INDENT_DECREMENT_BEFORE = 4;

  /**
   * Decrement indentation after appending the line.
   *
   * @since 1.0.0
   * @api
   */
  const C_INDENT_DECREMENT_AFTER = 8;

  /**
   * String for separating parts of the generated code. In most cases a comment with one character repeated many times.
   *
   * @var string
   *
   * @since 1.0.0
   * @api
   */
  protected $separator;

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

  /**
   * The maximum width of the generated code (in chars).
   *
   * @var int
   */
  private $width;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param int $indentation The number of spaces per indentation level.
   * @param int $width       The maximum width of the generated code (in chars).
   *
   * @since 1.0.0
   * @api
   */
  public function __construct(int $indentation = 2, int $width = 120)
  {
    $this->indentation = $indentation;
    $this->lines       = [];
    $this->width       = $width;
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
   * @since 1.0.0
   * @api
   */
  public function append($line, bool $trim = true): void
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
   * Appends the separator to the generated code.
   *
   * @since 1.0.0
   * @api
   */
  public function appendSeparator(): void
  {
    $this->append($this->separator, false);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a part of code to the last line of code.
   *
   * @param string $part The part of code to be to the last line.
   *
   * @since 1.0.0
   * @api
   */
  public function appendToLastLine(string $part): void
  {
    $this->lines[count($this->lines) - 1] .= $part;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Removes all code from this code store.
   *
   * @since 1.0.0
   * @api
   */
  public function clear(): void
  {
    $this->lines = [];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the generated code properly indented as a single string.
   *
   * @return string
   *
   * @since 1.0.0
   * @api
   */
  public function getCode(): string
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

      // If the line is a separator shorten the separator.
      if ($this->separator!==null && $line==$this->separator)
      {
        $line = $this->shortenSeparator($this->width - $this->indentation * $indentLevel);
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
   * Returns the last line of code (without indentation applied).
   *
   * @return string
   *
   * @since 1.1.0
   * @api
   */
  public function getLastLine(): string
  {
    if (empty($this->lines))
    {
      throw new \LogicException('No code in code store');
    }

    return $this->lines[count($this->lines) - 1];
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the raw code without indentation as an array of strings.
   *
   * @return string[]
   *
   * @since 1.0.0
   * @api
   */
  public function getRawCode(): array
  {
    return $this->lines;
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
   * @since 1.0.0
   * @api
   */
  abstract protected function indentationMode(string $line): int;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the separator to a required length.
   *
   * @param int $length The required length of the separator.
   *
   * @return string
   */
  protected function shortenSeparator(int $length): string
  {
    return substr($this->separator, 0, $length);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a line of code with the proper amount of indentationMode.
   *
   * @param string $line        The line of code.
   * @param int    $indentLevel The indentation level.
   *
   * @return string The indented line of code.
   */
  private function addIndentation(string $line, int $indentLevel): string
  {
    return ($line==='') ? '' : str_repeat(' ', $this->indentation * $indentLevel).$line;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Appends a line of code this this code.
   *
   * @param null|string $line The line of code to append. If null the line will be ignored.
   * @param bool        $trim If true the line of code will be trimmed before appending.
   */
  private function appendLine(?string $line, bool $trim): void
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
  private function appendLines(array $lines, bool $trim): void
  {
    foreach ($lines as $line)
    {
      $this->appendLine($line, $trim);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
