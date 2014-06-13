<?php
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

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

		add_action('wp_ajax_' . 'ti-term-image', array(&$this, 'on_ajax_ti_term_image'));
		add_action('wp_ajax_' . 'ti-remove-image', array(&$this, 'on_ajax_ti_remove_image'));

		add_shortcode('ti', array(&$this, 'ti_sc') );
	}

	function on_admin_menu(){
//		add_options_page(__('Terms Images', $this->text_domain), __('Terms Images', $this->text_domain), 'manage_categories', 'term-images', array(&$this, 'admin_menu_page') );
		
//		$this->jobs_menu_page = add_submenu_page('edit.php?post_type=jbp_job',
//		__('Term Images', JBP_TEXT_DOMAIN),
//		__('<span id="jbp-term-images">Term Images</span>', JBP_TEXT_DOMAIN),
//		'manage_categories', 'term-images',
//		array($this, 'admin_menu_page') );
//		
//		$this->jobs_menu_page = add_submenu_page('edit.php?post_type=jbp_pro',
//		__('Term Images', JBP_TEXT_DOMAIN),
//		__('Term Images', JBP_TEXT_DOMAIN),
//		'manage_categories', 'term-images',
//		array($this, 'admin_menu_page') );

	}

	private function _key( $term_id, $taxonomy='') {
		return sprintf( '%d', intval($term_id));
	}

	function on_admin_init(){
		register_setting( TERM_IMAGES_SETTINGS, TERM_IMAGES_SETTINGS, array(&$this,'term_images_validate') );
		add_settings_section('term_images_main', 'Main Settings', array(&$this,'term_images_main_html'), 'term-images');

		$settings = get_option($this->settings_name);
		if($settings) {
			foreach($settings as $key => $taxonomy) {
				if( $this->get_setting( "{$key}->use", 0) ) {
					add_filter( 'manage_' . $key . '_custom_column', array(&$this, 'taxonomy_rows'), 10, 3 );
					add_filter( 'manage_edit-' . $key . '_columns',  array(&$this, 'taxonomy_columns') );

					add_action("{$key}_edit_form_fields", array(&$this, 'on_edit_form_fields'), 10, 2 );

					add_action("after-{$key}-table", array(&$this, 'upload_js') );
				}
			}
		}
	}

	function taxonomy_rows($row, $column_name, $term_id ){
		global $taxonomy;

		if( 'term-images' !== $column_name ) return $row;

		return $row . $this->image_widget( $term_id );

	}

	function taxonomy_columns( $columns ){

		$new_columns = array_slice( $columns, 0, 1, true)
		+ array('term-images' => esc_html__( 'Image', $this->text_domain ) )
		+ array_slice( $columns, 1, null, true );

		return $new_columns;
	}

	function image_widget($term_id=0, $width=40, $height=40 ){
		global $taxonomy;

		$key = $this->_key($term_id, $taxonomy);
		$attachment_id = $this->get_id_by_meta( $key );
		$image = $attachment_id ? wp_get_attachment_image_src( $attachment_id, array($width, $height)): false;

		$o = sprintf('<span class="ti-remove-button" data-term-id="%d" data-taxonomy="%s" title="%s" style="position:absolute; margin: -7px 0 0 -7px;">%s</span>',
		$term_id,
		$taxonomy,
		__('Click to remove this images', $this->text_domain),
		$this->delete_img() );


		$o .= sprintf( '<button value="%d" id="ti_%d" name="ti_term_id" data-taxonomy="%s" title="%s" type="button" class="ti-upload-image-button" style="cursor: pointer; display: inline-block; min-width: 40px;  width: %dpx; height: %dpx; background-color: #fff; color: #ddd;  font-size: 30px; font-weight: 900; padding: 0;line-height: 30px; text-shadow: 1px 1px 1px #444;" >%s</button>',
		$term_id,
		$term_id,
		$taxonomy,
		__('Click to edit or change the images', $this->text_domain),
		$width,
		$height,
		$image ? sprintf('<img src="%s" style="width: 100%%; height:100%%; margin: 0;" />', $image[0]) : '?' );

		$o .= sprintf('<br /><code style="font-size: 9px;white-space: nowrap;">[ti term="%s"]</code>', $term_id);

		return $o;
	}

	function on_edit_form_fields( $tag = null, $taxonomy = null){

		if(empty($_GET['action']) || $_GET['action'] != 'edit') return;
		if(empty($_GET['tag_ID']) ) return;
		?>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="description"><?php esc_attr_e('Tag Image', $this->text_domain); ?></label></th>
			<td>

				<?php echo $this->image_widget( $tag->term_id,
				intval( $this->get_setting("{$tag->taxonomy}->medium_width", $this->medium_w) ),
				intval( $this->get_setting("{$tag->taxonomy}->medium_height", $this->medium_h) )
				); ?><br />

				<br />
				<span class="description"><?php esc_html_e('Click the image to edit or change it for this tag.'); ?></span>
				<?php $this->upload_js(); ?>
			</td>
		</tr>

		<?php
	}


	function on_ajax_ti_term_image(){
		$params = stripslashes_deep($_REQUEST);

		if( !wp_verify_nonce($params['_wpnonce'], 'ti-term-image') ){
			return;
		}

		$term_id = intval($params['term_id']);
		$taxonomy = $params['taxonomy'];

		if ( ! taxonomy_exists($taxonomy) ) {
			exit( __('Invalid taxonomy'));
		}

		$key = $this->_key($term_id, $taxonomy);

		//get any attachments with this term_id
		$attachment_id = $this->get_id_by_meta( $key );
		if($attachment_id){
			delete_post_meta($attachment_id, '_ti_term_image', $key);
		}
		//might have multiple for the same image.
		add_post_meta( $params['attachment_id'], '_ti_term_image', $key);
		exit;
	}

	function on_ajax_ti_remove_image(){
		$params = stripslashes_deep($_REQUEST);

		if( !wp_verify_nonce($params['_wpnonce'], 'ti-remove-image') ){
			exit('failed');
		}

		$term_id = intval($params['term_id']);
		$taxonomy = $params['taxonomy'];

		if ( ! taxonomy_exists($taxonomy) ) {
			exit( __('Invalid taxonomy'));
		}

		$key = $this->_key($term_id, $taxonomy);

		//get any attchemnts with this term_id
		$attachment_id = $this->get_id_by_meta( $key );
		if($attachment_id){
			delete_post_meta($attachment_id, '_ti_term_image', $key);
		}
		exit('ok');
	}

	/**
	* Get a  page by meta value
	*
	* @return int $page[0] / bool false
	*/
	function get_id_by_meta( $key ) {
		global $wpdb;

		//To avoid "the_posts" filters do a direct call to the database to find the post by meta
		$ids = array_keys(
		$wpdb->get_results($wpdb->prepare(
		"
		SELECT post_id
		FROM {$wpdb->postmeta}
		WHERE meta_key= %s
		AND meta_value=%s
		", "_ti_term_image", $key), OBJECT_K )
		);

		if ( isset( $ids[0] ) && 0 < $ids[0] ){
			return $ids[0];
		}

		return false;
	}

	/**
	* function get_key
	* @param string $key A setting key, or -> separated list of keys to go multiple levels into an array
	* @param mixed $default Returns when setting is not set
	*
	* an easy way to get to our settings array without undefined indexes
	*/
	function get_key($key, $default = null, $settings=array() ) {

		$keys = explode('->', $key);
		$keys = array_map('trim', $keys);
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

		<code><b>Shortcode:</b> [ti term="{term_id}" taxonomy="{taxonomy}" size="{size label}" width="{100}" height="{100}" ]</code>
		<table class="form-table">
			<thead>
				<tr>
					<th>
						<?php esc_html_e('Enable Images for', $this->text_domain); ?>
					</th>
					<th colspan="2">
						<span style="padding-left: 20px"><?php esc_html_e('Image Sizes', $this->text_domain); ?></span>
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
						<br /><code style="font-size: x-small;">[ti size="<?php echo"{$taxonomy->name}-thumb"?>"] </code>
						<br /><code style="font-size: x-small;">[ti size="<?php echo"{$taxonomy->name}-medium"?>"]</code>
						<br /><code style="font-size: x-small;">[ti size="<?php echo"{$taxonomy->name}-large"?>"] </code>
					</th>
					<td>
						<table>
							<tr>
								<td style="padding-top: 0; padding-bottom: 0;">
									<?php printf(__('Thumbnail Size: ', $this->text_domain) ); ?>
								</td>

								<td style="padding-top: 0; padding-bottom: 0;">
									<?php
									printf(__('Width <input type="text" id="%s_thumb_width" name="%s[%s][thumb_width]" value="%s" size="4" /> Height <input type="text" id="%s_thumb_height" name="%s[%s][thumb_height]" value="%s" size="4" /><br/>', $this->text_domain),
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
									printf(__('Width <input type="text" id="%s_medium_width" name="%s[%s][medium_width]" value="%s" size="4" /> Height <input type="text" id="%s_medium_height" name="%s[%s][medium_height]" value="%s" size="4" /><br/>', $this->text_domain),
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
									printf(__('Width <input type="text" id="%s_large_width" name="%s[%s][large_width]" value="%s" size="4" /> Height <input type="text" id="%s_large_height" name="%s[%s][large_height]" value="%s" size="4" /><br/>', $this->text_domain),
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

				<input class="button-primary" name="Submit" type="submit" value="<?php echo esc_attr('Save Settings'); ?>" />
			</form>

		</div>

		<?php
	}

	function add_image_sizes(){

		if( !$settings = get_option($this->settings_name) ) return;

		foreach($settings as $key => $taxonomy){
			if( $this->get_setting("{$key}->use", 0) ) {
				add_image_size("{$key}-thumb", intval($taxonomy['thumb_width']), intval($taxonomy['thumb_height']) );
				add_image_size("{$key}-medium", intval($taxonomy['medium_width']), intval($taxonomy['medium_height']) );
				add_image_size("{$key}-large", intval($taxonomy['large_width']), intval($taxonomy['large_height']) );
			}
		}
	}

	function ti_sc( $atts, $content = null ) {
		global $wp_query, $post;
		extract( shortcode_atts( array(
		'term' => '',
		'taxonomy' => '',
		'class' => '',
		'size' => 'thumbnail',
		'width' => '',
		'height' => '',
		), $atts ) );

		//var_dump($wp_query);

		$settings = get_option($this->settings_name);
		$queried_object = get_queried_object();

		$taxonomy = (empty($taxonomy) && is_tax() ) ? $queried_object->taxonomy : $taxonomy;

		$use = $settings ? !empty($settings[$taxonomy]['use']) : false;

		$term = (empty($term) && is_tax()) ? 	$queried_object->term_id : $term;


		$size = (min($width, $height) != 0) ? array($width, $height) : $size;

		$attr = empty($class) ? '' : array('class' => $class);

		if( $term_id = term_exists( intval($term), $taxonomy ) ) {
			if(is_array($term_id) ){
				$term_id = $term_id['term_id'];
			}
		}

		if( $use && in_array($size, array('thumb', 'thumbnail', 'medium', 'large') ) ) {
			switch ($size) {
				case 'thumb':
				case 'thumbnail' : $size = "{$taxonomy}-thumb"; break;
				case 'medium' : $size = "{$taxonomy}-medium"; break;
				case 'large' : $size = "{$taxonomy}-large"; break;
			}
		}

		$key = $this->_key($term_id);
		$attachment_id = $this->get_id_by_meta( $key );

		$image = $attachment_id ? wp_get_attachment_image( $attachment_id, $size, false, $attr ) : false;

		return $image;
	}

	function upload_js(){

		wp_enqueue_media();
		?>

		<script type="text/javascript">
			jQuery(document).ready( function($) {
				// Uploading files
				var ti_file_frame;
				var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
				var set_to_term_id = 0; // Set this
				var taxonomy = 0;
				var $button;

				$('.ti-remove-button').on('click', function( event ){
					event.preventDefault();

					$remove = $(this);
					$remove.next( 'button.ti-upload-image-button' ).html('?');

					$.get( '<?php echo admin_url('admin-ajax.php'); ?>', {
						"action": "ti-remove-image",
						"term_id": $remove.data('term-id'),
						"taxonomy": $remove.data('taxonomy'),
						"_wpnonce": "<?php echo wp_create_nonce('ti-remove-image');?>"
					});
				});

				$('.ti-upload-image-button').on('click', function( event ){
					event.preventDefault();
					$button = $(this);
					set_to_term_id = $button.val();
					taxonomy = $button.data('taxonomy');

					// If the media frame already exists, reopen it.
					if ( ti_file_frame ) {
						// Set the post ID to what we want
						ti_file_frame.uploader.uploader.param( 'post_id', set_to_term_id );
						// Open frame
						ti_file_frame.open();
						return;
					} else {
						// Set the wp.media post id so the uploader grabs the ID we want when initialised
						wp.media.model.settings.post.id = set_to_term_id;
					}

					// Create the media frame.
					ti_file_frame = wp.media.frames.ti_file_frame = wp.media({
						title: jQuery( this ).data( 'uploader_title' ),
						button: {
							text: jQuery( this ).data( 'uploader_button_text' ),
						},
						multiple: false  // Set to true to allow multiple files to be selected
					});

					// When an image is selected, run a callback.
					ti_file_frame.on( 'select', function() {
						// We set multiple to false so only get one image from the uploader
						attachment = ti_file_frame.state().get('selection').first().toJSON();

						// Do something with attachment.id and/or attachment.url here
						$button.empty();
						$('<img />',{
							src : attachment.url,
							alt : attachment.name
						}).css( {
							"width":"100%",
							"height": "100%",
							"margin": "0"
						})
						.appendTo( $button );


						$.get( '<?php echo admin_url('admin-ajax.php'); ?>', {
							"action": "ti-term-image",
							"term_id": set_to_term_id,
							"taxonomy": taxonomy,
							"attachment_id": attachment.id,
							"attachment_url": attachment.url,
							"_wpnonce": "<?php echo wp_create_nonce('ti-term-image');?>"
						});


						// Restore the main post ID
						wp.media.model.settings.post.id = wp_media_post_id;
					});

					// Finally, open the modal
					ti_file_frame.open();
				});

				// Restore the main ID when the add media button is pressed
				jQuery('a.add_media').on('click', function() {
					wp.media.model.settings.post.id = wp_media_post_id;
				});
			});
		</script>

		<?php
	}


	function delete_img(){

		return '<img alt="" src="data:image/png;base64,
		iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAACXBIWXMAAAsTAAALEwEAmpwYAAAKT2lD
		Q1BQaG90b3Nob3AgSUNDIHByb2ZpbGUAAHjanVNnVFPpFj333vRCS4iAlEtvUhUIIFJCi4AUkSYqIQkQ
		SoghodkVUcERRUUEG8igiAOOjoCMFVEsDIoK2AfkIaKOg6OIisr74Xuja9a89+bN/rXXPues852zzwfA
		CAyWSDNRNYAMqUIeEeCDx8TG4eQuQIEKJHAAEAizZCFz/SMBAPh+PDwrIsAHvgABeNMLCADATZvAMByH
		/w/qQplcAYCEAcB0kThLCIAUAEB6jkKmAEBGAYCdmCZTAKAEAGDLY2LjAFAtAGAnf+bTAICd+Jl7AQBb
		lCEVAaCRACATZYhEAGg7AKzPVopFAFgwABRmS8Q5ANgtADBJV2ZIALC3AMDOEAuyAAgMADBRiIUpAAR7
		AGDIIyN4AISZABRG8lc88SuuEOcqAAB4mbI8uSQ5RYFbCC1xB1dXLh4ozkkXKxQ2YQJhmkAuwnmZGTKB
		NA/g88wAAKCRFRHgg/P9eM4Ors7ONo62Dl8t6r8G/yJiYuP+5c+rcEAAAOF0ftH+LC+zGoA7BoBt/qIl
		7gRoXgugdfeLZrIPQLUAoOnaV/Nw+H48PEWhkLnZ2eXk5NhKxEJbYcpXff5nwl/AV/1s+X48/Pf14L7i
		JIEyXYFHBPjgwsz0TKUcz5IJhGLc5o9H/LcL//wd0yLESWK5WCoU41EScY5EmozzMqUiiUKSKcUl0v9k
		4t8s+wM+3zUAsGo+AXuRLahdYwP2SycQWHTA4vcAAPK7b8HUKAgDgGiD4c93/+8//UegJQCAZkmScQAA
		XkQkLlTKsz/HCAAARKCBKrBBG/TBGCzABhzBBdzBC/xgNoRCJMTCQhBCCmSAHHJgKayCQiiGzbAdKmAv
		1EAdNMBRaIaTcA4uwlW4Dj1wD/phCJ7BKLyBCQRByAgTYSHaiAFiilgjjggXmYX4IcFIBBKLJCDJiBRR
		IkuRNUgxUopUIFVIHfI9cgI5h1xGupE7yAAygvyGvEcxlIGyUT3UDLVDuag3GoRGogvQZHQxmo8WoJvQ
		crQaPYw2oefQq2gP2o8+Q8cwwOgYBzPEbDAuxsNCsTgsCZNjy7EirAyrxhqwVqwDu4n1Y8+xdwQSgUXA
		CTYEd0IgYR5BSFhMWE7YSKggHCQ0EdoJNwkDhFHCJyKTqEu0JroR+cQYYjIxh1hILCPWEo8TLxB7iEPE
		NyQSiUMyJ7mQAkmxpFTSEtJG0m5SI+ksqZs0SBojk8naZGuyBzmULCAryIXkneTD5DPkG+Qh8lsKnWJA
		caT4U+IoUspqShnlEOU05QZlmDJBVaOaUt2ooVQRNY9aQq2htlKvUYeoEzR1mjnNgxZJS6WtopXTGmgX
		aPdpr+h0uhHdlR5Ol9BX0svpR+iX6AP0dwwNhhWDx4hnKBmbGAcYZxl3GK+YTKYZ04sZx1QwNzHrmOeZ
		D5lvVVgqtip8FZHKCpVKlSaVGyovVKmqpqreqgtV81XLVI+pXlN9rkZVM1PjqQnUlqtVqp1Q61MbU2ep
		O6iHqmeob1Q/pH5Z/YkGWcNMw09DpFGgsV/jvMYgC2MZs3gsIWsNq4Z1gTXEJrHN2Xx2KruY/R27iz2q
		qaE5QzNKM1ezUvOUZj8H45hx+Jx0TgnnKKeX836K3hTvKeIpG6Y0TLkxZVxrqpaXllirSKtRq0frvTau
		7aedpr1Fu1n7gQ5Bx0onXCdHZ4/OBZ3nU9lT3acKpxZNPTr1ri6qa6UbobtEd79up+6Ynr5egJ5Mb6fe
		eb3n+hx9L/1U/W36p/VHDFgGswwkBtsMzhg8xTVxbzwdL8fb8VFDXcNAQ6VhlWGX4YSRudE8o9VGjUYP
		jGnGXOMk423GbcajJgYmISZLTepN7ppSTbmmKaY7TDtMx83MzaLN1pk1mz0x1zLnm+eb15vft2BaeFos
		tqi2uGVJsuRaplnutrxuhVo5WaVYVVpds0atna0l1rutu6cRp7lOk06rntZnw7Dxtsm2qbcZsOXYBtuu
		tm22fWFnYhdnt8Wuw+6TvZN9un2N/T0HDYfZDqsdWh1+c7RyFDpWOt6azpzuP33F9JbpL2dYzxDP2DPj
		thPLKcRpnVOb00dnF2e5c4PziIuJS4LLLpc+Lpsbxt3IveRKdPVxXeF60vWdm7Obwu2o26/uNu5p7ofc
		n8w0nymeWTNz0MPIQ+BR5dE/C5+VMGvfrH5PQ0+BZ7XnIy9jL5FXrdewt6V3qvdh7xc+9j5yn+M+4zw3
		3jLeWV/MN8C3yLfLT8Nvnl+F30N/I/9k/3r/0QCngCUBZwOJgUGBWwL7+Hp8Ib+OPzrbZfay2e1BjKC5
		QRVBj4KtguXBrSFoyOyQrSH355jOkc5pDoVQfujW0Adh5mGLw34MJ4WHhVeGP45wiFga0TGXNXfR3ENz
		30T6RJZE3ptnMU85ry1KNSo+qi5qPNo3ujS6P8YuZlnM1VidWElsSxw5LiquNm5svt/87fOH4p3iC+N7
		F5gvyF1weaHOwvSFpxapLhIsOpZATIhOOJTwQRAqqBaMJfITdyWOCnnCHcJnIi/RNtGI2ENcKh5O8kgq
		TXqS7JG8NXkkxTOlLOW5hCepkLxMDUzdmzqeFpp2IG0yPTq9MYOSkZBxQqohTZO2Z+pn5mZ2y6xlhbL+
		xW6Lty8elQfJa7OQrAVZLQq2QqboVFoo1yoHsmdlV2a/zYnKOZarnivN7cyzytuQN5zvn//tEsIS4ZK2
		pYZLVy0dWOa9rGo5sjxxedsK4xUFK4ZWBqw8uIq2Km3VT6vtV5eufr0mek1rgV7ByoLBtQFr6wtVCuWF
		fevc1+1dT1gvWd+1YfqGnRs+FYmKrhTbF5cVf9go3HjlG4dvyr+Z3JS0qavEuWTPZtJm6ebeLZ5bDpaq
		l+aXDm4N2dq0Dd9WtO319kXbL5fNKNu7g7ZDuaO/PLi8ZafJzs07P1SkVPRU+lQ27tLdtWHX+G7R7ht7
		vPY07NXbW7z3/T7JvttVAVVN1WbVZftJ+7P3P66Jqun4lvttXa1ObXHtxwPSA/0HIw6217nU1R3SPVRS
		j9Yr60cOxx++/p3vdy0NNg1VjZzG4iNwRHnk6fcJ3/ceDTradox7rOEH0x92HWcdL2pCmvKaRptTmvtb
		Ylu6T8w+0dbq3nr8R9sfD5w0PFl5SvNUyWna6YLTk2fyz4ydlZ19fi753GDborZ752PO32oPb++6EHTh
		0kX/i+c7vDvOXPK4dPKy2+UTV7hXmq86X23qdOo8/pPTT8e7nLuarrlca7nuer21e2b36RueN87d9L15
		8Rb/1tWeOT3dvfN6b/fF9/XfFt1+cif9zsu72Xcn7q28T7xf9EDtQdlD3YfVP1v+3Njv3H9qwHeg89Hc
		R/cGhYPP/pH1jw9DBY+Zj8uGDYbrnjg+OTniP3L96fynQ89kzyaeF/6i/suuFxYvfvjV69fO0ZjRoZfy
		l5O/bXyl/erA6xmv28bCxh6+yXgzMV70VvvtwXfcdx3vo98PT+R8IH8o/2j5sfVT0Kf7kxmTk/8EA5jz
		/GMzLdsAAAAgY0hSTQAAeiUAAICDAAD5/wAAgOkAAHUwAADqYAAAOpgAABdvkl/FRgAAAdlJREFUeNqs
		lMFrE0EUxr+dGXRN9GCSDcTWLNlDBP+DYCEHQRDEgCibQ/Um9CaeLGQ9dQv1VHoreNMeukSEloBQ6KEH
		6b8gCgm7pg1kjR7UdZWZrKcp2202COa7Dbz5zXvzvfcUJHS8vsLEyLe51zN592NZfP1CAIDmCmNmVD1W
		rjg0r1lXnj7n8XtK/HBkLy/+er//krtdFVPEdCO8cOPm4zlrbesM6LP1xA467Vb05zf+Rcq588jcebB6
		1d6wAIDITIJOuwVEyDaaoMVSKoAWS8g2mgAiBJ1268heXgQA5Xh9hQV7u9+521WzjSYut15ADAfwl0zw
		vnu6pHkd2qYDWizh2+oz/NzZBtONMHPr7iUiRr4t/yQ8PIAYDkCLJWibDti8PhEihgOEhwcAAO52VTHy
		bcK9nimDZSZJWBIiY6S41zOVTwvXhLQ47XX5N2kl01xhrHy4no8mWhyDxbNNQqQIZiRCc4XxtGzEcJBq
		QLw0woyqNw3iL5kTDTh1x6h6hJUrTrzZkhDed8H77hlYvGlZueIQmtcsphshAKi1eqo7SZhaq5/MHc1r
		liJH5MebV6+BCJnb904aM21E1Fodwbu3ABRcvP/o4Zy1tjWzoZ39GvnfxfZ3AAG9IKfF3BhfAAAAAElF
		TkSuQmCC" />';
	}

}

global $Term_Images;

$Term_Images  = new Term_Images;

function get_term_image_url( $term_id, $size='thumbnail' ){
	global $Term_Images, $post_ID;

	if(!is_int( intval( $term_id) ) ) return false;

	if( !term_exists($term_id) ) return false;

	$attachment_id = $Term_Images->get_id_by_meta($term_id);

	//Turn off global post ID so upload filter disabled.
	$id = $post_ID;
	$post_ID = 0;
	$url = wp_get_attachment_url( $attachment_id );
	$post_ID = $id;
	
	return $url;
}

endif;
