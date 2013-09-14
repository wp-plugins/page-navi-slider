<?php 

class WPNS_SETTING_PAGE{
	
	//Holds the values to be used in the fields callbacks
  private $_options;
	private $_default_settings;

  //Start up
	public function __construct($default_settings){
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
    add_action( 'admin_init', array( $this, 'page_init' ) );
		$this->_default_settings = $default_settings;
  }

  //Add options page
  public function add_plugin_page(){
		add_options_page('Page navi slider', 'Page navi slider', 'manage_options', 'wpns_settings_admin', array( $this, 'create_admin_page' ));
		
		wp_enqueue_style ('jquery-ui-style', plugins_url('../style/jquery-ui.css', __FILE__));
		wp_enqueue_script('accordion-style',  plugins_url('../js/accordion.js', __FILE__),	array( 'jquery', 'jquery-ui-slider', 'jquery-ui-accordion' ),true	);
		
		wp_register_style( 'page_navi_slider_style', plugins_url('../style/page-navi-slider.css', __FILE__) );
		wp_enqueue_style( 'page_navi_slider_style' );
		if(ereg('MSIE 7',$_SERVER['HTTP_USER_AGENT'])){
			wp_register_style('page_navi_slider_styleIE', plugins_url('../style/page-navi-slider.ie.css', __FILE__) );
			wp_enqueue_style('page_navi_slider_styleIE' );
		} 
		
		wp_enqueue_script('page-navi-slider-script',  plugins_url('../js/page-navi-slider.min.js', __FILE__),	array( 'jquery', 'jquery-ui-slider' ),true	);
		wp_enqueue_script('jQueryUiTouch',  plugins_url('../js/jquery.ui.touch-punch.min.js', __FILE__),	array( 'jquery' ), true);
		wp_enqueue_script('jscolor',  plugins_url('../js/jsColor/jscolor.js', __FILE__),	array( 'jquery' ), true);
  }

