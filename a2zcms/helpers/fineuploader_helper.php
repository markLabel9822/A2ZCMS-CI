<?php

class Fineuploader {

	static $uploader;
	static $uploadDirectory;

	public static function init($uploadDirectory) {
		self::$uploader = new qqFileUploader( array('jpg'));
		self::$uploadDirectory = $uploadDirectory;
	}

	public static function getName() {
		return self::$uploader -> getName();
	}

	public static function getUploadName() {
		return self::$uploader -> getUploadName();
	}

	public static function upload($filename) {
		return self::$uploader -> handleUpload(self::$uploadDirectory, $filename);
	}

}

class qqUploadedFileXhr {
	private $inputName;

	/**
	 * @param string $inputName; defaults to the javascript default: 'qqfile'
	 */
	public function __construct($inputName = 'qqfile') {
		$this -> inputName = $inputName;
	}

	/**
	 * Save the file to the specified path
	 * @return boolean TRUE on success
	 */
	public function save($path) {
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);

		if ($realSize != $this -> getSize()) {
			return false;
		}

		$target = fopen($path, "w");
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);

		return true;
	}

	/**
	 * Get the original filename
	 * @return string filename
	 */
	public function getName() {
		return $_GET[$this -> inputName];
	}

	/**
	 * Get the file size
	 * @return integer file-size in byte
	 */
	public function getSize() {
		if (isset($_SERVER["CONTENT_LENGTH"])) {
			return (int)$_SERVER["CONTENT_LENGTH"];
		} else {
			throw new Exception('Getting content length is not supported.');
		}
	}

}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {
	private $inputName;

	/**
	 * @param string $inputName; defaults to the javascript default: 'qqfile'
	 */
	public function __construct($inputName = 'qqfile') {
		$this -> inputName = $inputName;
	}

	/**
	 * Save the file to the specified path
	 * @return boolean TRUE on success
	 */
	public function save($path) {
		return move_uploaded_file($_FILES[$this -> inputName]['tmp_name'], $path);
	}

	/**
	 * Get the original filename
	 * @return string filename
	 */
	public function getName() {
		return $_FILES[$this -> inputName]['name'];
	}

	/**
	 * Get the file size
	 * @return integer file-size in byte
	 */
	public function getSize() {
		return $_FILES[$this -> inputName]['size'];
	}

}

/**
 * Class that encapsulates the file-upload internals
 */
class qqFileUploader {
	private $allowedExtensions;
	private $sizeLimit;
	private $file;
	private $uploadName;

	/**
	 * @param array $allowedExtensions; defaults to an empty array
	 * @param int $sizeLimit; defaults to the server's upload_max_filesize setting
	 * @param string $inputName; defaults to the javascript default: 'qqfile'
	 */
	function __construct(array $allowedExtensions = null, $sizeLimit = null, $inputName = 'qqfile') {
		if ($allowedExtensions === null) {
			$allowedExtensions = array();
		}
		if ($sizeLimit === null) {
			$sizeLimit = $this -> toBytes(ini_get('upload_max_filesize'));
		}

		$allowedExtensions = array_map("strtolower", $allowedExtensions);

		$this -> allowedExtensions = $allowedExtensions;
		$this -> sizeLimit = $sizeLimit;

		$this -> checkServerSettings();

		if (!isset($_SERVER['CONTENT_TYPE'])) {
			$this -> file = false;
		} else if (strpos(strtolower($_SERVER['CONTENT_TYPE']), 'multipart/') === 0) {
			$this -> file = new qqUploadedFileForm($inputName);
		} else {
			$this -> file = new qqUploadedFileXhr($inputName);
		}
	}

	/**
	 * Get the name of the uploaded file
	 * @return string
	 */
	public function getUploadName() {
		if (isset($this -> uploadName))
			return $this -> uploadName;
	}

	/**
	 * Get the original filename
	 * @return string filename
	 */
	public function getName() {
		if ($this -> file)
			return $this -> file -> getName();
	}

	/**
	 * Internal function that checks if server's may sizes match the
	 * object's maximum size for uploads
	 */
	private function checkServerSettings() {
		$postSize = $this -> toBytes(ini_get('post_max_size'));
		$uploadSize = $this -> toBytes(ini_get('upload_max_filesize'));

		if ($postSize < $this -> sizeLimit || $uploadSize < $this -> sizeLimit) {
			$size = max(1, $this -> sizeLimit / 1024 / 1024) . 'M';
			die(json_encode(array('error' => 'increase post_max_size and upload_max_filesize to ' . $size)));
		}
	}

	/**
	 * Convert a given size with units to bytes
	 * @param string $str
	 */
	private function toBytes($str) {
		$val = trim($str);
		$last = strtolower($str[strlen($str) - 1]);
		switch($last) {
			case 'g' :
				$val *= 1024;
			case 'm' :
				$val *= 1024;
			case 'k' :
				$val *= 1024;
		}
		return $val;
	}

	/**
	 * Handle the uploaded file
	 * @param string $uploadDirectory
	 * @param string $replaceOldFile=true
	 * @returns array('success'=>true) or array('error'=>'error message')
	 */
	function handleUpload($uploadDirectory, $filename) {
		if (!is_writable($uploadDirectory)) {
			return array('error' => "Server error. Upload directory isn't writable.");
		}

		if ($filename == null) {
			return array('error' => "No filename");
		}

		if (!$this -> file) {
			return array('error' => 'No files were uploaded.');
		}

		$size = $this -> file -> getSize();

		if ($size == 0) {
			return array('error' => 'File is empty');
		}

		if ($size > $this -> sizeLimit) {
			return array('error' => 'File is too large');
		}

		if (file_exists($uploadDirectory . DIRECTORY_SEPARATOR . $filename)) {
			return array('error' => 'File exists');
		}

		//$pathinfo = pathinfo($this->file->getName());
		//$filename = $pathinfo['filename'];
		//$filename = md5(uniqid());
		//$ext = @$pathinfo['extension'];		// hide notices if extension is empty

		/*if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
		 $these = implode(', ', $this->allowedExtensions);
		 return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
		 }*/

		//$ext = ($ext == '') ? $ext : '.' . $ext;

		$this -> uploadName = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;

		if ($this -> file -> save($this -> uploadName)) {
			return array('success' => true);
		} else {
			return array('error' => 'Could not save uploaded file.' . 'The upload was cancelled, or server error encountered');
		}

	}

}
