<?php
/**
 * @version 1.9.1
 * @package JEM
 * @copyright (C) 2013-2013 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
?>
<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data" id="adminForm">
	<fieldset>
		<legend><?php echo JText::_('COM_JEM_IMPORT_EVENTS'); ?></legend>
		<?php echo JText::_('COM_JEM_IMPORT_INSTRUCTIONS') ?>
		<ul>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_COLUMNNAMESEVENTS"); ?>
			</li>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_FIRSTROW"); ?>
			</li>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_CSVFORMAT"); ?>
			</li>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_CATEGORIES_DESC"); ?>
			</li>
			<li>
				<?php echo JText::_( "COM_JEM_IMPORT_POSSIBLECOLUMNS" );?><p>
				<?php echo 'categories, ' . implode(", ",$this->eventfields); ?></p>
			</li>
		</ul>
		<table>
			<tr>
				<td>
					<label for="file">
						<?php echo JText::_('COM_JEM_IMPORT_SELECTCSV').':'; ?>
					</label>
				</td>
				<td>
					<input type="file" id="event-file-upload" accept="text/*" name="Fileevents" />
					<input type="submit" id="event-file-upload-submit" value="<?php echo JText::_('COM_JEM_IMPORT_START'); ?>" onclick="document.getElementsByName('task')[0].value='import.csveventimport';return true;"/>
					<span id="upload-clear"></span>
				</td>
			</tr>
			<tr>
				<td>
					<label for="replace_events">
						<?php echo JText::_('COM_JEM_IMPORT_REPLACEIFEXISTS').':'; ?>
					</label>
				</td>
				<td>
					<?php
					$html = JHTML::_('select.booleanlist', 'replace_events', 'class="inputbox"', 0);
					echo $html;
					?>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('COM_JEM_IMPORT_VENUES'); ?></legend>
		<?php echo JText::_('COM_JEM_IMPORT_INSTRUCTIONS') ?>
		<ul>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_COLUMNNAMESCATEGORIES"); ?>
			</li>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_FIRSTROW"); ?>
			</li>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_CSVFORMAT"); ?>
			</li>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_POSSIBLECOLUMNS").implode(", ",$this->venuefields); ?>
			</li>
		</ul>
		<table>
			<tr>
				<td>
					<label for="file">
						<?php echo JText::_('COM_JEM_IMPORT_SELECTCSV').':'; ?>
					</label>
				</td>
				<td>
					<input type="file" id="venue-file-upload" accept="text/*" name="Filevenues" />
					<input type="submit" id="venue-file-upload-submit" value="<?php echo JText::_('COM_JEM_IMPORT_START'); ?>" onclick="document.getElementsByName('task')[0].value='import.csvvenuesimport';return true;"/>
					<span id="upload-clear"></span>
				</td>
			</tr>
			<tr>
				<td>
					<label for="replace_venues">
						<?php echo JText::_('COM_JEM_IMPORT_REPLACEIFEXISTS').':'; ?>
					</label>
				</td>
				<td>
					<?php
					$html = JHTML::_('select.booleanlist', 'replace_venues', 'class="inputbox"', 0);
					echo $html;
					?>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('COM_JEM_IMPORT_CATEGORIES'); ?></legend>
		<?php echo JText::_('COM_JEM_IMPORT_INSTRUCTIONS') ?>
		<ul>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_COLUMNNAMESCATEGORIES"); ?>
			</li>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_FIRSTROW"); ?>
			</li>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_CSVFORMAT"); ?>
			</li>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_POSSIBLECOLUMNS").implode(", ",$this->catfields); ?>
			</li>
		</ul>
		<table>
			<tr>
				<td>
					<label for="file">
						<?php echo JText::_('COM_JEM_IMPORT_SELECTCSV').':'; ?>
					</label>
				</td>
				<td>
					<input type="file" id="cat-file-upload" accept="text/*" name="Filecategories" />
					<input type="submit" id="cat-file-upload-submit" value="<?php echo JText::_('COM_JEM_IMPORT_START'); ?>" onclick="document.getElementsByName('task')[0].value='import.csvcategoriesimport';return true;"/>
					<span id="upload-clear"></span>
				</td>
			</tr>
			<tr>
				<td>
					<label for="replace_categories">
						<?php echo JText::_('COM_JEM_IMPORT_REPLACEIFEXISTS').':'; ?>
					</label>
				</td>
				<td>
					<?php
					$html = JHTML::_('select.booleanlist', 'replace_categories', 'class="inputbox"', 0);
					echo $html;
					?>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('COM_JEM_IMPORT_CAT_EVENTS'); ?></legend>
		<?php echo JText::_('COM_JEM_IMPORT_INSTRUCTIONS') ?>
		<ul>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_COLUMNNAMESCATEGORIES"); ?>
			</li>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_FIRSTROW"); ?>
			</li>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_CSVFORMAT"); ?>
			</li>
			<li>
				<?php echo JText::_("COM_JEM_IMPORT_POSSIBLECOLUMNS").implode(", ",$this->cateventsfields); ?>
			</li>
		</ul>
		<table>
			<tr>
				<td>
					<label for="file">
						<?php echo JText::_('COM_JEM_IMPORT_SELECTCSV').':'; ?>
					</label>
				</td>
				<td>
					<input type="file" id="catevents-file-upload" accept="text/*" name="Filecatevents" />
					<input type="submit" id="catevents-file-upload-submit" value="<?php echo JText::_('COM_JEM_IMPORT_START'); ?>" onclick="document.getElementsByName('task')[0].value='import.csvcateventsimport';return true;"/>
					<span id="upload-clear"></span>
				</td>
			</tr>
			<tr>
				<td>
					<label for="replace_catevents">
						<?php echo JText::_('COM_JEM_IMPORT_REPLACEIFEXISTS').':'; ?>
					</label>
				</td>
				<td>
					<?php
					$html = JHTML::_('select.booleanlist', 'replace_catevents', 'class="inputbox"', 0);
					echo $html;
					?>
				</td>
			</tr>
		</table>
	</fieldset>

	<input type="hidden" name="option" value="com_jem" />
	<input type="hidden" name="view" value="import" />
	<input type="hidden" name="controller" value="import" />
	<input type="hidden" name="task" value="" />
</form>

<p class="copyright">
	<?php echo JEMAdmin::footer(); ?>
</p>