  //Options page callback
  public function create_admin_page(){
  $this->_options = get_option( 'wpns_settings' );	
	wpns_install();
	?>

	<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Page navi slider</h2> 
		<div style="max-width : 50em;">
    <h3><?php _e('Preview','page-navi-slider');?></h3> 	
		<?php require_once(dirname( __FILE__ ) . '/preview.php'); ?>
		<h3><?php _e('Settings','page-navi-slider');?></h3> 
		
    <form method="post" action="options.php">
			<?php
				echo '<div id="wpns_accordion">';
				settings_fields( 'wpns_option_group' );
				$this->wpns_do_settings_sections( 'wpns_settings_admin' );
				echo '</div>';
			?>
			<table><tr>
			<td><?php submit_button(); ?></td>
			<td><?php submit_button(__('Reset to default','page-navi-slider'),'reset','reset_clicked'); ?></td>
			</tr></table>
		</form>
		</div>
	</div>

	<?php
	}

	
	//Adaptated do_settings_sections function 
	//Section info is included in the table
	//to be included in the jQuery accordions panels
	function wpns_do_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;
		if ( ! isset( $wp_settings_sections ) || !isset( $wp_settings_sections[$page] ) )		return;
		
		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			if ( $section['title'] ) 	echo "<h3>{$section['title']}</h3>\n";
			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )	continue;
			echo '<table class="form-table wpns_setting_table">';
			if ( $section['callback'] ){
				echo "<tr><td colspan=2>";
				call_user_func( $section['callback'], $section );
				echo "</td></tr>";
			};
			do_settings_fields( $page, $section['id'] );
			echo '</table>';
		}
	}

	//Register and add settings
	public function page_init(){        
		register_setting('wpns_option_group','wpns_settings', array( $this, 'sanitize' ));
		//sections
		add_settings_section('wpns_preview_section',__('Preview options','page-navi-slider'), array( $this, 'print_preview_info' ), 'wpns_settings_admin');
		add_settings_section('wpns_general_section',__('General settings','page-navi-slider'), array( $this, 'print_general_info' ), 'wpns_settings_admin');
		add_settings_section('wpns_caption_section',__('Caption','page-navi-slider'), array( $this, 'print_caption_info' ), 'wpns_settings_admin');
		add_settings_section('wpns_font_section',__('Fonts','page-navi-slider'), array( $this, 'print_font_info' ), 'wpns_settings_admin');
		add_settings_section('wpns_color_section',__('Colors','page-navi-slider'), array( $this, 'print_color_info' ), 'wpns_settings_admin');
		add_settings_section('wpns_border_section',__('Borders','page-navi-slider'), array( $this, 'print_border_info' ), 'wpns_settings_admin');
		add_settings_section('wpns_autodisplay_section',__('Auto display','page-navi-slider')." <b> - ".__('CAUTION !','page-navi-slider')."</b>", array( $this, 'print_autodisplay_info' ), 'wpns_settings_admin');
		//fields - Preview
		add_settings_field('wpns_preview_background_color',__('Background color','page-navi-slider'),array( $this, 'wpns_preview_background_color_callback' ),'wpns_settings_admin','wpns_preview_section');      
		add_settings_field('wpns_preview_pages',__('Number of pages','page-navi-slider'),array( $this, 'wpns_preview_pages_callback' ),'wpns_settings_admin','wpns_preview_section');      
		add_settings_field('wpns_preview_current',__('Current page','page-navi-slider'),array( $this, 'wpns_preview_current_callback' ),'wpns_settings_admin','wpns_preview_section');      
		//fields - General
		add_settings_field('wpns_margin',__('Wrapper margin','page-navi-slider'),array( $this, 'wpns_margin_callback' ),'wpns_settings_admin','wpns_general_section');      
		add_settings_field('wpns_padding',__('Wrapper padding','page-navi-slider'),array( $this, 'wpns_padding_callback' ),'wpns_settings_admin','wpns_general_section');      
		add_settings_field('wpns_display_if_one_page',__('Display even if there is only one page','page-navi-slider'),array( $this, 'wpns_display_if_one_page_callback' ),'wpns_settings_admin','wpns_general_section');      
		add_settings_field('wpns_align',__('Alignment','page-navi-slider'),array( $this, 'wpns_align_callback' ),'wpns_settings_admin','wpns_general_section');		
		add_settings_field('wpns_spacing',__('Spacing between page numbers','page-navi-slider'),array( $this, 'wpns_spacing_callback' ),'wpns_settings_admin','wpns_general_section');      
		add_settings_field('wpns_show_total',__('Show total pages in the current one','page-navi-slider'),array( $this, 'wpns_show_total_callback' ),'wpns_settings_admin','wpns_general_section');      
		//fields - Fonts
		add_settings_field('wpns_caption_font',__('Caption','page-navi-slider'),array( $this, 'wpns_caption_font_callback' ),'wpns_settings_admin','wpns_font_section');      
		add_settings_field('wpns_page_font',__('Page numbers','page-navi-slider'),array( $this, 'wpns_page_font_callback' ),'wpns_settings_admin','wpns_font_section');      
		add_settings_field('wpns_current_font',__('Current page','page-navi-slider'),array( $this, 'wpns_current_font_callback' ),'wpns_settings_admin','wpns_font_section');      
		add_settings_field('wpns_hover_font',__('On hover','page-navi-slider'),array( $this, 'wpns_hover_font_callback' ),'wpns_settings_admin','wpns_font_section');      
		//fields - Title
		add_settings_field('wpns_caption_text',__('Text<br><small>%%NUMBER%% : current page</small><br><small>%%TOTAL%% : total pages<small>','page-navi-slider'),array( $this, 'wpns_caption_text_callback' ),'wpns_settings_admin','wpns_caption_section');      
		add_settings_field('wpns_caption_position',__('Position','page-navi-slider'),array( $this, 'wpns_caption_position_callback' ),'wpns_settings_admin','wpns_caption_section');      
		add_settings_field('wpns_caption_alignment',__('Alignment','page-navi-slider'),array( $this, 'wpns_caption_alignment_callback' ),'wpns_settings_admin','wpns_caption_section');      
		//fields - Colors
		add_settings_field('wpns_caption_color',__('Caption','page-navi-slider'),array( $this, 'wpns_caption_color_callback' ),'wpns_settings_admin','wpns_color_section');   
		add_settings_field('wpns_page_fore_color',__('Page numbers','page-navi-slider'),array( $this, 'wpns_page_fore_color_callback' ),'wpns_settings_admin','wpns_color_section');   
		add_settings_field('wpns_page_back_color',__('Page numbers background','page-navi-slider'),array( $this, 'wpns_page_back_color_callback' ),'wpns_settings_admin','wpns_color_section');    
		add_settings_field('wpns_current_fore_color',__('Current page','page-navi-slider'),array( $this, 'wpns_current_fore_color_callback' ),'wpns_settings_admin','wpns_color_section');   
		add_settings_field('wpns_current_back_color',__('Current page background','page-navi-slider'),array( $this, 'wpns_current_back_color_callback' ),'wpns_settings_admin','wpns_color_section');    
		add_settings_field('wpns_hover_fore_color',__('On hover','page-navi-slider'),array( $this, 'wpns_hover_fore_color_callback' ),'wpns_settings_admin','wpns_color_section');   
		add_settings_field('wpns_hover_back_color',__('On hover background','page-navi-slider'),array( $this, 'wpns_hover_back_color_callback' ),'wpns_settings_admin','wpns_color_section');    
		add_settings_field('wpns_slider_color',__('Slider','page-navi-slider'),array( $this, 'wpns_slider_color_callback' ),'wpns_settings_admin','wpns_color_section');
		add_settings_field('wpns_cursor_color',__('Handle','page-navi-slider'),array( $this, 'wpns_cursor_color_callback' ),'wpns_settings_admin','wpns_color_section');    
		//fields - Borders
		add_settings_field('wpns_radius',__('Radius <small>(<b>em</b>/px)</small>','page-navi-slider'),array( $this, 'wpns_radius_callback' ),'wpns_settings_admin','wpns_border_section');      
		add_settings_field('wpns_page_border',__('Page numbers','page-navi-slider'),array( $this, 'wpns_page_border_callback' ),'wpns_settings_admin','wpns_border_section');      
		add_settings_field('wpns_current_border',__('Current page','page-navi-slider'),array( $this, 'wpns_current_border_callback' ),'wpns_settings_admin','wpns_border_section');      
		add_settings_field('wpns_hover_border',__('On hover','page-navi-slider'),array( $this, 'wpns_hover_border_callback' ),'wpns_settings_admin','wpns_border_section');      
		add_settings_field('wpns_slider_border',__('Slider','page-navi-slider'),array( $this, 'wpns_slider_border_callback' ),'wpns_settings_admin','wpns_border_section');      
		add_settings_field('wpns_cursor_border',__('Cursor','page-navi-slider'),array( $this, 'wpns_cursor_border_callback' ),'wpns_settings_admin','wpns_border_section');      
		//fields - Auto display
		add_settings_field('wpns_auto_display',__('Display in footer','page-navi-slider'),array( $this, 'wpns_auto_display_callback' ),'wpns_settings_admin','wpns_autodisplay_section');      
		add_settings_field('wpns_auto_display_position',__('Location','page-navi-slider'),array( $this, 'wpns_auto_display_position_callback' ),'wpns_settings_admin','wpns_autodisplay_section');      
		
		}

	 /*Sanitize each setting field as needed
		 @param array $input Contains all settings fields as array keys*/
	public function sanitize( $input ){
		//if( !is_numeric( $input['id_number'] ) ) $input['id_number'] = '';  
		//if( !empty( $input['title'] ) ) $input['title'] = sanitize_text_field( $input['title'] );
		if (isset($_POST['reset_clicked'])){$input=$this->_default_settings;}
		return $input;
	}

	//Print the Section text
	public function print_preview_info(){
		echo "<span class='wpns_setting_small'>";
		_e('These settins only affect the preview on that page; ','page-navi-slider');
		_e('they do not have any impact on the plugin itself.','page-navi-slider');
		echo "<br/>";
		_e('The dotted border is just for preview purpose and will not be displayed on your page.','page-navi-slider');
		echo "</span>";
	}
	public function print_general_info(){
		echo "<span class='wpns_setting_small'>";
		_e('Use CSS shorthand for margin and padding; Syntax : top right bottom left.','page-navi-slider');
		echo "<br/>px / em / %";
		_e('can be used and mixed.','page-navi-slider');
		echo "<br/><b>";
		_e('The slider is shown only if the number of pages exceeds the plugin widht.','page-navi-slider');
		echo "</b><br/>";
		_e('Example : Set the margin to &laquo;1em 20% 0 20%&raquo;','page-navi-slider');
		echo "<br/>";
		_e('The plugin will use 60% of the available width and will apply a top margin of 1 em.','page-navi-slider');
		echo "</span>";
	}
	public function print_caption_info(){}
	public function print_font_info(){
		echo "<span class='wpns_setting_small'>";
		_e('Use CSS shorthand; Syntax : font-weight font-style font-variant font-size line-height font-family.','page-navi-slider');
		echo "<br/>";
		_e('Example: bold italic small-caps 1em/1.5em verdana,sans-serif','page-navi-slider');
		echo "<br/>";
		_e('Only work if you are specifying both the <b>font-size</b> and the <b>font-family</b>.','page-navi-slider');
		echo "<br/>";
		_e('The <b>font-family</b> command must always be <b>at the very end</b> of this shorthand command; <b>font-size</b> must come directly before this.','page-navi-slider');
		echo "<br/>";
		_e('If you do not specify the <b>font-weight, font-style or font-variant</b> then these values will automatically default to a value of <b>normal</b>.','page-navi-slider');
		echo "<br/>";
		_e('Let empty to use the parent element font.','page-navi-slider');
		echo "<br/>";
		_e('Choosing a bigger size for <b>current</b> and/or <b>On hover</b> will occur a <b>zoom effect</b>.','page-navi-slider');
		echo "<br/>";
		echo "</span>";
	}
	public function print_color_info(){
		echo "<span class='wpns_setting_small'>";
		_e('Let empty to use the parent element color.','page-navi-slider');
		echo "<br/>";
		_e('<b>transparent</b> allowed.','page-navi-slider');
		echo "<br/>";
		echo "</span>";
	}
	public function print_border_info(){
		echo "<span class='wpns_setting_small'>";
		_e('Use CSS shorthand; Syntax : thickness style color.','page-navi-slider');
		echo "<br/>";
		_e('<b>High radius</b> will occur in <b>surrounded</b> page nubers.','page-navi-slider');
		echo "<br/>";
		echo "</span>";
	}
	public function print_autodisplay_info(){
		echo "<span class='wpns_setting_small'><b>";
		_e('This feature depends on your theme and could return unexpected results.','page-navi-slider');
		echo "</b><br/>";
		_e('Prefer manual installation by adding the following code in your theme templates where you want the plugin to be displayed:','page-navi-slider');
		echo "<br/><b>";
		echo "&lt;php if(function_exists('page_navi_slider')){page_navi_slider()}; ?&gt;";
		echo "</b></span>";
	}


	//Get the settings option array and print one of its values
	//Preview	
	public function wpns_preview_background_color_callback(){
		printf('<input type="text" class="color {hash:true, adjust:false, required:false}" id="wpns_preview_background_color" name="wpns_settings[wpns_preview_background_color]" value="%s" />',esc_attr( $this->_options['wpns_preview_background_color']));
	}
	public function wpns_preview_pages_callback(){printf('<input type="text" id="wpns_preview_pages" name="wpns_settings[wpns_preview_pages]" value="%s" />',esc_attr( $this->_options['wpns_preview_pages']));}
	public function wpns_preview_current_callback(){printf('<input type="text" id="wpns_preview_current" name="wpns_settings[wpns_preview_current]" value="%s" />',esc_attr( $this->_options['wpns_preview_current']));}
	//General	
	public function wpns_margin_callback(){printf('<input type="text" id="wpns_margin" name="wpns_settings[wpns_margin]" value="%s" />',esc_attr( $this->_options['wpns_margin']));}
	public function wpns_padding_callback(){printf('<input type="text" id="wpns_padding" name="wpns_settings[wpns_padding]" value="%s" />',esc_attr( $this->_options['wpns_padding']));}
	public function wpns_display_if_one_page_callback(){printf('<input type="checkbox" id="wpns_display_if_one_page" name="wpns_settings[wpns_display_if_one_page]" value="1"' . checked( 1,$this->_options['wpns_display_if_one_page'],false) . '/>',esc_attr( $this->_options['wpns_display_if_one_page']));}
	public function wpns_align_callback(){
		printf('<select type="text" id="wpns_align" name="wpns_settings[wpns_align]" value="%s" />',esc_attr( $this->_options['wpns_align']));
		printf('<option value="left"'.selected( 'left',$this->_options['wpns_align'],false).'>left</option>');
		printf('<option value="center"'.selected( 'center',$this->_options['wpns_align'],false).'>center</option>');
		printf('<option value="right"'.selected( 'right',$this->_options['wpns_align'],false).'>right</option>');
		printf('</select>');
	}
	public function wpns_spacing_callback(){printf('<input type="text" id="wpns_spacing" name="wpns_settings[wpns_spacing]" value="%s" />',esc_attr( $this->_options['wpns_spacing']));}
	public function wpns_show_total_callback(){printf('<input type="checkbox" id="wpns_show_total" name="wpns_settings[wpns_show_total]" value="1"' . checked( 1,$this->_options['wpns_show_total'],false) . '/>',esc_attr( $this->_options['wpns_show_total']));}
	//Caption
	public function wpns_caption_text_callback(){printf('<input style="width : 100%%" type="text" id="wpns_caption_text" name="wpns_settings[wpns_caption_text]" value="%s" />',esc_attr( $this->_options['wpns_caption_text']));}
	public function wpns_caption_position_callback(){
		printf('<select type="text" id="wpns_caption_position" name="wpns_settings[wpns_caption_position]" value="%s" />',esc_attr( $this->_options['wpns_caption_position']));
		printf('<option value="top"'.selected( 'top',$this->_options['wpns_caption_position'],false).'>top</option>');
		printf('<option value="bottom"'.selected( 'bottom',$this->_options['wpns_caption_position'],false).'>bottom</option>');
		printf('</select>');
	}
	public function wpns_caption_alignment_callback(){
		printf('<select type="text" id="wpns_caption_alignment" name="wpns_settings[wpns_caption_alignment]" value="%s" />',esc_attr( $this->_options['wpns_caption_alignment']));
		printf('<option value="left"'.selected( 'left',$this->_options['wpns_caption_alignment'],false).'>left</option>');
		printf('<option value="center"'.selected( 'center',$this->_options['wpns_caption_alignment'],false).'>center</option>');
		printf('<option value="right"'.selected( 'right',$this->_options['wpns_caption_alignment'],false).'>right</option>');
		printf('</select>');
	}
	//Fonts
	public function wpns_caption_font_callback(){printf('<input style="width : 100%%" type="text" id="wpns_caption_font" name="wpns_settings[wpns_caption_font]" value="%s" />',esc_attr( $this->_options['wpns_caption_font']));}
	public function wpns_page_font_callback(){printf('<input style="width : 100%%" type="text" id="wpns_page_font" name="wpns_settings[wpns_page_font]" value="%s" />',esc_attr( $this->_options['wpns_page_font']));}
	public function wpns_current_font_callback(){printf('<input style="width : 100%%" type="text" id="wpns_current_font" name="wpns_settings[wpns_current_font]" value="%s" />',esc_attr( $this->_options['wpns_current_font']));}
	public function wpns_hover_font_callback(){printf('<input style="width : 100%%" type="text" id="wpns_hover_font" name="wpns_settings[wpns_hover_font]" value="%s" />',esc_attr( $this->_options['wpns_hover_font']));}
	//Colors
	public function wpns_caption_color_callback(){printf('<input class="color {hash:true, adjust:false, required:false}" type="text" id="wpns_caption_color" name="wpns_settings[wpns_caption_color]" value="%s" />',esc_attr( $this->_options['wpns_caption_color']));}
	public function wpns_page_fore_color_callback(){printf('<input class="color {hash:true, adjust:false, required:false}" type="text" id="wpns_page_fore_color" name="wpns_settings[wpns_page_fore_color]" value="%s" />',esc_attr( $this->_options['wpns_page_fore_color']));}
	public function wpns_page_back_color_callback(){printf('<input class="color {hash:true, adjust:false, required:false}" type="text" id="wpns_page_back_color" name="wpns_settings[wpns_page_back_color]" value="%s" />',esc_attr( $this->_options['wpns_page_back_color']));}
	public function wpns_current_fore_color_callback(){printf('<input class="color {hash:true, adjust:false, required:false}" type="text" id="wpns_current_fore_color" name="wpns_settings[wpns_current_fore_color]" value="%s" />',esc_attr( $this->_options['wpns_current_fore_color']));}
	public function wpns_current_back_color_callback(){printf('<input class="color {hash:true, adjust:false, required:false}" type="text" id="wpns_current_back_color" name="wpns_settings[wpns_current_back_color]" value="%s" />',esc_attr( $this->_options['wpns_current_back_color']));}
	public function wpns_hover_fore_color_callback(){printf('<input class="color {hash:true, adjust:false, required:false}" type="text" id="wpns_hover_fore_color" name="wpns_settings[wpns_hover_fore_color]" value="%s" />',esc_attr( $this->_options['wpns_hover_fore_color']));}
	public function wpns_hover_back_color_callback(){printf('<input class="color {hash:true, adjust:false, required:false}" type="text" id="wpns_hover_back_color" name="wpns_settings[wpns_hover_back_color]" value="%s" />',esc_attr( $this->_options['wpns_hover_back_color']));}
	public function wpns_slider_color_callback(){printf('<input class="color {hash:true, adjust:false, required:false}" type="text" id="wpns_slider_color" name="wpns_settings[wpns_slider_color]" value="%s" />',esc_attr( $this->_options['wpns_slider_color']));}
	public function wpns_cursor_color_callback(){printf('<input class="color {hash:true, adjust:false, required:false}" type="text" id="wpns_cursor_color" name="wpns_settings[wpns_cursor_color]" value="%s" />',esc_attr( $this->_options['wpns_cursor_color']));}
	//Borders
	public function wpns_radius_callback(){printf('<input type="text" id="wpns_radius" name="wpns_settings[wpns_radius]" value="%s" />',esc_attr( $this->_options['wpns_radius']));}
	public function wpns_page_border_callback(){printf('<input type="text" id="wpns_page_border" name="wpns_settings[wpns_page_border]" value="%s" />',esc_attr( $this->_options['wpns_page_border']));}
	public function wpns_current_border_callback(){printf('<input type="text" id="wpns_current_border" name="wpns_settings[wpns_current_border]" value="%s" />',esc_attr( $this->_options['wpns_current_border']));}
	public function wpns_hover_border_callback(){printf('<input type="text" id="wpns_hover_border" name="wpns_settings[wpns_hover_border]" value="%s" />',esc_attr( $this->_options['wpns_hover_border']));}
	public function wpns_slider_border_callback(){printf('<input type="text" id="wpns_slider_border" name="wpns_settings[wpns_slider_border]" value="%s" />',esc_attr( $this->_options['wpns_slider_border']));}
	public function wpns_cursor_border_callback(){printf('<input type="text" id="wpns_cursor_border" name="wpns_settings[wpns_cursor_border]" value="%s" />',esc_attr( $this->_options['wpns_cursor_border']));}
	//Auto display
	public function wpns_auto_display_callback(){printf('<input type="checkbox" id="wpns_auto_display" name="wpns_settings[wpns_auto_display]" value="1"' . checked( 1,$this->_options['wpns_auto_display'],false) . '/>',esc_attr( $this->_options['wpns_auto_display']));}
	public function wpns_auto_display_position_callback(){
		printf('<select type="text" id="wpns_auto_display_position" name="wpns_settings[wpns_auto_display_position]" value="%s" />',esc_attr( $this->_options['wpns_auto_display_position']));
		printf('<option value="footer top"'.selected( 'footer top',$this->_options['wpns_auto_display_position'],false).'>footer top</option>');
		printf('<option value="footer bottom"'.selected( 'footer bottom',$this->_options['wpns_auto_display_position'],false).'>footer bottom</option>');
		printf('</select>');
	}
}

if( is_admin() ) $wpns_settings_page = new WPNS_SETTING_PAGE($default_settings);
?>