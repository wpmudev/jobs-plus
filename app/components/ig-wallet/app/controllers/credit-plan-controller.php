<?php

/**
 * @author:Hoang Ngo
 */
class Credit_Plan_Controller extends IG_Request {
	public function __construct() {
		add_action( 'wp_loaded', array( &$this, 'process_save_plan' ) );
		add_action( 'wp_loaded', array( &$this, 'process_delete_plan' ) );
		add_action( 'wp_loaded', array( &$this, 'process_settings' ) );
		add_action( 'mp_order_paid', array( &$this, 'process_credit_purchased' ) );
		add_action( 'mp_order_order_paid', array( &$this, 'mp3_process_credit_purchased' ) );
		add_filter( 'je_buttons_on_single_page', array( &$this, 'append_nav_button' ) );
		add_filter( 'the_content', array( &$this, 'append_nav_button' ) );
		add_shortcode( 'jbp-my-wallet-btn', array( &$this, 'btn_shortcode' ) );
		add_action( 'wp_ajax_jbp_create_credits_page', array( &$this, 'create_pages' ) );
		add_shortcode( 'jbp-my-wallet', array( &$this, 'my_wallet' ) );
		add_action( 'je_credit_settings_content_general', array( &$this, 'general' ) );
		add_action( 'je_credit_settings_content_give_credit', array( &$this, 'sending_credit' ) );

		add_filter( 'mp_product_price_html', array( &$this, 'addition_price_info' ), 10, 4 );
		add_filter( 'mp_product_name_display_in_cart', array( &$this, 'addition_cart_name_info' ), 10, 2 );

		//add_filter('mp_order_status', array(&$this, 'alter_order_table'), 10, 2);
		add_filter( 'get_post_metadata', array( &$this, 'remove_download_link' ), 10, 4 );
	}

	function alter_order_table( $content, $order ) {
		return $content;
	}

	function remove_download_link( $value, $object_id, $meta_key, $single ) {
		global $wp_query;
		if ( $meta_key == 'mp_file' && isset( $wp_query->query['pagename'] ) && $wp_query->query['pagename'] == 'orderstatus' ) {
			//first we need to check does the product is wallet stuffs
			$check = get_post_meta( $object_id, 'je_wallet_append_info', true );
			if ( $check !== false ) {
				return '';
			}
		}

		return $value;
	}

	function addition_cart_name_info( $name, $product_id ) {
		$plan = Credit_Plan_Model::find( $product_id );
		if ( is_object( $plan ) && $plan->append_credits_info == 1 ) {
			$name .= ' (' . $plan->credits . ' ' . __( "credits", je()->domain ) . ')';
		}

		return $name;
	}

	function addition_price_info( $price_html, $post_id, $label, $price ) {
		$plan = Credit_Plan_Model::find( $post_id );
		if ( is_object( $plan ) && $plan->append_credits_info == 1 ) {
			//we will modify the html, to append credits info
			$dom = new SmartDOMDocument();
			$dom->loadHTML( $price_html );
			$xpath     = new DOMXPath( $dom );
			$classname = 'mp_current_price';
			$element   = $xpath->query( "//*[@class='" . $classname . "']" );
			if ( $element->length > 0 ) {
				$element->item( 0 )->nodeValue = $element->item( 0 )->nodeValue . ' ' . sprintf( __( "for %s credit(s)", je()->domain ), $plan->credits );
			}
			$price_html = $dom->saveHTMLExact();
		}

		return $price_html;
	}

