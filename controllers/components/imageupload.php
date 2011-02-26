<?php
App::import('Core', 'Inflector');
class ImageuploadComponent extends Object {
	
	var $config = array(
		'upload_dir' => 'uploads',
		'rm_original_file' => false,
		'quality' => 100,
		'thumbnails' => array(
			'big' => array(600, 800, 'resize'),
			'med' => array(300, 400, 'resizeCrop'),
			'small' => array(100,  100, 'resizeCrop')
		)
	);
	
	function initialize(&$controller, $config){
		$this->config = array_merge(
			$this->config,
			$config
		);
	}
	
	function upload(&$data){
		
		return $this->upload_FS($data);
	}
	
	function upload_FS(&$data){
		$error = 0;
		$tmp_uploaddir  = WWW_ROOT.$this->config['upload_dir'].DS.'tmp';
		
		if(!is_dir($tmp_uploaddir)){
			mkdir($tmp_uploaddir, 0755, true);
		}
		
		$file_type = end(split('\.', $data['name']));
		$file_name = String::uuid();
		settype($file_name, 'string');
		$file_name .= '.' . $file_type;

		/* Security check */
		if (!is_uploaded_file($data['tmp_name'])) {
			return $this->log_cakephp_error_and_return('Error uploading file (sure it was a POST request?).');
		}
		
		if($this->is_image($file_type)){
			$this->copy_or_log_error($data['tmp_name'], $tmp_uploaddir, $file_name);
			foreach($this->config['thumbnails'] as $size_name => $options){
				$this->thumbnail($tmp_uploaddir.DS.$file_name, $size_name, $options[0], $options[1], $options[2]);
			}
			
			if(!$this->config['rm_original_file']){
				$this->copy_or_log_error($tmp_uploaddir.DS.$file_name, WWW_ROOT.$this->config['upload_dir'], $file_name);
			}
			unlink($tmp_uploaddir.DS.$file_name);
		}
		else {
			return $this->log_cakephp_error_and_return('File type not allowed (only images files).');
		}
		return $file_name;
	}
	
	function thumbnail($tmp_file, $size_name, $max_width, $max_height, $crop = 'resize') {
		$file_name = end(split(DS, $tmp_file));
		$this->resize_image($crop, $tmp_file, $size_name, $file_name, $max_width, $max_height, $this->config['quality']);
	}
	
	function resize_image($crop_type = 'resize', $tmp_file, $size_name, $dst_name = false, $new_width = false, $new_height = false, $quality = 100) {
		$original_image = $tmp_file;
		list($original_width, $original_height, $type) = getimagesize($original_image);
		$ext = $this->image_type_to_extension($type);
		
		$destination_image = WWW_ROOT.$this->config['upload_dir'].DS.$size_name.'_'.$dst_name;

		/* Check if something is requested, otherwise do not resize */
		if ($new_width or $new_height) {
			/* Delete tmp file if it exists */
			if (file_exists($destination_image)) {
				unlink($dstimg);
			} else {
				switch ($crop_type) {
				default:
				case 'resize':
					// Maintains the aspect ratio of the image and makes sure
					// that it fits within the maxW and maxH
					$width_scale  = 2;
					$height_scale = 2;

					/* Check if we're overresizing (or set new scale) */
					if ($new_width) {
						if ($new_width > $original_width) $new_width = $original_width;
						$width_scale = $new_width / $original_width;
					}
					if ($new_height) {
						if ($new_height > $original_height) $new_height = $original_height;
						$height_scale = $new_height / $original_height;
					}
					if ($width_scale < $height_scale) {
						$max_width  = $new_width;
						$max_height = false;
					} elseif ($width_scale > $height_scale ) {
						$max_height = $new_height;
						$max_width  = false;
					} else {
						$max_height = $new_height;
						$max_width  = $new_width;
					}

					if ($max_width > $max_height){
						$apply_width  = $max_width;
						$apply_height = ($original_height * $apply_width) / $original_width;
					} elseif ($max_height > $max_width) {
						$apply_height = $max_height;
						$apply_width  = ($apply_height * $original_width) / $original_height;
					} else {
						$apply_width  = $max_width;
						$apply_height = $max_height;
					}
					$start_x = 0;
					$start_y = 0;
					break;

				case 'resizeCrop':
					/* Check if we're overresizing (or set new scale) */
					/* resize to max, then crop to center */
					if ($new_width > $original_width) $new_width = $original_width;
						$ratio_x = $new_width / $original_width;

					if ($new_height > $original_height) $new_height = $original_height;
						$ratio_y = $new_height / $original_height;

					if ($ratio_x < $ratio_y) {
						$start_x = round(($original_width - ($new_width / $ratio_y)) / 2);
						$start_y = 0;
						$original_width  = round($new_width / $ratio_y);
						$original_height = $original_height;
					} else {
						$start_x = 0;
						$start_y = round(($original_height - ($new_height / $ratio_x)) / 2);
						$original_width  = $original_width;
						$original_height = round($new_height / $ratio_x);
					}
					$apply_width  = $new_width;
					$apply_height = $new_height;
					break;

				case 'crop':
					// straight centered crop
					$start_y = ($original_height - $new_height) / 2;
					$start_x = ($original_width - $new_width) / 2;
					$original_height = $new_height;
					$apply_height = $new_height;
					$original_width = $new_width;
					$apply_width = $new_width;
					break;
				}

				switch($ext) {
				case 'gif' :
					$old_image = imagecreatefromgif($original_image);
					break;
				case 'png' :
					$old_image = imagecreatefrompng($original_image);
					break;
				case 'jpg' :
				case 'jpeg' :
					$old_image = imagecreatefromjpeg($original_image);
					break;
				default :
					// image type is not a possible option
					return false;
					break;
				}

				// Create new image
				$new_image = imagecreatetruecolor($apply_width, $apply_height);
				// Put old image on top of new image
				imagealphablending($new_image, false);
				imagesavealpha($new_image, true);
				imagecopyresampled($new_image, $old_image, 0, 0, $start_x, $start_y, $apply_width, $apply_height, $original_width, $original_height);

				switch($ext) {
				case 'gif' :
					imagegif($new_image, $destination_image, $quality);
					break;
				case 'png' :
					imagepng($new_image, $destination_image, round($quality/10));
					break;
				case 'jpg' :
				case 'jpeg' :
					imagejpeg($new_image, $destination_image, $quality);
					break;
				default :
					return false;
					break;
				}

				imagedestroy($new_image);
				imagedestroy($old_image);
				return true;
			}
		} else { /* Nothing requested */
			return false;
		}
	}
	
