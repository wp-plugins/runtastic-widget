<?php

	/*

	The MIT License (MIT)

	Copyright (c) 2014 Timo Schlueter <timo.schlueter@me.com>

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
	
	*/

	class Runtastic {
		private $loginUsername;
		private $loginPassword;
		private $loginUrl;
		private $logoutUrl;
		private $sessionsApiUrl;
		private $ch;
		private $cookieJar;
		private $authenticityToken;
		private $loggedIn;
		private $runtasticUsername;
		private $runtasticUid;
		private $runtasticToken;
		private $runtasticActivityIds;
		private $runtasticRawData;
		private $doc;
		private $timeout;
		
        public function getRawData(){
            return $this->runtasticRawData;  
            //return curl_error($this->ch);
        }
		public function __construct() {
			libxml_use_internal_errors(true);
			$this->loginUrl = "https://www.runtastic.com/en/d/users/sign_in.json";
			$this->logoutUrl = "https://www.runtastic.com/en/d/users/sign_out";
			$this->sessionsApiUrl = "https://www.runtastic.com/api/run_sessions/json";
			$this->ch = curl_init();
			$this->doc = new DOMDocument();
			$this->cookieJar = getcwd() . "/cookiejar";
			$this->runtasticToken = "";
			$this->loggedIn = false;
			$this->timeout = 10;
		}
  
 
		
		public function login() {
			$postData = array(
				"user[email]" => $this->loginUsername,
				"user[password]" => $this->loginPassword,
				"authenticity_token" => $this->runtasticToken
			);

			curl_setopt($this->ch, CURLOPT_URL, $this->loginUrl); 
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($this->ch,CURLOPT_POST, count($postData));
			curl_setopt($this->ch,CURLOPT_POSTFIELDS, $postData);
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookieJar);
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookieJar);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST , "false");
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER  , "false");

			$responseOutput = curl_exec($this->ch); 
			$responseStatus = curl_getinfo($this->ch);
			
			$responseOutputJson = json_decode($responseOutput);

			if ($responseStatus["http_code"] == 200) {
				$this->loggedIn = true;
				
				$this->doc->loadHTML($responseOutputJson->update);
				$inputTags = $this->doc->getElementsByTagName('input');
				foreach ($inputTags as $inputTag) {
					if ($inputTag->getAttribute("name") == "authenticity_token") {
						$this->runtasticToken = $inputTag->getAttribute("value");
					}
				}
			
				$aTags = $this->doc->getElementsByTagName('a');
				foreach ($aTags as $aTag) {
					if (preg_match("/https\:\/\/www\.runtastic\.com\/en\/users\/(.*)\/dashboard/", $aTag->getAttribute("href"), $matches)) {
						$this->runtasticUsername = $matches[1];
					}
				}
				
				$sessionsUrl = "https://www.runtastic.com/de/benutzer/" . $this->runtasticUsername . "/sportaktivitaeten";
			
				curl_setopt($this->ch, CURLOPT_URL, $sessionsUrl);
				curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
				curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookieJar);
				curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookieJar);
				curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'GET');
				curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
				
				$frontpageOutput = curl_exec($this->ch); 
				
				$this->doc->loadHTML($frontpageOutput);
				$scriptTags = $this->doc->getElementsByTagName('script');
						
				foreach ($scriptTags as $scriptTag) {
					if(strstr($scriptTag->nodeValue, 'index_data')) {
						$this->runtasticRawData = $scriptTag->nodeValue;
					}
				}

				preg_match("/uid: (.*)\,/", $this->runtasticRawData, $matches);
				$this->runtasticUid = $matches[1];
			
				$this->loggedIn = true;
				return true;
			} else {
				$this->loggedIn = false;
				return false;
			}
		}
		
		public function logout() {
			curl_setopt($this->ch, CURLOPT_URL, $this->logoutUrl);
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookieJar);
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookieJar);
			curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
			if (curl_exec($this->ch)) {
				curl_close($this->ch); 
				return true;
			} else {
				return false;
			}
		}
		
		public function setUsername($loginUsername) {
			$this->loginUsername = $loginUsername;
		}
		
		public function setPassword($loginPassword) {
			$this->loginPassword = $loginPassword;
		}
		
		public function setTimeout($timeout) {
			$this->timeout = $timeout;
		}
		
		public function getUid() {
			if ($this->loggedIn) {
				return $this->runtasticUid;
			} else {
				return false;
			}
		}
		
		public function getUsername() {
			if ($this->loggedIn) {
				return $this->runtasticUsername;
			} else {
				return false;
			}
		}
		
		public function getToken() {
			if ($this->loggedIn) {
				return $this->runtasticToken;
			} else {
				return false;
			}
		}
		
		public function getActivities() {
			if ($this->loggedIn) {			
				preg_match("/var index_data = (.*)\;/", $this->runtasticRawData, $matches);
				$itemJsonData = json_decode($matches[1]);
				
				foreach ($itemJsonData as $item) {
					$itemList .= $item[0] . ",";
				}
				
				$itemList = substr($itemList, 0, -1);

				$postData = array(
					"user_id" => $this->getUid(),
					"items" => $itemList,
					"authenticity_token" => $this->getToken()
				);
				
				curl_setopt($this->ch, CURLOPT_URL, $this->sessionsApiUrl);
				curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
				curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($this->ch,CURLOPT_POST, count($postData));
				curl_setopt($this->ch,CURLOPT_POSTFIELDS, $postData);
				curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookieJar);
				curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookieJar);
				curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
				
				$sessionsOutput = curl_exec($this->ch); 
				
				$sessionOutputJson = json_decode($sessionsOutput);
				//$this->logout();
				return $sessionOutputJson;
			} else {
				return false;
			}
			
		}
	}
?>