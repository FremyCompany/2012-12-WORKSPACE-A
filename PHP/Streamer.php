<?php
require_once($_SERVER['DOCUMENT_ROOT']."/PHP/init.php");
//require_once($_SERVER['DOCUMENT_ROOT']."/PHP/siteexplorer.php");

class File_Upload
{
	private $fileName;
	//File || Sign || Key || KeySign
	private $fileType;
	private $contentLength;
	private $path;
	private $user;

	public function __construct()
	{
		if (array_key_exists('HTTP_X_FILE_TYPE',$_SERVER) && array_key_exists('HTTP_X_FILE_NAME', $_SERVER) && array_key_exists('CONTENT_LENGTH', $_SERVER)) {
			$this->fileType =  $_SERVER['HTTP_X_FILE_TYPE'];
			$this->fileName = $_SERVER['HTTP_X_FILE_NAME'];
			$this->contentLength = $_SERVER['CONTENT_LENGTH'];
		} else throw new Exception("Error retrieving headers");
	}

	public function setDestination($p)
	{
		$this->path = $p;
	}

	public function receive()
	{
		if (!$this->contentLength > 0) {
			throw new Exception('No file uploaded!');
		}
		if (!is_dir($this->path."/".$this->fileType)) {
			mkdir($this->path."/".$this->fileType);
		}
		//createDir(safeDir($this->path."/".$this->fileType));	
		file_put_contents(
				$this->path."/".$this->fileType."/".$this->fileName,
				file_get_contents("php://input")
		);

		return true;
	}
}
