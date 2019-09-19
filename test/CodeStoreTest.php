<?php
declare(strict_types=1);

namespace SetBased\Helper\CodeStore\Test;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class CodeStore.
 */
class CodeStoreTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for illegal argument.
   */
  public function testAppendObject(): void
  {
    $this->expectException(\InvalidArgumentException::class);

    $store = new ConcreteCodeStore();
    $store->append('begin');
    $store->append($this);
    $store->append('end');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test append additional code to the last line.
   */
  public function testAppendToLastLine(): void
  {
    $store = new ConcreteCodeStore();
    $store->append('begin');
    $store->append('statement');
    $store->appendToLastLine(' # comment');
    $store->append('end');
    $code = $store->getCode();

    $expected = <<< EOL
begin
  statement # comment
end

EOL;

    $this->assertEquals($expected, $code);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for clearing the code store.
   */
  public function testClear(): void
  {
    $store = new ConcreteCodeStore();

    $store->append('begin');
    $store->append('bug1');
    $store->append('bug2');
    $store->append('end');

    $store->clear();

    $code = $store->getCode();

    $this->assertSame('', $code);

    $store->append('begin');
    $store->append('statement');
    $store->append('statement');
    $store->append('end');

    $expected = <<< EOL
begin
  statement
  statement
end

EOL;

    $code = $store->getCode();
    $this->assertEquals($expected, $code);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test empty code store returns empty string.
   */
  public function testEmpty(): void
  {
    $store = new ConcreteCodeStore();
    $code  = $store->getCode();
    $this->assertSame('', $code);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for get raw code.
   */
  public function testGetLines(): void
  {
    $store = new ConcreteCodeStore();

    $store->append('begin');
    $store->append('statement');
    $store->append('statement');
    $store->append('end');

    $expected = ['begin', 'statement', 'statement', 'end'];

    $code = $store->getRawCode();
    $this->assertEquals($expected, $code);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test indentation levels and null values with array.
   */
  public function testIndentationLevels1(): void
  {
    $lines = ['statement',
              'BEGIN',
              'statement',
              'begin',
              'statement',
              'BEGIN',
              'statement',
              null,
              'statement',
              'MIDDLE',
              'statement',
              'statement',
              'END',
              'statement',
              'statement',
              'middle',
              'statement',
              'heredoc-start',
              'line1',
              'line2',
              'line3',
              'heredoc-end',
              'statement',
              'end',
              'begin',
              'begin',
              'statement',
              'end-end',
              'END',
              'statement'];

    $expected = <<< EOL
statement
  BEGIN
  statement
  begin
    statement
      BEGIN
      statement
      statement
        MIDDLE
      statement
      statement
      END
    statement
    statement
  middle
    statement
    heredoc-start
line1
line2
line3
heredoc-end
    statement
  end
  begin
    begin
      statement
  end-end
  END
statement

EOL;

    $store = new ConcreteCodeStore();
    $store->append($lines);
    $code = $store->getCode();

    $this->assertEquals($expected, $code);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test indentation levels and null values with single lines.
   */
  public function testIndentationLevels2(): void
  {
    $lines = ['statement',
              'BEGIN',
              'statement',
              'begin',
              'statement',
              'BEGIN',
              'statement',
              null,
              'statement',
              'MIDDLE',
              'statement',
              'statement',
              'END',
              'statement',
              'statement',
              'middle',
              'statement',
              'statement',
              'end',
              'END',
              'statement'];

    $expected = <<< EOL
statement
  BEGIN
  statement
  begin
    statement
      BEGIN
      statement
      statement
        MIDDLE
      statement
      statement
      END
    statement
    statement
  middle
    statement
    statement
  end
  END
statement

EOL;

    $store = new ConcreteCodeStore();

    foreach ($lines as $line)
    {
      $store->append($line);
    }
    $code = $store->getCode();

    $this->assertEquals($expected, $code);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for separator.
   */
  public function testSeparator(): void
  {
    $store = new ConcreteCodeStore();

    $store->appendSeparator();
    $store->append('begin');
    $store->append('statement');
    $store->appendSeparator();
    $store->append('statement');
    $store->append('end');
    $store->appendSeparator();

    $expected = <<< EOL
#--------------
begin
  statement
  #------------
  statement
end
#--------------

EOL;

    $code = $store->getCode();
    $this->assertEquals($expected, $code);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test trimming and not trimming whitespace.
   */
  public function testTrim(): void
  {
    $store = new ConcreteCodeStore();
    $store->append('begin');
    $store->append('  statement  ');
    $store->append('  statement  ', false);
    $store->appendToLastLine('# 2 spaces');
    $store->append('end');
    $code = $store->getCode();

    $expected = <<< EOL
begin
  statement
    statement  # 2 spaces
end

EOL;

    $this->assertEquals($expected, $code);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test getLastLine.
   */
  public function testGetLastLine1(): void
  {
    $store = new ConcreteCodeStore();
    $store->append('begin');
    $store->append('statement1');
    $store->append('statement2');

    $this->assertEquals('statement2', $store->getLastLine());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test getLastLine with empty code store.
   */
  public function testGetLastLine2(): void
  {
    $this->expectException(\LogicException::class);

    $store = new ConcreteCodeStore();
    $store->getLastLine();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
