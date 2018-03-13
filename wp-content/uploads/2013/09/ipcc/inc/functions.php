<?php

function getExtension($filename) {
	return substr(strrchr($filename, '.'), 1);
}

function get_existing_images() {
	$images = array();
	$path = './dest';
	$d = dir($path);
	while (false !== ($entry = $d->read())) {
	  	if(is_file($path.'/'.$entry)) {
			$ext = getExtension($entry);
			if($ext=='xml') {
				$images[] = basename($entry, ".xml"); ;
			}
		}
	}
	$d->close();
	
	return $images;
}	

function processUpload() {
	if (empty($_FILES["upload"]["name"])) {
		set_message('Error. Please select image for upload.', 'error');
		header('Location: '.$_SERVER['HTTP_REFERER']);
		die();
	}
	
	$uploads_dir = 'source';
    if ($_FILES["upload"]["error"] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES["upload"]["tmp_name"];
        $name = $_FILES["upload"]["name"];
		$ext = getExtension($name);
		if($_FILES["upload"]["type"]!='image/jpeg' || !in_array($ext, array('jpg', 'jpeg'))) {
			set_message('Error. Incorrect file format. Only "jpg" supported.', 'error');
		}
        if(!move_uploaded_file($tmp_name, "$uploads_dir/$name")) {
			set_message('Error. Can not move file.', 'error');
		}
		else {
			//generate tiles
			require_once('lib/Oz/Deepzoom/ImageCreator.php');
			$converter = new Oz_Deepzoom_ImageCreator();
			$tilename = str_replace('.'.$ext,'',$name);
			$converter->create( realpath($uploads_dir.'/'.$name), 'dest/'.$tilename.'.xml');
			
			//save thumb
			$levelImage = $converter->getImage(10);
			$levelImage->save( 'dest/'.$tilename.'_files/thumb.jpg');
			
			set_message('Success. Tiles has been generated. Please select your image from select list.');
		}
    }
	else {
		set_message('Error. Some error occurs.', 'error');
	}
	header('Location: '.$_SERVER['HTTP_REFERER']);
	die();
}

function set_message($message = NULL, $type = 'status', $repeat = TRUE) 
{
	if ($message) {
		if (!isset($_SESSION['messages'])) {
			$_SESSION['messages'] = array();
		}
		if (!isset($_SESSION['messages'][$type])) {
			$_SESSION['messages'][$type] = array();
		}
		if ($repeat || !in_array($message, $_SESSION['messages'][$type])) {
			$_SESSION['messages'][$type][] = $message;
		}
	}
	// messages not set when DB connection fails
	return isset($_SESSION['messages']) ? $_SESSION['messages'] : NULL;
}

function get_messages($type = NULL, $clear_queue = TRUE) 
{
	if ($messages = set_message()) {
		if ($type) {
			if ($clear_queue) {
				unset($_SESSION['messages'][$type]);
			}
			if (isset($messages[$type])) {
				return array($type => $messages[$type]);
			}
		}
		else {
			if ($clear_queue) {
				unset($_SESSION['messages']);
			}
			return $messages;
		}
	}
	return array();
}

function show_messages($display = NULL) 
{
	$output = '';
	foreach (get_messages($display) as $type => $messages) {
		$output .= '<div class="messages '.$type.'">'."\n";
		if (count($messages) > 1) {
			$output .= ' <ul>'."\n";
			foreach ($messages as $message) {
				$output .= '  <li>' . $message . '</li>'."\n";
			}
			$output .= ' </ul>'."\n";
		}
		else {
			$output .= $messages[0];
		}
		$output .= '</div>'."\n";
	}
	return $output;
}
