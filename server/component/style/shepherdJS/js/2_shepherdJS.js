jQuery(document).ready(function () {
    console.log('shepherd js loaded');
    initShepherdJS();
});

function initShepherdJS() {
    const tour = new Shepherd.Tour({
        useModalOverlay: true,
        defaultStepOptions: {
            classes: 'shadow-md bg-purple-dark',
            scrollTo: true
        }
    });
    tour.addStep({
        id: 'example-step',
        text: 'This step is attached to the bottom of the <code>.example-css-selector</code> element.',
        attachTo: {
            element: '.style-section-277',
            on: 'bottom'
        },
        classes: 'example-step-extra-class',
        buttons: [
            {
                text: 'Next',
                action: tour.next
            }
        ]
    });
    tour.addStep({
        id: 'example-step2',
        text: 'Step 2',
        attachTo: {
            element: '.cms-edit',
            on: 'top'
        },
        classes: 'example-step-extra-class',
        buttons: [
            {
                text: 'Back',
                action: tour.back
            }
        ]
    });
    tour.start();
}