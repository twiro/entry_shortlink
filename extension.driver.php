<?php

if(!defined("__IN_SYMPHONY__")) die("<h2>Error</h2><p>You cannot directly access this file</p>");

require_once(EXTENSIONS . '/entry_shortlink/fields/field.entry_shortlink.php');

class extension_entry_shortlink extends Extension
{
    /**
     * name of the extension
     *
     * @since version 1.0.0
     */

    const EXT_NAME = 'Entry Shortlink';

    /**
     * install the extension
     *
     * @since version 1.0.0
     */

    public function install()
    {
        return FieldEntry_Shortlink::createFieldTable();
    }

    /**
     * uninstall the extension
     *
     * @since version 1.0.0
     */

    public function uninstall()
    {
        return FieldEntry_Shortlink::deleteFieldTable();
    }

    /**
     * get subscribed delegates
     *
     * @since version 1.0.0
     */

    public function getSubscribedDelegates()
    {
        return array(
            array(
                'page' => '/backend/',
                'delegate' => 'InitaliseAdminPageHead',
                'callback' => 'appendResources'
            )
        );
    }

    /**
     * append resources (js/css)
     *
     * @since version 1.0.0
     */

    public function appendResources(Array $context)
    {
        // store the callback array localy
        $c = Administration::instance()->getPageCallback();

        // publish page (new or edit)
        if(isset($c['context']['section_handle']) && in_array($c['context']['page'], array('new', 'edit'))){
            Administration::instance()->Page->addStylesheetToHead(
                URL . '/extensions/entry_shortlink/assets/publish.entry_shortlink.css',
                'screen',
                time(),
                false
            );
            Administration::instance()->Page->addScriptToHead(
                URL . '/extensions/entry_shortlink/assets/publish.entry_shortlink.js',
                time() + 1,
                false
            );
            return;
        }
    }
}
