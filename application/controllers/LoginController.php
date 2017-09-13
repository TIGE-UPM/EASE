<?php
class LoginController extends Zend_Controller_Action{
    public function getForm()
    {
        return new Form_Login(array(
            'method' => 'post',
        ));
    }

    public function getAuthAdapter($formValues)
    {
	    $bootstrap = $this->getInvokeArg('bootstrap'); // gets the boostrapper
		$dbAdapter = $bootstrap->getResource('db'); // get the resource from it
		$authAdapter = new Zend_Auth_Adapter_DbTable(
		    $dbAdapter,
		    'users',
		    'email',
		    'password'
		);
		$authAdapter
		    ->setIdentity($formValues['username'])
		    ->setCredential(md5($formValues['password']))
		;

		return $authAdapter;
    }
	public function preDispatch(){
	    
    }
	public function indexAction(){
			$this->view->headTitle($this->view->title, 'PREPEND');
	        $this->view->form = $this->getForm();
			$request = $this->getRequest();
			if (isset ($_GET['register']) && $_GET['register']=='ok'){
				$this->view->message="Cuenta creada correctamente";
			}
	        // Check if we have a POST request
	        if ($request->isPost()) {
	        
		        $this->processAction();
			}
			
	}
	public function processAction()
	    {
			$request = $this->getRequest();

	        // Get our form and validate it
	        $form = $this->getForm();
	        if (!$form->isValid($request->getPost())) {
	            // Invalid entries
	            $this->view->form = $form;
	            return $this->render('index'); // re-render the login form
	        }

	        // Get our authentication adapter and check credentials
	        $adapter = $this->getAuthAdapter($form->getValues());
	        $auth    = Zend_Auth::getInstance();
	        $result  = $auth->authenticate($adapter);
	        if (!$result->isValid()) {
	            // Invalid credentials
	            $form->setDescription('Nombre de usuario o contraseña incorrecto.');
	            $this->view->form = $form;
	            return $this->render('index'); // re-render the login form
	        }

	        // We're authenticated! Redirect to the home page
	        $this->_helper->redirector('index', 'index');
	    }
	public function logoutAction()
	    {
	        Zend_Auth::getInstance()->clearIdentity();
	        $this->_helper->redirector('index', 'index'); // back to login page
	    }
}
?>