<?php

class Vimeo_class implements Vimeo {

	private $token;
	private $error = "";

	function __construct($setting) {
		$this->token = $setting["vimeo_access_token"];
	}

	function get_vimeo_id_from_URL($url) {
		// Check if the input is already a numeric ID
		if (is_numeric($url)) {
			return $url; // Return the input directly as it is already a Vimeo ID
		}

		// Regular expression pattern to match different Vimeo URL formats
		$pattern = '/vimeo\.com\/(?:manage\/videos\/|video\/|)(\d+)|player\.vimeo\.com\/video\/(\d+)/';

		// Use preg_match to find the Vimeo ID in the URL
		if (preg_match($pattern, $url, $matches)) {
			// Check if the first or second capturing group holds the Vimeo ID
			if (isset($matches[1]) && $matches[1] !== '') {
				return $matches[1]; // Standard, manage, or short Vimeo URL
			} elseif (isset($matches[2]) && $matches[2] !== '') {
				return $matches[2]; // Embed Vimeo URL
			}
		}

		// Return false if no valid ID is found
		return false;
	}

	function upload($download_url, $file_size = null) {

		// filesize
		if ($file_size == null) {
			$file_size = $this->getVideoFileSize($download_url);
		}

		// Step 1: Request an upload URL
		$url = "https://api.vimeo.com/me/videos";

		$data = [
		    "upload" => [
			"approach" => "pull",
			"size" => $file_size, // Get file size
			"link" => $download_url
		    ]
		];

		// Initialize cURL session to request upload URL
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		    "Authorization: Bearer $this->token",
		    "Content-Type: application/json",
		]);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

			$response = curl_exec($ch);
			$error = curl_error($ch);

		if ($error) {
			return "cURL Error: " . $error;
		}

		$response_data = json_decode($response, true);

		$uri_moto = $response_data["uri"]; // e.g., /videos/12345
		// Extract the Vimeo ID by removing '/videos/' from the URI
		$vimeo_id = str_replace('/videos/', '', $uri_moto);

		// Handle the response or error
		return $vimeo_id;
	}

	function getVideoFileSize($url) {
		$curl = curl_init();

		// CURLオプションの設定
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_NOBODY, true); // ヘッダーのみ取得する
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // リダイレクトをたどる
		curl_setopt($curl, CURLOPT_MAXREDIRS, 10); // リダイレクト回数制限
		// 実行
		$response = curl_exec($curl);
		$size = 0;

		if ($response !== false) {
			// ヘッダー情報を解析
			$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);

			// Content-Lengthを探す
			if (preg_match('/Content-Length: (\d+)/', $header, $matches)) {
				$size = (int) $matches[1];
			}
		}

			return $size;
	}

	function edit($vimeo_id, $title, $description): bool {
		
		if(empty($vimeo_id)){
			$this->error = "vimeo_id is empty";
			return false;
		}

		if (empty($title)) {
			$title = "Undefined";
		}

		if (empty($description)) {
			$description = "";
		}

		$url = "https://api.vimeo.com/videos/" . $vimeo_id;

		$data = [
		    "name" => $title,
		    "description" => $description,
		    "privacy" => [
			"view" => "disable",
			"download" => false
		    ],
		    "embed" => [
			"logos" => [
			    "vimeo" => false
			],
			"buttons" => [
			    "share" => false,
			    "like" => false,
			    "watchlater" => false,
			    "embed" => false
			],
			"title" => [
			    "name" => "hide",
			    "owner" => "hide",
			    "portrait" => "hide"
			]
		    ]
		];

		// Initialize cURL session
		$ch = curl_init($url);

		// Set options for the cURL session
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		    "Authorization: Bearer $this->token",
		    "Content-Type: application/json"
		]);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		// Handle the response or error
		return $this->check_error_and_close_curl($ch);
	}

	function delete($vimeo_id): bool {

		$url = "https://api.vimeo.com/videos/" . $vimeo_id;

		// Initialize cURL session
		$ch = curl_init($url);

		// Set options for the cURL session
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		    "Authorization: Bearer $this->token"
		]);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

		// Handle the response or error
		return $this->check_error_and_close_curl($ch);
	}

	function get_error(): string {
		return $this->error;
	}

	private function check_error_and_close_curl($ch) {
		// Execute the cURL session
			$response = curl_exec($ch);
			$error = curl_error($ch);

		// Handle cURL error
		if ($error) {
			$this->error = "cURL Error: " . $error;
			return false;
		}

		// Decode the response as JSON
		$response_data = json_decode($response, true);

		// Check for API-specific error
		if (isset($response_data['error'])) {
			$this->error = "API Error: " . $response_data['error'] . " For Developer:" . $response_data["developer_message"];
			return false;
		}

		// Success
		return true;
	}

	function tmpfile_with_data($data) {
		$tmp = tmpfile();
		fwrite($tmp, $data);
		rewind($tmp);
		return $tmp;
	}
}
