<?php

if( ! class_exists('Term_Images') ):

define('TERM_IMAGES_VERSION', '1.0');
define('TERM_IMAGES_SETTINGS', 'term_images_settings');



class Term_Images{

	public $text_domain = 'ti';
	public $settings_name = TERM_IMAGES_SETTINGS;

	public $thumb_w;
	public $thumb_h;
	public $medium_w;
	public $medium_h;
	public $large_w;
	public $large_h;

	function __construct(){

		$this->thumb_w = get_option('thumnail_size_w', 150);
		$this->thumb_h = get_option('thumnail_size_h', 150);
		$this->medium_w = get_option('medium_size_w', 300);
		$this->medium_h = get_option('medium_size_h', 300);
		$this->large_w = get_option('large_size_w', 1024);
		$this->large_h = get_option('large_size_h', 1024);

		add_action('admin_menu', array(&$this, 'on_admin_menu') );
		add_action('admin_init', array(&$this, 'on_admin_init') );

		add_action('init', array(&$this, 'add_image_sizes') );
	}

	function on_admin_menu(){
		add_options_page(__('Terms Images', $this->text_domain), __('Terms Images', $this->text_domain), 'manage_categories', 'term-images', array(&$this, 'admin_menu_page') );

	}

	function on_admin_init(){
		register_setting( TERM_IMAGES_SETTINGS, TERM_IMAGES_SETTINGS, array(&$this,'term_images_validate') );
		add_settings_section('term_images_main', 'Main Settings', array(&$this,'term_images_main_html'), 'term-images');

		$settings = get_option($this->settings_name);
		if($settings) {
			foreach($settings as $key => $taxonomy) {
				if($taxonomy['use']) {
					add_filter( 'manage_' . $key . '_custom_column', array(&$this, 'taxonomy_rows'), 10, 3 );
					add_filter( 'manage_edit-' . $key . '_columns',  array(&$this, 'taxonomy_columns') );


				}
			}
		}

	}

	function taxonomy_rows($row, $column_name, $term_id ){

		//return $column_name . $row;

		if( 'term-images' !== $column_name ) return $row;

		global $taxonomy;

		return $row . $this->image_widget( $term_id, $taxonomy );

	}

	function taxonomy_columns( $columns ){

		$new_columns = array_slice( $columns, 0, 1, true)
		+ array('term-images' => esc_html__( 'Image', $this->text_domain ) )
		+ array_slice( $columns, 1, null, true );

		return $new_columns;
	}

	function image_widget($term_id, $taxonomy){
		$o  = '<div>';
		$o .= '<span style="position:relative; display: inline-block; overflow: hidden; cursor: pointer; vertical-align: top;">';
		$o .= '<input type="file" name="file" class="" size="1" style="opacity: 0;filter: alpha(opacity=1); cursor: pointer; position: absolute; top: 0; right: 0; width: auto; font-size: 400%;" />';
		$o .= '<button type="button" class="" style="cursor: pointer; display: inline-block; width: 40px; height: 40px; background-color: #999; color: #ddd; border: 0; font-size: 30px; font-weight: 900; line-height: 30px; text-shadow: 1px 1px 1px #444;">?</button>';
		$o .= '</span>';
		$o .= '</div>';
		return $o;
	}

	/**
	* function get_setting
	* @param string $key A setting key, or -> separated list of keys to go multiple levels into an array
	* @param mixed $default Returns when setting is not set
	*
	* an easy way to get to our settings array without undefined indexes
	*/
	function get_setting($key, $default = false) {
		$settings = get_option( $this->settings_name );
		$keys = explode('->', $key);
		array_map('trim', $keys);
		if (count($keys) == 1)
		$setting = isset($settings[$keys[0]]) ? $settings[$keys[0]] : $default;
		else if (count($keys) == 2)
		$setting = isset($settings[$keys[0]][$keys[1]]) ? $settings[$keys[0]][$keys[1]] : $default;
		else if (count($keys) == 3)
		$setting = isset($settings[$keys[0]][$keys[1]][$keys[2]]) ? $settings[$keys[0]][$keys[1]][$keys[2]] : $default;
		else if (count($keys) == 4)
		$setting = isset($settings[$keys[0]][$keys[1]][$keys[2]][$keys[3]]) ? $settings[$keys[0]][$keys[1]][$keys[2]][$keys[3]] : $default;

		return $setting;
	}


