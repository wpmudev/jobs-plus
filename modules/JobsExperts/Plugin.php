<?php

// +----------------------------------------------------------------------+
// | Copyright Incsub (http://incsub.com/)                                |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License, version 2, as  |
// | published by the Free Software Foundation.                           |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,               |
// | MA 02110-1301 USA                                                    |
// +----------------------------------------------------------------------+

/**
 * The core plugin class.
 *
 * @category JobsExperts
 *
 * @since    1.0.0
 */
class JobsExperts_Plugin {

	const NAME    = 'jobs_and_experts';
	const VERSION = '1.0.0';

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @access private
	 * @var JobsExperts_Plugin
	 */
	private static $_instance = null;

	/**
	 * The array of registered modules.
	 *
	 * @since  1.0.0
	 *
	 * @access private
	 * @var array
	 */
	private $_modules = array();

	/**
	 * Module physic path
	 *
	 * @var string
	 */
	public $_module_path;

	/**
	 * Module Url
	 * @var string
	 */
	public $_module_url;

	/**
	 * Private constructor.
	 *
	 * @since  1.0.0
	 *
	 * @access private
	 */
	private function __construct() {
		$this->_module_path = plugin_dir_path( __FILE__ );
		$this->_module_url  = plugin_dir_url( __FILE__ );
	}

	/**
	 * GLobal vars sharing
	 * @var array
	 */
	public $global = array();

	/**
	 * Private clone method.
	 *
	 * @since  1.0.0
	 *
	 * @access private
	 */
	private function __clone() {
	}

	/**
	 * Returns singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 * @return JobsExperts_Plugin
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new JobsExperts_Plugin();
		}

		return self::$_instance;
	}

	/**
	 * Returns a module if it was registered before. Otherwise NULL.
	 *
	 * @since  1.0.0
	 *
	 * @access public
	 *
	 * @param string $name The name of the module to return.
	 *
	 * @return JobsExperts_Module|null Returns a module if it was registered or NULL.
	 */
	public function get_module( $name ) {
		return isset( $this->_modules[$name] ) ? $this->_modules[$name] : null;
	}

	/**
	 * Determines whether the module has been registered or not.
	 *
	 * @since  1.0.0
	 *
	 * @access public
	 *
	 * @param string $name The name of a module to check.
	 *
	 * @return boolean TRUE if the module has been registered. Otherwise FALSE.
	 */
	public function has_module( $name ) {
		return isset( $this->_modules[$name] );
	}

	/**
	 * Register new module in the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @access public
	 *
	 * @param string $module The name of the module to use in the plugin.
	 */
	public function set_module( $class ) {
		$this->_modules[$class] = new $class( $this );
	}

	/**
	 * Shorthand to get jobs post type
	 * @return mixed
	 */
	public function get_job_type() {
		return get_post_type_object( 'jbp_job' );
	}

	/**
	 * Shorthand to get experts post type
	 * @return mixed
	 */
	public function get_expert_type() {
		return get_post_type_object( 'jbp_pro' );
	}

	function settings() {
		return new JobsExperts_Core_Models_Settings();
	}

	function front_module() {
		return $this->get_module( JobsExperts_Core_Frontend::NAME );
	}

	function shortcode_module() {
		return $this->get_module( JobsExperts_Shortcode::NAME );
	}

	function page_module() {
		$class = JobsExperts_Core_PageFactory::instance();

		return $class;
	}
}