	function process_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}

		if ( je()->post( 'je_credit_setting_save', 0 ) == 1 ) {
			$model = new Credit_Plan_Settings_Model();
			$model->import( je()->post( 'Credit_Plan_Settings_Model' ) );
			$model->save();
			$this->set_flash( 'wallet_settings_saved', __( "Your settings have been saved successfully.", je()->domain ) );
			$this->refresh();
		}

		if ( je()->post( 'je-credit-send', 0 ) == 1 ) {
			$model = new Sending_Credit_Model();
			$model->import( je()->post( 'Sending_Credit_Model' ) );
			if ( $model->validate() ) {
				//update users credit
				$log = sprintf( __( "You received %s credits from admin for reason: \"%s\"", je()->domain ), $model->amount, $model->reason );
				User_Credit_Model::update_balance( $model->amount, $model->user_id, '', $log, __( 'Purchased Credits', je()->domain ) );
				$user = get_userdata( $model->user_id );
				$this->set_flash( 'wallet_settings_saved', sprintf( __( "You have been sent <strong>%s credits</strong> to the user <strong>%s</strong> successfully.", je()->domain ), $model->amount, $user->user_login ) );
				$this->refresh();
			} else {
				je()->global['je_credit_send_model'] = $model;
			}
		}
	}

	function my_wallet() {
		wp_enqueue_script( 'jquery-ui-tabs' );
		if ( ! is_user_logged_in() ) {
			return $this->render( je()->plugin_path . 'app/views/login.php', array(), false );
		} else {
			return $this->render( 'credit/my_wallets', array(), false );
		}
	}

	function create_pages() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}

		$shortcodes = '<p style="text-align: center">[jbp-job-browse-btn][jbp-expert-browse-btn][jbp-job-post-btn][jbp-expert-post-btn][jbp-my-job-btn][jbp-expert-profile-btn]</p>';
		if ( isset( $_POST['type'] ) ) {
			$model = new Credit_Plan_Settings_Model();
			$model->load();
			switch ( $_POST['type'] ) {
				case 'wallet_page':
					$new_id = wp_insert_post( apply_filters( 'je_create_my_wallet_page', array(
						'post_title'     => __( "My Wallets", je()->domain ),
						'post_content'   => "$shortcodes [jbp-my-wallet]",
						'post_status'    => 'publish',
						'post_type'      => 'page',
						'ping_status'    => 'closed',
						'comment_status' => 'closed'
					) ) );

					$model->my_wallet_page = $new_id;
					$model->save();
					//update
					echo $new_id;
					break;
				case 'plans_page':
					$new_id = wp_insert_post( apply_filters( 'je_create_credit_plan_page', array(
						'post_title'     => __( "Credit Plans", je()->domain ),
						'post_content'   => "[mp_list_products category=\"je-credits\"]",
						'post_status'    => 'publish',
						'post_type'      => 'page',
						'ping_status'    => 'closed',
						'comment_status' => 'closed'
					) ) );

					$model->plans_page = $new_id;
					$model->save();
					//update
					echo $new_id;
					break;
			}
		}
		exit;
	}

	function settings() {
		$model = new Credit_Plan_Settings_Model();
		$this->render( 'credit/settings', array(
			'model' => $model
		) );
	}

	function btn_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'text'     => __( 'My wallet', je()->domain ),
			'view'     => 'both', //loggedin, loggedout, both
			'class'    => je()->settings()->theme,
			'template' => '',
			'url'      => get_permalink( ig_wallet()->settings()->my_wallet_page )
		), $atts ) );

		if ( ! $this->can_view( $view ) ) {
			return '';
		}

		$ob = sprintf( '<a class="ig-container jbp-shortcode-button jbp-my-wallet %s" href="%s">
			<i style="display: block" class="glyphicon glyphicon-piggy-bank fa-2x"></i>%s
		</a>', esc_attr( $class ), $url, esc_html( $text ) );

		return $ob;
	}

	function append_nav_button( $content ) {
		$pattern = get_shortcode_regex();
		if ( preg_match_all( '/' . $pattern . '/s', $content, $matches )
		     && array_key_exists( 2, $matches )
		     && in_array( 'jbp-expert-profile-btn', $matches[2] )
		) {
			//getting the raw shortcode
			$key         = array_search( 'jbp-expert-profile-btn', $matches[2] );
			$sc          = $matches[0][ $key ];
			$new_content = str_replace( $sc, $sc . '[jbp-my-wallet-btn]', $content );

			return $new_content;
		}

		return $content;
	}

	function process_credit_purchased( $order ) {
		$cart = $order->mp_cart_info;
		//je()->get_logger()->log(var_export($order, true));
		foreach ( $cart as $id => $item ) {
			$model = Credit_Plan_Model::find( $id );
			if ( is_object( $model ) ) {
				$log = sprintf( __( "You have purchased %s credits for %s through %s", je()->domain ),
					$model->credits, JobsExperts_Helper::format_currency( '', $item[0]['price'] ), $order->mp_payment_info['gateway_public_name'] );

				User_Credit_Model::update_balance( $model->credits, $order->post_author, $item[0]['price'], $log, __( 'Purchased Credits', je()->domain ) );
			}
		}
	}

	public function mp3_process_credit_purchased( MP_Order $order ) {
		$cart = $order->get_cart();
		foreach ( $cart->get_items() as $id => $qty ) {
			$model = Credit_Plan_Model::find( $id );
			if ( is_object( $model ) ) {
				$product = new MP_Product( $id );
				$log     = sprintf( __( "You have purchased %s credits for %s through %s", je()->domain ),
					$model->credits, JobsExperts_Helper::format_currency( '', $product->get_price( 'lowest' ) ), $order->get_meta( 'mp_payment_info->gateway_public_name' ) );

				User_Credit_Model::update_balance( $model->credits, $order->post_author, $product->get_price( 'lowest' ), $log, __( 'Purchased Credits', je()->domain ) );
			}
		}
	}

	function process_delete_plan() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! wp_verify_nonce( je()->post( 'je_delete_plan_nonce' ), 'je_delete_plan' ) ) {
			return;
		}

		$model = Credit_Plan_Model::find( je()->post( 'id' ) );
		if ( is_object( $model ) ) {
			Credit_Plan_Model::delete_plan( je()->post( 'id' ) );
			$this->set_flash( 'plan_save', sprintf( __( "Plan <strong>%s</strong> has been deleted!", je()->domain ), $model->title ) );
			$this->redirect( admin_url( 'admin.php?page=ig-credit-plans' ) );
		}
	}

	function process_save_plan() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( je()->post( 'je_credit_submit', null ) == null ) {
			return;
		}
		//check nonce
		if ( ! wp_verify_nonce( je()->post( 'je_credit_submit' ), 'ig_wallet_save_plan' ) ) {
			return;
		}

		$model = new Credit_Plan_Model();
		$model->import( je()->post( 'Credit_Plan_Model' ) );
		if ( $model->validate() ) {
			$model->add_plan( $model->title, $model->description, $model->cost, $model->credits, $model->sale_price, $model->product_id, $model->append_credits_info );

			$this->set_flash( 'plan_save', sprintf( __( "Plan <strong>%s</strong> has been saved!", je()->domain ), $model->title ) );
			$this->redirect( admin_url( 'admin.php?page=ig-credit-plans' ) );
		}
		ig_wallet()->global['model'] = $model;
	}

	function rules() {
		wp_enqueue_script( 'jquery-ui-accordion' );
		$this->render( 'credit/hook' );
	}

	function main() {
		$models = Credit_Plan_Model::find_all();
		if ( isset( ig_wallet()->global['model'] ) ) {
			$model = ig_wallet()->global['model'];
		} else {
			$model = '';
			if ( je()->get( 'id', null ) != null ) {
				$model = Credit_Plan_Model::find( je()->get( 'id' ) );
			}
			if ( ! is_object( $model ) ) {
				$model = new Credit_Plan_Model();
			}
		}
		$this->render( 'credit/main', array(
			'models' => $models,
			'model'  => $model
		) );
	}

	public function can_view( $view = 'both' ) {
		$view = strtolower( $view );
		if ( is_user_logged_in() ) {
			if ( $view == 'loggedout' ) {
				return false;
			}
		} else {
			if ( $view == 'loggedin' ) {
				return false;
			}
		}

		return true;
	}

	function general() {
		$model = new Credit_Plan_Settings_Model();
		$this->render( 'settings/general', array(
			'model' => $model
		) );
	}

	function sending_credit() {
		if ( isset( je()->global['je_credit_send_model'] ) ) {
			$model = je()->global['je_credit_send_model'];
		} else {
			$model = new Sending_Credit_Model();
		}
		$this->render( 'settings/sending_credit', array(
			'model' => $model
		) );
	}

	function getting_start() {
		$this->render( 'credit/getting_start' );
	}
}