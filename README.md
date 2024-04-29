# SelfHelp plugin - [ShepherdJS](https://shepherdjs.dev)

This is a SelfHelpPlugin that is used for [ShepherdJS](https://shepherdjs.dev) integration


# Installation

 - Download the code into the `plugin` folder
 - Checkout the latest version 
 - Execute all `.sql` script in the DB folder in their version order 

# Usage

## Add style shepherdJS
Insert style `shepherdJS` on the page where you want to show a Tour

## Define [steps](https://shepherdjs.dev/docs/Step.html)
Create a series of steps for your tour, each containing descriptive text and instructions for the user. These steps should be defined as JSON objects. Each step should include an `id`, `text` describing the step, `attachTo` specifying the element to which the step should be attached, `canClickTarget` indicating if the target can be clicked, `classes` for styling, and `buttons` defining any action buttons.

## Set [options](https://shepherdjs.dev/docs/Tour.html)
Configure the options for your tour, including whether to use a modal overlay, default step options such as styling and scrolling behavior, and a unique tour name

## Reusable shepherd across pages (Optional)
If your tour needs to be displayed across multiple pages, consider using a reference container approach. Create a reference container within your Selfhelp system and load it with the Shepherd style as a child element on all pages where the tour is required.

# Requirements

 - SelfHelp v6.12.1+
