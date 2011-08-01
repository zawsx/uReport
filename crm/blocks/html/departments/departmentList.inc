<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param DepartmentList $this->departmentList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userIsAllowed('Departments')) {
				echo $this->template->linkButton(
					'Add',BASE_URL.'/departments/updateDepartment.php','add'
				);
			}
		?>
		Departments
	</h1>
	<table>
		<thead>
			<tr><th></th>
				<th>Name</th>
				<th>Default Person</th>
				<th>Categories</th>
				<th>Actions</th>
				<th>Statuses</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->departmentList as $department) {
				$editButton = '';
				$deleteButton = '';
				if (userIsAllowed('Departments')) {
					$editButton = $this->template->linkButton(
						'Edit',
						BASE_URL."/departments/updateDepartment.php?department_id={$department->getId()}",
						'edit'
					);
					// Departments should only be deleted when there's no people in them
					if (!count($department->getPeople())) {
						$deleteButton = $this->template->linkButton(
							'Delete',
							BASE_URL."/departments/deleteDepartment.php?department_id={$department->getId()}",
							'delete'
						);
					}
				}
				$name = View::escape($department->getName());

				$defaultPerson = $department->getDefaultPerson();
				if ($defaultPerson) {
					$defaultPerson = View::escape($defaultPerson->getFullname());
				}

				echo "
				<tr><td>$editButton
						$deleteButton
					</td>
					<td>$name</td>
					<td>$defaultPerson</td>
					<td><ul>
				";
						foreach ($department->getCategories() as $category) {
							echo '<li>'.View::escape($category['name']).'</li>';
						}
				echo "
						</ul>
					</td>
					<td><ul>
				";
						foreach ($department->getActions() as $action) {
							echo '<li>'.View::escape($action['name']).'</li>';
						}
				echo "
						</ul>
					</td>
					<td><ul>
				";
						foreach ($department->getCustomStatuses() as $status) {
							echo '<li>'.View::escape($status).'</li>';
						}
				echo "
						</ul>
					</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>