	function copy_or_log_error($tmp_name, $dst_folder, $dst_filename){
		if (is_writeable($dst_folder)) {
			if (!copy($tmp_name, $dst_folder.DS.$dst_filename)) {
				unset($dst_filename);
				return $this->log_cakephp_error_and_return('Error uploading file.', 'publicaciones');
			}
		} else {
			// if dst_folder not writeable, let developer know
			debug('You must allow proper permissions for image processing. And the folder has to be writable.');
			debug("Run 'chmod 755 $dst_folder', and make sure the web server is it's owner.");
			return $this->log_cakephp_error_and_return('No write permissions on attachments folder.');
		}
	}
	
	function log_cakephp_error_and_return($msg) {
		$_error["{$this->config['default_col']}_file_name"] = $msg;
		$this->controller->{$this->controller->modelClass}->validationErrors = array_merge($_error, $this->controller->{$this->controller->modelClass}->validationErrors);
		$this->log($msg, 'attachment-component');
		return false;
	}
	
	function is_image($file_type) {
		$image_types = array('jpeg', 'jpg', 'gif', 'png');
		return in_array(strtolower($file_type), $image_types);
	}
	
	function delete($filename){
		if(is_file(WWW_ROOT.$this->config['upload_dir'].DS.$filename)) {
			unlink(WWW_ROOT.$this->config['upload_dir'].DS.$filename);
		}
		
		foreach($this->config['thumbnails'] as $size_name => $options){
			unlink(WWW_ROOT.$this->config['upload_dir'].DS.$size_name.'_'.$filename);
		}
	}
	
	function image_type_to_extension($image_type) {
		if (empty($image_type)) return false;
		switch($image_type) {
			case IMAGETYPE_TIFF_II : return 'tiff';
			case IMAGETYPE_TIFF_MM : return 'tiff';
			case IMAGETYPE_GIF  : return 'gif';
			case IMAGETYPE_JPEG : return 'jpg';
			case IMAGETYPE_PNG  : return 'png';
			case IMAGETYPE_SWF  : return 'swf';
			case IMAGETYPE_PSD  : return 'psd';
			case IMAGETYPE_BMP  : return 'bmp';
			case IMAGETYPE_JPC  : return 'jpc';
			case IMAGETYPE_JP2  : return 'jp2';
			case IMAGETYPE_JPX  : return 'jpf';
			case IMAGETYPE_JB2  : return 'jb2';
			case IMAGETYPE_SWC  : return 'swc';
			case IMAGETYPE_IFF  : return 'aiff';
			case IMAGETYPE_WBMP : return 'wbmp';
			case IMAGETYPE_XBM  : return 'xbm';
			default             : return false;
		}
	}
}
?>