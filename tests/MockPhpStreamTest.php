<?php

class MockPhpStreamTest
	extends \PHPUnit\Framework\TestCase
{
	public function setUp(): void
	{
		stream_wrapper_register('mps', MockPhpStream::class);
	}

	public function tearDown(): void
	{
		stream_wrapper_unregister('mps');
	}

	public function testStreamIndividualCommands()
	{
		$f1 = fopen('mps://input', 'w+');
		fwrite($f1, 'test');
		fclose($f1);

		self::assertEquals('test', file_get_contents('mps://input'));
	}

	public function testOpenTruncatesForFilePutContents()
	{
		file_put_contents('mps://request-body', 'kakaw');
		file_put_contents('mps://request-body', 'kakaw');
		self::assertEquals('kakaw', file_get_contents('mps://request-body'));
	}

	public function testSlimRequestBodyMethod()
	{
		// immedate Slim's RequestBody copy of php://input to php://temp
		file_put_contents('mps://input', 'test');
		$s = fopen("mps://temp", "w+");
		stream_copy_to_stream(fopen("mps://input", 'r'), $s);
		rewind($s);

		self::assertEquals('te', fread($s, 2));
	}

	public function testPhpOverride()
	{
		MockPhpStream::register();

		self::assertEquals(5, file_put_contents("php://input", 'kakaw'));
		self::assertEquals('kakaw', file_get_contents("php://input"));

		MockPhpStream::restore();

		// because the built-in is read-only, we'd get an error/warning from PHP itself hences the suppression
		self::assertFalse(@file_put_contents("php://input", 'bird'));
	}

	/**
	 * php://temp is basically a new file evey time it's called
	 */
	public function testPhpTempIsNotPersistent()
	{
		MockPhpStream::register();

		self::assertEquals(5, file_put_contents("php://temp", 'kakaw'));
		self::assertEmpty(file_get_contents("php://temp"));

		// include the maxmemory option as well
		self::assertEquals(5, file_put_contents('php://temp/maxmemory:4', 'kakaw'));
		self::assertEmpty(file_get_contents('php://temp/maxmemory:4'));

		MockPhpStream::restore();
	}

	public function testPhpMemoryIsNotPersistent()
	{
		MockPhpStream::register();

		self::assertEquals(5, file_put_contents("php://memory", 'kakaw'));
		self::assertEmpty(file_get_contents("php://memory"));

		MockPhpStream::restore();
	}
}