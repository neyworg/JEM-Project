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
 * View class for the JEM eventelement screen
 *
 * @package JEM
 *
 */
class JEMViewEventelement extends JViewLegacy {

	public function display($tpl = null)
	{
		$app =  JFactory::getApplication();

		//initialise variables
		$user 		=  JFactory::getUser();
		$db			=  JFactory::getDBO();
		$jemsettings =  JEMAdmin::config();
		$document	=  JFactory::getDocument();

		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal');

		//get var
		$filter_order		= $app->getUserStateFromRequest( 'com_jem.eventelement.filter_order', 'filter_order', 'a.dates', 'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( 'com_jem.eventelement.filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$filter 			= $app->getUserStateFromRequest( 'com_jem.eventelement.filter', 'filter', '', 'int' );
		$filter_state 		= $app->getUserStateFromRequest( 'com_jem.eventelement.filter_state', 'filter_state', '*', 'word' );
		$search 			= $app->getUserStateFromRequest( 'com_jem.eventelement.filter_search', 'filter_search', '', 'string' );
		$search 			= $db->escape( trim(JString::strtolower( $search ) ) );
		$template 			= $app->getTemplate();

		//prepare the document
		$document->setTitle(JText::_('COM_JEM_SELECTEVENT'));
		$document->addStyleSheet(JURI::root().'media/com_jem/css/backend.css');

		//Get data from the model
		$rows      	=  $this->get( 'Data');
		$pagination 	=  $this->get( 'Pagination' );

		//publish unpublished filter
		$lists['state']	= JHTML::_('grid.state', $filter_state );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		//Create the filter selectlist
		$filters = array();
		$filters[] = JHTML::_('select.option', '1', JText::_('COM_JEM_EVENT_TITLE'));
		$filters[] = JHTML::_('select.option', '2', JText::_('COM_JEM_VENUE'));
		$filters[] = JHTML::_('select.option', '3', JText::_('COM_JEM_CITY'));
	//	$filters[] = JHTML::_('select.option', '4', JText::_('COM_JEM_CATEGORY'));
		$lists['filter'] = JHTML::_('select.genericlist', $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter );

		// search filter
		$lists['search']= $search;

		//assign data to template
		$this->lists 		= $lists;
		$this->rows 		= $rows;
		$this->pagination 	= $pagination;
		$this->jemsettings 	= $jemsettings;
		$this->user 		= $user;

		parent::display($tpl);
	}

}//end class
?>