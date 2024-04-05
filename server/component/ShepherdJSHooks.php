<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/. */
?>
<?php
require_once __DIR__ . "/../../../../component/BaseHooks.php";
require_once __DIR__ . "/../../../../component/style/BaseStyleComponent.php";

/**
 * The class to define the hooks for the plugin.
 */
class ShepherdSHooks extends BaseHooks
{
    /* Constructors ***********************************************************/

    /**
     * The constructor creates an instance of the hooks.
     * @param object $services
     *  The service handler instance which holds all services
     * @param object $params
     *  Various params
     */
    public function __construct($services, $params = array())
    {
        parent::__construct($services, $params);
    }

    /* Private Methods *********************************************************/

    /**
     * Check if the page contains a shepherd js
     * @param string $page_keyword
     * The keyword of the page
     * @return boolean
     * Return true if the page contains shepherd js or false
     */
    private function page_has_shepherd_js($page_keyword, $id_page = null)
    {
        if ($id_page == null) {
            $id_page = $this->db->fetch_page_id_by_keyword($page_keyword);
        }
        $sql = "CALL get_all_sections_in_page(:id_page)";
        $res = $this->db->query_db($sql, array(":id_page" => $id_page));
        if (!$res) {
            return false;
        } else {
            foreach ($res as $key => $value) {
                if (isset($value['style_name'])) {
                    if ($value['style_name'] == 'shepherdJS') {
                        // the page has shepherdJS
                        return true;
                    }
                } else {
                    return false;
                }
            }
        }
    }

    /* Public Methods *********************************************************/

    /**
     * Set csp rules     
     * @return string
     * Return csp_rules
     */
    public function setCspRules($args)
    {
        $res = $this->execute_private_method($args);
        $resArr = explode(';', strval($res));
        foreach ($resArr as $key => $value) {
            if (strpos($value, 'script-src') !== false) {
                if ($this->router->route && $this->page_has_shepherd_js($this->router->route['name'])) {
                    $value = str_replace("'unsafe-inline'", "'unsafe-inline' 'unsafe-eval'", $value);
                } else if (
                    $this->router->route && in_array($this->router->route['name'], array("cmsSelect", "cmsUpdate")) &&
                    isset($this->router->route['params']['pid']) && $this->page_has_shepherd_js($this->router->route['name'], $this->router->route['params']['pid'])
                ) {
                    $value = str_replace("'unsafe-inline'", "'unsafe-inline' 'unsafe-eval'", $value);
                }
                $resArr[$key] = $value;
            } else if (strpos($value, 'font-src') !== false) {
                $value = str_replace("'self'", "'self' https://fonts.gstatic.com", $value);
                $resArr[$key] = $value;
            }
        }
        return implode(";", $resArr);
    }

    /**
     * Get the plugin version
     */
    public function get_plugin_db_version($plugin_name = 'lab-js')
    {
        return parent::get_plugin_db_version($plugin_name);
    }
}
?>
