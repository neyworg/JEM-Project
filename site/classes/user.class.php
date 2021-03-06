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
 * Holds all authentication logic
 *
 * @package JEM
 */
class JEMUser {
	/**
	 * Checks access permissions of the user regarding on the groupid
	 *
	 *
	 * @param int $recurse
	 * @param int $level
	 * @return boolean True on success
	 */
	static function validate_user($recurse, $level) {
		$user 		= JFactory::getUser();

		//only check when user is logged in
		if ( $user->get('id') ) {
			//open for superuser or registered and thats all what is needed
			//level = -1 all registered users
			//level = -2 disabled
			if ((( $level == -1 ) && ( $user->get('id') )) || (( JFactory::getUser()->authorise('core.manage') ) && ( $level == -2 ))) {
				return true;
			}
		//end logged in check
		}
		//oh oh, user has no permissions
		return false;
	}

	/**
	 * Checks if the user is allowed to edit an item
	 *
	 *
	 * @param int $allowowner
	 * @param int $ownerid
	 * @param int $recurse
	 * @param int $level
	 * @return boolean True on success
	 */
	static function editaccess($allowowner, $ownerid, $recurse, $level) {
		$user		= JFactory::getUser();

		$generalaccess = JEMUser::validate_user( $recurse, $level );

		if ($allowowner == 1 && ( $user->get('id') == $ownerid && $ownerid != 0 ) ) {
			return true;
		} elseif ($generalaccess == 1) {
			return true;
		}
		return false;
	}

	/**
	 * Checks if the user is a superuser
	 * A superuser will allways have access if the feature is activated
	 *
	 * @return boolean True on success
	 */
	static function superuser() {
		$user 		= JFactory::getUser();
		$userGroups = $user->getAuthorisedGroups();

		$group_ids = array(
					7, //administrator
					8  //super administrator
					);

		foreach ($userGroups as $gid) {
			if (in_array($gid, $group_ids)) return true;
		}

		return false;
	}

	/**
	 * Checks if the user has the privileges to use the wysiwyg editor
	 *
	 * We could use the validate_user method instead of this to allow to set a groupid
	 * Not sure if this is a good idea
	 *
	 * @return boolean True on success
	 */
	static function editoruser() {
		$user 		= JFactory::getUser();
		$userGroups = $user->getAuthorisedGroups();

		$group_ids = array(
 					2, // registered
					3, // author
					4, // editor
					5, // publisher
					6, // manager
					7, // administrator
					8  // Super Users
					);

		foreach ($userGroups as $gid) {
			if (in_array($gid, $group_ids)) return true;
		}

		return false;
	}

	/**
	 * Checks if the user is a maintainer of a category
	 *
	 * 
	 */
	static function ismaintainer() {
		//lets look if the user is a maintainer
		$db 	= JFactory::getDBO();
		$user	= JFactory::getUser();

		$query = 'SELECT g.group_id'
				. ' FROM #__jem_groupmembers AS g'
				. ' WHERE g.member = '.(int) $user->get('id')
				;
		$db->setQuery( $query );

		$catids = $db->loadColumn();


		//no results, no maintainer
		if (!$catids) {
			return null;
		}

		$categories = implode(' OR groupid = ', $catids);

		//count the maintained categories
		$query = 'SELECT COUNT(id)'
				. ' FROM #__jem_categories'
				. ' WHERE published = 1'
				. ' AND groupid = '.$categories
				;
		$db->setQuery( $query );

		$maintainer = $db->loadResult();

		return $maintainer;
	}




	/**
	 * Checks if an user is a groupmember and if so
	 * if the group is allowed to add-venues
	 *
	 */
	static function addvenuegroups() {
		//lets look if the user is a maintainer
		$db 	= JFactory::getDBO();
		$user	= JFactory::getUser();


		/*
		 * just a basic check to see if the current user is in an usergroup with
		 * access for submitting venues
		 *
		 * if a result then return true, otherwise false
		 *
		 * views:
		 * venues, venue, editvenue
		 *
		 */

				$query = 'SELECT gr.id'
				. ' FROM #__jem_groups AS gr'
				. ' LEFT JOIN #__jem_groupmembers AS g ON g.group_id = gr.id'
				. ' AND gr.addvenue = 1 '
				. ' WHERE g.member = '.(int) $user->get('id')
				;
				$db->setQuery( $query );

				$groupnumber = $db->loadResult();

				//no results
				if (!$groupnumber) {
				return null;
				}  else {

				return $groupnumber;

				}
	}


	/**
	 * Checks if an user is a groupmember and if so
	 * if the group is allowed to publish-venues
	 *
	 */
		static function publishvenuegroups() {
		//lets look if the user is a maintainer
		$db 	= JFactory::getDBO();
		$user	= JFactory::getUser();


		/*
		 * just a basic check to see if the current user is in an usergroup with
		* access for publishing venues
		*
		* if a result then return true, otherwise false
		*
		*/

		$query = 'SELECT gr.id'
				. ' FROM #__jem_groups AS gr'
				. ' LEFT JOIN #__jem_groupmembers AS g ON g.group_id = gr.id'
				. ' AND gr.publishvenue = 1 '
				. ' WHERE g.member = '.(int) $user->get('id')
				;
				$db->setQuery( $query );

				$groupnumber = $db->loadResult();

				//no results
				if (!$groupnumber) {
						return null;
				}  else {

				return $groupnumber;

				}
	}


	/**
	 * Checks if an user is a groupmember and if so
	 * if the group is allowed to edit-venues
	 *
	 */
	static function editvenuegroups() {
		//lets look if the user is a maintainer
		$db 	= JFactory::getDBO();
		$user	= JFactory::getUser();


		/*
		 * just a basic check to see if the current user is in an usergroup with
		* access for editing venues
		*
		* if a result then return true, otherwise false
		*
		*/

		$query = 'SELECT gr.id'
				. ' FROM #__jem_groups AS gr'
				. ' LEFT JOIN #__jem_groupmembers AS g ON g.group_id = gr.id'
				. ' AND gr.editvenue = 1 '
				. ' WHERE g.member = '.(int) $user->get('id')
				;
				$db->setQuery( $query );

				$groupnumber = $db->loadResult();

				//no results
				if (!$groupnumber) {
						return null;
						}  else {

				return $groupnumber;

				}
	}





}