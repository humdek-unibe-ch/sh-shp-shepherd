-- add plugin entry in the plugin table
INSERT IGNORE INTO plugins (name, version) 
VALUES ('shepherd-js', 'v1.0.0');

-- Add new style `shepherdJS`
INSERT IGNORE INTO `styles` (`name`, `id_type`, `id_group`, `description`) VALUES ('shepherdJS', (SELECT id FROM styleType WHERE `name` = 'component'), (select id from styleGroup where `name` = 'Wrapper' limit 1), 'A style which uses [ShepherdJS](https://shepherdjs.dev) to create a tutorial');
INSERT IGNORE INTO `styles_fields` (`id_styles`, `id_fields`, `default_value`, `help`) VALUES (get_style_id('shepherdJS'), get_field_id('css'), NULL, 'Allows to assign CSS classes to the root item of the style.');
INSERT IGNORE INTO `styles_fields` (`id_styles`, `id_fields`, `default_value`, `help`) VALUES (get_style_id('shepherdJS'), get_field_id('css_mobile'), NULL, 'Allows to assign CSS classes to the root item of the style for the mobile version.');
INSERT IGNORE INTO `styles_fields` (`id_styles`, `id_fields`, `default_value`, `help`) VALUES (get_style_id('shepherdJS'), get_field_id('condition'), NULL, 'The field `condition` allows to specify a condition. Note that the field `condition` is of type `json` and requires\n1. valid json syntax (see https://www.json.org/)\n2. a valid condition structure (see https://github.com/jwadhams/json-logic-php/)\n\nOnly if a condition resolves to true the sections added to the field `children` will be rendered.\n\nIn order to refer to a form-field use the syntax `"@__form_name__#__from_field_name__"` (the quotes are necessary to make it valid json syntax) where `__form_name__` is the value of the field `name` of the style `formUserInput` and `__form_field_name__` is the value of the field `name` of any form-field style.');
INSERT IGNORE INTO `styles_fields` (`id_styles`, `id_fields`, `default_value`, `help`) VALUES (get_style_id('shepherdJS'), get_field_id('data_config'), '', 'Define data configuration for fields that are loaded from DB and can be used inside the style with their param names. The name of the field can be used between {{param_name}} to load the required value');

INSERT IGNORE INTO `fields` (`id`, `name`, `id_type`, `display`) VALUES (NULL, 'steps', get_field_type_id('json'), '1');
INSERT IGNORE INTO `styles_fields` (`id_styles`, `id_fields`, `default_value`, `help`) VALUES (get_style_id('shepherdJS'), get_field_id('steps'), '', 'Array which contain `JSON` objects. Each object is a shepherd [step](https://shepherdjs.dev/docs/Step.html). Example: 
```
[
	{
		"id": "example-step",
		"text": "This step is attached to the bottom of the <code>.example-css-selector</code> element.",
		"attachTo": {
			"element": ".style-section-277",
			"on": "bottom"
		},
		"classes": "example-step-extra-class",
		"buttons": [
			{
				"text": "Next",
				"action": "tour.next"
			}
		]
	},
    {
		"id": "example-step2",
		"text": "Step 2",
		"attachTo": {
			"element": ".cms-edit",
			"on": "top"
		},
		"classes": "example-step-extra-class",
		"buttons": [
			{
				"text": "Back",
				"action": "tour.back"
			},
            {
				"text": "Finish",
				"action": "tour.complete"
			}
		]
	}
]
```
');

INSERT IGNORE INTO `fields` (`id`, `name`, `id_type`, `display`) VALUES (NULL, 'options', get_field_type_id('json'), '0');
INSERT IGNORE INTO `styles_fields` (`id_styles`, `id_fields`, `default_value`, `help`) VALUES (get_style_id('shepherdJS'), get_field_id('options'), '', '`JSON` configuration for the [tour](https://shepherdjs.dev/docs/Tour.html). Example: 
```
{
  "useModalOverlay": false,
  "defaultStepOptions": {
    "classes": "shadow-md bg-purple-dark",
    "scrollTo": true    
  },
  "tourName": "shepherd-tour-name"
}

```
');

-- add field show_once to style shepherdJS
INSERT IGNORE INTO `fields` (`id`, `name`, `id_type`, `display`) VALUES (NULL, 'show_once', get_field_type_id('checkbox'), '0');
INSERT IGNORE INTO `styles_fields` (`id_styles`, `id_fields`, `default_value`, `help`) VALUES (get_style_id('shepherdJS'), get_field_id('show_once'), 1, 'If enabled the tutorial tour will be shown only once. When the tour is completed the status will be changed to finished and it will not be shown anymore for the user. If the user is not logged in and is as a guest, the state is kept in the `localStorage`. If the local storage is cleared then the user can see the tutorial again.');

-- register hook get_csp_rules
INSERT IGNORE INTO `hooks` (`id_hookTypes`, `name`, `description`, `class`, `function`, `exec_class`, `exec_function`, `priority`) VALUES ((SELECT id FROM lookups WHERE lookup_code = 'hook_overwrite_return' LIMIT 0,1), 'shepherd-js-addCspRule', 'Add csp rule for ShepherdJS', 'BasePage', 'getCspRules', 'ShepherdJSHooks', 'setCspRules', 1);

-- add field use_javascript to style shepherdJS
INSERT IGNORE INTO `fields` (`id`, `name`, `id_type`, `display`) VALUES (NULL, 'use_javascript', get_field_type_id('checkbox'), '0');
INSERT IGNORE INTO `styles_fields` (`id_styles`, `id_fields`, `default_value`, `help`) VALUES (get_style_id('shepherdJS'), get_field_id('use_javascript'), 0, 'Enabling `use_javascript`, button actions string will cause them to be converted to JavaScript using the `eval()` function. Make sure that the provided strings are safe and do not pose a security risk.');