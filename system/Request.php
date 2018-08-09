<?php 

class Request {

	public static $base_url = '/';
	public static $index_file = 'index.php';
    public static $user_agent = '';
	public static $client_ip = '0.0.0.0';
    public static $trusted_proxies = array('127.0.0.1', 'localhost', 'localhost.localdomain');
	public static $current;
	
	protected static $initial = false;
	protected static $_app = false;
	
	protected static $_route;
	protected static $_routes;
	protected static $_cache_time = 10;
	protected static $controller_prefix = 'Controller';
	protected static $action_prefix = 'action';
	protected static $_is_mobile = false;
	
	protected $default_route = array('controller'=>'Index', 'action'=>'Index');
	
	protected $_requested_with;
    protected $_method;
	protected $_protocol;
	protected $_secure;
	protected $_referrer;
	protected $_response;
	protected $_header;
	protected $_body;
	protected $_directory = '';
	protected $_controller;
	protected $_action;
	protected $_uri;
	protected $_external = FALSE;
	protected $_params = array();
	protected $_get = array();
	protected $_post = array();
	protected $_cookies = array();
	protected $_client;
	
	public function __construct() {
		
		if (self::$initial != NULL)	{
			//$this = self::$initial;
			return self::$initial;
		}
		else {
			$this->_uri = $uri = trim($this->detect_uri(), '/');
			$this->_get = $_GET;
			$this->_post = $_POST;
			$this->_protocol = isset($_SERVER['SERVER_PROTOCOL'])?$_SERVER['SERVER_PROTOCOL']:'HTTP/1.0';
			$this->_method = isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:'GET';
			$this->_secure = !empty($_SERVER['HTTPS']) AND filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN);
			$this->_referrer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:FALSE;
				
			self::$user_agent = $user_agent= isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:FALSE;
			
			//Detect mobile browser
			if ($user_agent && preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $user_agent)) { self::$_is_mobile = true; }
			
