<?php

/**
 *
 * @category    Anahita
 * @package     com_settings
 *
 * @author      Rastin Mehr <rastin@anahitapolis.com>
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link        http://www.GetAnahita.com
 */

 class ComSettingsSetting extends AnObject implements AnServiceInstantiatable
 {
     /**
     *  path to the settings file
     *  @var string
     */
     protected $_path = '';

     /**
     *  filename
     *  @var string
     */
     protected $_file = '';

     /**
     *
     */
     protected $_settings = null;

     /**
     * @param string defaul template name
     */
     protected $_default_template;

     /**
     * @param string defaul language name
     */
     protected $_default_language;

     /**
     * Class constructor.
     *
     * @param	integer	A client identifier.
     */
     public function __construct($config = array())
     {
         parent::__construct($config);

         $this->_path = $config->path;
         $this->_file = $config->file;

         $filepath = $this->path();

         if (!file_exists($filepath) || (filesize($filepath) < 10)) {
             throw new AnException('No configuration file found!', 500);
         }

         require_once $filepath;

         $this->_settings = new AnSiteConfig();
         $this->_default_template = 'shiraz';
         $this->_default_language = 'en-GB';
     }

     /**
      * Initializes the options for the object
      *
      * Called from {@link __construct()} as a first step of object instantiation.
      *
      * @param 	object 	An optional AnConfig object with configuration options.
      * @return  void
      */
     protected function _initialize(AnConfig $config)
     {
     	$config->append(array(
     		'path' => ANPATH_ROOT,
            'file' => 'configuration.php'
         ));

         parent::_initialize($config);
     }

     /**
      * Force creation of a singleton
      *
      * @param 	object 	An optional AnConfig object with configuration options
      * @param 	object	A AnServiceInterface object
      * @return AnDatabaseTableInterface
      */
     public static function getInstance(AnConfigInterface $config, AnServiceInterface $container)
     {
         if (!$container->has($config->service_identifier)) {
             $classname = $config->service_identifier->classname;
             $instance  = new $classname($config);
             $container->set($config->service_identifier, $instance);
         }

         return $container->get($config->service_identifier);
     }

     /**
     *  obtains a setting parameter value
     *
     *  @param string
     *  @param mixed default value
     *
     *  @return mixed
     */
     public function get($key = null, $default = null)
     {
         if (!empty($this->_settings->$key)) {
             return $this->_settings->$key;
         }

         return $default;
     }

     public function __get($key)
     {
         if ($key === 'template' && !is_dir(ANPATH_THEMES.DS.$this->get($key))) {
             return $this->_default_template;
         }

         if ($key === 'language' && !is_dir(ANPATH_LANGUAGE.DS.$this->get($key))) {
             return $this->_default_language;
         }

         return $this->get($key);
     }

     /**
     *  sets a setting parameter value
     *
     *  @param string
     *  @param mixed
     *
     *  @return mixed
     */
     public function set($key, $value = null)
     {
         if (isset($this->_settings->$key)) {
             $this->_settings->$key = $value;
         }

         return $this->_settings->$key;
     }

     public function __set($key, $value)
     {
         return $this->set($key, $value);
     }

     /**
     *  returns path to the configuration file
     *
     *  @return string
     */
     public function path()
     {
         return $this->_path.DS.$this->_file;
     }
 }