	function term_images_main_html(){

		global $_wp_additional_image_sizes;

		$settings = get_option( TERM_IMAGES_SETTINGS );
		$taxonomies = get_taxonomies( array('show_ui' => true), 'objects');
		//var_dump($_wp_additional_image_sizes);
		?>

		<table class="form-table">
			<thead>
				<tr>
					<th>
						<?php _e('Enable Images for', $this->text_domain); ?>
					</th>
					<th colspan="2">
						<?php _e('Image Sizes', $this->text_domain); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($taxonomies as $taxonomy): ?>
				<tr style="border-top: 1px solid #ddd;">
					<th>
						<?php
						printf('<input type="hidden" name="%s[%s][use]" value="0" />',
						TERM_IMAGES_SETTINGS,
						$taxonomy->name
						);

						printf('<label><input type="checkbox" id="%s_%s" name="%s[%s][use]" %s value="1" /> %s</label><br/>',
						TERM_IMAGES_SETTINGS,
						$taxonomy->name,
						TERM_IMAGES_SETTINGS,
						$taxonomy->name,
						checked($this->get_setting("{$taxonomy->name}->use", '0'), '1', false),
						$taxonomy->label
						);
						?>
					</th>
					<td>
						<table>
							<tr>
								<td style="padding-top: 0; padding-bottom: 0;">
									<?php printf(__('Thumbnail Size: ', $this->text_domain) ); ?>
								</td>

								<td style="padding-top: 0; padding-bottom: 0;">
									<?php
									printf(__('Width <input type="text" id="%s_thumb_width" name="%s[%s][thumb_width]" value="%s" size="2" /> Height <input type="text" id="%s_thumb_height" name="%s[%s][thumb_height]" value="%s" size="4" /><br/>', $this->text_domain),
									$taxonomy->name,
									TERM_IMAGES_SETTINGS,
									$taxonomy->name,
									intval( $this->get_setting("{$taxonomy->name}->thumb_width", $this->thumb_w) ),
									$taxonomy->name,
									TERM_IMAGES_SETTINGS,
									$taxonomy->name,
									intval( $this->get_setting("{$taxonomy->name}->thumb_height", $this->thumb_h) )
									) ;
									?>
								</td>
							</tr>
							<tr>
								<td style="padding-top: 0; padding-bottom: 0;">
									<?php printf(__('Medium Size: <br/>', $this->text_domain) ); ?>
								</td>
								<td style="padding-top: 0; padding-bottom: 0;">
									<?php
									printf(__('Width <input type="text" id="%s_medium_width" name="%s[%s][medium_width]" value="%s" size="2" /> Height <input type="text" id="%s_medium_height" name="%s[%s][medium_height]" value="%s" size="4" /><br/>', $this->text_domain),
									$taxonomy->name,
									TERM_IMAGES_SETTINGS,
									$taxonomy->name,
									intval( $this->get_setting("{$taxonomy->name}->medium_width", $this->medium_w) ),
									$taxonomy->name,
									TERM_IMAGES_SETTINGS,
									$taxonomy->name,
									intval( $this->get_setting("{$taxonomy->name}->medium_height", $this->medium_h) )
									) ;
									?>
								</td>
							</tr>
							<tr>
								<td style="padding-top: 0; padding-bottom: 0;">
									<?php printf(__('Large Size: <br/>', $this->text_domain) ); ?>
								</td>
								<td style="padding-top: 0; padding-bottom: 0;">
									<?php
									printf(__('Width <input type="text" id="%s_large_width" name="%s[%s][large_width]" value="%s" size="2" /> Height <input type="text" id="%s_large_height" name="%s[%s][large_height]" value="%s" size="4" /><br/>', $this->text_domain),
									$taxonomy->name,
									TERM_IMAGES_SETTINGS,
									$taxonomy->name,
									intval( $this->get_setting("{$taxonomy->name}->large_width", $this->large_w) ),
									$taxonomy->name,
									TERM_IMAGES_SETTINGS,
									$taxonomy->name,
									intval( $this->get_setting("{$taxonomy->name}->large_height", $this->large_h) )
									) ;
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>

		<?php
	}

	function term_images_validate($input){

		return $input;
	}

	function admin_menu_page(){
		?>

		<div class="wrap">
			<h2><?php printf( __('Term Images Settings %s', $this->text_domain), TERM_IMAGES_VERSION );?></h2>
			<form action="options.php" method="POST" >
				<?php settings_fields(TERM_IMAGES_SETTINGS); ?>

				<?php do_settings_sections('term-images'); ?>

				<input class="button-primary" name="Submit" type="submit" value="<?php esc_attr_e('Save Settings'); ?>" />
			</form>

		</div>

		<?php
	}

	function add_image_sizes(){

		if( !$settings = get_option($this->settings_name) ) return;

		foreach($settings as $key => $taxonomy){
			if($taxonomy['use']) {
				add_image_size("{$key}-thumb", intval($taxonomy['thumb_width']), intval($taxonomy['thumb_height']) );
				add_image_size("{$key}-medium", intval($taxonomy['medium_width']), intval($taxonomy['medium_height']) );
				add_image_size("{$key}-large", intval($taxonomy['large_width']), intval($taxonomy['large_height']) );
			}
		}
	}

}

global $Term_Images;

$Term_Images  = new Term_Images;

endif;
