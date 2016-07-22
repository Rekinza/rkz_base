<?php
/**
 * @package ET_Filter
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Filter_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard{

    public function initControllerRouters(Varien_Event_Observer $observer){
        $front = $observer->getEvent()->getFront();
        $this->collectRoutes('frontend', 'standard');
        $front->addRouter('et_catalog', $this);
    }

    public function match(Zend_Controller_Request_Http $request){
        $helper = Mage::helper('filter');
        if (! $helper->isEnabled()) {
            return false;
        }

        $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
        $identifier = ltrim($request->getPathInfo(), '/');
        $identifier = substr($identifier, 0, strlen($identifier) - strlen($suffix));
        $urlSplit = explode($helper->getRoutingSuffix(), $identifier, 2);
        
        if (!isset($urlSplit[1])) {
            return false;
        }

        $urlRewrite = Mage::getModel('core/url_rewrite');
        $urlRewrite->setStoreId(Mage::app()->getStore()->getId());
        $cat = $urlSplit[0];

        $catPath = $cat . $suffix;
        $urlRewrite->loadByRequestPath($catPath);

        if ($urlRewrite->getId()) {
            $modules = $this->getModuleByFrontName('catalog');

            $found = false;

            foreach ($modules as $realModule) {
                $request->setRouteName($this->getRouteByFrontName('catalog'));

                $this->_checkShouldBeSecure($request, '/catalog/category/view');

                $controllerClassName = $this->_validateControllerClassName($realModule, 'category');
                if (!$controllerClassName) {
                    continue;
                }

                $controllerInstance = Mage::getControllerInstance($controllerClassName, $request, $this->getFront()->getResponse());

                if (!$controllerInstance->hasAction('view')) {
                    continue;
                }
                $found = true;
                break;
            }

            if (!$found) {
                return false;
            }

            $request->setPathInfo($urlRewrite->getTargetPath());
            $request->setRequestUri('/' . $urlRewrite->getTargetPath());
            $request->setModuleName('catalog')
                ->setControllerName('category')
                ->setActionName('view')
                ->setControllerModule($realModule)
                ->setParam('id', $urlRewrite->getCategoryId())
                ->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $catPath
            );

            $params = explode('/', trim($urlSplit[1], '/'));
            $layerParams = array();
            $total = count($params);
            for ($i = 0; $i < $total - 1; $i++) {
                if (isset($params[$i + 1])) {
                    $layerParams[$params[$i]] = urldecode($params[$i + 1]);
                    ++$i;
                }
            }

            $layerParams += $request->getPost();

            $request->setParams($layerParams);

            Mage::register('layer_params', $layerParams);

            $request->setDispatched(true);
            $controllerInstance->dispatch('view');

            return true;
        }
        return false;
    }

}
