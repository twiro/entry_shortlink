<?php

if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

require_once(TOOLKIT . '/class.field.php');

class FieldEntry_Shortlink extends Field
{
    /*------------------------------------------------------------------------*/
    /* DEFINITION & SETTINGS
    /*------------------------------------------------------------------------*/

    /**
     * FIELD TABLE NAME
     *
     * @since version 1.0.0
     */

    const FIELD_TBL_NAME = 'tbl_fields_entry_shortlink';

    /**
     * DEFAULT SHORTLINK URL
     *
     * @since version 1.0.0
     */

    const DEFAULT_SHORTLINK_URL = '/id/';

    /**
     * DEFAULT SHORTLINK LABEL
     *
     * @since version 1.0.0
     */

    const DEFAULT_SHORTLINK_LABEL = '→ Frontend';

    /**
     * CONSTRUCT
     *
     * Constructor for the Field object
     *
     * @since version 1.0.0
     */

    public function __construct()
    {
        // call the parent constructor
        parent::__construct();

        // set the name of the field
        $this->_name = __('Shortlink');

        // default settings
        $this->_required = false;
        $this->_showcolumn = true;
        $this->set('location', 'sidebar');
        $this->set('required', 'no');
    }

    public function isSortable()
    {
        return false;
    }

    public function canFilter()
    {
        return false;
    }

    public function canImport()
    {
        return false;
    }

    public function canPrePopulate()
    {
        return false;
    }

    public function allowDatasourceOutputGrouping()
    {
        return false;
    }

    public function requiresSQLGrouping()
    {
        return false;
    }

    public function allowDatasourceParamOutput()
    {
        return false;
    }

    /*------------------------------------------------------------------------*/
    /* SECTIONS : PROCESS & SAVE FIELD DATA
    /*------------------------------------------------------------------------*/

    /**
     * SET FROM POST
     *
     * Fill the input data array with default values for known keys (provided
     * these settings are not already set). The input array is then used to set
     * the values of the corresponding settings for this field.
     *
     * @since version 1.0.0
     */

    public function setFromPOST(Array $settings = array())
    {
        // call the default behavior
        parent::setFromPOST($settings);

        // declare a new setting array
        $new_settings = array();

        // insert default url if 'shortlink_url' is empty
        $new_settings['shortlink_url'] = $settings['shortlink_url'] !== '' ? $settings['shortlink_url'] : self::DEFAULT_SHORTLINK_URL;

        // insert default label if 'shortlink_label' is empty
        $new_settings['shortlink_label'] = $settings['shortlink_label'] !== '' ?  $settings['shortlink_label'] : self::DEFAULT_SHORTLINK_LABEL;

        // save it into the array
        $this->setArray($new_settings);
    }

    /**
     * COMMIT
     *
     * Save field settings into the field's table
     *
     * @since version 1.0.0
     */

    public function commit()
    {
        // if the default implementation works...
        if(!parent::commit()) return FALSE;

        $id = $this->get('id');

        // exit if there is no id
        if($id == false) return FALSE;

        // declare an array contains the field's settings
        $settings = array();

        // the field id
        $settings['field_id'] = $id;

        // shortlink url
        $settings['shortlink_url'] = $this->get('shortlink_url');

        // shortlink label
        $settings['shortlink_label'] = $this->get('shortlink_label');

        // display url
        $settings['display_url'] = $this->get('display_url') == 'yes' ? 'yes' : 'no';

        // officially save it
        return FieldManager::saveSettings($id, $settings);
    }

    /*------------------------------------------------------------------------*/
    /* SECTIONS : UI
    /*------------------------------------------------------------------------*/

    /**
     * DISPLAY SETTINGS PANEL
     *
     * builds the UI for the field's settings when creating/editing a section
     *
     * @since version 1.0.0
     */

    public function displaySettingsPanel(XMLElement &$wrapper, $errors=NULL)
    {
        // first line, label and such
        parent::displaySettingsPanel($wrapper, $errors);

        // new line
        $opts_wrap = new XMLElement('div', NULL, array('class' => 'two columns'));

        // shortlink url
        $url_wrap = new XMLElement('div', NULL, array('class' => 'column'));
        $url_title = new XMLElement('label', __('Shortlink URL <i>The static part of the URL (to which the entry ID will be appended)</i>'));
        $url_title->appendChild(Widget::Input(
            'fields['.$this->get('sortorder').'][shortlink_url]',
            $this->get('shortlink_url'),
            'text',
            array('placeholder' => self::DEFAULT_SHORTLINK_URL)
        ));
        $url_wrap->appendChild($url_title);
        $opts_wrap->appendChild($url_wrap);

        // shortlink label
        $label_wrap = new XMLElement('div', NULL, array('class' => 'column'));
        $label_title = new XMLElement('label', __('Shortlink Label <i>The text label of the generated button/link</i>'));
        $label_title->appendChild(Widget::Input(
            'fields['.$this->get('sortorder').'][shortlink_label]',
            $this->get('shortlink_label'),
            'text',
            array('placeholder' => self::DEFAULT_SHORTLINK_LABEL)
        ));
        $label_wrap->appendChild($label_title);
        $opts_wrap->appendChild($label_wrap);

        // new line, check boxes
        $chk_wrap = new XMLElement('div', NULL, array('class' => 'two columns'));
        $this->appendShowColumnCheckbox($chk_wrap);
        $this->appendDisplayUrlCheckbox($chk_wrap);

        $wrapper->appendChild($opts_wrap);
        $wrapper->appendChild($chk_wrap);
    }

    /**
     * APPEND DISPLAY URL CHECKBOX
     *
     * private function to append a checkbox for the 'display url' setting
     *
     * @since version 1.0.0
     */