			$this->_requested_with = isset($_SERVER['HTTP_X_REQUESTED_WITH'])?$_SERVER['HTTP_X_REQUESTED_WITH']:FALSE;
				
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND isset($_SERVER['REMOTE_ADDR']) AND in_array($_SERVER['REMOTE_ADDR'], self::$trusted_proxies)){
				$client_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				self::$client_ip = array_shift($client_ips);
				unset($client_ips);
			}
			elseif (isset($_SERVER['HTTP_CLIENT_IP']) AND isset($_SERVER['REMOTE_ADDR']) AND in_array($_SERVER['REMOTE_ADDR'], self::$trusted_proxies)) {
				$client_ips = explode(',', $_SERVER['HTTP_CLIENT_IP']);
				self::$client_ip = array_shift($client_ips);
				unset($client_ips);
			}
			elseif (isset($_SERVER['REMOTE_ADDR'])){
				self::$client_ip = $_SERVER['REMOTE_ADDR'];
			}
			
			return self::$initial = $this;
		}
	}

	public function execute(){
		
		$params = $this->getRoute($this->_uri);
		
		if ($params === NULL || $params === false) {
			$uri = $this->_uri;
			throw new Exception('Unable to find a route to match the URI: '.$uri);
			return false;
		}
		
		if (isset($params['directory'])){
			// Controllers are in a sub-directory
			$this->_directory = $params['directory'];
		}
		
		if (isset($params['app'])){
			// Controllers are in a sub-directory
			if ($app_dir = trim((string) $params['app'], ' /'))	{
				self::$_app = $app_dir;
			}			
		}

		if (isset($params['controller'])){
			$this->_controller = $params['controller'];
		}
		else {
			$this->_controller = $this->default_route['controller'];
		}
		
		$this->_controller = ucfirst($this->_controller);
		
		if (isset($params['action'])){
			// Store the action
			$this->_action = $params['action'];
		}
		else {
			// Use the default action
			$this->_action = self::$action_prefix.$this->default_route['action'];
		}

		unset($params['controller'], $params['action'], $params['directory'], $params['app']);
		$this->_params = $params;
		$previous = self::$current;
		self::$current = $this;
		$controller_name = $this->_controller;
		$controller_class_dir = self::$_app.'\\'.($this->_directory?$this->_directory.'\\':'').self::$controller_prefix.'\\';
		$this->_controller = $controller = $controller_class_dir.$controller_name;
		
		try	{
			
			if (!class_exists($controller))
			{	
				throw new Exception('The requested conreller - "'.$controller.'" was not found on this server.');
			}
			
			// Load the controller using reflection
			$class = new \ReflectionClass($controller);
			
			if ($class->isAbstract()){
				throw new Exception('Cannot create instances of abstract '.$controller);
			}

			// Create a new instance of the controller
			$controller = $class->newInstance($this);
			
			$class->getMethod('before')->invoke($controller);
			// Determine the action to use
			$action = $this->_action;
			
			if (!$class->hasMethod(self::$action_prefix.$action)){
				throw new Exception('The requested URL "'.$this->uri().'" not find action = "action'.$action.'" in conroller="'.$this->_controller.'"');
			}

			$class->getMethod(self::$action_prefix.$action)->invoke($controller);

			$class->getMethod('after')->invoke($controller);
			
		}
		catch (Exception $e) {
			if ($previous instanceof Request){
				self::$current = $previous;
			}
			throw $e;
		}
		
		// Restore the previous request
		self::$current = $previous;
		return $this;	
	}
	
	public static function detect_uri(){
		if (PHP_SAPI === 'cli') {
			GLOBAL $argv;
			$a = $argv;
			unset($a[0]);
			$uri = implode('/', $a);
			return $uri;
		}
		
        if (!empty($_SERVER['PATH_INFO'])) {
			$uri = $_SERVER['PATH_INFO'];
		}
		else {
			if (isset($_SERVER['REQUEST_URI']))
				$uri = $_SERVER['REQUEST_URI'];
			elseif (isset($_SERVER['PHP_SELF']))
				$uri = $_SERVER['PHP_SELF'];
			elseif (isset($_SERVER['REDIRECT_URL']))
				$uri = $_SERVER['REDIRECT_URL'];
			else
				throw new Exception('Unable to detect the URI using PATH_INFO, REQUEST_URI, PHP_SELF or REDIRECT_URL');
			
			if ($request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
				$uri = $request_uri;
			
			$uri = rawurldecode($uri);
			
			$base_url = parse_url(self::$base_url, PHP_URL_PATH);
			
			if ($base_url AND (strpos($uri, $base_url) === 0))
				$uri = (string) substr($uri, strlen($base_url));

			if (self::$index_file AND strpos($uri, self::$index_file) === 0)
				$uri = (string) substr($uri, strlen(self::$index_file));
			
		}
		return $uri;
	}
	
	public static function addRoutes($routes = false) {
	
		if ($routes && (gettype($routes) == 'array')) {			
			
			$r_key     = '<([a-zA-Z0-9_]++)>';
			$r_segment = '[^/.,;?\n]++';
			$r_escape  = '[.\\+*?[^\\]${}=!|]';
			
			//parse routes for preg_match
			$search = array('#[.\\+*?[^\\]${}=!|]#', '(', ')', '<', '>');
			$replace = array('\\\\$0', '(?:', ')?', '(?P<', '>[^/.,;?\n]++)');
			
			foreach ($routes as $name => $route) {	
				$e_routes[$name]['default'] = array_merge(array('action' => 'index'), $route['default']);
				$e_routes[$name]['expression'] = '#^'.str_replace($search, $replace, $route['uri']).'$#uD';	
				self::$_routes[$name] = $e_routes[$name];
			}
		}
		else return false;		
	}
	
	public static function getRoutes() {
		if (isset(self::$_routes) && !empty(self::$_routes)) {
			return self::$_routes;
		}
		return false;
	}
	
	public function getRoute($uri) {
		$uri = (string) $uri;
		
		if ($routes = $this->getRoutes()){
			foreach ($routes as $name=>$rt){	
				$expression = $rt['expression'];
				if (preg_match($expression, $uri, $matches)){
					$params = $rt['default'];
					foreach ($matches as $key => $value){
						if (is_int($key)) 
							continue;
						$params[$key] = $value;
					}
					return $params;
				}
			}
		}
		return false;
	}
	
	public function uri(){return empty($this->_uri) ? self::$base_url : $this->_uri;}

	public function param($key = NULL, $default = NULL){
		if ($key === NULL){
			return $this->_params;
		}
		return isset($this->_params[$key]) ? $this->_params[$key] : $default;
	}	
	
	static function route(){
		if (isset(self::$_route) && !empty(self::$_route)){
			// Act as a getter
			return self::$_route;
		}
		return false;
	}

	public function directory($directory = NULL){
		if ($directory === NULL){
			return $this->_directory;
		}
		$this->_directory = (string) $directory;
		return $this;
	}

	public function controller($controller = NULL) {
		if ($controller === NULL){
			return $this->_controller;
		}
		$this->_controller = (string) $controller;
		return $this;
	}
	
	static function app($app = NULL){
		if ($app === NULL){
			return self::$_app;
		}
		return false;
	}

	public function action($action = NULL){
		if ($action === NULL){
			return $this->_action;
		}
		$this->_action = (string) $action;
		return $this;
	}
	
	public function controller_uri(){	
		$uri = $this->_uri;
		$uri = rtrim(str_replace($this->_action, '', $uri),'/');
		return $uri;
	}
	
	public function requested_with($requested_with = NULL){
		if ($requested_with === NULL){
			return $this->_requested_with;
		}
		$this->_requested_with = strtolower($requested_with);
		return $this;
	}
	
	public function isAjax() {
		return ($this->requested_with() === 'xmlhttprequest' || (isset($_POST['ajax']) && $_POST['ajax']) || (isset($_GET['ajax']) && $_GET['ajax']));
	}
	
	public function body($content = NULL) {
		if ($content === NULL){
			return $this->_body;
		}
		$this->_body = $content;
		return $this;
	}
	
	public function post($key = false){
		if ($key !== false && $key = (string) $key)
		{
			if (isset($this->_post[$key])){
				return $this->_post[$key];
			}
			else {
				return NULL;
			}
		}
		return $this->_post;
	}
	
	static function redirect($url, $statusCode = 303){
	   header('Location: ' . $url, true, $statusCode);
	   die();
	}
	
	public static function getUserAgent() { return self::$user_agent; }
	
	public static function isMobile() { return self::$_is_mobile; }
	
	static function initial(){return self::$initial;}
	
	public static function current(){return self::$current;}

} // End Request