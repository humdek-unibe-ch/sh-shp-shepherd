<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/. */
?>
<?php
require_once __DIR__ . "/../../../../../../component/style/StyleModel.php";

/**
 * This class is used to prepare all data related to the form style
 * components such that the data can easily be displayed in the view of the
 * component.
 */
class ShepherdJSModel extends StyleModel
{
    /* Private Properties *****************************************************/


    /* Constructors ***********************************************************/

    /**
     * The constructor fetches all session related fields from the database.
     *
     * @param object $services
     *  An associative array holding the different available services. See the
     *  class definition base page for a list of all services.
     * @param int $id
     *  The section id of the navigation wrapper.
     * @param array $params
     *  The list of get parameters to propagate.
     * @param number $id_page
     *  The id of the parent page
     * @param array $entry_record
     *  An array that contains the entry record information.
     */
    public function __construct($services, $id, $params)
    {
        parent::__construct($services, $id, $params);
    }

    /* Private Methods *********************************************************/

    /**
     * Prepares data for storage, ensuring it contains necessary properties and formatting.
     *
     * @param array $data - The data to be prepared.
     * @return array|bool - Returns the prepared data or false if the 'tourName' property is missing.
     */
    private function prepare_data($data)
    {
        if (!$data || !isset($data['tourName']) || !$data['tourName']) {
            return false;
        }
        $tourName = $data['tourName'];
        $fullObject = json_encode($data);
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // add all children directly in the data        
                foreach ($value as $val_key => $val_value) {
                    if (is_array($val_value)) {
                        $data[$key . "_" . $val_key] = json_encode($val_value); // the value is array, save it as json
                    } else {
                        $data[$key . "_" . $val_key] = $val_value;
                    }
                }
                unset($data[$key]); // remove the nested result
            }
        }
        $data[$tourName] = $fullObject;
        $data['id_users'] = $_SESSION['id_user'];
        return $data;
    }

    /**
     * Get the name for the Shepherd form.
     *
     * @return string - The name for the Shepherd form.
     */
    function get_form_name()
    {
        return "shepherd-" . $this->section_id;
    }

    /* Public Methods *********************************************************/

    /**
     * Get Shepherd data.
     *
     * Retrieves Shepherd data associated with the current section and user.
     *
     * @return array|bool - Returns an array of Shepherd data on success or false if the data is not found.
     */
    public function get_shepherd_state()
    {
        $form_id = $this->user_input->get_form_id($this->get_form_name(), FORM_EXTERNAL);
        if(!$form_id){
            return false;
        }
        $res = $this->user_input->get_data($form_id, ' ORDER BY record_id DESC', true, FORM_EXTERNAL, $_SESSION['id_user'], true);
        if ($res && $res['tourName'] && $res[$res['tourName']]) {
            $res['state'] = json_decode($res[$res['tourName']], true);
            foreach ($res['state'] as $key => $value) {
                // Check if the value is numeric and convert it to an integer
                if (is_numeric($value) && ctype_digit((string)$value)) {
                    $res['state'][$key] = intval($value);
                }
            }
        }
        return $res;
    }

    /**
     * Saves Shepherd tour data.
     *
     * @param array $data - The data to be saved.
     * @return bool|array - Returns true on success or false if the data is not prepared, or an array if there is an error.
     */
    public function save_shepherd($data)
    {
        $data = $this->prepare_data($data);
        if (!$data) {
            return false;
        }
        $shepherd_name = $this->get_form_name();
        if (isset($data['trigger_type'])) {
            if ($data['trigger_type'] == actionTriggerTypes_started) {
                return $this->user_input->save_external_data(transactionBy_by_user, $shepherd_name, $data);
            } else {
                return $this->user_input->save_external_data(transactionBy_by_user, $shepherd_name, $data, array(
                    "id_users" => $data['id_users']
                ));
            }
        }
        return false;
    }
}
?>
