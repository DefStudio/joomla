<?php
/**
 * IceShortCodes Extension for Joomla 3 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2013 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/extensions/iceshortocdes
 * @Support 	http://www.icetheme.com/forum/categories/iceshortcodes/listings
 *
 */
 
// no direct access
 defined('_JEXEC') or die('Restricted access');
 
 
 jimport('joomla.plugin.plugin');
 if(!defined("DS"))
	define("DS",DIRECTORY_SEPARATOR);
$lang = JFactory::getLanguage();
$lang->load( "plg_system_iceshortcodes",JPATH_SITE.DS."administrator" );  


 class PlgSystemIceShortCodes extends JPlugin {
 	var $_shortcodes = array("button"=> array("codeHolder"=>"[LOFG-BUTTON-HOLDER-%d]",
												"codeCheck"=>"[icebutton",
												"codeModifier"=>"#\[icebutton(.*?)\](.*?)\[/icebutton\]#e",
												"codeMakeHolder"=>"'[LOFG-BUTTON-HOLDER-' . PlgSystemIceShortCodes::getCount('button') . ']'",
												"regex"=>'/\[icebutton(.*?)\](.*?)\[\/icebutton\]/im'),
	
							"box"=> array("codeHolder"=>"[LOFG-BOX-HOLDER-%d]",
											"codeCheck"=>"[icebox",
											"codeModifier"=>"#\[icebox(.*?)\](.*?)\[/icebox\]#e",
											"codeMakeHolder"=>"'[LOFG-BOX-HOLDER-' . PlgSystemIceShortCodes::getCount('box') . ']'",
											"regex"=>'/\[icebox(.*?)\](.*?)\[\/icebox\]/im'),
							
							"column"=>array("codeHolder"=>"[LOFG-COLUMN-HOLDER-%d]",
											"codeCheck"=>"[icecolumns",
											"codeModifier"=>"#\[icecolumns(.*?)\](.*?)\[/icecolumns\]#e",
											"codeMakeHolder"=>"'[LOFG-COLUMN-HOLDER-' . PlgSystemIceShortCodes::getCount('column') . ']'",
											"regex"=>'/\[icecolumns(.*?)\](.*?)\[\/icecolumns\]/im'),
							
							"tooltip"=>array("codeHolder"=>"[LOFG-TOOLTIP-HOLDER-%d]",
											"codeCheck"=>"[icetooltip",
											"codeModifier"=>"#\[icetooltip(.*?)\](.*?)\[/icetooltip\]#e",
											"codeMakeHolder"=>"'[LOFG-TOOLTIP-HOLDER-' . PlgSystemIceShortCodes::getCount('tooltip') . ']'",
											"regex"=>'/\[icetooltip(.*?)\](.*?)\[\/icetooltip\]/im'),
							
							"social"=>array("codeHolder"=>"[LOFG-SOCIAL-HOLDER-%d]",
											"codeCheck"=>"[icesocial",
											"codeModifier"=>"#\[icesocial(.*?)\]#e",
											"codeMakeHolder"=>"'[LOFG-SOCIAL-HOLDER-' . PlgSystemIceShortCodes::getCount('social') . ']'",
											"regex"=>'/\[icesocial(.*?)\]/im'),
							
							"tab"=>array("codeHolder"=>"[LOFG-TAB-HOLDER-%d]",
										"codeCheck"=>"[icetabs",
										"codeModifier"=>"#\[icetabs(.*?)\](.*?)\[/icetabs\]#e",
										"codeMakeHolder"=>"'[LOFG-TAB-HOLDER-' . PlgSystemIceShortCodes::getCount('tab') . ']'",
										"regex"=>'/\[icetabs(.*?)\](.*?)\[\/icetabs\]/im'),
							
							"slideshow"=>array("codeHolder"=>"[LOFG-SLIDESHOW-HOLDER-%d]",
												"codeCheck"=>"[iceslideshow",
												"codeModifier"=>"#\[iceslideshow(.*?)\](.*?)\[/iceslideshow\]#e",
												"codeMakeHolder"=>"'[LOFG-SLIDESHOW-HOLDER-' . PlgSystemIceShortCodes::getCount('slideshow') . ']'",
												"regex"=>'/\[iceslideshow(.*?)\](.*?)\[\/iceslideshow\]/im'),
							
							"accordion"=>array("codeHolder"=>"[LOFG-ACCORDION-HOLDER-%d]",
												"codeCheck"=>"[iceaccordion",
												"codeModifier"=>"#\[iceaccordion(.*?)\](.*?)\[/iceaccordion\]#e",
												"codeMakeHolder"=>"'[LOFG-ACCORDION-HOLDER-' . PlgSystemIceShortCodes::getCount('accordion') . ']'",
												"regex"=>'/\[iceaccordion(.*?)\](.*?)\[\/iceaccordion\]/im'));
							
	
	function PlgSystemIceShortCodes(& $subject, $config = array())
	{
		parent::__construct($subject, $config);
		$this->plugin = &JPluginHelper::getPlugin ( 'system', 'iceshortcodes' );
		$this->_params = $this->params;// new JParameter ($this->plugin->params );
		$this->setCacheImagePath( 'iceshortcodes' );
		$mainframe = &JFactory::getApplication();
		if ( $mainframe->isAdmin() ) { 
			return; 
		}
		$document = & JFactory::getDocument();
		$uribase = JURI::base();
		
		// Check IceShortcode.css If Exist to ice template/css ;
		$pathtocheck = JPATH_BASE.DS.'templates'.DS.$mainframe->getTemplate().DS;

		$icefilecheck = $pathtocheck.'css'.'\iceshortcodes.css';
		if (file_exists($icefilecheck)) {
		$document = JHTML::stylesheet( 'templates/'.$mainframe->getTemplate().'/css/iceshortcodes.css');
		} else {
		$document->addStyleSheet("plugins/system/iceshortcodes/assets/iceshortcodes.css");
		}
		
		// Check Bootstrap to IceTheme Template and add if not exist.;
		if($this->_params->get("enable_bootstrap",0)){

		$icefilecheck = $pathtocheck.'bootstrap'.'\css\bootstrap.min.css';
		$icefilecheck = $pathtocheck.'bootstrap'.'\css\bootstrap-responsive.min.css';
		$icefilecheck = $pathtocheck.'bootstrap'.'\js\bootstrap.min.js';
		
		if (file_exists($icefilecheck)) {
		$document = JHTML::stylesheet( 'templates/'.$mainframe->getTemplate().'/bootstrap/css/bootstrap.min.css');
		$document = JHTML::stylesheet( 'templates/'.$mainframe->getTemplate().'/bootstrap/css/bootstrap-responsive.min.css');
		$document = JHTML::script( 'templates/'.$mainframe->getTemplate().'/bootstrap/js/bootstrap.min.js');
		
		} else {
		$document->addStyleSheet("plugins/system/iceshortcodes/assets/bootstrap/css/bootstrap.min.css");
		$document->addStyleSheet("plugins/system/iceshortcodes/assets/bootstrap/css/bootstrap-responsive.min.css");
		$document->addScript("plugins/system/iceshortcodes/assets/bootstrap/js/bootstrap.min.js");
		}
		}
	}
	
	function prepareContentByType($type, $matches=array(), $body = ""){
		if(!empty($type) && isset($this->_shortcodes[$type])){
			switch($type){
				case "button":
					foreach($matches[1] as $id => $match){
						$str_options = $match;
						$str_content = isset($matches[2][$id])?$matches[2][$id]:"";
						$tmp["options"] = $this->parseOptions($str_options);
						$tmp["content"] = $str_content;
						$body = preg_replace ( $this->_shortcodes[ $type ]["codeModifier"], $this->_shortcodes[ $type ]["codeMakeHolder"], $body, - 1, $count );
						
						$output = $this->generateButton($tmp);
						$holder = sprintf ( $this->_shortcodes[$type]["codeHolder"], $id );
						$body = str_replace ( $holder, $output, $body );
					}
				break;
				case "box":
					foreach($matches[1] as $id=>$match){
						$str_options = $match;
						$str_content = isset($matches[2][$id])?$matches[2][$id]:"";
						$tmp["options"] = $this->parseOptions($str_options);
						$tmp["content"] = $str_content;
						$body = preg_replace ( $this->_shortcodes[ $type ]["codeModifier"], $this->_shortcodes[ $type ]["codeMakeHolder"], $body, - 1, $count );
						$output = $this->generateBox($tmp);
						$holder = sprintf ( $this->_shortcodes[$type]["codeHolder"], $id );
						$body = str_replace ( $holder, $output, $body );
					}
				break;
				case "column":
					foreach($matches[1] as $id=>$match){
						$str_options = $match;
						$str_content = isset($matches[2][$id])?$matches[2][$id]:"";
						$tmp["options"] = $this->parseOptions($str_options);
						$tmp["items"] = $this->parseContentColumn($str_content);
						$body=preg_replace ($this->_shortcodes[ $type ]["codeModifier"],$this->_shortcodes[ $type ]["codeMakeHolder"], $body, -1, $count );
						$output = $this->generateColumn($tmp);
						$holder = sprintf ( $this->_shortcodes[$type]["codeHolder"], $id );
						$body = str_replace ( $holder, $output, $body);
					}
				break;
				case "tooltip":
					foreach($matches[1] as $id=>$match){
						$str_options = $match;
						$str_content = isset($matches[2][$id])?$matches[2][$id]:"";
						$tmp["options"] = $this->parseOptions($str_options);
						$tmp["content"] = $str_content;
						$body=preg_replace ($this->_shortcodes[ $type ]["codeModifier"],$this->_shortcodes[ $type ]["codeMakeHolder"], $body, -1, $count );
						$output = $this->generateTooltip($tmp);
						$holder = sprintf( $this->_shortcodes[ $type ]["codeHolder"], $id);
						$body = str_replace ( $holder, $output, $body);
					}
				break;
				case "social":
					foreach($matches[1] as $id=>$match){
						$str_options = $match;
						$str_content = isset($matchs[2][$id])?$matches[2][$id]:"";
						$tmp["options"] = $this->parseOptions($str_options);
						$body = preg_replace ($this->_shortcodes[ $type ]["codeModifier"],$this->_shortcodes[ $type ]["codeMakeHolder"], $body, -1, $count );
						$output = $this->generateSocial($tmp);
						$holder = sprintf( $this->_shortcodes[ $type]["codeHolder"], $id);
						$body = str_replace ( $holder, $output, $body);
					} 
				break;
				case "tab":
					foreach($matches[1] as $id=>$match){
						$str_options = $match;
						$str_content = isset($matches[2][$id])?$matches[2][$id]:"";
						$tmp["options"] = $this->parseOptions($str_options);
						$tmp["items"] = $this->parseContentTab($str_content);
						$body = preg_replace ($this->_shortcodes[ $type ]["codeModifier"],$this->_shortcodes[ $type ]["codeMakeHolder"], $body, -1, $count);
						$output = $this->generateTab($tmp);
						$holder = sprintf( $this->_shortcodes[ $type ]["codeHolder"], $id);
						$body = str_replace ($holder,$output,$body);
					}
				break;
				case "slideshow":
					foreach($matches[1] as $id=>$match){
						$str_options = $match;
						$str_content = isset($matches[2][$id])?$matches[2][$id]:"";
						$tmp["options"] = $this->parseOptions($str_options);
						$tmp["items"] = $this->parseContentSlideshow($str_content);
						$body = preg_replace ($this->_shortcodes[ $type ]["codeModifier"],$this->_shortcodes[ $type ]["codeMakeHolder"], $body, -1, $count);
						$output = $this->generateSlideshow($tmp);
						$holder = sprintf( $this->_shortcodes[ $type ]["codeHolder"], $id);
						$body = str_replace ($holder,$output,$body);
					}
				break;
				case "accordion":
					foreach($matches[1] as $id=>$match){
						$str_options = $match;
						$str_content = isset($matches[2][$id])?$matches[2][$id]:"";
						$tmp["options"] = $this->parseOptions($str_options);
						$tmp["items"] = $this->parseContentAccordion($str_content);
						$body = preg_replace ($this->_shortcodes[ $type ]["codeModifier"],$this->_shortcodes[ $type ]["codeMakeHolder"], $body, -1, $count);
						$output = $this->generateAccordion($tmp);
						//echo '<pre>'.print_r($output);die();
						$holder = sprintf( $this->_shortcodes[ $type ]["codeHolder"], $id);
						$body = str_replace ($holder,$output,$body);
					}
				break;
				default:
				break;
			}
			
		}
		return $body;
	}
	private function generateButton($params = array()){
		$html = "";
		if(!empty($params)){
			$options = isset($params["options"])?$params["options"]:array();
			$content = isset($params["content"])?$params["content"]:"";
			$link = isset($options["link"])?$options["link"]:"";
			if(JString::strpos( $link, "http://" ) === false && JString::strpos( $link, "https://" )=== false){
				$link = "http://".$link;
			}
			$type = isset($options["type"])?$options["type"]:"";
			$size = isset($options["size"])?$options["size"]:"";
			$color = isset($options["color"])?$options["color"]:"";
			$icon = isset($options["icon"])?$options["icon"]:"";
			$target = isset($options["target"])?$options["target"]:"";
			$html = ' <a href="'.$link.'" class="btn '.$size.' '.$color.' '.$type.'" target="_'.$target.'">'.$icon.'
			'.$content.'</a>';
			
		}
		
		return $html;
	}
	private function generateBox($params = array()){
		$html = "";
		if(!empty($params)){
			$options = isset($params["options"])?$params["options"]:array();
			$content = isset($params["content"])?$params["content"]:array();
			$type = isset($options["type"])?$options["type"]:"";
			$icon = isset($options["icon"])?$options["icon"]:"";
			$title = isset($options["title"])?$options["title"]:"";
			if($icon == "yes"){ 
				$html = '<div class="box '.$type.' icebox_icon">'.'<h3>'.$title.'</h3>'.$content.'</div>'; 
			} else {
				$html = '<div class="box '.$type.' ">'.'<h3>'.$title.'</h3>'.$content.'</div>';	
			}
			
			
			
		}
		return $html;
	}
	private function generateColumn($params = array()){
		$html = "";
		if(!empty($params)){
			$options = isset($params["options"])?$params["options"]:array();
			$number = isset($options["number"])?$options["number"]:"";
			$items = isset($params["items"])?$params["items"]:array();
			$html = '<div class="icecolumns columns'.$number.'">';
			if(!empty($items)){
				foreach($items as $item){
					$title = isset($item["title"])?'<h2>'.$item['title'].'</h2>':"";
					$content = isset($item["content"])?$item["content"]:"";
					$html .="
						<div class='icecol'>
						{$title}
						{$content}
						</div>
						";
				}
			}
			$html .='</div>';
		}
		return $html;
	}
	private function generateTooltip($params = array()){
		$html = "";
		if(!empty($params)){
			$options = isset($params["options"])?$params["options"]:array();
			$content = isset($params["content"])?$params["content"]:array();
			$placement= isset($options["placement"])?$options["placement"]:"";
			$title = isset($options["title"])?$options["title"]:"";
			$html = '<span rel="tooltip" data-placement="'.$placement.'" title="'.$title.'">'.$content.'</span>';
		}
		return $html;
	}
	private function generateSocial($params= array()){
		$html = "";
		if(!empty($params)){
			$options = isset($params["options"])?$params["options"]:array();
			$mode = isset($options["mode"])?$options["mode"]:array();
			$name = isset($options["name"])?$options["name"]:"";
			$width = isset($options["width"])?$options["width"]:"";
			$height = isset($options["height"])?$options["height"]:"";
			$id	  = isset($options["id"])?$options["id"]:"";
			if($mode=="facebook_like"){
				$html='
					<b:if cond="data:blog.pageType != &quot;static_page&quot;">
						<div style="float: none;">
							<div class="fb-like" expr:data-href="data:post.canonicalUrl" data-send="true" data-layout="standard" data-show-faces="false" data-action="like" data-colorscheme="light" data-font="arial"></div>
						</div>
					</b:if>';
			}else if($mode=="facebook_fanpage"){
				$html = '
				<div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=482716238406590";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, \'script\', \'facebook-jssdk\'));</script>
				<div class="fb-like-box" data-href="http://www.facebook.com/'.$name.'" data-width="'.$width.'" data-height="'.$height.'" data-show-faces="true" data-stream="false" data-header="true"></div>
				';
			}else if($mode=="twitter_follow"){
				$html ='
						<a href="https://twitter.com/'.$name.'" class="twitter-follow-button" data-show-count="false">Follow @twitter</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
				';
			}else if($mode=="twitter_tweet"){
				$html ='
						<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en">Tweet</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
				';
			}
		}
		return $html;
	}
	private function generateTab($params = array()){
		$html = "";
		if(!empty($params)){
			$options = isset($params["options"])?$params["options"]:array();
			$theme = isset($options["theme"])?$options["theme"]:"";
			$active = isset($options["active"])?$options["active"]:"";
			$items = isset($params["items"])?$params["items"]:array();
			$html = '<div class="icetabs '.$theme.'">';
			if(!empty($items)){
				$html .='<ul class="nav nav-tabs">';
					$i =1;
					foreach($items as $item){
						$title = isset($item["title"])?$item["title"]:"";
							if($active == $i){
								$html .= '<li class="active"><a href="#icetab'.$i.'" data-toggle="tab">'.$title.'</a></li>';
							}else{
								$html .='
										<li><a href="#icetab'.$i.'" data-toggle="tab">'.$title.'</a></li>
								';
							}		
					$i++;	
					}
					
				$html .='</ul>';
				$html .='<div class="tab-content">';
					$i =1;
					foreach($items as $item){
						$content = isset($item["content"])?$item["content"]:"";
						if($active == $i){
							$html .= '<div id="icetab'.$i.'" class="tab-pane active">
											'.$content.'
									 </div>
								';
						}else{
							$html .='
									<div id="icetab'.$i.'" class="tab-pane">
									'.$content.'
									</div>
							';
						}
					$i++;	
					}
				$html .='</div>';
			}	
			$html .='</div>'; 
		}
		return $html;
	}
	private function generateSlideshow($params= array()){
		$html = "";
		
		if(!empty($params)){
			$options = isset($params["options"])?$params["options"]:array();
			$theme = isset($options["theme"])?$options["theme"]:"";
			$active = isset($options["active"])?$options["active"]:"";
			$directory = isset($options["directory"])?$options["directory"]:"";
			$indicators = isset($options["directory"])?$options["directory"]:"";
			$controls =isset($options["controls"])?$options["controls"]:"";
			$items = isset($params["items"])?$params["items"]:array();
			$id=rand(2,100);
			$html ='<div id="iceslideshow'.$id.'" class="carousel slide">';
			if(!empty($items)){	
				if($indicators=="no"){
				$html .='<ol class="carousel">';
				}else{
				$html .='<ol class="carousel-indicators">';
				}
					$i=1;
					foreach($items as $item){
						if($active==$i){
							$html .='<li data-target="#iceslideshow'.$id.'" data-slide-to="'.($i-1).'" class="active"></li>';
						}else{
							$html .='<li data-target="#iceslideshow'.$id.'" data-slide-to="'.($i-1).'" class=""></li>';
						}
					$i++;	
					}
				$html .='</ol>';
				$html .='<div class="carousel-inner">';
					$i=1;
					foreach($items as $item){
						$content= isset($item["content"])?$item["content"]:"";
						$caption = isset($item["caption"])?$item["caption"]:"";
						if($active==$i){
							$html .='
								<div class="item active">
									<a href="#1"><img src="'.$directory.'/'.$content.'" alt="'.$caption.'"/></a>
									<div class="carousel-caption">
										<h4><a href="#">'.$caption.'</a></h4>
									</div>
								</div>
							';
						}else{
							$html .='
								<div class="item">
									<a href="#1"><img src="'.$directory.'/'.$content.'" alt="'.$caption.'"/></a>
									<div class="carousel-caption">
										<h4><a href="#">'.$caption.'</a></h4>
									</div>
								</div>
							';
						}
					$i++;	
					}	
				$html .='</div>';
			}	
			$html .='<a class="carousel-control left" href="#iceslideshow'.$id.'" data-slide="prev">&lsaquo;</a>
					<a class="carousel-control right" href="#iceslideshow'.$id.'" data-slide="next">&rsaquo;</a>';
			$html .='</div>';
			
		}
		return $html;
	}
	private function generateAccordion($params= array()){
		$html = "";
		if(!empty($params)){
			$options = isset($params["options"])?$params["options"]:array();
			$theme = isset($options["theme"])?$params["options"]:"";
			$items = isset($params["items"])?$params["items"]:array();
			$id=rand(1,100);
			if(!empty($items)){
				$html .='  <div id="accordion'.$id.'" class="iceaccordion">';
					$i=1;
					foreach($items as $item){
						$content= isset($item["content"])?$item["content"]:"";
						$title= isset($item["title"])?$item["title"]:"";
						$html .='
							<div class="accordion-group">
								<div class="accordion-heading">
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion'.$id.'" href="#collapse'.$i.'">'.$title.'</a>
								</div>
								<div id="collapse'.$i.'" class="accordion-body collapse" style="height: 0px;">
									<div class="accordion-inner">
										'.$content.'
									</div>
								</div>
							</div>
						';
					$i ++;	
					}
				$html .='</div>';
			}
		}
		return $html;
	}
	function getCount($type = "button",$reset = false) {
		switch($type){
			case "buton":
				static $count_button = 0;
				if ($reset)
					$count_button = - 1;
				return $count_button ++;
			break;
			case "box":
				static $count_box = 0;
				if ($reset)
					$count_box = - 1;
				return $count_box++;
			break;
			case "column":
				static $count_column = 0;
				if($reset){
					$count_column = -1;
				}
				return $count_column ++;
			break;	
			case "tooltip":
				static $count_tooltip = 0;
				if($reset){
					$count_tooltip = -1;
				}
				return $count_tooltip++;
			break;
			case "social":
				static $count_social = 0;
				if($reset){
					$count_social = -1;
				}
				return $count_social++;
			break;	
			case "tab":
				static $count_tab = 0;
				if($reset){
					$count_tab = -1;
				}
				return $count_tab++;
			break;
			case "slideshow":
				static $count_slideshow = 0;
				if($reset){
					$count_slideshow = -1;
				}
				return $count_slideshow++;
			break;			
			case "accordion":
				static $count_accordion = 0;
				if($reset){
					$count_accordion = -1;
				}
				return $count_accordion++;
			break;
			default:
				static $count = 0;
				if ($reset)
					$count = - 1;
				return $count ++;
			break;
		}
		return 0;
	}
	function setCacheImagePath( $folder_name ='icecache'){
		
	}
	function parseData( &$content ) {
		$matches_list = array();
		if(!empty($this->_shortcodes)){
			foreach($this->_shortcodes as $key=>$val){
				$regex = isset($val["regex"])?$val["regex"]:"";
				if(!empty($regex)){
					preg_match_all ($regex, $content, $matches );
					$matches_list[$key] = !empty($matches) ?  $matches : array();
				}
			}
		}
		return $matches_list;
	}
	/**
	 *
	 */
	function parseOptions ($str_options = "")
	{
		$regex = "/\s*([^=\s]+)\s*=\s*('([^']*)'|\"([^\"]*)\"|([^\s]*))/";
		preg_match_all($regex, $str_options, $matches);
		$paramArray = null;
		if(count($matches)){
			$paramArray = array();
				for ($i=0;$i<count($matches[1]);$i++){ 
					$key = $matches[1][$i];
					$val = $matches[3][$i]?$matches[3][$i]:($matches[4][$i]?$matches[4][$i]:$matches[5][$i]);
					$paramArray[$key] = $val;
				}
		}
		return $paramArray;
	}
	
	/**
	 *
	 */
	function parseContentColumn($strcontent = "")
	{
		$regex = '#\[icecol ([^\]]*)\]([^\[]*)\[/icecol\]#m';

		preg_match_all ($regex, $strcontent, $matches, PREG_SET_ORDER);
		$itemArray = array();

		foreach ($matches as $match) {
			$params = $this->parseOptions($match[1]);
			$tmp = array();
			$tmp["content"] =  isset($match[2])?str_replace(array("\n", "\r"),array("<br />", ""),trim($match[2])):"";
			
			if (is_array($params)) {
				$title = isset($params['title'])?trim($params['title']):'';
				$tmp["title"] = $title;
			}
			$itemArray[] = $tmp;
		}

		return $itemArray;
	}
	function parseContentTab($strcontent= ""){
		$regex = '#\[icetab ([^\]]*)\]([^\[]*)\[/icetab\]#m';
		
		preg_match_all ($regex, $strcontent, $matches, PREG_SET_ORDER);
		$itemArray = array();
		
		foreach($matches as $match){
			$params = $this->parseOptions($match[1]);
			$tmp = array();
			$tmp["content"] =  isset($match[2])?str_replace(array("\n", "\r"),array("<br />", ""),trim($match[2])):"";
			if(is_array($params)){
				$title = isset($params['title'])?trim($params['title']):'';
				$tmp["title"]=$title;
			}
			$itemArray[] = $tmp;
		}
		return $itemArray;
	}
	function parseContentSlideshow($strcontent= ""){
		$regex = '#\[iceslide ([^\]]*)\]([^\[]*)\[/iceslide\]#m';
		
		preg_match_all ($regex, $strcontent, $matches, PREG_SET_ORDER);
		$itemArray = array();
		
		foreach($matches as $match){
			$params = $this->parseOptions($match[1]);
			$tmp = array();
			$tmp["content"] =  isset($match[2])?str_replace(array("\n", "\r"),array("<br />", ""),trim($match[2])):"";
			if(is_array($params)){
				$caption = isset($params["caption"])?trim($params["caption"]):'';
				$link  = isset($params["link"])?trim($params["link"]):"";
				$tmp["caption"]=$caption;
				$tmp["link"] = $link;
			}
			$itemArray[] = $tmp;
		}
		return $itemArray;
	}
	function parseContentAccordion($strcontent = ""){
		$regex = '#\[accordionslide ([^\]]*)\]([^\[]*)\[/accordionslide\]#m';
		preg_match_all ($regex, $strcontent, $matches, PREG_SET_ORDER);
		$itemArray = array();
		foreach($matches as $match){
			$params = $this->parseOptions($match[1]);
			$tmp = array();
			$tmp["content"] =  isset($match[2])?str_replace(array("\n", "\r"),array("<br />", ""),trim($match[2])):"";
			if(is_array($params)){
				$title = isset($params["title"])?trim($params["title"]):'';
				$tmp["title"] = $title;
			}
			$itemArray[] = $tmp;
		}
		return $itemArray;
	}
	/**
	 *
	 */
	function onAfterRender(){

		$mainframe = JFactory::getApplication();
		if ( $mainframe->isAdmin() ) { 
			return; 
		}
		$_body = JResponse::getBody();
		$_body = $this->PrepareContent($_body);
		if ( $_body ) {
			JResponse::setBody( $_body );
		}
		return true;
	}
	private function checkExistsShortCode( $content = ""){
		$checked = false;
		if(!empty($this->_shortcodes)){
			
			foreach($this->_shortcodes as $key=>$val){
				$test = JString::strpos( $content, $val["codeCheck"] );
				if (isset($val["codeCheck"]) && JString::strpos( $content, $val["codeCheck"] ) !== false){
					$checked = true;
					break;
				}
			}
		}
		return $checked;
	}
	private function loadMediaFile($content = ""){
		
		return $content;
	}
	
	/**
	 *
	 */
	function PrepareContent( &$body )
	{ 
		if (!$this->checkExistsShortCode($body)){
			return $body;
		}
		$disablePlugin = $this->_params->get( 'disable_plugin', '0' ); 
		if( !(int)$disablePlugin ){
			$body = $this->loadMediaFile( $body );
		}
		$matches_list = $this->parseData($body);
		$dataArray = array();
		
		if( !empty($matches_list) && count($matches_list) > 0 )
		{
			foreach($matches_list as $key=>$matches){
				$body = $this->prepareContentByType($key, $matches, $body);
			}
			
		}
		return $body;
	}
	
 }
 ?>