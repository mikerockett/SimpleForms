<?php

/**
 * SimpleForms for ProcessWire
 * A simple form processor. Uses AJAX, configurable with JSON. Front-end is up to you.
 *
 * Module
 *
 * Copyright (c) 2015, Mike Rockett. All Rights Reserved.
 * Licence: MIT License - http://mit-license.org/
 */

class SimpleFormsConfig extends ModuleConfig
{
    /**
     * Given a fieldtype, create, populate, and return an Inputfield
     * @param  string $fieldNameId
     * @param  array  $meta
     * @return Inputfield
     */
    protected function buildInputField($fieldTypeName, $meta)
    {
        $field = wire('modules')->get($fieldTypeName);

        foreach ($meta as $metaNames => $metaInfo) {
            $metaNames = explode('+', $metaNames);
            foreach ($metaNames as $metaName) {
                $field->$metaName = $metaInfo;
            }

        }

        return $field;
    }

    /**
     * Get default condifguration, automatically passed to input fields.
     * @return array
     */
    public function getDefaults()
    {
        return [
            'autoPrependStylesheets' => true,
            'minifyStylesheets' => true,
            'allowFileUploads' => true,
            'perFormUploads' => true,
            'enableEmailDisclaimer' => false,
        ];
    }

    /**
     * Render input fields on config Page.
     * @return string
     */
    public function getInputFields()
    {
        // Start inputfields
        $inputfields = parent::getInputfields();

        // Checkbox: autoPrependStylesheets
        $inputfields->add($this->buildInputField('InputfieldCheckbox', [
            'name+id' => 'autoPrependStylesheets',
            'label' => $this->_('Auto-Prepend Form Stylesheets'),
            'label2' => $this->_('Automatically prepend form stylesheets to HTML Email Templates'),
            'description' => $this->_('If enabled, `<form>.css` will be automatically prepended to HTML Email Templates, so that you need not use `{stylesheet.name}` in each one.'),
            'autocheck' => true,
            'columnWidth' => 55,
        ]));

        // Checkbox: minifyStylesheets
        $inputfields->add($this->buildInputField('InputfieldCheckbox', [
            'name+id' => 'minifyStylesheets',
            'label' => $this->_('Minify Stylesheets'),
            'label2' => $this->_('Minify before including/prepending stylesheets'),
            'description' => $this->_('If enabled, HTML Email Template Stylesheets will be minified before being inserted (thus saving a little bandwidth).'),
            'autocheck' => true,
            'columnWidth' => 45,
        ]));

        // Radios: allowFileUploads
        $inputfields->add($this->buildInputField('InputfieldCheckbox', [
            'name+id' => 'allowFileUploads',
            'label' => $this->_("File Uploads"),
            'label2' => $this->_('Yes, allow files to be uploaded'),
            'columnWidth' => 43,
            'description' => $this->_('If disabled, all future uploads will not be allowed, and visitors will be notified accordingly, until this setting is enabled again.'),
            'autocheck' => true,
        ]));

        // Checkbox: perFormUploads
        $inputfields->add($this->buildInputField('InputfieldCheckbox', [
            'name+id' => 'perFormUploads',
            'label' => $this->_("Upload Destination"),
            'label2' => $this->_('Yes, save uploads on a per-form basis'),
            'description' => $this->_('If enabled, user uploads will be stored in the `uploads` directory of the respective form being processed. If disabled, uploads will be stored in `site/assets/simple-forms/uploads`.'),
            'autocheck' => true,
            'columnWidth' => 57,
        ]));

        // Fieldset: Email Disclaimer
        $fieldset = $this->buildInputField('InputfieldFieldset', array(
            'label' => $this->_('Email Disclaimer'),
            'collapsed' => Inputfield::collapsedYes,
        ));

        // Checkbox: enableEmailDisclaimer
        $fieldset->add($this->buildInputField('InputfieldCheckbox', [
            'name+id' => 'enableEmailDisclaimer',
            'label' => $this->_('Append an email disclaimer to all emails sent by SimpleForms'),
            'autocheck' => true,
        ]));

        // CKEditor: emailDisclaimer
        $fieldset->add($this->buildInputField('InputfieldCKEditor', [
            'name+id' => 'emailDisclaimer',
            'showIf' => 'enableEmailDisclaimer=1',
            'toolbar' => "PasteText\nRemoveFormat, -, Bold, Italic, Underline\nPWLink, Unlink\nHorizontalRule",
            'skipLabel' => Inputfield::skipLabelHeader,
        ]));

        $inputfields->add($fieldset);

        return $inputfields;
    }
}
