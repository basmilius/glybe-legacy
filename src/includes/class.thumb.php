<?php
Class Thumb {
	private $_imageSource;
	private $_savePath;
	private $_size;
	private $_image;
	private $_thumb;
	private $_extension;
	
	public function __construct() {
		$this->_imageSource = null;
		$this->_savePath = null;
		$this->_size = Array(32, 32);
	}
	
	public function savePath($path) {
		$this->_savePath = $path;
	}
	
	public function openImage($path) {
		$this->_imageSource = $path;
	}
	
	public function setSize($s) {
		$this->_size = Array($s, $s);
	}
	
	public function save() {
		$this->_extension = strrchr($this->_imageSource, ".");
		switch($this->_extension) {
			case ".gif":
				$this->_image = imagecreatefromgif($this->_imageSource);
				$this->createImage();
				break;
			case ".png":
				$this->_image = imagecreatefrompng($this->_imageSource);
				$this->createImage();
				break;
			case ".bmp":
				$this->_image = imagecreatefromwbmp($this->_imageSource);
				$this->createImage();
				break;
			case ".jpg":
			case ".jpeg":
				$this->_image = imagecreatefromjpeg($this->_imageSource);
				$this->createImage();
				break;
		}
	}
	
	public function draw() {
		$this->_extension = strrchr($this->_imageSource, ".");
		switch($this->_extension) {
			case ".gif":
				$this->_image = imagecreatefromgif($this->_imageSource);
				$this->drawImage();
				break;
			case ".png":
				$this->_image = imagecreatefrompng($this->_imageSource);
				$this->drawImage();
				break;
			case ".bmp":
				$this->_image = imagecreatefromwbmp($this->_imageSource);
				$this->drawImage();
				break;
			case ".jpg":
			case ".jpeg":
				$this->_image = imagecreatefromjpeg($this->_imageSource);
				$this->drawImage();
				break;
		}
	}
	
	private function resizeImage() {
		$old = Array();
		$new = Array();
		$old['w'] = imagesx($this->_image);
		$old['h'] = imagesy($this->_image);
		
		if($old['w'] < $old['h']) {
			$new['w'] = $this->_size[0];
			$new['h'] = ($old['h'] * ($this->_size[1] / $old['w']));
			$new['x'] = 0;
			$new['y'] = (0 - (($new['h'] - $new['w']) / 2));
		} else if($old['w'] > $old['h']) {
			$new['w'] = ($old['w'] * ($this->_size[0] / $old['h']));
			$new['h'] = $this->_size[1];
			$new['x'] = (0 - (($new['w'] - $new['h']) / 2));
			$new['y'] = 0;
		} else {
			$new['w'] = $this->_size[0];
			$new['h'] = $this->_size[1];
			$new['x'] = 0;
			$new['y'] = 0;
		}
		
		imagecopyresampled($this->_thumb, $this->_image, $new['x'], $new['y'], 0, 0, $new['w'], $new['h'], $old['w'], $old['h']);
	}
	
	private function addBackground() {
		$white = imagecolorallocate($this->_thumb, 255, 255, 255);
		imagefilledrectangle($this->_thumb, 0, 0, $this->_size[0], $this->_size[1], $white);
	}
	
	private function createImage() {
		if(!$this->_image) {
			throw new Exception("Error: Image not found!");
		}
		
		$this->_thumb = imagecreatetruecolor($this->_size[0], $this->_size[1]);
		$this->addBackground();
		$this->resizeImage();
		
		imagepng($this->_thumb, $this->_savePath);
	}
	
	private function drawImage() {
		if(!$this->_image) {
			throw new Exception("Error: Image not found!");
		}
		
		$this->_thumb = imagecreatetruecolor($this->_size[0], $this->_size[1]);
		$this->addBackground();
		$this->resizeImage();
		
		header("Content-Type: image/" . str_replace(".", "", $this->_extension));
		imagepng($this->_thumb);
		imagedestroy($this->_thumb);
	}
	
	public function destroy() {
		imagedestroy($this->_thumb);
	}
}
?>