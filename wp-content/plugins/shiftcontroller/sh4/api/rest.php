<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class SH4_Api_Rest
{
	public $self, $api;

	public function __construct(
		HC3_Hooks $hooks,
		SH4_Api_Api $api
		)
	{
		$this->api = $hooks->wrap( $api );
		$this->self = $hooks->wrap( $this );

		add_action( 'rest_api_init', array($this, 'routes') );
	}

	public function checkAdmin( $request )
	{
		$myAuthCode = get_option( 'sh4-rest_auth_code', '' );
		$authCode = $request->get_header( 'X-WP-ShiftController-AuthCode' );

		if( $authCode && ($authCode == $myAuthCode) ){
			$ret = true;
			return $ret;
		}

	// try body params
		$isJson = wp_is_json_request();
		$values = $isJson ? $request->get_json_params() : $request->get_body_params();

		if( ! $values ){
			$body = $request->get_body();
			parse_str( $body, $values );
		}

		if( isset($values['X-WP-ShiftController-AuthCode']) ){
			$authCode = $values['X-WP-ShiftController-AuthCode'];
			if( $authCode && ($authCode == $myAuthCode) ){
				$ret = true;
				return $ret;
			}
		}

	// try get params
		$values = $request->get_query_params();
		if( isset($values['X-WP-ShiftController-AuthCode']) ){
			$authCode = $values['X-WP-ShiftController-AuthCode'];
			if( $authCode && ($authCode == $myAuthCode) ){
				$ret = true;
				return $ret;
			}
		}

		$errors = 'not allowed: ' . join( array_keys($values) );
		$ret = new WP_Error( 'error', $errors, array('status' => 500) );
		sleep( 1 );

		return $ret;
	}

	public function routes()
	{
		$enabledOptionName = 'sh4-rest_enabled';
		$v = get_option( $enabledOptionName, 1 );
		if( ! $v ){
			return;
		}

		$startUrl = 'shiftcontroller/v4';

		register_rest_route( $startUrl, '/available-employees',
			array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => array($this->self, 'availableEmployeesGet'),
					'permission_callback' => array( $this->self, 'checkAdmin' ),
					),
				)
		);

		register_rest_route( $startUrl, '/shifts',
			array(
				array(
					'methods'	=> WP_REST_Server::READABLE,
					'callback'	=> array($this->self, 'shiftsGet'),
					// 'permission_callback'	=> '__return_true',
					'permission_callback'	=> array( $this->self, 'checkAdmin' ),
					),
				array(
					'methods'	=> WP_REST_Server::CREATABLE,
					'callback'	=> array($this->self, 'shiftsCreate'),
					'permission_callback'	=> array( $this->self, 'checkAdmin' ),
					),
				)
		);

		register_rest_route( $startUrl, '/shifts/(?P<id>\d+)',
			array(
				array(
					'methods'	=> WP_REST_Server::READABLE,
					'callback'	=> array( $this->self, 'shiftsGetById' ),
					// 'permission_callback'	=> '__return_true',
					'permission_callback'	=> array( $this->self, 'checkAdmin' ),
				),
				array(
					'methods'	=> WP_REST_Server::DELETABLE,
					'callback'	=> array( $this->self, 'shiftsDeleteById' ),
					'permission_callback'	=> array( $this->self, 'checkAdmin' ),
				),
				array(
					'methods'	=> WP_REST_Server::EDITABLE,
					'callback'	=> array( $this->self, 'shiftsUpdateById' ),
					'permission_callback'	=> array( $this->self, 'checkAdmin' ),
				),
			)
		);
	}

	public function availableEmployeesGet( $request )
	{
		$queryParams = $request->get_query_params();
		$ret = $this->api->availableEmployeesGet( $queryParams );
		$ret = new WP_REST_Response( $ret );
		return $ret;
	}

	public function shiftsGet( $request )
	{
		$queryParams = $request->get_query_params();
		$ret = $this->api->shiftsGet( $queryParams );
		$ret = new WP_REST_Response( $ret );
		return $ret;
	}

	public function shiftsGetById( $request )
	{
		$id = $request['id'];
		return $this->api->shiftsGetById( $id );
	}

	public function shiftsCreate( $request )
	{
		$isJson = wp_is_json_request();
		$values = $isJson ? $request->get_json_params() : $request->get_body_params();

		if( ! $values ){
			$body = $request->get_body();
			parse_str( $body, $values );
		}

		return $this->api->shiftsCreate( $request );
	}

	public function shiftsDeleteById( $request )
	{
		$id = $request['id'];
		return $this->api->shiftsDeleteById( $id );
	}

	public function shiftsUpdateById( $request )
	{
		$id = $request['id'];

		$isJson = wp_is_json_request();
		$values = $isJson ? $request->get_json_params() : $request->get_body_params();

		if( ! $values ){
			$body = $request->get_body();
			parse_str( $body, $values );
		}

		return $this->api->shiftsUpdateById( $id, $values );
	}
}