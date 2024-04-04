jQuery(document).ready(function () {
    console.log('shepherd js loaded');
    $('.selfhelp-shepherd').each(function () {
        initShepherd(this);
    });
});

function initShepherd(shepherd_element) {
    $shepherd_data = $(shepherd_element).data('shepherd');
    console.log($shepherd_data);
    var tourName = $shepherd_data['options']['tourName'];
    var currentShepherdState = {};
    if (window.localStorage.getItem(tourName)) {
        currentShepherdState = JSON.parse(window.localStorage.getItem(tourName));
    }
    console.log(tourName);
    if (!$shepherd_data['is_cms']) {
        // if not cms load it
        const tour = new Shepherd.Tour($shepherd_data['options']);
        // load steps
        $shepherd_data['steps'].forEach(step => {
            step.buttons.forEach(button => {
                console.log(button.action);
                if (button.action) {
                    if (button.action.includes('next')) {
                        button.action = tour.next;
                    } else if (button.action.includes('back')) {
                        button.action = tour.back;
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
                console.log(event);
                currentShepherdState['step_index'] = event.tour.steps.indexOf(event.tour.currentStep) + 1;
                window.localStorage.setItem(tourName, JSON.stringify(currentShepherdState));
                console.log('store', JSON.stringify(currentShepherdState));
                console.log('storeObj', currentShepherdState);
            }
        });
    }
}