    private function appendDisplayUrlCheckbox(&$wrapper)
    {
        $label = new XMLElement('label', NULL, array('class' => 'column'));
        $chk = new XMLElement('input', NULL, array('name' => 'fields['.$this->get('sortorder').'][display_url]', 'type' => 'checkbox', 'value' => 'yes'));

        $label->appendChild($chk);
        $label->setValue(__('Display URL in entries table (Instead of Shortlink Label)'), false);

        if ($this->get('display_url') === 'yes') {
            $chk->setAttribute('checked','checked');
        }

        $wrapper->appendChild($label);
    }

    /*------------------------------------------------------------------------*/
    /* PUBLISH AREA : UI
    /*------------------------------------------------------------------------*/

    /**
     * PREPARE TABLE VALUE
     *
     * builds the ui for the table view
     *
     * @since version 1.0.0
     */

    public function prepareTableValue($data, XMLElement $link = null, $entry_id = null)
    {
        // fetch data
        $display_url = $this->get('display_url');
        $shortlink_url = $this->get('shortlink_url');
        $shortlink_label = $this->get('shortlink_label');

        // build complete url and generate the label for the shortlink
        $url = $this->getShortlinkUrl($shortlink_url, $entry_id);
        $label = $this->getShortlinkLabel($shortlink_label, $url, $display_url);

        // if cell doesn't yet serve as a link, wrap the html with a link
        if (!$link) {
            $link = new XMLElement('a');
            $link->setAttribute('href', $url);
        }

        // set the label
        $link->setValue($label);

        return $link->generate();
    }

    /**
     * DISPLAY PUBLISH PANEL
     *
     * builds the ui for the publish page
     *
     * @since version 1.0.0
     */

    public function displayPublishPanel(XMLElement &$wrapper, $data = NULL, $flagWithError = NULL, $fieldnamePrefix = NULL, $fieldnamePostfix = NULL, $entry_id = NULL)
    {
        // don't add anything if a new entry is created
        if (!$entry_id ) return;

        // fetch data
        $shortlink_url = $this->get('shortlink_url');
        $shortlink_label = $this->get('shortlink_label');

        // build complete url and generate the label for the shortlink
        $url = $this->getShortlinkUrl($shortlink_url, $entry_id);
        $label = $this->getShortlinkLabel($shortlink_label, $url);

        // add attributes to element
        $wrapper->setAttribute('data-url', $url);
        $wrapper->setAttribute('data-label', $label);
    }

    /**
     * GET SHORTLINK URL
     *
     * @since version 1.0.0
     */

     private function getShortlinkUrl($shortlink_url, $entry_id)
     {
         if ($shortlink_url) {
             $url = $shortlink_url . $entry_id;
         } else {
             $url = self::DEFAULT_SHORTLINK_URL . $entry_id;
         }
         return $url;
     }

    /**
     * GET SHORTLINK LABEL
     *
     * @since version 1.0.0
     */

    private function getShortlinkLabel($shortlink_label, $url, $display_url = 'No')
    {
        if ($display_url == 'yes') {
            $label = $url;
        } else if ($shortlink_label) {
            $label = $shortlink_label;
        } else {
            $label = self::DEFAULT_SHORTLINK_LABEL;
        }
        return $label;
    }

    /*------------------------------------------------------------------------*/
    /* PROCESS & SAVE ENTRY DATA
    /*------------------------------------------------------------------------*/

    /**
     * CHECK POST FIELD DATA
     *
     * @since version 1.0.1
     */

    public function checkPostFieldData($data, &$message, $entry_id = null)
    {
        $message = NULL;
        return self::__OK__;
    }

    /**
     * PROCESS RAW FIELD DATA
     *
     * @since version 1.0.1
     */

    public function processRawFieldData($data, &$status, &$message = null, $simulate = false, $entry_id = null)
    {
        $status = self::__OK__;
        return $data;
    }

    /*------------------------------------------------------------------------*/
    /* DATA SOURCE
    /*------------------------------------------------------------------------*/

    /**
     * APPEND FORMATTED ELEMENT
     *
     * appends data into the XML tree of a Data Source
     *
     * @since version 1.0.0
     */

    public function appendFormattedElement(XMLElement &$wrapper, $data, $encode = false, $mode = NULL, $entry_id = NULL)
    {
        // nothing
    }

    /*------------------------------------------------------------------------*/
    /* DATABASE & TABLES
    /*------------------------------------------------------------------------*/

    /**
     * CREATE TABLE
     *
     * creates the table that stores the actual field data
     *
     * @since version 1.0.0
     */

    public function createTable()
    {
        // no table is needed for entries
        return true;
    }

    /**
     * CREATE FIELD TABLE
     *
     * creates the table that stores the field settings
     *
     * @since version 1.0.0
     */

    public static function createFieldTable()
    {
        $tbl = self::FIELD_TBL_NAME;

        return Symphony::Database()->query("
            CREATE TABLE IF NOT EXISTS `$tbl` (
                `id`                int(11) unsigned NOT NULL auto_increment,
                `field_id`          int(11) unsigned NOT NULL,
                `shortlink_url`     varchar(255) NULL,
                `shortlink_label`   varchar(255) NULL,
                `display_url`       ENUM('yes', 'no') DEFAULT 'no',
                PRIMARY KEY (`id`),
                UNIQUE KEY `field_id` (`field_id`)
            )  ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
    }

    /**
     * DELETE FIELD TABLE
     *
     * @since version 1.0.0
     */

    public static function deleteFieldTable()
    {
        $tbl = self::FIELD_TBL_NAME;

        return Symphony::Database()->query("
            DROP TABLE IF EXISTS `$tbl`
        ");
    }

}
