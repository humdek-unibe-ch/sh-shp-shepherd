jQuery(document).ready(function () {
    console.log('shepherd js loaded');
    $('.selfhelp-shepherd').each(function () {
        initShepherd(this);
    });
});

function initShepherd(shepherd_element) {
    var shepherd_data = $(shepherd_element).data('shepherd');
    console.log(shepherd_data);
    var tourName = shepherd_data['options']['tourName'];
    var currentShepherdState = {};
    if (window.localStorage.getItem(tourName)) {
        currentShepherdState = JSON.parse(window.localStorage.getItem(tourName));
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
                console.log(button.action);
                if (button.action) {
                    if (button.action.includes('next')) {
                        button.action = tour.next;
                    } else if (button.action.includes('back')) {
                        button.action = tour.back;
                    } else if (button.action.includes('complete')) {
                        button.action = tour.complete;
                    } else {
                        button['orig_action'] = button['action'];
                        button.action = () => {
                            console.log('Wrong action for step:', step);
                            alert('Wrong action, check the console log for more information!');
                        };
                    }
                }
            });
            console.log(step);
            tour.addStep(step);
        });

        tour.start();
        if (currentShepherdState['step_index'] != undefined) {
            tour.show(currentShepherdState['step_index']);
        }

        // Listen for when a step changes and update the stored step index
        tour.on('show', function (event) {
            if (tourName && currentShepherdState) {
                currentShepherdState['step_index'] = event.tour.steps.indexOf(event.tour.currentStep) + 1;
                if (!currentShepherdState['trigger_type']) {
                    currentShepherdState['trigger_type'] = 'started';
                } else if (currentShepherdState['trigger_type'] !== 'finished') {
                    currentShepherdState['trigger_type'] = 'updated';
                }
                window.localStorage.setItem(tourName, JSON.stringify(currentShepherdState));
            }
        });

        // Catch the complete event
        tour.on('complete', function () {
            // Your code to execute when the tour is complete
            if (shepherd_data['show_once'] == "1") {
                // keep the state that is finished
                currentShepherdState['trigger_type'] = 'finished';
                window.localStorage.setItem(tourName, JSON.stringify(currentShepherdState));
            } else {
                // clear the state
                window.localStorage.removeItem(tourName);
            }
        });
    }
}