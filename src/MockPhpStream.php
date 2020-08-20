<?php

/**
 * Allows you to override the php stream wrapper functionality
 *
 * This should be used sparingly and only when you need to mock php://input for a test
 */
class MockPhpStream
{
	private static $data = array();

	/**
	 * @var string the current file path
	 */
	private $path;

	/**
	 * @var string the file content
	 */
	private $content = '';

	/**
	 * @var int The current position in the content
	 */
	private $index = 0;

	/**
	 * @var int The length of the content
	 */
	private $length = 0;

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

	public function stream_stat()
	{
		return array();
	}

	public function stream_open($path, $mode, $options, &$opened_path)
	{
		if (strpos($mode, 'w') !== false) {
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

	public function stream_write($data)
	{
		$this->content .= $data;
		$this->length  += strlen($data);

		return strlen($data);
	}

	public function stream_eof()
	{
		return $this->index >= $this->length;
	}

	public function stream_read($count)
	{
		if (empty($this->content)) {
			return '';
		}

		$length      = min($count, $this->length - $this->index);
		$data        = substr($this->content, $this->index, $length);
		$this->index += $length;

		return $data;
	}

	public function stream_close()
	{
		if (!empty($this->content)) {
			if (isset(self::$data)) {
				self::$data[$this->path] = $this->content;
			}
		}
	}

    /**
     * stream_seek method mock implementation
     * just returns seek success response - that's enough for basic tests
     * stream position pointer moving is not implemented
     * @param $offset
     * @param int $whence
     * @return bool
     */
    public function stream_seek($offset, $whence = SEEK_SET)
    {
        return true;
    }

    /**
     * stream_tell method mock implementation
     * requred for testing code that uses fseek() on mocked stream
     * @return int
     */
    public function stream_tell()
    {
        return $this->index;
    }

}