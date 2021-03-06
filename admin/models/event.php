<?php
/**
 * @version 1.9.1
 * @package JEM
 * @copyright (C) 2013-2013 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * JEM Component Event model.
 *
 */
class JEMModelEvent extends JModelAdmin
{
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->published != -2)
			{
				return ;
			}

			$user = JFactory::getUser();

			if (!empty($record->catid)) {
				$db = JFactory::getDbo();

				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__jem_cats_event_relations'));
				$query->where('itemid = '.$record->id);

				$db->setQuery($query);
				$db->query();

				return $user->authorise('core.delete', 'com_jem.category.'.(int) $record->catid);
			} else {
				$db = JFactory::getDbo();

				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__jem_cats_event_relations'));
				$query->where('itemid = '.$record->id);

				$db->setQuery($query);
				$db->query();

				return $user->authorise('core.delete', 'com_jem');
			}
		}
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		if (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_jem.category.'.(int) $record->catid);
		} else {
			return $user->authorise('core.edit.state', 'com_jem');
		}
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 *
	 */
	public function getTable($type = 'Event', $prefix = 'JEMTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 *
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_jem.event', 'event', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$jemsettings = JEMAdmin::config();

		if ($item = parent::getItem($pk)) {

			$db = JFactory::getDbo();

			$query = $db->getQuery(true);
			$query->select(array('count(id)'));
			$query->from('#__jem_register');
			$query->where(array('event= '.$db->quote($item->id), 'waiting= 0'));

			$db->setQuery($query);
			$res = $db->loadResult();
			$item->booked = $res;

			$files = JEMAttachment::getAttachments('event'.$item->id);
			$item->attachments = $files;
		}


		if ($item->id)
		{
				$item->recurrence_type == '0';
				$item->recurrence_number = '';
				$item->recurrence_byday = '';
				$item->recurrence_counter = '';
				$item->recurrence_first_id = '';
				$item->recurrence_type = '';
				$item->recurrence_limit = '';
				$item->recurrence_limit_date = '';
		}




		$item->author_ip = $jemsettings->storeip ? getenv('REMOTE_ADDR') : 'DISABLED';

		if (empty($item->id)) {
			$item->country = $jemsettings->defaultCountry;
		}

		return $item;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 */
	protected function loadFormData()
	{

		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jem.edit.event.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * With $table you can call a table name
	 *
	 */
	protected function prepareTable(&$table)
	{
		$app = JFactory::getApplication();
		$date = JFactory::getDate();
		$jemsettings = JEMAdmin::config();

		// Debug
		/* var_dump($_POST);exit; */
		/* var_dump($_FILES);exit; */
		/* var_dump($table);exit; */

		/* JInput
		 *
		 * Example: $foo = $jinput->get('varname', 'default_value', 'filter');
		 * Possible filters: http://docs.joomla.org/Retrieving_request_data_using_JInput
		 *
		 */

		$jinput = JFactory::getApplication()->input;
		$user	= JFactory::getUser();

		if ($table->id) {
			// Existing item
			$table->modified	= $date->toSql();
			$table->modified_by	= $user->get('id');

			$table->recurrence_first_id = '';

		} else {
			// New Event. An event created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.

			if (!intval($table->created)) {
				$table->created = $date->toSql();
			}

			if (empty($this->created_by)) {
				$table->created_by = $user->get('id');
			}
		}


		// Bind the form fields to the table
		//if (!$table->bind( JRequest::get( 'post' ) )) {
		//return JError::raiseWarning( 500, $table->getError() );
		//}



		$cats = $jinput->get( 'cid', array(), 'post', 'array');

		$recurrencenumber = $jinput->get('recurrence_number','','int');
		$recurrencebyday = $jinput->get('recurrence_byday','','string');

		$metakeywords = $jinput->get( 'meta_keywords','','');
		$metadescription = $jinput->get( 'meta_description','','');

		$hits = $jinput->get( 'hits','','int');
		$table->hits = $hits;


		if($table->dates == null || $table->recurrence_type == '0') {
			$table->recurrence_number = '';
			$table->recurrence_byday = '';
			$table->recurrence_counter = '';
			$table->recurrence_type = '';
			$table->recurrence_limit = '';
			$table->recurrence_limit_date = '';
		} else {
			$table->recurrence_number = $recurrencenumber;
			$table->recurrence_byday = $recurrencebyday;
		}



		$table->meta_keywords = $metakeywords;
		$table->meta_description = $metadescription;

		// Check if image was selected
		jimport('joomla.filesystem.file');
		$format 	= JFile::getExt(JPATH_SITE.'/images/jem/events/'.$table->datimage);

		$allowable 	= array ('gif', 'jpg', 'png');
		if (in_array($format, $allowable)) {
			$table->datimage = $table->datimage;
		} else {
			$table->datimage = '';
		}


		$table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);

		// Increment the content version number.
		$table->version++;

		// Make sure the data is valid
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		if (!$table->store(true)) {
			JError::raiseError(500, $table->getError() );
		}

		$fileFilter = new JInput($_FILES);

		// attachments
		// new ones first
		$attachments = $fileFilter->get( 'attach', null, 'array' );
		$attachments['customname'] = $jinput->post->get( 'attach-name', null, 'array' );
		$attachments['description'] = $jinput->post->get( 'attach-desc', null, 'array' );
		$attachments['access'] = $jinput->post->get( 'attach-access', null, 'array' );
		JEMAttachment::postUpload($attachments, 'event'.$table->id);

		// and update old ones
		$attachments = array();
		$old['id'] = $jinput->post->get( 'attached-id', array(), 'array' );
		$old['name'] = $jinput->post->get( 'attached-name', array(), 'array' );
		$old['description'] = $jinput->post->get( 'attached-desc', array(), 'array' );
		$old['access'] = $jinput->post->get( 'attached-access', array(), 'array' );

		foreach ($old['id'] as $k => $id)
		{
			$attach = array();
			$attach['id'] = $id;
			$attach['name'] = $old['name'][$k];
			$attach['description'] = $old['description'][$k];
			$attach['access'] = $old['access'][$k];
			JEMAttachment::update($attach);
		}


		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__jem_cats_event_relations'));
		$query->where('itemid = '.$table->id);

		$db->setQuery($query);
		$db->query();

		foreach($cats as $cat)
		{
			// Get a db connection.
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Insert columns.
			$columns = array('catid', 'itemid');

			// Insert values.
			$values = array($cat, $table->id);

			// Prepare the insert query.
			$query
				->insert($db->quoteName('#__jem_cats_event_relations'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));

			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			$db->query();
		}


		// check for recurrence, when filled it will perform the cleanup function
		if ($table->recurrence_number > 0 && !$table->dates == null)
		{
			JEMHelper::cleanup(1);
		}
	}
}
