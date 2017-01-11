# SimpleForm
Library to render web forms, handle it's submits, managing forms sessions and errors.

## Main Features
- creates, renders and submits web forms very dynamicly
- possibility to extend anything anywhere
- input types:
  - inputs: text, password, email, number, hidden, range, multi-range
  - select, multi-select, country select
  - checkbox, checkbox group, radios
  - buttons - button:submit, button:reset, input:submit
  - textarea
  - no-type input to extend basic input with your custom class
- submit validators
  - automatic cross site request forgery token validation in all form types
  - each form filed has natural validator(s) by it's type
  - possible submit values validators:
	- email, max length, safe string, preg_replace pattern
	- integer, number, float, range
    - company vat id/tax id (implemented in CZ/SK), zip code, phone
	- checked, value in options, min/max selected options
	- required
- custom js/css assets for any field type
  - possible to render immediately after form HTML body
  - possible to render as external linked file
- error messages - stored in session only for one hook
- loading default values or previous submit values from session
- clearing session after submitting
- declarating error url, success url, next step url
- templates rendering automaticly or by custom template

## Examples
- [**Application Questionnaires (mvccore/app-questionnaires)**](https://github.com/mvccore/app-questionnaires)