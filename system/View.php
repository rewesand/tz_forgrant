<?php 

class View {

	protected static $_global_data = array();
    public static $_time_now = false;
	protected $_file;
	protected $_data = array();

	public static function factory($file = NULL, array $data = NULL){return new View($file, $data);}

	protected static function capture($view_filename, array $view_data){
		extract($view_data, EXTR_SKIP);
		if (View::$_global_data){
			extract(View::$_global_data, EXTR_SKIP | EXTR_REFS);
		}
		ob_start();
		
		try{
			include $view_filename;
		}
		catch (Exception $e){
			ob_end_flush();
			throw $e;
		}
		
		return ob_get_clean();
	}

	public static function set_global($key, $value = NULL){
		if (is_array($key)){
			foreach ($key as $key2 => $value){
				View::$_global_data[$key2] = $value;
			}
		}
		else{
			View::$_global_data[$key] = $value;
		}
	}

	public static function bind_global($key, & $value){self::$_global_data[$key] =& $value;}
	
	public function __construct($file = NULL, array $data = NULL){
		if ($file !== NULL){
			$this->set_filename($file);
		}

		if ($data !== NULL){
			// Add the values to the current data
			$this->_data = $data + $this->_data;
		}
        
        if (!View::$_time_now){
            View::$_time_now = getdate();
        }
	}

	public function & __get($key){
		if (array_key_exists($key, $this->_data)){
			return $this->_data[$key];
		}
		elseif (array_key_exists($key, View::$_global_data)){
			return View::$_global_data[$key];
		}
		else {
			throw new Exception('View variable is not set: :var',
				array(':var' => $key));
		}
	}

	public function __set($key, $value){ $this->set($key, $value); }
	
	public function __isset($key){return (isset($this->_data[$key]) OR isset(View::$_global_data[$key]));}

	public function __unset($key){unset($this->_data[$key], View::$_global_data[$key]);}

	public function __toString(){
		try{
			return $this->render();
		}
		catch (Exception $e){
			// Display the exception message
			throw new Exception($e);
		}
	}

	public function set_filename($file){
		$view_dir = 'Views';
		if (class_exists('Request') && $app = Request::app()){
			if (findFile($app.DS.$view_dir,$file)){
				$view_dir = $app.DS.$view_dir;
			}
		}
		
		if (($path = findFile($view_dir,$file)) === FALSE){
			throw new Exception('The requested view '.$file.' could not be found');
		}

		$this->_file = $path;
		return $this;
	}

	public function set($key, $value = NULL){
		if (is_array($key)){
			foreach ($key as $name => $value){
				$this->_data[$name] = $value;
			}
		}
		else{
			$this->_data[$key] = $value;
		}
		return $this;
	}

	public function bind($key, & $value){ $this->_data[$key] =& $value; return $this;}

	public function render($file = NULL){
		if ($file !== NULL){
			$this->set_filename($file);
		}
		
		if (empty($this->_file)){
			throw new View_Exception('You must set the file to use within your view before rendering');
		}
		
        return View::capture($this->_file, $this->_data);
	}
    
    public static function cutString($string, $maxlen, $strip_tags = true){
        if (mb_strlen($string, 'UTF-8') > $maxlen){
			if ($strip_tags){
				$string = mb_substr(html_entity_decode(strip_tags($string),ENT_QUOTES,'utf-8'),0,$maxlen,'utf-8');
			}
			else {
				$string = mb_substr(html_entity_decode($string,ENT_QUOTES,'utf-8'),0,$maxlen,'utf-8');
			}
            $string .= '...';
        }
        return $string;
    }
    
    public static function formatDate($date = false, $seek = false, $show_time = true){
        if ($date = (int) $date){
            $res = $date;
            $now = View::$_time_now;
            $d = getdate($date);
			
			if ($show_time === true){            
				if (($d['year'] == $now['year']) && ($d['yday'] == $now['yday'])){
					return ($seek?'':'Сегодня ').date('H:i',$date);
				}
				elseif (($d['year'] == $now['year']) && ($d['yday'] - $now['yday'] == 1)){
					return ($seek?'':'Вчера ').date('H:i',$date);
				} 
				return date('d-m-Y H:i', $date);
            }
			else {
				if (($d['year'] == $now['year']) && ($d['yday'] == $now['yday'])){
					return ($seek?'':'Сегодня ').date('H:i',$date);
				} 
				elseif (($d['year'] == $now['year']) && ($now['yday'] - $d['yday'] == 1)){
					return ($seek?'':'Вчера ').date('H:i',$date);
				} 
				return date('d-m-Y', $date);
			}
        }
        
    }
	
	static function htmlspecialchars_uni($message){
		$message = preg_replace("#&(?!\#[0-9]+;)#si", "&amp;", $message); // Fix & but allow unicode
		$message = str_replace("<", "&lt;", $message);
		$message = str_replace(">", "&gt;", $message);
		$message = str_replace("\"", "&quot;", $message);
		return $message;
	}

} // End View
