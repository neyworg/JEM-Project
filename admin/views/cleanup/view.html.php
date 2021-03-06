<?php
/**
 * @version 1.9.1
 * @package JEM
 * @copyright (C) 2013-2013 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;


/**
 * View class for the JEM Cleanup screen
 *
 * @package JEM
 * 
 */
class JEMViewCleanup extends JViewLegacy {

	public function display($tpl = null) {

		$app =  JFactory::getApplication();

		//initialise variables
		$document		=  JFactory::getDocument();
		$user			=  JFactory::getUser();
		
		$this->totalcats      	=  $this->get( 'Countcats');
		

		//only admins have access to this view
		if (!JFactory::getUser()->authorise('core.manage')) {
			JError::raiseWarning( 'SOME_ERROR_CODE', JText::_( 'COM_JEM_ALERTNOTAUTH'));
			$app->redirect( 'index.php?option=com_jem&view=jem' );
		}

		//add css and submenu to document
		$document->addStyleSheet(JURI::root().'media/com_jem/css/backend.css');


		// add toolbar
		$this->addToolbar();
		
		parent::display($tpl);
	}
	
	
	/*
	 * Add Toolbar
	*/
	
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/helper.php';
		
		//create the toolbar
		JToolBarHelper::back();
		JToolBarHelper::title( JText::_( 'COM_JEM_CLEANUP' ), 'housekeeping' );
		JToolBarHelper::help( 'cleanup', true );	
	}
	
	
} // end of class
?>