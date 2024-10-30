<?php
/*
Plugin Name: Clicks Counter
Plugin URI: http://blogwordpress.ws/plugin-clicks-counter
Description: Plugin to count how many clicks (access) posts and pages have
Author: Anderson Makiyama
Version: 0.1
Author URI: http://blogwordpress.ws
*/


class Anderson_Makiyama_Clicks_Counter{
	const CLASS_NAME = 'Anderson_Makiyama_Clicks_Counter';
	public static $CLASS_NAME = self::CLASS_NAME;
	const PLUGIN_ID = 7;
	public static $PLUGIN_ID = self::PLUGIN_ID;
	const PLUGIN_NAME = 'Clicks Counter';
	public static $PLUGIN_NAME = self::PLUGIN_NAME;
	const PLUGIN_PAGE = 'http://blogwordpress.ws/plugin-clicks-counter';
	public static $PLUGIN_PAGE = self::PLUGIN_PAGE;
	const AUTHOR_PAGE = 'http://blogwordpress.ws';
	public static $AUTHOR_PAGE = self::AUTHOR_PAGE;	
	const PLUGIN_VERSION = '0.1';
	public static $PLUGIN_VERSION = self::PLUGIN_VERSION;
	public $plugin_slug = "anderson_makiyama_";
	public $plugin_base_name;
	
    public function getStaticVar($var) {
        return self::$$var;
    }	
	
	public function __construct(){
		$this->plugin_base_name = plugin_basename(__FILE__);
		$this->plugin_slug.= strtolower(str_replace(" ","_",self::PLUGIN_NAME));
		load_plugin_textdomain( self::CLASS_NAME, '', strtolower(str_replace(" ","-",self::PLUGIN_NAME)) . '/lang' );	
		
	}
	
	public function settings_link($links) { 
	  $settings_link = '<a href="options-general.php?page='. strtolower(str_replace(" ","-",self::PLUGIN_NAME)) .'/index.php">'. __('Settings', self::CLASS_NAME). '</a>'; 
	  array_unshift($links, $settings_link); 
	  return $links; 
	}
	
	public function options(){
		global $anderson_makiyama;
		global $user_level;
		get_currentuserinfo();
		if ($user_level < 10) {
			return;
		}

		  if (function_exists('add_options_page')) {
			add_options_page(__(self::PLUGIN_NAME), __(self::PLUGIN_NAME), 1, __FILE__, array(self::CLASS_NAME,'options_page'));
		  }
		
	}	
	
	public function options_page(){
		global $anderson_makiyama;
		global $user_level;
		get_currentuserinfo();
		if ($user_level < 10) {
			return;
		}
		
		$options = get_option($anderson_makiyama[self::PLUGIN_ID]->plugin_slug."_options");
		
		if ($_POST[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_submit']) {
			$options[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_status'] = $_POST[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_status'];
			$options[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_user'] = (int)$_POST[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_user'];
			$options[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_category'] = (int)$_POST[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_category'];
			$options[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_max'] = (int)$_POST[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_max'];
			
			if($options[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_user'] < 1) $options[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_user'] = 1;
			if($options[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_category'] < 1) $options[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_category'] = 1;
			if($options[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_max'] < 0) $options[$anderson_makiyama[self::PLUGIN_ID]->plugin_slug.'_max'] = -1;
			
			update_option($anderson_makiyama[self::PLUGIN_ID]->plugin_slug."_options", $options);
			 echo '<div class="updated"><p>' . __('Options saved', self::CLASS_NAME) . '</p></div>';
		}
		
				
		
		include("templates/options.php");
	}	
	
	public function show_clicks($atts=array(),$content=""){
		global $post, $anderson_makiyama;
		
		$clicks = (int)get_option($anderson_makiyama[self::PLUGIN_ID]->plugin_slug . "_" . $post->ID);
		
		if(isset($clicks)){
			return 	$clicks;
		}else{
			return 0;
		}
	}
	
	public static function get_clicks_of($post_id){
		global $anderson_makiyama;
		
		$clicks = (int)get_option($anderson_makiyama[7]->plugin_slug . "_" . $post_id);
		
		if(isset($clicks)){
			return 	$clicks;
		}else{
			return 0;
		}			
	}
	
	public function update_clicks($post = null){
		global $anderson_makiyama;
		
		if(!is_single() && !is_page()) return;
		
		if(isset($post->ID)){
			$clicks = (int)get_option($anderson_makiyama[self::PLUGIN_ID]->plugin_slug . "_" . $post->ID);
			$clicks++;
			update_option($anderson_makiyama[self::PLUGIN_ID]->plugin_slug . "_" . $post->ID, $clicks);
		}
	}
	
	public static function makeData($data, $anoConta,$mesConta,$diaConta){
	   $ano = substr($data,0,4);
	   $mes = substr($data,5,2);
	   $dia = substr($data,8,2);
	   return date('Y-m-d',mktime (0, 0, 0, $mes+($mesConta), $dia+($diaConta), $ano+($anoConta)));	
	}
	
	public static function get_data_array($data,$part=''){
	   $data_ = array();
	   $data_["ano"] = substr($data,0,4);
	   $data_["mes"] = substr($data,5,2);
	   $data_["dia"] = substr($data,8,2);
	   if(empty($part))return $data_;
	   return $data_[$part];
	}	
	
	public static function is_selected($campo, $varCampo){
		if($campo==$varCampo) return " selected=selected ";
		return "";
	}	
}
if(!isset($anderson_makiyama)) $anderson_makiyama = array();
$anderson_makiyama_indice = Anderson_Makiyama_Clicks_Counter::PLUGIN_ID;
$anderson_makiyama[$anderson_makiyama_indice] = new Anderson_Makiyama_Clicks_Counter();
add_filter("admin_menu", array($anderson_makiyama[$anderson_makiyama_indice]->getStaticVar('CLASS_NAME'), 'options'),30);

add_filter("plugin_action_links_". plugin_basename(__FILE__), array($anderson_makiyama[$anderson_makiyama_indice]->getStaticVar('CLASS_NAME'), 'settings_link') );
add_shortcode('clicks_counter',array($anderson_makiyama[$anderson_makiyama_indice]->getStaticVar('CLASS_NAME'), 'show_clicks'));
add_action('the_post',array($anderson_makiyama[$anderson_makiyama_indice]->getStaticVar('CLASS_NAME'), 'update_clicks'));

?>