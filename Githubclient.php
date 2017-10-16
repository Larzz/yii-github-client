<?php 
/**
* 
*/
class Githubclient extends CComponent
{

	private $OAUTH2_CLIENT_ID = 'b2f07536a7cdcb8f26ef';
	private $OAUTH2_CLIENT_SECRET = '0891ada88037606dd61142e27b56e5067f390c96';
	private $authorizeURL = 'https://github.com/login/oauth/authorize';
	private $tokenURL = 'https://github.com/login/oauth/access_token';
	private $apiURLBase = 'https://api.github.com/';


	public function init()
	{

	}

	public function LoginUrl($callback) {

		$params = array(
			'client_id' => $this->OAUTH2_CLIENT_ID,
			'redirect_uri' => $callback,
			'scope' => 'user',
			'state' => $this->SetSessionState() // get session state
			);

		// Redirect the user to Github's authorization page
		$login_url = 'https://github.com/login/oauth/authorize' . '?' . http_build_query($params);
		return $login_url;
	} 

	// setting the session state
	private function SetSessionState() {
		// $session = new Session;		
		$state = hash('sha256', microtime(TRUE).rand().$_SERVER['REMOTE_ADDR']);
		$_SESSION['state'] = $state;
		// $session->set('state', $state);
		return $state;
	}

	// getting the session of the state
	private function GetSessionState() {
		//$session = new Session;		
		$session = Yii::$app->session;
		$state =$_SESSION['state']; 
		return $state;
	}

	public function apiRequest($url, $post=FALSE, $headers=array()) {

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		if($post)
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

		$headers[] = 'Accept: application/json';
		$headers[] = 'User-Agent: Contrib Developers App';

		$access_token = $_SESSION['access_token']; 

		if($access_token)

			$headers[] = 'Authorization: Bearer ' . $access_token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		return json_decode($response);
	}

	private function get($key, $default=NULL) {
		return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
	}
	
	private function session($key, $default=NULL) {
		return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
	}

}


?>