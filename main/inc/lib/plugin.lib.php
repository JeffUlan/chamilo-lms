<?php
/* See license terms in /license.txt */

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
        'footer_left',
        'footer_center',
        'footer_right',
        'course_tool_plugin'
    );

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
     * @param int $urlId
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
            null,
            null,
            null,
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
     * @param int $urlId
     */
    public function uninstall($pluginName, $urlId = null)
    {
        if (empty($urlId)) {
            $urlId = api_get_current_access_url_id();
        } else {
            $urlId = intval($urlId);
        }
        api_delete_settings_params(
            array('category = ? AND access_url = ? AND subkey = ? ' => array('Plugins', $urlId, $pluginName))
        );
        $pluginPath = api_get_path(SYS_PLUGIN_PATH).$pluginName.'/uninstall.php';
        if (is_file($pluginPath) && is_readable($pluginPath)) {
            // Execute the uninstall procedure.

            require $pluginPath;
        }
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
     * @param string $template
     * @param bool $forced
     *
     * @return null|string
     */
    public function load_region($region, $template, $forced = false)
    {
        if ($region == 'course_tool_plugin') {
            return null;
        }
        ob_start();
        $this->get_all_plugin_contents_by_region($region, $template, $forced);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Loads the translation files inside a plugin if exists. It loads by default english see the hello world plugin
     *
     * @todo add caching
     * @param string $plugin_name
     */
    public function load_plugin_lang_variables($plugin_name)
    {
        global $language_interface;
        $root = api_get_path(SYS_PLUGIN_PATH);

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
            }
        }
    }

    /**
     *
     * @param string $block
     * @param Template $template
     * @todo improve this function
     */
    public function get_all_plugin_contents_by_region($region, $template, $forced = false)
    {
        global $_plugins;
        if (isset($_plugins[$region]) && is_array($_plugins[$region])) {
        //if (1) {
            //Load the plugin information
            foreach ($_plugins[$region] as $plugin_name) {

                //The plugin_info variable is available inside the plugin index
                $plugin_info = $this->get_plugin_info($plugin_name, $forced);

                //We also know where the plugin is
                $plugin_info['current_region'] = $region;

                // Loading the plugin/XXX/index.php file
                $plugin_file = api_get_path(SYS_PLUGIN_PATH)."$plugin_name/index.php";

                if (file_exists($plugin_file)) {

                    //Loading the lang variables of the plugin if exists
                    self::load_plugin_lang_variables($plugin_name);

                    //Printing the plugin index.php file
                    require $plugin_file;

                    //If the variable $_template is set we assign those values to be accesible in Twig
                    if (isset($_template)) {
                        $_template['plugin_info'] = $plugin_info;
                    } else {
                        $_template = array();
                        $_template['plugin_info'] = $plugin_info;
                    }

                    //Setting the plugin info available in the template if exists

                    $template->assign($plugin_name, $_template);

                    //Loading the Twig template plugin files if exists
                    $template_list = array();
                    if (isset($plugin_info) && isset($plugin_info['templates'])) {
                        $template_list = $plugin_info['templates'];
                    }

                    if (!empty($template_list)) {
                        foreach ($template_list as $plugin_tpl) {
                            if (!empty($plugin_tpl)) {
                                //$template_plugin_file = api_get_path(SYS_PLUGIN_PATH)."$plugin_name/$plugin_tpl"; //for smarty
                                $template_plugin_file = "$plugin_name/$plugin_tpl"; // for twig
                                $template->display($template_plugin_file);
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
     * @staticvar array $plugin_data
     * @param string plugin name
     * @param bool load from DB or from the static array
     * @todo filter setting_form
     * @return array
     */
    public function get_plugin_info($plugin_name, $forced = false)
    {
        static $plugin_data = array();

        if (isset($plugin_data[$plugin_name]) && $forced == false) {
            return $plugin_data[$plugin_name];
        } else {
            $plugin_file = api_get_path(SYS_PLUGIN_PATH)."$plugin_name/plugin.php";

            $plugin_info = array();
            if (file_exists($plugin_file)) {
                require $plugin_file;
            }

            //extra options
            $plugin_settings = api_get_settings_params(array("subkey = ? AND category = ? AND type = ? " =>
                                            array($plugin_name, 'Plugins','setting')));
            $settings_filtered = array();
            foreach ($plugin_settings as $item) {
                $settings_filtered[$item['variable']] = $item['selected_value'];
            }
            $plugin_info['settings'] = $settings_filtered;
            $plugin_data[$plugin_name] = $plugin_info;
            return $plugin_info;
        }
    }

    /**
     * Get the template list
     * @param  string $pluginName
     * @return bool
     */
    public function get_templates_list($pluginName)
    {
        $plugin_info = $this->get_plugin_info($pluginName);
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
                    'category = ? AND type = ? AND access_url = ? AND subkey = ? ' => array('Plugins', 'region', $access_url_id, $plugin)
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
        $access_url_id = api_get_current_access_url_id();
        api_add_setting($plugin, $region, $plugin, 'region', 'Plugins', $plugin, null, null, null, $access_url_id, 1);
    }

    /**
     * @param int $course_id
     */
    public function install_course_plugins($course_id)
    {
        $plugin_list = $this->get_installed_plugins();

        if (!empty($plugin_list)) {
            foreach ($plugin_list as $plugin_name) {
                $plugin_path = api_get_path(SYS_PLUGIN_PATH).$plugin_name.'/plugin.php';
                if (file_exists($plugin_path)) {
                    require_once $plugin_path;
                    if (isset($plugin_info) && isset($plugin_info['plugin_class'])) {
                        $plugin_info['plugin_class']::create()->course_install($course_id);
                    }
                }
            }
        }
    }

    /**
     * @param FormValidator $form
     */
    public function add_course_settings_form($form)
    {
        $plugin_list = $this->get_installed_plugins();
        foreach ($plugin_list as $plugin_name) {
            $plugin_info = $this->get_plugin_info($plugin_name);
            if (isset($plugin_info['plugin_class'])) {
                $obj = $plugin_info['plugin_class']::create();

                if (!empty($obj->course_settings)) {
                    $icon = Display::return_icon($plugin_name.'.png', Security::remove_XSS($plugin_info['title']),'', ICON_SIZE_SMALL);
                    //$icon = null;
                    $form->addElement('html', '<div><h3>'.$icon.' '.Security::remove_XSS($plugin_info['title']).'</h3><div>');

                    $groups = array();
                    foreach ($obj->course_settings as $setting) {
                        if ($setting['type'] != 'checkbox') {
                            $form->addElement($setting['type'], $setting['name'], $obj->get_lang($setting['name']));
                        } else {
                            //if (isset($groups[$setting['group']])) {
                                $element = & $form->createElement($setting['type'], $setting['name'], '', $obj->get_lang($setting['name']));
                                if ($setting['init_value'] == 1) {
                                    $element->setChecked(true);
                                }
                                $groups[$setting['group']][] = $element;
                            //}
                        }
                    }
                    foreach ($groups as $k => $v) {
                        $form->addGroup($groups[$k], $k, array($obj->get_lang($k)));
                    }
                    $form->addElement('style_submit_button', null, get_lang('SaveSettings'), 'class="save"');
                    $form->addElement('html', '</div></div>');
                }
            }
        }
    }

    /**
     * @param array $values
     */
    public function set_course_settings_defaults(& $values)
    {
        $plugin_list = $this->get_installed_plugins();
        foreach ($plugin_list as $plugin_name) {
            $plugin_info = $this->get_plugin_info($plugin_name);
            if (isset($plugin_info['plugin_class'])) {
                $obj = $plugin_info['plugin_class']::create();
                if (!empty($obj->course_settings)) {
                    foreach ($obj->course_settings as $setting) {
                        if (isset($setting['name'])) {
                             $result = api_get_course_setting($setting['name']);
                             if ($result != '-1') {
                                $values[$setting['name']] = $result;
                             }
                        }
                    }
                }
            }
        }
    }

    /**
     * When saving the plugin values in the course settings, check whether
     * a callback method should be called and send it the updated settings
     * @param array The new settings the user just saved
     * @return void
     */
    public function save_course_settings($values)
    {
        $plugin_list = $this->get_installed_plugins();
        foreach ($plugin_list as $plugin_name) {
            $settings = $this->get_plugin_course_settings($plugin_name);
            $subvalues = array();
            $i = 0;
            foreach ($settings as $v) {
                if (isset($values[$v])) {
                    $subvalues[$v] = $values[$v];
                    $i++;
                }
            }
            if ($i>0) {
                $plugin_info = $this->get_plugin_info($plugin_name);

                if (isset($plugin_info['plugin_class'])) {
                    $obj = $plugin_info['plugin_class']::create();
                    $obj->course_settings_updated($subvalues);
                }
            }
        }
    }

    /**
     * Gets a nice array of keys for just the plugin's course settings
     * @param string The plugin ID
     * @return array Nice array of keys for course settings
     */
    public function get_plugin_course_settings($plugin_name)
    {
        $settings = array();
        if (empty($plugin_name)) { return $settings; }
        $plugin_info = $this->get_plugin_info($plugin_name);

        if (isset($plugin_info['plugin_class'])) {
            $obj = $plugin_info['plugin_class']::create();
            if (is_array($obj->course_settings)) {
                foreach ($obj->course_settings as $item) {
                    if (isset($item['group'])) {
                        if (!in_array($item['group'],$settings)) {
                            $settings[] = $item['group'];
                        }
                    } else {
                        $settings[] = $item['name'];
                    }
                }
            }
            unset($obj); unset($plugin_info);
        }
        return $settings;
    }
}
