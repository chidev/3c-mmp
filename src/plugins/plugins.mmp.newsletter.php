<?php defined('TC_SYSTEM_PATH') OR die('No direct access allowed.<br/>'.__FILE__);
/**
 * MMP - NEWSLETTER
 *
 * @version			0.0.1
 * @author			Jean-Patrick Smith
 * @copyright		2013 3rd Corner Studios
 * @url					http://www.3rdcornerstudios.com
 */

	class TC_MMP_Newsletter extends TC_Plugin
	{
		public $class_name;

		private $_finished;
		private $_success;
		private $_error;
		private $_email;
		private $_return;


		/**
		 * CONSTRUCT CLASS
		 */
		public function __construct($id = null)
		{
			$this->class_name = get_class($this);
			//($id === null) and $this->_id = mt_rand();
			parent::__construct();

			// So far so good
			$this->_signed_up = NULL;

			// No errors yet
			$this->_error = FALSE;

			$this->_email = isset( $_POST['newsletter_email'] )
															? $_POST['newsletter_email']
															: 'enter email address';

			if ( isset( $_POST['mmp_newsletter'] ) && $_POST['mmp_newsletter'] == 'true' )
			{
				$this->process_signup();
			}

		} // __construct()

		public function render()
		{
			$tpl = TC_PLUGIN_TPL_PATH.'/mmp.newsletter.php';
			if ( file_exists( $tpl ) )
			{
				$finished = $this->_finished;
				$success = $this->_success;
				$email = $this->_email;
				include( $tpl );
			}
		}

		public function process_signup()
		{
			// Get config and mailchimp API wrapper
			require_once EXTERNAL_LIBPATH.'/mailchimp/MCAPI.class.php';
			require_once EXTERNAL_LIBPATH.'/mailchimp/config.inc.php'; //contains apikey

			$mailchimp_api = new MCAPI( $apikey );

			// Get email address
			$this->_email = $_POST['newsletter_email'];

			// Attempt to subscribe
			$mcapi_call = $mailchimp_api->listSubscribe( $listId, $visitor_email, array('') );

			// Check for errors
			if ( $mailchimp_api->errorCode ){

				$this->_error = $mailchimp_api->errorMessage;

				$this->return = array(
					'status'		=> 'error',
					'message'		=> 'Unable to load listSubscribe()! Code='.$mailchimp_api->errorCode.' Msg='.$mailchimp_api->errorMessage
				);
			}

			// Subscribed successfully
			else {
				$this->return = array(
					'status'		=> 'success',
					'message'		=> ''
				);
			}

			// Is ajax request, echo some json
			if ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] )
					 && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' )
			{

				echo json_encode( $this->return );

				die;

			}

			// Otherwise replace newsletter signup box with a thank you or error message
			else {

				$this->_finished = ($newsletter_error === FALSE);

			}

			/**
			 * MAILCHIMP DEBUG
			 */
			/*
			echo '<div style="padding:20px; background:#efefef;font-size:16px;color:#333;text-align:left;"><pre>';
			var_dump($return, $newsletter_signed_up, $newsletter_error, $mcapi_call, $listId, $visitor_email, $apikey);
			echo '</pre></div>';
			die;
			*/
		}

	} // TC_MMP_Newsletter

?>