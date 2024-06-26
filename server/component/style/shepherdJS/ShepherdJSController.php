<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/. */
?>
<?php
require_once __DIR__ . "/../../../../../../component/BaseController.php";
/**
 * The controller class of formUserInput style component.
 */
class ShepherdJSController extends BaseController
{
    /* Private Properties *****************************************************/



    /* Constructors ***********************************************************/

    /**
     * The constructor.
     *
     * @param object $model
     *  The model instance of the login component.
     */
    public function __construct($model)
    {
        parent::__construct($model);
        $condition = $model->calc_condition();
        if ($condition['result'] && isset($_POST['trigger_type']) && $_SESSION['id_user'] > 0) {
            // save only if it is a logged in user
            $this->model->save_shepherd($_POST);
        }
    }

    /* Private Methods ********************************************************/
}
?>
