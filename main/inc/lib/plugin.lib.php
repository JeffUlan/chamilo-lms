<?php
/* See license terms in /license.txt */

use ChamiloSession as Session;

/**
 * Class AppPlugin
 */
class AppPlugin
{
    public $plugin_regions = array(
        'main_top',
        'main_bottom',
        'login_top',
        'login_bottom',
        'menu_top',
        'menu_bottom',
        'content_top',
        'content_bottom',
        'header_main',
        'header_center',
        'header_left',
        'header_right',
        'pre_footer',
        'footer_left',
        'footer_center',
        'footer_right',
        'menu_administrator',
        'course_tool_plugin'
    );

    public $installedPluginListName = array();
    public $installedPluginListObject = array();

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Read plugin from path
     * @return array
     */
    public function read_plugins_from_path()
    {
        /* We scan the plugin directory. Each folder is a potential plugin. */
        $pluginPath = api_get_path(SYS_PLUGIN_PATH);
        $plugins = array();
        $handle = @opendir($pluginPath);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..' && is_dir(api_get_path(SYS_PLUGIN_PATH).$file)) {
                $plugins[] = $file;
            }
        }
        @closedir($handle);
        sort($plugins);

        return $plugins;
    }

    /**
     * @return array
     */
    public function get_installed_plugins_by_region()
    {
        $plugins = array();
        /* We retrieve all the active plugins. */
        $result = api_get_settings('Plugins');
        if (!empty($result)) {
            foreach ($result as $row) {
                $plugins[$row['variable']][] = $row['selected_value'];
            }
        }

        return $plugins;
    }

    /**
     * @return array
     */
    public function getInstalledPluginListName()
    {
        if (empty($this->installedPluginListName)) {
            $this->installedPluginListName = $this->get_installed_plugins();
        }

        return $this->installedPluginListName;
    }

    /**
     * @return array List of Plugin
     */
    public function getInstalledPluginListObject()
    {
        if (empty($this->installedPluginListObject)) {
            $this->setInstalledPluginListObject();
        }

        return $this->installedPluginListObject;
    }

    /**
     * @return array
     */
    public function setInstalledPluginListObject()
    {
        $pluginListName = $this->getInstalledPluginListName();
        $pluginList = array();
        if (!empty($pluginListName)) {
            foreach ($pluginListName as $pluginName) {
                $pluginInfo = $this->getPluginInfo($pluginName);
                if (isset($pluginInfo['plugin_class'])) {
                    $pluginList[] = $pluginInfo['plugin_class']::create();
                }
            }
        }
        $this->installedPluginListObject = $pluginList;
    }

    /**
     * @return array
     */
    public function get_installed_plugins()
    {
        $installedPlugins = array();
        $plugins = api_get_settings_params(
            array(
                "variable = ? AND selected_value = ? AND category = ? " => array('status', 'installed', 'Plugins')
            )
        );

        if (!empty($plugins)) {
            foreach ($plugins as $row) {
                $installedPlugins[$row['subkey']] = true;
            }
            $installedPlugins = array_keys($installedPlugins);
        }

        return $installedPlugins;
    }

    /**
     * @param string $pluginName
     * @param int    $urlId
     */
    public function install($pluginName, $urlId = null)
    {
        if (empty($urlId)) {
            $urlId = api_get_current_access_url_id();
        } else {
            $urlId = intval($urlId);
        }

        api_add_setting(
            'installed',
            'status',
            $pluginName,
            'setting',
            'Plugins',
            $pluginName,
            '',
            '',
            '',
            $urlId,
            1
        );

        $pluginPath = api_get_path(SYS_PLUGIN_PATH).$pluginName.'/install.php';

        if (is_file($pluginPath) && is_readable($pluginPath)) {
            // Execute the install procedure.

            require $pluginPath;
        }
    }

    /**
     * @param string $pluginName
     * @param int    $urlId
     */
    public function uninstall($pluginName, $urlId = null)
    {
        if (empty($urlId)) {
            $urlId = api_get_current_access_url_id();
        } else {
            $urlId = intval($urlId);
        }

        // First call the custom uninstall to allow full access to global settings
        $pluginPath = api_get_path(SYS_PLUGIN_PATH).$pluginName.'/uninstall.php';
        if (is_file($pluginPath) && is_readable($pluginPath)) {
            // Execute the uninstall procedure.

            require $pluginPath;
        }
        // Second remove all remaining global settings
        api_delete_settings_params(
            array('category = ? AND access_url = ? AND subkey = ? ' => array('Plugins', $urlId, $pluginName))
        );
    }

    /**
     * @param string $pluginName
     *
     * @return array
     */
    public function get_areas_by_plugin($pluginName)
    {
        $result = api_get_settings('Plugins');
        $areas = array();
        foreach ($result as $row) {
            if ($pluginName == $row['selected_value']) {
                $areas[] = $row['variable'];
            }
        }

        return $areas;
    }

    /**
     * @param string $location
     *
     * @return bool
     */
    public function is_valid_plugin_location($location)
    {
        return in_array($location, $this->plugin_list);
    }

    /**
     * @param string $pluginName
     *
     * @return bool
     */
    public function is_valid_plugin($pluginName)
    {
        if (is_dir(api_get_path(SYS_PLUGIN_PATH).$pluginName)) {
            if (is_file(api_get_path(SYS_PLUGIN_PATH).$pluginName.'/index.php')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function get_plugin_regions()
    {
        sort($this->plugin_regions);

        return $this->plugin_regions;
    }

    /**
     * @param string $region
     * @param Template $template
     * @param bool   $forced
     *
     * @return null|string
     */
    public function load_region($region, $template, $forced = false)
    {
        if ($region == 'course_tool_plugin') {
            return '';
        }

        ob_start();
        $this->get_all_plugin_contents_by_region($region, $template, $forced);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Loads the translation files inside a plugin if exists.
     * It loads by default english see the hello world plugin
     *
     * @param string $plugin_name
     *
     * @todo add caching
     */
    public function load_plugin_lang_variables($plugin_name)
    {
        global $language_interface;
        $root = api_get_path(SYS_PLUGIN_PATH);
        $strings = null;

        // 1. Loading english if exists
        $english_path = $root.$plugin_name."/lang/english.php";

        if (is_readable($english_path)) {
            include $english_path;

            foreach ($strings as $key => $string) {
                $GLOBALS[$key] = $string;
            }
        }

        // 2. Loading the system language
        if ($language_interface != 'english') {
            $path = $root.$plugin_name."/lang/$language_interface.php";

            if (is_readable($path)) {
                include $path;
                if (!empty($strings)) {
                    foreach ($strings as $key => $string) {
                        $GLOBALS[$key] = $string;
                    }
                }
            } else {
                $interfaceLanguageId = api_get_language_id($language_interface);
                $interfaceLanguageInfo = api_get_language_info($interfaceLanguageId);
                $languageParentId = intval($interfaceLanguageInfo['parent_id']);

                if ($languageParentId > 0) {
                    $languageParentInfo = api_get_language_info($languageParentId);
                    $languageParentFolder = $languageParentInfo['dokeos_folder'];

                    $parentPath = "{$root}{$plugin_name}/lang/{$languageParentFolder}.php";
                    if (is_readable($parentPath)) {
                        include $parentPath;
                        if (!empty($strings)) {
                            foreach ($strings as $key => $string) {
                                $this->strings[$key] = $string;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $region
     * @param Template $template
     * @param bool $forced
     *
     * @return bool
     *
     * @todo improve this function
     */
    public function get_all_plugin_contents_by_region($region, $template, $forced = false)
    {
        global $_plugins;
        if (isset($_plugins[$region]) && is_array($_plugins[$region])) {
            // Load the plugin information
            foreach ($_plugins[$region] as $plugin_name) {
                // The plugin_info variable is available inside the plugin index
                $plugin_info = $this->getPluginInfo($plugin_name, $forced);

                // We also know where the plugin is
                $plugin_info['current_region'] = $region;

                // Loading the plugin/XXX/index.php file
                $plugin_file = api_get_path(SYS_PLUGIN_PATH)."$plugin_name/index.php";

                if (file_exists($plugin_file)) {
                    //Loading the lang variables of the plugin if exists
                    self::load_plugin_lang_variables($plugin_name);

                    // Printing the plugin index.php file
                    require $plugin_file;

                    // If the variable $_template is set we assign those values to be accessible in Twig
                    if (isset($_template)) {
                        $_template['plugin_info'] = $plugin_info;
                    } else {
                        $_template = array();
                        $_template['plugin_info'] = $plugin_info;
                    }

                    // Setting the plugin info available in the template if exists.
                    $template->assign($plugin_name, $_template);

                    // Loading the Twig template plugin files if exists
                    $template_list = array();
                    if (isset($plugin_info) && isset($plugin_info['templates'])) {
                        $template_list = $plugin_info['templates'];
                    }

                    if (!empty($template_list)) {
                        foreach ($template_list as $plugin_tpl) {
                            if (!empty($plugin_tpl)) {
                                $template_plugin_file = "$plugin_name/$plugin_tpl"; // for twig
                                $template->display($template_plugin_file, false);
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Loads plugin info
     *
     * @staticvar array $plugin_data
     * @param string    $plugin_name
     * @param bool      $forced load from DB or from the static array
     *
     * @return array
     * @todo filter setting_form
     */
    public function getPluginInfo($plugin_name, $forced = false)
    {
        $pluginData = Session::read('plugin_data');

        if (isset($pluginData[$plugin_name]) && $forced == false) {
            return $pluginData[$plugin_name];
        } else {
            $plugin_file = api_get_path(SYS_PLUGIN_PATH)."$plugin_name/plugin.php";

            $plugin_info = array();
            if (file_exists($plugin_file)) {
                require $plugin_file;
            }

            // @todo check if settings are already added
            // Extra options
            $plugin_settings = api_get_settings_params(
                array(
                    "subkey = ? AND category = ? AND type = ? AND access_url = ?" => array(
                        $plugin_name,
                        'Plugins',
                        'setting',
                        api_get_current_access_url_id()
                    )
                )
            );

            $settings_filtered = array();
            foreach ($plugin_settings as $item) {
                if (!empty($item['selected_value'])) {
                    if (@unserialize($item['selected_value']) !== false) {
                        $item['selected_value'] = unserialize($item['selected_value']);
                    }
                }
                $settings_filtered[$item['variable']] = $item['selected_value'];
            }
            $plugin_info['settings'] = $settings_filtered;
            $pluginData[$plugin_name] = $plugin_info;
            Session::write('plugin_data', $pluginData);
            return $plugin_info;
        }
    }

    /**
     * Get the template list
     * @param  string $pluginName
     *
     * @return bool
     */
    public function get_templates_list($pluginName)
    {
        $plugin_info = $this->getPluginInfo($pluginName);
        if (isset($plugin_info) && isset($plugin_info['templates'])) {
            return $plugin_info['templates'];
        } else {
            return false;
        }
    }

    /**
     * Remove all regions of an specific plugin
     */
    public function remove_all_regions($plugin)
    {
        $access_url_id = api_get_current_access_url_id();
        if (!empty($plugin)) {
            api_delete_settings_params(
                array(
                    'category = ? AND type = ? AND access_url = ? AND subkey = ? ' => array(
                        'Plugins',
                        'region',
                        $access_url_id,
                        $plugin,
                    ),
                )
            );
        }
    }

    /**
     * Add a plugin to a region
     * @param string $plugin
     * @param string $region
     */
    public function add_to_region($plugin, $region)
    {
        api_add_setting(
            $plugin,
            $region,
            $plugin,
            'region',
            'Plugins',
            $plugin,
            '',
            '',
            '',
            api_get_current_access_url_id(),
            1
        );
    }

    /**
     * @param int $courseId
     */
    public function install_course_plugins($courseId)
    {
        $pluginList = $this->getInstalledPluginListObject();

        if (!empty($pluginList)) {
            /** @var Plugin $obj */
            foreach ($pluginList as $obj) {
                $pluginName = $obj->get_name();
                $plugin_path = api_get_path(SYS_PLUGIN_PATH).$pluginName.'/plugin.php';

                if (file_exists($plugin_path)) {
                    require $plugin_path;
                    if (isset($plugin_info) && isset($plugin_info['plugin_class']) && $obj->isCoursePlugin) {
                        $obj->course_install($courseId);
                    }
                }
            }
        }
    }

    /**
     * Add the course settings to the course settings form
     * @param FormValidator $form
     */
    public function add_course_settings_form($form)
    {
        $pluginList = $this->getInstalledPluginListObject();
        /** @var Plugin $obj */
        foreach ($pluginList as $obj) {
            $plugin_name = $obj->get_name();
            $pluginTitle = $obj->get_title();
            if (!empty($obj->course_settings)) {
                if (is_file(api_get_path(SYS_CODE_PATH).'img/icons/'.ICON_SIZE_SMALL.'/'.$plugin_name.'.png')) {
                    $icon = Display::return_icon(
                        $plugin_name.'.png',
                        Security::remove_XSS($pluginTitle),
                        '',
                        ICON_SIZE_SMALL
                    );
                } else {
                    $icon = Display::return_icon(
                        'plugins.png',
                        Security::remove_XSS($pluginTitle),
                        '',
                        ICON_SIZE_SMALL
                    );
                }

                $form->addHtml('<div class="panel panel-default">');
                $form->addHtml('
                    <div class="panel-heading" role="tab" id="heading-' . $plugin_name.'-settings">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-' . $plugin_name.'-settings" aria-expanded="false" aria-controls="collapse-'.$plugin_name.'-settings">
                ');
                $form->addHtml($icon.' '.$pluginTitle);
                $form->addHtml('
                            </a>
                        </h4>
                    </div>
                ');
                $form->addHtml('
                    <div id="collapse-' . $plugin_name.'-settings" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-'.$plugin_name.'-settings">
                        <div class="panel-body">
                ');

                $groups = array();
                foreach ($obj->course_settings as $setting) {
                    if ($obj->validateCourseSetting($setting['name']) === false) {
                        continue;
                    }
                    if ($setting['type'] != 'checkbox') {
                        $form->addElement($setting['type'], $setting['name'], $obj->get_lang($setting['name']));
                    } else {
                        $element = & $form->createElement(
                            $setting['type'],
                            $setting['name'],
                            '',
                            $obj->get_lang($setting['name'])
                        );
                        if (isset($setting['init_value']) && $setting['init_value'] == 1) {
                            $element->setChecked(true);
                        }
                        $form->addElement($element);

                        if (isset($setting['group'])) {
                            $groups[$setting['group']][] = $element;
                        }
                    }
                }
                foreach ($groups as $k => $v) {
                    $form->addGroup($groups[$k], $k, array($obj->get_lang($k)));
                }
                $form->addButtonSave(get_lang('SaveSettings'));
                $form->addHtml('
                        </div>
                    </div>
                ');
                $form->addHtml('</div>');
            }
        }
    }

    /**
     * Get all course settings from all installed plugins.
     * @return array
     */
    public function getAllPluginCourseSettings()
    {
        $pluginList = $this->getInstalledPluginListObject();
        /** @var Plugin $obj */
        $courseSettings = array();
        if (!empty($pluginList)) {
            foreach ($pluginList as $obj) {
                $pluginCourseSetting = $obj->getCourseSettings();
                $courseSettings = array_merge($courseSettings, $pluginCourseSetting);
            }
        }

        return $courseSettings;
    }

    /**
     * When saving the plugin values in the course settings, check whether
     * a callback method should be called and send it the updated settings
     * @param array $values The new settings the user just saved
     * @return void
     */
    public function saveCourseSettingsHook($values)
    {
        $pluginList = $this->getInstalledPluginListObject();

        /** @var Plugin $obj */
        foreach ($pluginList as $obj) {
            $settings = $obj->getCourseSettings();

            $subValues = array();
            if (!empty($settings)) {
                foreach ($settings as $v) {
                    if (isset($values[$v])) {
                        $subValues[$v] = $values[$v];
                    }
                }
            }

            if (!empty($subValues)) {
                $obj->course_settings_updated($subValues);
            }
        }
    }

    /**
     * Get first SMS plugin name
     * @return string|boolean
     */
    public function getSMSPluginName()
    {
        $installedPluginsList = $this->getInstalledPluginListObject();
        foreach ($installedPluginsList as $installedPlugin) {
            if ($installedPlugin->isMailPlugin) {
                return get_class($installedPlugin);
            }
        }

        return false;
    }

    /**
     * @return SmsPluginLibraryInterface
     */
    public function getSMSPluginLibrary()
    {
        $className = $this->getSMSPluginName();
        $className = str_replace('Plugin', '', $className);

        if (class_exists($className)) {
            return new $className;
        }

        return false;
    }
}
