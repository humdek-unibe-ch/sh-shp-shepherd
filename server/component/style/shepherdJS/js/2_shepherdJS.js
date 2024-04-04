jQuery(document).ready(function () {
    console.log('shepherd js loaded');
    $('.selfhelp-shepherd').each(function () {
        initShepherd(this);
    });
});

function initShepherd(shepherd_element) {
    $shepherd_data = $(shepherd_element).data('shepherd');
    console.log($shepherd_data);
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
    }
}