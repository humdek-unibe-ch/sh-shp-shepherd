<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/. */
?>
<?php
require_once __DIR__ . "/../../../../../../component/BaseComponent.php";
require_once __DIR__ . "/ShepherdJSView.php";
require_once __DIR__ . "/ShepherdJSModel.php";
require_once __DIR__ . "/ShepherdJSController.php";

/**
 * A component class for a ShepherdJS style component. 
 *
 */
class ShepherdJSComponent extends BaseComponent
{
    /* Constructors ***********************************************************/

    /**
     * The constructor creates an instance of the Model class and the View
     * class and passes the view instance to the constructor of the parent
     * class.
     *
     * @param object $services
     *  An associative array holding the different available services. See the
     *  class definition basepage for a list of all services.
     * @param int $id
     *  The section id of this navigation component.
     * @param array $params
     *  The list of get parameters to propagate.
     * @param number $id_page
     *  The id of the parent page
     * @param array $entry_record
     *  An array that contains the entry record information.
     */
    public function __construct($services, $id, $params)
    {
        $model = new ShepherdJSModel($services, $id, $params);        
        $controller = null;
        if(!$model->is_cms_page())
            $controller = new ShepherdJSController($model);
        $view = new ShepherdJSView($model, $controller);
        parent::__construct($model, $view, $controller);
    }
}
?>
