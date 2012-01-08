<?php
/**
 * Eternizer.
 *
 * @copyright Michael Ueberschaer
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package Eternizer
 * @author Michael Ueberschaer <kontakt@webdesign-in-bremen.com>.
 * @link http://www.webdesign-in-bremen.com
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.5.4 (http://modulestudio.de) at Fri Jan 06 18:59:41 CET 2012.
 */


/**
 * Admin controller class.
 */
class Eternizer_Controller_Base_Admin extends Zikula_AbstractController
{
    /**
     * Post initialise.
     *
     * Run after construction.
     *
     * @return void
     */
    protected function postInitialize()
    {
        // Set caching to true by default.
        $this->view->setCaching(Zikula_View::CACHE_ENABLED);
    }


    /**
     * This method is the default function, and is called whenever the application's
     * Admin area is called without defining arguments.
     *
     * @return mixed Output.
     */
    public function main($args)
    {
// DEBUG: permission check aspect starts
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Eternizer::', '::', ACCESS_ADMIN));
// DEBUG: permission check aspect ends


        // return main template
        return $this->view->fetch('admin/main.tpl');

    }

    /**
     * This method provides a generic item list overview.
     *
     * @param string  $ot           Treated object type.
     * @param string  $sort         Sorting field.
     * @param string  $sortdir      Sorting direction.
     * @param int     $pos          Current pager position.
     * @param int     $num          Amount of entries to display.
     * @param string  $tpl          Name of alternative template (for alternative display options, feeds and xml output)
     * @param boolean $raw          Optional way to display a template instead of fetching it (needed for standalone output)
     * @return mixed Output.
     */
    public function view($args)
    {
// DEBUG: permission check aspect starts
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Eternizer::', '::', ACCESS_ADMIN));
// DEBUG: permission check aspect ends

        // parameter specifying which type of objects we are treating
        $objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'entry', FILTER_SANITIZE_STRING);
        $utilArgs = array('controller' => 'admin', 'action' => 'view');
        if (!in_array($objectType, Eternizer_Util_Controller::getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = Eternizer_Util_Controller::getDefaultObjectType('controllerAction', $utilArgs);
        }
        $repository = $this->entityManager->getRepository('Eternizer_Entity_' . ucfirst($objectType));

        $tpl = (isset($args['tpl']) && !empty($args['tpl'])) ? $args['tpl'] : $this->request->getGet()->filter('tpl', '', FILTER_SANITIZE_STRING);
        if ($tpl == 'tree') {
            $trees = ModUtil::apiFunc($this->name, 'selection', 'getAllTrees', array('ot' => $objectType));
            $this->view->assign('trees', $trees)
                       ->assign($repository->getAdditionalTemplateParameters('controllerAction', $utilArgs));
            // fetch and return the appropriate template
            return Eternizer_Util_View::processTemplate($this->view, 'admin', $objectType, 'view', $args);
        }

        // parameter for used sorting field
        $sort = (isset($args['sort']) && !empty($args['sort'])) ? $args['sort'] : $this->request->getGet()->filter('sort', '', FILTER_SANITIZE_STRING);
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields())) {
            $sort = $repository->getDefaultSortingField();
        }

        // parameter for used sort order
        $sdir = (isset($args['sortdir']) && !empty($args['sortdir'])) ? $args['sortdir'] : $this->request->getGet()->filter('sortdir', '', FILTER_SANITIZE_STRING);
        $sdir = strtolower($sdir);
        if ($sdir != 'asc' && $sdir != 'desc') {
            $sdir = 'asc';
        }

        // convenience vars to make code clearer
        $currentUrlArgs = array('ot' => $objectType);

        $selectionArgs = array(
            'ot' => $objectType,
            'where' => '',
            'orderBy' => $sort . ' ' . $sdir
        );

        $showAllEntries = (int) (isset($args['all']) && !empty($args['all'])) ? $args['all'] : $this->request->getGet()->filter('all', 0, FILTER_VALIDATE_INT);
        $this->view->assign('showAllEntries', $showAllEntries);
        if ($showAllEntries == 1) {
            // item list without pagination
            $entities = ModUtil::apiFunc($this->name, 'selection', 'getEntities', $selectionArgs);
            $objectCount = count($entities);
            $currentUrlArgs['all'] = 1;
        } else {
            // item list with pagination

            // the current offset which is used to calculate the pagination
            $currentPage = (int) (isset($args['pos']) && !empty($args['pos'])) ? $args['pos'] : $this->request->getGet()->filter('pos', 1, FILTER_VALIDATE_INT);

            // the number of items displayed on a page for pagination
            $resultsPerPage = (int) (isset($args['num']) && !empty($args['num'])) ? $args['num'] : $this->request->getGet()->filter('num', 0, FILTER_VALIDATE_INT);
            if ($resultsPerPage == 0) {
                $csv = (int) (isset($args['usecsv']) && !empty($args['usecsv'])) ? $args['usecsv'] : $this->request->getGet()->filter('usecsvext', 0, FILTER_VALIDATE_INT);
                $resultsPerPage = ($csv == 1) ? 999999 : $this->getVar('pagesize', 10);
            }

            $selectionArgs['currentPage'] = $currentPage;
            $selectionArgs['resultsPerPage'] = $resultsPerPage;
            list($entities, $objectCount) = ModUtil::apiFunc($this->name, 'selection', 'getEntitiesPaginated', $selectionArgs);

            $this->view->assign('currentPage', $currentPage)
                       ->assign('pager', array('numitems'     => $objectCount,
                                               'itemsperpage' => $resultsPerPage));
        }

        // build ModUrl instance for display hooks
        $currentUrlObject = new Zikula_ModUrl($this->name, 'admin', 'view', ZLanguage::getLanguageCode(), $currentUrlArgs);

        // assign the object data, sorting information and details for creating the pager
        $this->view->assign('items', $entities)
                   ->assign('sort', $sort)
                   ->assign('sdir', $sdir)
                   ->assign('currentUrlObject', $currentUrlObject)
                   ->assign($repository->getAdditionalTemplateParameters('controllerAction', $utilArgs));

        // fetch and return the appropriate template
        return Eternizer_Util_View::processTemplate($this->view, 'admin', $objectType, 'view', $args);
    }

    /**
     * This method provides a generic item detail view.
     *
     * @param string  $ot           Treated object type.
     * @param string  $tpl          Name of alternative template (for alternative display options, feeds and xml output)
     * @param boolean $raw          Optional way to display a template instead of fetching it (needed for standalone output)
     * @return mixed Output.
     */
    public function display($args)
    {
// DEBUG: permission check aspect starts
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Eternizer::', '::', ACCESS_ADMIN));
// DEBUG: permission check aspect ends

        // parameter specifying which type of objects we are treating
        $objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'entry', FILTER_SANITIZE_STRING);
        $utilArgs = array('controller' => 'admin', 'action' => 'display');
        if (!in_array($objectType, Eternizer_Util_Controller::getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = Eternizer_Util_Controller::getDefaultObjectType('controllerAction', $utilArgs);
        }
        $repository = $this->entityManager->getRepository('Eternizer_Entity_' . ucfirst($objectType));

        $idFields = ModUtil::apiFunc($this->name, 'selection', 'getIdFields', array('ot' => $objectType));

        // retrieve identifier of the object we wish to view
        $idValues = Eternizer_Util_Controller::retrieveIdentifier($this->request, $args, $objectType, $idFields);
        $hasIdentifier = Eternizer_Util_Controller::isValidIdentifier($idValues);
        $this->throwNotFoundUnless($hasIdentifier, $this->__('Error! Invalid identifier received.'));

        $entity = ModUtil::apiFunc($this->name, 'selection', 'getEntity', array('ot' => $objectType, 'id' => $idValues));
        $this->throwNotFoundUnless($entity != null, $this->__('No such item.'));

        // build ModUrl instance for display hooks
        $currentUrlArgs = array('ot' => $objectType);
        foreach ($idFields as $idField) {
            $currentUrlArgs[$idField] = $idValues[$idField];
        }
        $currentUrlObject = new Zikula_ModUrl($this->name, 'admin', 'display', ZLanguage::getLanguageCode(), $currentUrlArgs);

        // assign output data to view object.
        $this->view->assign($objectType, $entity)
                   ->assign('currentUrlObject', $currentUrlObject)
                   ->assign($repository->getAdditionalTemplateParameters('controllerAction', $utilArgs));

        // fetch and return the appropriate template
        return Eternizer_Util_View::processTemplate($this->view, 'admin', $objectType, 'display', $args);
    }

    /**
     * This method provides a generic handling of all edit requests.
     *
     * @param string  $ot           Treated object type.
     * @param string  $tpl          Name of alternative template (for alternative display options, feeds and xml output)
     * @param boolean $raw          Optional way to display a template instead of fetching it (needed for standalone output)
     * @return mixed Output.
     */
    public function edit($args)
    {
// DEBUG: permission check aspect starts
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Eternizer::', '::', ACCESS_ADMIN));
// DEBUG: permission check aspect ends

        // parameter specifying which type of objects we are treating
        $objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'entry', FILTER_SANITIZE_STRING);
        $utilArgs = array('controller' => 'admin', 'action' => 'edit');
        if (!in_array($objectType, Eternizer_Util_Controller::getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = Eternizer_Util_Controller::getDefaultObjectType('controllerAction', $utilArgs);
        }

        // create new Form reference
        $view = FormUtil::newForm($this->name, $this);

        // build form handler class name
        $handlerClass = 'Eternizer_Form_Handler_Admin_' . ucfirst($objectType) . '_Edit';

        // execute form using supplied template and page event handler
        return $view->execute('admin/' . $objectType . '/edit.tpl', new $handlerClass());
    }

    /**
     * This method provides a generic handling of simple delete requests.
     *
     * @param string  $ot           Treated object type.
     * @param int     $id           Identifier of entity to be deleted.
     * @param boolean $confirmation Confirm the deletion, else a confirmation page is displayed.
     * @param string  $tpl          Name of alternative template (for alternative display options, feeds and xml output)
     * @param boolean $raw          Optional way to display a template instead of fetching it (needed for standalone output)
     * @return mixed Output.
     */
    public function delete($args)
    {
// DEBUG: permission check aspect starts
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Eternizer::', '::', ACCESS_ADMIN));
// DEBUG: permission check aspect ends

        // parameter specifying which type of objects we are treating
        $objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'entry', FILTER_SANITIZE_STRING);
        $utilArgs = array('controller' => 'admin', 'action' => 'delete');
        if (!in_array($objectType, Eternizer_Util_Controller::getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = Eternizer_Util_Controller::getDefaultObjectType('controllerAction', $utilArgs);
        }
        
        $idFields = ModUtil::apiFunc($this->name, 'selection', 'getIdFields', array('ot' => $objectType));
        
        // retrieve identifier of the object we wish to delete
        $idValues = Eternizer_Util_Controller::retrieveIdentifier($this->request, $args, $objectType, $idFields);
        $hasIdentifier = Eternizer_Util_Controller::isValidIdentifier($idValues);

        $this->throwNotFoundUnless($hasIdentifier, $this->__('Error! Invalid identifier received.'));

        $entity = ModUtil::apiFunc($this->name, 'selection', 'getEntity', array('ot' => $objectType, 'id' => $idValues));
        $this->throwNotFoundUnless($entity != null, $this->__('No such item.'));

        $confirmation = (bool) (isset($args['confirmation']) && !empty($args['confirmation'])) ? $args['confirmation'] : $this->request->getPost()->filter('confirmation', false, FILTER_VALIDATE_BOOLEAN);

        if ($confirmation) {
            $this->checkCsrfToken();

            // TODO call pre delete validation hooks
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            $this->registerStatus($this->__('Done! Item deleted.'));
            // TODO call post delete process hooks

            // clear view cache to reflect our changes
            $this->view->clear_cache();

            // redirect to the list of the current object type
            $this->redirect(ModUtil::url($this->name, 'admin', 'view',array('ot' => $objectType)));
        }

        $repository = $this->entityManager->getRepository('Eternizer_Entity_' . ucfirst($objectType));

        // assign the object we loaded above
        $this->view->assign($objectType, $entity)
                   ->assign($repository->getAdditionalTemplateParameters('controllerAction', $utilArgs));

        // fetch and return the appropriate template
        return Eternizer_Util_View::processTemplate($this->view, 'admin', $objectType, 'delete', $args);
    }

    /**
     * This method cares for a redirect within an inline frame.
     */
    public function handleInlineRedirect()
    {
        $itemId = (int) $this->request->getGet()->filter('id', 0, FILTER_VALIDATE_INT);
        $idPrefix = $this->request->getGet()->filter('idp', '', FILTER_SANITIZE_STRING);
        $commandName = $this->request->getGet()->filter('com', '', FILTER_SANITIZE_STRING);
        if (empty($idPrefix)) {
            return false;
        }

        $this->view->assign('itemId', $itemId)
                   ->assign('idPrefix', $idPrefix)
                   ->assign('commandName', $commandName)
                   ->assign('jcssConfig', JCSSUtil::getJSConfig())
                   ->display('admin/inlineRedirectHandler.tpl');
        return true;
    }

    /**
     * This method takes care of the application configuration.
     *
     * @return string Output
     */
    public function config()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN));

        // Create new Form reference
        $view = FormUtil::newForm($this->name, $this);

        // Execute form using supplied template and page event handler
        return $view->execute('admin/config.tpl', new Eternizer_Form_Handler_Admin_Config());
    }
}
