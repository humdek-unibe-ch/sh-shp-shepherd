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

    /**
     * Array which contain `JSON` objects. Each object is a shepherd [step](https://shepherdjs.dev/docs/Step.html).
     */
    private $steps;

    /**
     * `JSON` configuration for the [tour](https://shepherdjs.dev/docs/Tour.html).
     */
    private $options;

    /**
     * If enabled the tutorial tour will be shown only once. When the tour is completed the status will be changed to finished and it will not be shown anymore for the user. If the user is not logged in and is as a guest, the state is kept in the `localStorage`. If the local storage is cleared then the user can see the tutorial again.
     */
    private $show_once;

    /**
     * Enabling `use_javascript`, button actions string will cause them to be converted to JavaScript using the `eval()` function. Make sure that the provided strings are safe and do not pose a security risk.
     */
    private $use_javascript;

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
        $this->steps = $this->model->get_db_field('steps');
        $this->options = $this->model->get_db_field('options');
        $this->show_once = $this->model->get_db_field('show_once', 1);
        $this->use_javascript = $this->model->get_db_field('use_javascript', 0);
    }


    /* Public Methods *********************************************************/

    /**
     * Render the style view.
     */
    public function output_content()
    {
        $shepherd_data = array();
        $shepherd_data['is_cms'] = $this->model->is_cms_page();
        $shepherd_data['steps'] = $this->steps;
        $shepherd_data['options'] = $this->options;
        $shepherd_data['show_once'] = $this->show_once;
        $shepherd_data['use_javascript'] = $this->use_javascript;
        $shepherd_data['page_keyword'] = $this->model->get_services()->get_router()->get_keyword_from_url();
        $shepherd_data['last_url'] = $this->model->get_services()->get_router()->get_url('#'.$shepherd_data['page_keyword']);
        $shepherd_data['state'] = $this->model->get_shepherd_state();        
        $shepherd_data['id_users'] = intval($_SESSION['id_user']);
        if ($shepherd_data['state'] && $shepherd_data['state']['state']) {
            // load only the state, leave the rest, not needed now
            $record_id = intval($shepherd_data['state']['record_id']);
            $shepherd_data['state'] = $shepherd_data['state']['state'];            
            $shepherd_data['state']['record_id'] = $record_id;
        }
        require __DIR__ . "/tpl_shepherdJS.php";
    }

    /**
     * Output JSON for mobile
     */
    public function output_content_mobile()
    {
        $style = parent::output_content_mobile();
        $state = $this->model->get_shepherd_state();
        $style['page_keyword'] = $this->model->get_services()->get_router()->get_keyword_from_url();
        $style['last_url'] = $this->model->get_services()->get_router()->get_url('#'.$style['page_keyword']);
        if ($state && $state['state']) {
            // load only the state, leave the rest, not needed now
            $style['state'] = $state['state'];
        }
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
                $local = array(__DIR__ . "/../../../../js/ext/shepherd.min.js?v=" . $this->model->get_services()->get_db()->get_git_version(__DIR__));
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

