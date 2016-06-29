<?php

class SpecialAkhet extends IncludableSpecialPage {

	public function __construct() {
		parent::__construct( 'Akhet' );
		include __DIR__ ."/../akhet-php-client/AkhetClient.php";
	}

	public function execute( $param = null ) {
		$out = "";
		try{
			if(is_null($param)){
				global $akhethosts;
				$out = "''Akhet''\n\nI'm being viewed as a Special Page\n\n";
				foreach ($akhethosts as $akhethost_id => $akhethost_data) {
					if($this->allowHost($akhethost_id)){
						$out .=	"\n\n" . "[[Speciale:Akhet/" . $akhethost_id . "|" . $akhethost_data['display_name'] . "]]";
					}
				}

			}else{
				if(strpos($param,"/")!==false){
					$akhethost_id = substr($param,0,strpos($param,"/"));
					$akhetimage = substr($param,strlen($akhethost_id)+1);
				}else{
					$akhethost_id = $param;
				}
				if($this->allowHost($akhethost_id)){
					global $akhethosts;
					if(isset($akhethosts[$akhethost_id])){
						$akhethost_data = $akhethosts[$akhethost_id];
					}
					$akhet = new AkhetClient\AkhetClient(
						$akhethost_data['api_hostname'],
						$akhethost_data['api_username'],
						$akhethost_data['api_password'],
						$akhethost_data['api_protocol']
					);
					if(strlen($akhetimage)>0){
						$user_data = $this->getUser();
						$out = "Your image is: " . $akhetimage;
						$token = $akhet->createInstance(
							 array(
					  		 "image" => $akhetimage,
								 "user" => $user_data->getName(),
								 "user_label" => $user_data->getName(),
							 )
						);
						//$token = "AKHET_TOKEN_slkdfjalkf";
						$out .= "<div id='akhetwait' data-akhethost='" . $akhethost_id . "' data-token='" . $token . "'>Please wait..</div>";

					}else{
						$akhet_images = $akhet->listImages();
  					foreach ($akhet_images as $akhet_image_key => $akhet_image_data) {
							foreach ($akhet_image_data->versions as $akhet_image_version) {
								$out .= "* [[Speciale:Akhet/" . $akhethost_id . "/". $akhet_image_key . ":" .$akhet_image_version . "|".$akhet_image_key . ":" .$akhet_image_version. "]]\n\n";
							}
					 	}
					}
				}else{
					$out = "You don't have the permission to access to this akhet host";
				}
			}
		}catch(Exception $e){
			$out = $e->getMessage();
		}
		if ( ! $this->including() ) {
			$this->setHeaders();
		}
		$this->getOutput()->addModules( 'Akhet.js' );
		$this->getOutput()->addWikiText( $out );
	}

	private function allowHost($akhethost_id,$akhethost_data=null){
		$allow_host = true;
		if(is_null($akhethost_data)){
			global $akhethosts;
			if(isset($akhethosts[$akhethost_id])){
				$akhethost_data = $akhethosts[$akhethost_id];
			} else {
				$allow_host =false;
			}
		}
		if($allow_host){
			$user_data = $this->getUser();

			$akhethost_acl = $akhethost_data['acl'];
			if(isset($akhethost_acl['requirelogin']) && $akhethost_acl['requirelogin']){
				if($user_data->isAnon()){
					$allow_host = false;
				}
			}
			if(isset($akhethost_acl['requiregroups'])){
				if(is_array($akhethost_acl['requiregroups'])){
					foreach ($akhethost_acl['requiregroups'] as  $req_group) {
						if(!in_array($req_group,$user_data->getEffectiveGroups())){
							$allow_host = false;
						}
					}
				}else{
					die("Check Akhet settings");
				}
			}
			if(isset($akhethost_acl['requirematch'])){
				$akhethost_requirematch = $akhethost_acl['requirematch'];
				if(isset($akhethost_requirematch['maildomain'])) {
					$mail_match = false;
					$email_explode = explode('@', $user_data->getEmail());
					$email_domain = array_pop($email_explode);
					foreach ($akhethost_requirematch['maildomain'] as $req_mail_domain) {
						$mail_match = $mail_match || ($req_mail_domain == $email_domain);
					}
					if(!$mail_match){
						$allow_host = false;
					}
				}
			}
		}
		return $allow_host;
	}

	public static function pollingInstance( $akhethost_id, $token ) {
		try {
			include __DIR__ ."/../akhet-php-client/AkhetClient.php";
			global $akhethosts;
			if(isset($akhethosts[$akhethost_id])){
				$akhethost_data = $akhethosts[$akhethost_id];
			}
			$akhet = new AkhetClient\AkhetClient(
				$akhethost_data['api_hostname'],
				$akhethost_data['api_username'],
				$akhethost_data['api_password'],
				$akhethost_data['api_protocol']
			);
			return json_encode($akhet->getInstanceInfo($token));
		} catch(Exception $e){
			return json_encode(array("error"=>$e->getMessage()));
		}
	}
}
