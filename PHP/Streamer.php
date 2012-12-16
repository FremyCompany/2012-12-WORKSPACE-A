<?php
require_once($_SERVER['DOCUMENT_ROOT']."/PHP/init.php");

class File_Upload
{
	private $file;
	
	private $fileName;
	//File || FileSign || Key || KeySign
	private $fileType;
	private $contentLength;
	private $path;
	private $user;

	public function __construct()
	{
		if (array_key_exists('HTTP_X_FILE',$_SERVER) && array_key_exists('HTTP_X_FILE_TYPE',$_SERVER) && array_key_exists('HTTP_X_FILE_NAME', $_SERVER) && array_key_exists('CONTENT_LENGTH', $_SERVER)) {
			$this->fileType =  $_SERVER['HTTP_X_FILE_TYPE'];
			$this->fileName = $_SERVER['HTTP_X_FILE_NAME'];
			$this->file = $_SERVER['HTTP_X_FILE'];
			if(array_key_exists('HTTP_X_FILE_USER',$_SERVER)){
				$this->user = $_SERVER['HTTP_X_FILE_USER']; 
			}
			else {
				$this->user = $_SESSION['login'];
			}
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
		if($this->fileType=="File" || $this->fileType=="FileSign"){
			$this->path=$this->path."/FILES";
			if (!is_dir($this->path)) {
				mkdir(safeDir($this->path));
			}
		}else if($this->fileType=="Key" || $this->fileType=="KeySign"){
			$this->path=$this->path."/KEYS";
			if (!is_dir($this->path)) {
				mkdir(safeDir($this->path));
			}			
			$this->path=$this->path."/".pathEncode($this->user);
			if (!is_dir($this->path)) {
				mkdir(safeDir($this->path));
			}

		}
		$this->path=$this->path."/".pathEncode($this->file);
		if (!is_dir($this->path)) {
			mkdir(safeDir($this->path));
		}
		if($this->fileType=="File" || $this->fileType=="Key")
		{
			$path=$this->path."/.data";
		}
		if($this->fileType=="FileSign" || $this->fileType=="KeySign") 
		{
			$path=$this->path."/.sign";
		}
		file_put_contents(
				safeDir($path),
				file_get_contents("php://input")
		);
		if(file_exists($this->path."/.data") && (file_exists($this->path."/.sign"))){
			//if(checkFileSign($this->path,$_SERVER['DOCUMENT_ROOT']."/USERS/".$_SESSION['login']."/public.key")){
				return 2;
			//}
			/*else{
			 	deleteFile($this->path);
				return 0;
			 }*/
		}else{
			return -1;
		}
	}
}
