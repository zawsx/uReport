<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class AccountController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('backend');
	}

	private function redirectToErrorUrl(Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/account');
		exit();
	}

	public function index()
	{
		$this->template->blocks[] = new Block(
			'people/personInfo.inc',
			array('person'=>$_SESSION['USER'])
		);
	}

	public function update()
	{
		if (isset($_POST['firstname'])) {
			$_SESSION['USER']->handleUpdate($_POST);
			try {
				$_SESSION['USER']->save();
				header('Location: '.BASE_URL.'/account');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block(
			'people/updatePersonForm.inc',
			array(
				'person'=>$_SESSION['USER'],
				'title'=>'Update my info',
				'return_url'=>BASE_URI.'/account'
			)
		);
	}

	/**
	 * Helper function for handling foreign key object deletions
	 *
	 * Email, Phone, and Address are all handled exactly the same way.
	 *
	 * @param string $item
	 */
	private function deleteLinkedItem($item)
	{
		$class = ucfirst($item);

		if (isset($_REQUEST[$item.'_id'])) {
			try {
				$o = new $class($_REQUEST[$item.'_id']);
				$o->delete();
				header('Location: '.BASE_URL.'/account');
				exit();
			}
			catch (Exception $e) { $this->redirectToErrorUrl($e); }
		}
		else {
			$this->redirectToErrorUrl(new Exception("people/unknown$class"));
		}
	}
	public function deleteEmail()   { $this->deleteLinkedItem('email');   }
	public function deletePhone()   { $this->deleteLinkedItem('phone');   }
	public function deleteAddress() { $this->deleteLinkedItem('address'); }

	/**
	 * Helper function for handling foreign key object updates
	 *
	 * Email, Phone, and Address are all handled exactly the same way.
	 *
	 * @param string $item
	 * @param string $requiredField The field to look for in the POST which
	 *								determines whether this item has been posted
	 */
	private function updateLinkedItem($item, $requiredField)
	{
		$class = ucfirst($item);

		if (isset($_REQUEST[$item.'_id'])) {
			try {
				$object = new $class($_REQUEST[$item.'_id']);
				if ($object->getPerson_id() != $_SESSION['USER']->getId()) {
					$this->redirectToErrorUrl(new Exception("people/unknown$class"));
				}
			}
			catch (Exception $e) { $this->redirectToErrorUrl($e); }
		}
		else {
			$object = new $class();
			$object->setPerson($_SESSION['USER']);
		}

		if (!$object->getPerson_id()) {
			$this->redirectToErrorUrl(new Exception('people/unknownPerson'));
		}

		if (isset($_POST[$requiredField])) {
			try {
				$object->handleUpdate($_POST);
				$object->save();
				header('Location: '.BASE_URL.'/account');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block("people/update{$class}Form.inc", array($item=>$object));
	}
	public function updateEmail()   { $this->updateLinkedItem('email',   'email');   }
	public function updatePhone()   { $this->updateLinkedItem('phone',   'number');  }
	public function updateAddress() { $this->updateLinkedItem('address', 'address'); }

	public function updateMyDepartment()
	{
		$return_url = BASE_URL.'/account';

		// Load the User's department
		$department = $_SESSION['USER']->getDepartment();

		if (!$department) {
			$_SESSION['errorMessages'][] = new Exception('departments/unknownDepartment');
			header('Location: '.$return_url);
			exit();
		}

		// Handle any data they post
		if (isset($_POST['name'])) {
			try {
				$department->handleUpdate($_POST);
				$department->save();
				header('Location: '.$return_url);
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		// Display the form
		$this->template->blocks[] = new Block(
			'departments/updateDepartmentForm.inc',
			array(
				'department'=>$department,
				'action'=>BASE_URI.'/account/updateMyDepartment',
				'return_url'=>BASE_URI.'/account'
			)
		);
	}

	public function changePassword()
	{
		if ($_SESSION['USER']->getAuthenticationMethod() != 'local') {
			$_SESSION['errorMessages'][] = new Exception('users/passwordNotAllowed');
			header('Location: '.BASE_URL.'/account');
			exit();
		}

		if (isset($_POST['current_password'])) {
			try {
				$_SESSION['USER']->handleChangePassword($_POST);
				$_SESSION['USER']->save();
				header('Location: '.BASE_URL.'/account');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('users/changePasswordForm.inc');
	}
}
