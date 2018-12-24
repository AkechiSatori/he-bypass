<?php
namespace HENET;
class Core {
	public $headers;
	public $redirect_msg = '<html><body>You are being <a href="https://bgp.he.net/cc">redirected</a>.</body></html>';
	public function __construct() {
		$this->headers = array(
			'Connection' => 'keep-alive',
			'Upgrade-Insecure-Requests' => '1',
			'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
			'Referer' => 'https://bgp.he.net/cc',
			'Accept-Encoding' => 'gzip, deflate, br',
			'Accept-Language' => 'zh-CN,zh;q=0.9,ja-JP;q=0.8,ja;q=0.7,en-US;q=0.6,en;q=0.5',
		);
	}
	public function Request($url, $self_ip = false) {
		try {
			$curl = (new \Satori\cURL)->url($url)->header($this->headers)->go();
			if ($curl->data() == $this->redirect_msg) {
				$anticc_request = $this->_RequestAntiCC($curl->info()['response_cookie']['path'], $self_ip);
				$cookie = $anticc_request['response_cookie'];
				if (isset($cookie['_bgp_session'])) {
					return (new \Satori\cURL)->url($url)->cookie($cookie)->header($this->headers)->go();
				} else {
					throw $anticc_request;
				}
			} else {
				throw new Exception($curl->data(), -1);
			}
		} catch (\Exception $e) {
			throw $e;
		}
	}

	public function _RequestAntiCC($path, $self_ip = false) {
		$data = [
			'p' => md5(urldecode($path)),
			'i' => isset($self_ip) ? md5($self_ip) : md5($this->_RequestSelfIP()),
		];
		try {
			$curl = (new \Satori\cURL)->url("https://bgp.he.net/jc")->cookie([
				'path' => $path,
			])->header($this->headers)->post($data)->go();
			return $curl->info();
		} catch (\Exception $e) {
			throw $e;
		}
	}
	public function _RequestSelfIP() {
		try {
			$curl = (new \Satori\cURL)->url("https://bgp.he.net/i")->header($this->headers)->go();
			return $curl->data();
		} catch (\Exception $e) {
			throw $e;
		}
	}
}