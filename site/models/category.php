<?php
/**
 * @version 1.9.1
 * @package JEM
 * @copyright (C) 2013-2013 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * JEM Component Category Model
 *
 * @package JEM
 *
 */
class JEMModelCategory extends JModelLegacy
{
	/**
	 * Category id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Categories items Data
	 *
	 * @var mixed
	 */
	var $_data = null;

	/**
	 * Childs
	 *
	 * @var mixed
	 */
	var $_childs = null;

	/**
	 * category data array
	 *
	 * @var array
	 */
	var $_category = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();

		$app =  JFactory::getApplication();
		$jemsettings =  JEMHelper::config();

		$this->setdate(time());

		// Get the paramaters of the active menu item
		$params 	=  $app->getParams();

		if (JRequest::getVar('id')) {
			$id = JRequest::getVar('id');
		} else {
			$id = $params->get('id');
		}

		$this->setId((int)$id);

		//get the number of events from database
		$limit			= $app->getUserStateFromRequest('com_jem.category.limit', 'limit', $jemsettings->display_num, 'int');
		$limitstart 	= $app->getUserStateFromRequest('com_jem.category.limitstart', 'limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

	}

	function setdate($date)
	{
		$this->_date = $date;
	}

	/**
	 * Method to set the category id
	 *
	 * @access	public
	 * @param	int	category ID number
	 */
	function setId($id)
	{
		// Set new category ID and wipe data
		$this->_id			= $id;
		$this->_data		= null;
	}

	/**
	 * set limit
	 * @param int value
	 */
	function setLimit($value)
	{
		$this->setState('limit', (int) $value);
	}

	/**
	 * set limitstart
	 * @param int value
	 */
	function setLimitStart($value)
	{
		$this->setState('limitstart', (int) $value);
	}

	/**
	 * Method to get the events
	 *
	 * @access public
	 * @return array
	 */
	function &getData()
	{
		$user = JFactory::getUser();

		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
				$pagination = $this->getPagination();
				$query = $this->_buildQuery();
				$this->_data = $this->_getList($query, $pagination->limitstart, $pagination->limit);
		}

		if ($this->_data)
		{
			$this->_data = JEMHelper::getAttendeesNumbers($this->_data);

			$count = count($this->_data);
			for($i = 0; $i < $count; $i++)
			{
				$item = $this->_data[$i];
				$item->categories = $this->getCategories($item->id);

				//child categories
			//	$query	= $this->_buildChildsQuery($item->id);
			//	$this->_db->setQuery($query);
			//	$item->categories = $this->_db->loadObjectList();

				//remove events without categories (users have no access to them)
				if (empty($item->categories)) {
					unset($this->_data[$i]);
				}
			}
	}
		return $this->_data;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Total nr of Categories
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the total nr if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Build the query
	 *
	 * @access private
	 * @return string
	 */
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildCategoryWhere();
		$orderby	= $this->_buildCategoryOrderBy();

		//Get Events from Database
		$query = 'SELECT DISTINCT a.id, a.datimage, a.dates, a.enddates, a.times, a.endtimes, a.title, a.locid, a.datdescription, a.created, '
			. ' a.maxplaces, a.waitinglist, '
			. ' l.venue, l.city, l.state, l.url, c.color, c.catname, l.street, ct.name AS countryname, '
				. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug,'
				. ' CASE WHEN CHAR_LENGTH(l.alias) THEN CONCAT_WS(\':\', a.locid, l.alias) ELSE a.locid END as venueslug'
				. ' FROM #__jem_events AS a'
				. ' INNER JOIN #__jem_cats_event_relations AS rel ON rel.itemid = a.id'
				. ' INNER JOIN #__jem_categories AS c ON c.id = rel.catid'
				. ' LEFT JOIN #__jem_venues AS l ON l.id = a.locid'
				. ' LEFT JOIN #__jem_countries AS ct ON ct.iso2 = l.country '
				. $where
				. ' GROUP BY a.id'
				. $orderby
				;

