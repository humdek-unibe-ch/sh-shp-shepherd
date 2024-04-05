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
        "tourName": tourName,
        "id_users": shepherd_data['id_users']
    };
    if (window.localStorage.getItem(tourName)) {
        currentShepherdState = JSON.parse(window.localStorage.getItem(tourName));
    }
    if (shepherd_data['state']) {
        // use the one form DB, if it exist
        currentShepherdState = shepherd_data['state'];
    }
    if (shepherd_data['show_once'] == "0" && currentShepherdState['trigger_type'] === 'finished') {
        // it was finished, but it can be done multiple times
        // reset
        currentShepherdState = {
            "tourName": tourName,
            "id_users": shepherd_data['id_users'],
            "trigger_type": "updated" // set it with status updated, because the entry is already in DB
        };
    }
    if (shepherd_data['show_once'] == "1" && currentShepherdState['trigger_type'] === 'finished') {
        return;
    }
    if (!shepherd_data['is_cms']) {
        // if not cms load it
        const tour = new Shepherd.Tour(shepherd_data['options']);
        // load steps
        shepherd_data['steps'].forEach(step => {
            step.buttons.forEach(button => {
                if (button.action) {
                    $('.style-section-277').trigger( 'click' );
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
        tour.on('show', function () {
            if (tourName && currentShepherdState) {
                currentShepherdState['step_index'] = tour.steps.indexOf(tour.currentStep) + 1;
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
}

/**
 * Saves the state of the Shepherd tour to localStorage and optionally sends it to the server.
 * @param {Object} currentShepherdState - The state object to be saved.
 * @param {string} currentShepherdState.tourName - The name of the tour.
 * @param {number} [currentShepherdState.id_users] - The ID of the user (optional).
 */
function saveShepherdState(currentShepherdState) {
    window.localStorage.setItem(currentShepherdState["tourName"], JSON.stringify(currentShepherdState));
    if (currentShepherdState['id_users'] && currentShepherdState['id_users'] > 1) {
        // save for the user
        $.ajax({
            type: 'post',
            url: window.location,
            data: currentShepherdState
        });
    }
}