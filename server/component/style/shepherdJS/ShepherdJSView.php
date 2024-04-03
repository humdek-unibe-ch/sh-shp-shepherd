<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/. */
?>
<?php
require_once __DIR__ . "/../../../../../../component/style/StyleView.php";

/**
 * The view class of the formUserInput style component.
 */
class ShepherdJSView extends StyleView
{
    /* Private Properties *****************************************************/

    /* Constructors ***********************************************************/

    /**
     * The constructor.
     *
     * @param object $model
     *  The model instance of a base style component.
     * @param object $controller
     *  The controller instance of the component.
     */
    public function __construct($model, $controller)
    {
        parent::__construct($model, $controller);
    }


    /* Public Methods *********************************************************/

    /**
     * Render the style view.
     */
    public function output_content()
    {
        require __DIR__ . "/tpl_shepherdJS.php";
    }

    /**
     * Output JSON for mobile
     */
    public function output_content_mobile()
    {
        $style = parent::output_content_mobile();
        return $style;
    }

    /**
     * Get js include files required for this component. This overrides the
     * parent implementation.
     *
     * @return array
     *  An array of js include files the component requires.
     */
    public function get_js_includes($local = array())
    {
        if (empty($local)) {
            if (DEBUG) {
                $local = array(
                    __DIR__ . "/js/1_shepherd.min.js",
                    __DIR__ . "/js/2_shepherdJS.js",
                );
            } else {
                $local = array(__DIR__ . "/../../../../js/ext/shepherd.min.js?v=" .$this->model->get_services()->get_db()->get_git_version(__DIR__));
            }
        }
        return parent::get_js_includes($local);
    }

    /**
     * Get css include files required for this component. This overrides the
     * parent implementation.
     *
     * @return array
     *  An array of css include files the component requires.
     */
    public function get_css_includes($local = array())
    {
        if (empty($local)) {
            if (DEBUG) {
                $local = array(
                    __DIR__ . "/css/shepherd.css"
                );
            } else {
                $local = array(__DIR__ . "/../../../../css/ext/shepherd.min.css?v=" . $this->model->get_services()->get_db()->get_git_version(__DIR__));
            }
        }
        return parent::get_css_includes($local);
    }
}
?>
