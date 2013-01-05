<?php defined('TC_SYSTEM_PATH') OR die('No direct access allowed.<br/>'.__FILE__);
/**
 * MMP - Lyrics
 *
 * @version			0.0.1
 * @author			Jean-Patrick Smith
 * @copyright		2013 3rd Corner Studios
 * @url					http://www.3rdcornerstudios.com
 */

	class TC_MMP_Lyrics extends TC_Plugin
	{
		public $class_name;

		private $_errors = array();

		public function __construct($id = null)
		{
			$this->class_name = get_class($this);
			//($id === null) and $this->_id = mt_rand();
			parent::__construct();

			// echo 'lyrics:<br/>';
			// var_dump( $this );

		}
		public function render()
		{

		}
	}
?>