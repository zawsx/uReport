<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
?>
<div class="responseForm">
	<h2><?php echo $this->translate('labels.add_response'); ?></h2>
	<form method="post" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">
		<fieldset><legend><?php echo $this->translate('messages.how_did_you_respond'); ?></legend>
			<input name="issue_id" type="hidden" value="<?php echo $this->issue->getId(); ?>" />
			<table>
				<tr><td><label for="contactMethod_id">
						<?php echo $this->translate(array('labels.contactMethod', 'labels.contactMethods', 1)); ?>
						</label>
					</td>
					<td><select name="contactMethod_id" id="contactMethod_id">
						<?php
							$list = new ContactMethodList();
							$list->find();
							foreach ($list as $method) {
								$name = View::escape($method->getName());
								$selected = $method->getId() == $this->issue->getContactMethod_id()
									? 'selected="selected"'
									: '';
								echo "<option value=\"{$method->getId()}\" $selected>$name</option>";
							}
						?>
						</select>
					</td>
				</tr>
			</table>
			<div><label for="notes"><?php echo $this->translate(array('labels.comment', 'labels.comments', 2)); ?></label></div>
			<div><textarea name="notes" id="notes"></textarea></div>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons($this->issue->getTicket()->getURL());
			?>
		</fieldset>
	</form>
</div>