		return $query;
	}

	/**
	 * Build the order clause
	 *
	 * @access private
	 * @return string
	 */
	function _buildCategoryOrderBy()
	{
		$app =  JFactory::getApplication();

		$filter_order		= $app->getUserStateFromRequest('com_jem.category.filter_order', 'filter_order', 'a.dates', 'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.category.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$filter_order		= JFilterInput::getInstance()->clean($filter_order, 'cmd');
		$filter_order_Dir	= JFilterInput::getInstance()->clean($filter_order_Dir, 'word');

		if ($filter_order != '') {
			$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;
		} else {
			$orderby = ' ORDER BY a.dates, a.times ';
		}

		return $orderby;


	}

	/**
	 * Method to build the WHERE clause
	 *
	 * @access private
	 * @return array
	 */
	function _buildCategoryWhere()
	{
		$app =  JFactory::getApplication();
		$task 		= JRequest::getWord('task');
		$params 	=  $app->getParams();
		$jemsettings =  JEMHelper::config();

		$user = JFactory::getUser();
		$gid = JEMHelper::getGID($user);


		$filter_state 	= $app->getUserStateFromRequest('com_jem.category.filter_state', 'filter_state', '', 'word');
		$filter 		= $app->getUserStateFromRequest('com_jem.category.filter', 'filter', '', 'int');
		$search 		= $app->getUserStateFromRequest('com_jem.category.filter_search', 'filter_search', '', 'string');
		$search 		= $this->_db->escape(trim(JString::strtolower($search)));


		$where = array();

		// First thing we need to do is to select only needed events
		if ($task == 'archive') {
			$where[] = ' a.published = 2';
		} else {
			$where[] = ' a.published = 1';
		}

		// display event from direct childs ?
		if (!$params->get('displayChilds', 0)) {
			$where[] = ' rel.catid = '.$this->_id;
		} else {
			$where[] = ' (rel.catid = '.$this->_id . ' OR c.parent_id = '.$this->_id . ')';
		}

		// display all event of recurring serie ?
		if ($params->get('only_first',0)) {
			$where[] = ' a.recurrence_first_id = 0 ';
		}

		$where[] = ' c.published = 1';
		$where[] = ' c.access  <= '.$gid;


		/*
		// get excluded categories
		$excluded_cats = trim($params->get('excluded_cats', ''));

		if ($excluded_cats != '') {
			$cats_excluded = explode(',', $excluded_cats);
			$where [] = '  (c.id!=' . implode(' AND c.id!=', $cats_excluded) . ')';
		}
		// === END Excluded categories add === //
		 * */


		if ($jemsettings->filter)
		{

			if ($search && $filter == 1) {
				$where[] = ' LOWER(a.title) LIKE \'%'.$search.'%\' ';
			}

			if ($search && $filter == 2) {
				$where[] = ' LOWER(l.venue) LIKE \'%'.$search.'%\' ';
			}

			if ($search && $filter == 3) {
				$where[] = ' LOWER(l.city) LIKE \'%'.$search.'%\' ';
			}

			if ($search && $filter == 4) {
				$where[] = ' LOWER(c.catname) LIKE \'%'.$search.'%\' ';
			}

			if ($search && $filter == 5) {
				$where[] = ' LOWER(l.state) LIKE \'%'.$search.'%\' ';
			}

		} // end tag of jemsettings->filter decleration

		$where 		= (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;

	}

	/**
	 * Method get the count of direct sub categories events
	 *
	 * @access private
	 * @return array
	 */
	function getChilds()
	{
		$query = $this->_buildChildsquery();
		$this->_childs = $this->_getList($query);
		return $this->_childs;
	}

	/**
	 * build query for direct child categories event count
	 *
	 * @access private
	 * @return array
	 */
	function _buildChildsQuery()
	{
		$user = JFactory::getUser();
		$gid = JEMHelper::getGID($user);

		$ordering = 'c.ordering ASC';

		//build where clause
		$where = ' WHERE cc.published = 1';
		$where .= ' AND cc.parent_id = '.(int)$this->_id;
		$where .= ' AND cc.access <= '.$gid;
		//$where .= ' AND cc.access IN ('.$gid.')';

		//TODO: Make option for categories without events to be invisible in list
		//check archive task and ensure that only categories get selected if they contain a published/archived event
		$task 	= JRequest::getWord('task');
		if($task == 'archive') {
			$where .= ' AND i.published = 2';
		} else {
			$where .= ' AND i.published = 1';
		}

		$query = 'SELECT c.*,'
				. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END AS slug,'
				. ' ec.assignedevents'
				. ' FROM #__jem_categories AS c'
				. ' INNER JOIN ('
						. ' SELECT COUNT(DISTINCT i.id) AS assignedevents, cc.id'
						. ' FROM #__jem_events AS i'
						. ' INNER JOIN #__jem_cats_event_relations AS rel ON rel.itemid = i.id'
						. ' INNER JOIN #__jem_categories AS cc ON cc.id = rel.catid'
						. $where
						. ' GROUP BY cc.id'
				. ')'
				. ' AS ec ON ec.id = c.id'
				. ' ORDER BY '.$ordering
				;

		return $query;
	}

	/**
	 * Method to get the Category
	 *
	 * @access public
	 * @return integer
	 */
	function getCategory()
	{
		//initialize some vars

		$user = JFactory::getUser();
		$gid = JEMHelper::getGID($user);

		$query = 'SELECT *,'
				.' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\', id, alias) ELSE id END as slug'
				.' FROM #__jem_categories'
				.' WHERE id = '.$this->_id;

		$this->_db->setQuery($query);

		$this->_category = $this->_db->loadObject();

		$groups = $user->getAuthorisedViewLevels();
			$allowed = in_array($this->_category->access, $groups);

		//Make sure the category is published
		if (!$this->_category->published)
		{
			// TODO Translation
			JError::raiseError(404, JText::sprintf('CATEGORY #%d NOT FOUND', $this->_id));
			return false;
		}

		//check whether category access level allows access
		//additional check
		if ($this->_category->access > $gid)
		{
			return JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		$this->_category->attachments = JEMAttachment::getAttachments('category'.$this->_category->id, $gid);

		return $this->_category;
	}

	/**
	 * get event categories
	 *
	 * @param int event id
	 * @return array
	 */
	function getCategories($id)
	{
		$user = JFactory::getUser();
		$gid = JEMHelper::getGID($user);

		$query = 'SELECT DISTINCT c.id, c.catname, c.color, c.access, c.checked_out AS cchecked_out,'
				. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug'
				. ' FROM #__jem_categories AS c'
				. ' LEFT JOIN #__jem_cats_event_relations AS rel ON rel.catid = c.id'
				. ' WHERE rel.itemid = '.(int)$id
				. ' AND c.published = 1'
				. ' AND c.access  <= '.$gid;
				;

		$this->_db->setQuery($query);

		$this->_cats = $this->_db->loadObjectList();

		return $this->_cats;
	}

}
?>