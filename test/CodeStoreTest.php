<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Helper\CodeStore\Test;

//----------------------------------------------------------------------------------------------------------------------
class CodeStoreTest extends \PHPUnit_Framework_TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for illegal argument.
   *
   * @expectedException \InvalidArgumentException
   */
  public function testAppendObject()
  {
    $store = new ConcreteCodeStore();
    $store->append('begin');
    $store->append($this);
    $store->append('end');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test append additional code to the last line.
   */
  public function testAppendToLastLine()
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
  public function testClear()
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
  public function testEmpty()
  {
    $store = new ConcreteCodeStore();
    $code  = $store->getCode();
    $this->assertSame('', $code);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for get raw code.
   */
  public function testGetLines()
  {
    $store = new ConcreteCodeStore();

    $store->append('begin');
    $store->append('statement');
    $store->append('statement');
    $store->append('end');

    $expected = ['begin', 'statement', 'statement', 'end'];

    $code = $store->getLines();
    $this->assertEquals($expected, $code);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test indentation levels and null values with array.
   */
  public function testIndentationLevels1()
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
    $store->append($lines);
    $code = $store->getCode();

    $this->assertEquals($expected, $code);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test indentation levels and null values with single lines.
   */
  public function testIndentationLevels2()
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
  public function testSeparator()
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
  public function testTrim()
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
}

//----------------------------------------------------------------------------------------------------------------------
