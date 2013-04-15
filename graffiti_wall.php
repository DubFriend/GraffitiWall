<?php

class Graffiti_Wall {
	const MAX_X = 1600;
	const MAX_Y = 1066;
	const MAX_RADIUS = 50;

	private $filePath;

	function __construct($jsonFilePath) {
		$this->filePath = $jsonFilePath;
	}

	function get_json_data() {
		$file_handle = fopen($this->filePath, "r");
		$data = "";
		while (!feof($file_handle)) {
			$data .= fgets($file_handle);
		}
		fclose($file_handle);
		return $data;
	}

	function is_json_valid($Json) {
		$isValid = TRUE;
		$validKeys = array("x", "y", "c", "s");
		$Json = json_decode($Json, TRUE);
		if(count($Json) > 0) {
			foreach($Json as $entry) {

				foreach($entry as $key => $value) {
					if(!in_array($key, $validKeys)) {
						$isValid = FALSE;
						return $isValid;
					}
				}

				if(
				!isset($entry['x']) ||
				!isset($entry['y']) ||
				!is_numeric($entry['x']) ||
				!is_numeric($entry['y'])) {
					$isValid = FALSE;
					break;
				}
				if($entry['x'] < 0 || $entry['x'] > self::MAX_X) {
					$isValid = FALSE;
					break;
				}
				if($entry['y'] < 0 || $entry['y'] > self::MAX_Y) {
					$isValid = FALSE;
					break;
				}
				if(isset($entry['c'])) {
					if(! preg_match('/^[0-9A-F]{6}$/', $entry['c'])) {
						$isValid = FALSE;
						break;
					}
				}
				if(isset($entry['s'])) {
					if(!is_numeric($entry['s'])) {
						$isValid = FALSE;
						break;
					}
					if($entry['s'] < 1 || $entry['s'] > self::MAX_RADIUS) {
						$isValid = FALSE;
						break;
					}
				}
			}
		}
		else {
			$isValid = FALSE;
		}
		return $isValid;
	}

	function save_painting($json) {
		if($this->is_json_valid($json)) {
			$oldData = $this->get_json_data();
			if(!$oldData || $oldData == "null") {
				$oldData = "[]";
			}
			$fp = fopen($this->filePath, 'w');
			fwrite(
				$fp,
				json_encode(
					array_merge(
						json_decode($oldData, TRUE),
						json_decode($json, TRUE)
					)
				)
			);
			fclose($fp);
		}
	}
}
?>