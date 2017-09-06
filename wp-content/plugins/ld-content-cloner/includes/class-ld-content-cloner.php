<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Ld_Content_Cloner
 * @subpackage Ld_Content_Cloner/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ld_Content_Cloner
 * @subpackage Ld_Content_Cloner/includes
 * @author     WisdmLabs <info@wisdmlabs.com>
 */
namespace LdContentCloner;

class LdContentCloner
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Ld_Content_Cloner_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->pluginName = 'ld-content-cloner';
        $this->version = '1.0.0';

        $this->loadDependencies();
        $this->setLocale();
        $this->defineAdminHooks();
        $this->definePublicHooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Ld_Content_Cloner_Loader. Orchestrates the hooks of the plugin.
     * - Ld_Content_Cloner_i18n. Defines internationalization functionality.
     * - Ld_Content_Cloner_Admin. Defines all hooks for the admin area.
     * - Ld_Content_Cloner_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function loadDependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ld-content-cloner-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ld-content-cloner-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ld-content-cloner-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ld-content-cloner-public.php';

        /**
         * The class responsible for defining LD course cloning functionality of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ldcc-course.php';


        /**
         * The class responsible for defining LD group cloning functionality of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ldcc-group.php';

        /**
         * The class responsible for defining LD course bulk renaming functionality of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ldcc-bulk-rename.php';

        $this->loader = new \LdContentClonerLoader\LdContentClonerLoader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Ld_Content_Cloner_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function setLocale()
    {
        $plugin_i18n = new \LdContentCloneri18n\LdContentCloneri18n();
        $plugin_i18n->setDomain($this->getPluginName());

        $this->loader->addAction('plugins_loaded', $plugin_i18n, 'loadPluginTextdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function defineAdminHooks()
    {
        $plugin_admin = new \LdContentClonerAdmin\LdContentClonerAdmin($this->getPluginName(), $this->getVersion());
        $ld_course = new \LdccCourse\LdccCourse();


        $ld_group = new \LdccGroup\LdccGroup();

        $ld_bulk_rename = new \LdBulkRename\LdBulkRename();

        $this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueueStyles');
        $this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueueScripts');

        if (@version_compare(LEARNDASH_VERSION, '2.2.1', '<')) {
            $this->loader->addFilter('page_row_actions', $ld_course, 'addCourseRowActions', 10, 2);
        } else {
            $this->loader->addFilter('post_row_actions', $ld_course, 'addCourseRowActions', 10, 2);
        }

        $this->loader->addFilter('post_row_actions', $ld_group, 'addGroupRowActions', 10, 2);
        $this->loader->addAction('wp_ajax_duplicate_group', $ld_group, 'createDuplicateGroup');

        $this->loader->addAction('wp_ajax_duplicate_course', $ld_course, 'createDuplicateCourse');
        $this->loader->addAction('wp_ajax_duplicate_lesson', $ld_course, 'createDuplicateLesson');
        
        $this->loader->addAction('wp_ajax_duplicate_quiz', $ld_course, 'duplicateQuiz');
        
        $this->loader->addAction('admin_footer', $ld_course, 'addModalStructure');
        $this->loader->addAction('admin_footer', $ld_group, 'addModalStructure');
        
        // for bulk rename functionality
        $this->loader->addAction('admin_menu', $ld_bulk_rename, 'ldbrRenameSubmenuPage', 100);
        $this->loader->addFilter('learndash_submenu', $ld_bulk_rename, 'ldbrAddSubmenu');

        $this->loader->addAction('wp_ajax_ldbr_bulk_rename', $ld_bulk_rename, 'ldbrBulkRenameCallback');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function definePublicHooks()
    {
        $plugin_public = new \LdContentClonerPublic\LdContentClonerPublic($this->getPluginName(), $this->getVersion());

        $this->loader->addAction('wp_enqueue_scripts', $plugin_public, 'enqueueStyles');
        $this->loader->addAction('wp_enqueue_scripts', $plugin_public, 'enqueueScripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function getPluginName()
    {
        return $this->pluginName;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Ld_Content_Cloner_Loader    Orchestrates the hooks of the plugin.
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function getVersion()
    {
        return $this->version;
    }
}
