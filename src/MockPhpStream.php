<?php

/**
 * Allows you to override the php stream wrapper functionality
 *
 * This should be used sparingly and only when you need to mock php://input for a test
 */
class MockPhpStream
{
	/**
	 * The current context, or null. Required to be public so that PHP can populate it.
	 *
	 * @see {https://www.php.net/manual/en/class.streamwrapper.php}
	 * @var resource
	 */
	public $context;

	/**
	 * @var array<string, string> data for the given stream
	 */
	private static array $data = array();

	/**
	 * @var string the current file path
	 */
	private string $path;

	/**
	 * @var string the file content
	 */
	private string $content = '';

	/**
	 * @var int The current position in the content
	 */
	private int $index = 0;

	/**
	 * @var int The length of the content
	 */
	private int $length = 0;

	/**
	 * Registers this class as the 'php' stream wrapper
	 */
	public static function register()
	{
		stream_wrapper_unregister('php');
		stream_wrapper_register('php', self::class);
	}

	/**
	 * Removes this class as the registered stream wrapper for 'php'
	 */
	public static function restore()
	{
		stream_wrapper_restore('php');
	}

	// Stream functions

	public function stream_stat(): array|false
	{
		return array();
	}

	public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool
	{
		if (str_contains($mode, 'w')) {
			unset(self::$data[$path]);
		}

		if (isset(self::$data[$path])) {
			$this->content = self::$data[$path];
			$this->index   = 0;
			$this->length  = strlen($this->content);
		}

		$this->path = $path;

		return true;
	}

	public function stream_write(string $data): int
	{
		$this->content .= $data;
		$this->length  += strlen($data);

		return strlen($data);
	}

	public function stream_eof(): bool
	{
		return $this->index >= $this->length;
	}

	public function stream_read(int $count): string|false
	{
		if (empty($this->content)) {
			return '';
		}

		$length      = min($count, $this->length - $this->index);
		$data        = substr($this->content, $this->index, $length);
		$this->index += $length;

		return $data;
	}

	public function stream_close(): void
	{
		if (str_starts_with($this->path, "php://temp") || $this->path === "php://memory") {
			return;
		}

		if (!empty($this->content) && isset(self::$data)) {
			self::$data[$this->path] = $this->content;
		}
	}

	/**
	 * just returns seek success response - that's enough for basic tests
	 *
	 * NOTE: stream position pointer moving is not implemented
	 */
	public function stream_seek(int $offset, int $whence = SEEK_SET): bool
	{
		return true;
	}

	/**
	 * requred for testing code that uses fseek() on mocked stream
	 */
	public function stream_tell(): int
	{
		return $this->index;
	}
}