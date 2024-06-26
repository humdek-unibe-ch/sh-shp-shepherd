jQuery(document).ready(function () {
    $('.selfhelp-shepherd').each(function () {
        initShepherd(this);
    });
});

/**
 * Initialize Shepherd tour.
 * @param {HTMLElement} shepherd_element - The element that triggers the Shepherd tour.
 */
function initShepherd(shepherd_element) {
    var shepherd_data = $(shepherd_element).data('shepherd');
    var tourName = shepherd_data['options']['tourName'];
    var currentShepherdState = {
        "tourName": tourName
    };
    $(shepherd_element).removeAttr('data-shepherd');
    var last_url;
    if (window.localStorage.getItem(tourName)) {
        currentShepherdState = JSON.parse(window.localStorage.getItem(tourName));
        if (currentShepherdState['last_url']) {
            last_url = currentShepherdState['last_url'];
        }
    }
    if (shepherd_data['state'] && shepherd_data['state']['trigger_type']) {
        // use the one form DB, if it exist
        // use only the trigger_type, if it was finished;
        currentShepherdState['trigger_type'] = shepherd_data['state']['trigger_type'];
        currentShepherdState['record_id'] = shepherd_data['state']['record_id'];
    }
    if (shepherd_data['show_once'] == "0" && currentShepherdState['trigger_type'] === 'finished') {
        // it was finished, but it can be done multiple times
        // reset
        currentShepherdState = {
            "tourName": tourName,
            "trigger_type": "updated" // set it with status updated, because the entry is already in DB
        };
    }
    if (shepherd_data['state'] && shepherd_data['show_once'] == "1" && currentShepherdState['trigger_type'] === 'finished') {
        return;
    }
    if (!shepherd_data['is_cms']) {
        if (last_url && last_url != shepherd_data['last_url']) {
            window.location.href = last_url;
            return;
        }
        currentShepherdState['page_keyword'] = shepherd_data['page_keyword'];
        currentShepherdState['last_url'] = shepherd_data['last_url'];
        currentShepherdState['id_users'] = shepherd_data['id_users'];        
        // if not cms load it
        const tour = new Shepherd.Tour(shepherd_data['options']);
        // load steps
        shepherd_data['steps'].forEach(step => {
            step.buttons.forEach(button => {
                if (button.action) {
                    if (shepherd_data['use_javascript'] == "1") {
                        try {
                            button.action = eval(button.action);
                        } catch (error) {
                            console.log('Wrong code for: ', button.action, error);
                        }
                    } else {
                        if (button.action.includes('next')) {
                            button.action = tour.next;
                        } else if (button.action.includes('back')) {
                            button.action = tour.back;
                        } else if (button.action.includes('complete')) {
                            button.action = tour.complete;
                        } else {
                            button['orig_action'] = button['action'];
                            button.action = () => {
                                alert('Wrong action, check the console log for more information!');
                            };
                        }
                    }
                }
            });
            tour.addStep(step);
        });

        tour.start();
        if (currentShepherdState['step_index'] != undefined) {
            tour.show(currentShepherdState['step_index']);
        }

        // Listen for when a step changes and update the stored step index
        tour.on('show', function (event) {
            delete currentShepherdState['last_url'];
            if (tourName && currentShepherdState) {
                currentShepherdState['step_index'] = event.tour.steps.indexOf(event.step);
                if (!currentShepherdState['trigger_type']) {
                    currentShepherdState['trigger_type'] = 'started';
                } else if (currentShepherdState['trigger_type'] !== 'finished') {
                    currentShepherdState['trigger_type'] = 'updated';
                }
                saveShepherdState(currentShepherdState);
            }
        });

        // Catch the complete event
        tour.on('complete', function () {
            currentShepherdState['trigger_type'] = 'finished';
            saveShepherdState(currentShepherdState);
        });

    }
    saveShepherdState(currentShepherdState);
}

/**
 * Saves the state of the Shepherd tour to localStorage and optionally sends it to the server.
 * @param {Object} currentShepherdState - The state object to be saved.
 * @param {string} currentShepherdState.tourName - The name of the tour.
 */
function saveShepherdState(currentShepherdState) {
    window.localStorage.setItem(currentShepherdState["tourName"], JSON.stringify(currentShepherdState));
    // save for the user
    $.ajax({
        type: 'post',
        url: window.location,
        data: currentShepherdState
    });
    if (currentShepherdState['trigger_type'] == 'finished' && currentShepherdState['id_users'] > 1) {
        // tour is finished, the user is logged in, remove the state from local storage. That will be check in the DB
        window.localStorage.removeItem(currentShepherdState["tourName"]);
    }
}