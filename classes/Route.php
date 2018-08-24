<?php
namespace classes{
use \classes\Config as Config;
    class Route
    {
        private $_controller;
        private $_method, $_role;

        public function __construct()
		{
            $this->_setController();
        }
        private function _setController()
        {
            $potentialClass = (!$_GET["controller"])? "Home" : ucfirst(escape($_GET['controller']));
            $potentialSection = (file_exists(__CONTROLLER__.$potentialClass.".php"));
            $this->_controller = ($_GET['controller'] && $potentialSection)? $potentialClass : "Home";
            unset($_GET['controller']);
			$this->_controller =  $potentialClass;
            $this->_renderController();
        }
        private function _renderController()
        {
          $controllerName = __CONTROLLERS__.$this->_controller;
          new $controllerName();
        }
    }
}
