<?php
/**
 * -----------------------------------------------------------------------------
 * VoodooPHP
 * -----------------------------------------------------------------------------
 * @author      Mardix (http://twitter.com/mardix)
 * @github      https://github.com/VoodooPHP/Voodoo
 * @package     VoodooPHP
 *
 * @copyright   (c) 2012 Mardix (http://github.com/mardix)
 * @license     MIT
 * -----------------------------------------------------------------------------
 *
 * @name        Ini
 * @since       Aug 8, 2011
 * @desc        Access any INI file in the Application/Config directory
 *
 */

namespace Voodoo\Core;

class Config
{
    /**
     * Hold topics list that has been loaded
     * @var Array
     */
    private static $Config = array();

    private $namespace = "";
   
    /**
     * Constructor
     * @param string $namespace - A unique name that will hold data for each 
     * set of config
     */
    public function __construct($namespace)
    {
        $this->namespace = $namespace;
        
        if (!$this->namespaceExists()) {
            self::$Config[$this->namespace] = array();
        }
    }
   
    /**
     * Check if a namespace exists
     * @return bool
     */
    public function namespaceExists(){
        return isset(self::$Config[$this->namespace]);
    }
    /**
     * To load an .ini file
     * 
     * @param type $file
     * @param type $keyname 
     */
    public function loadFile($file, $keyname = ""){
      $cnf = parse_ini_file($file, true);
      $this->set($cnf, $keyname);
    }  
    
    
    
   /**
    * Return the INI array
    * @return Array
    */
   public function toArray()
   {
      return self::$Config[$this->namespace];
   }

    /**
     * Access value of an array with dot notation
     * @param  type $dotNotation - the key. ie: Key.Field1
     * @param  type $emptyValue  - Use this value if empty
     * @return Mix
     *
     * ie
     *  self::get("QA.UpVoteQuestion"); Will return the value of [QA][UpVoteQuestion]
     */
    public function get($dotNotation="",$emptyValue = null)
    {
        return Helpers::getArrayDotNotationValue($this->toArray(), $dotNotation, $emptyValue);
    }

    /**
     * Set data in the loaded
     * @param array $data
     * @param type  $keyName
     */
    public function set(Array $data, $keyName="")
    {
        if($keyName){
            if (!isset(self::$Config[$this->namespace][$keyName])){
                self::$Config[$this->namespace][$keyName] = array();
            }
            self::$Config[$this->namespace][$keyName] = Helpers::arrayExtend(self::$Config[$this->namespace][$keyName], $data);
        } else {
            self::$Config[$this->namespace] = Helpers::arrayExtend(self::$Config[$this->namespace], $data);
        }

        return $this;
    }

    /**
     * Save the ini file.
     * ATTENTION: It will remove all comments added. At this time, it's limited to 2 depth
     * @param type $fileName - The filename without the extension
     * @return $this
     */
    public function save($fileName="")
    {
        $fileName = $fileName ?: $this->namespace;

        $data = self::arrayToINI($this->toArray());

        file_put_contents(Path::AppConfig()."/{$fileName}.ini", $data);

        return $this;
    }

    /**
     * Statically load any INI file. IE \Core\INI::Settings()->toArray()
     * @param  type $name
     * @param  type $args
     * @return INI
     * Set the 1st agrs to false to not show error if class doesnt exist
     *  Core\INI::Settings(false)->toArray()
     */
    public static function __callStatic($name, $args)
    {
        $ini = new self($name);
        $file = Path::AppConfig()."/{$name}.ini";
        
        if(file_exists($file)) {
            $ini->loadFile($file);
        } else if(!$ini->namespaceExists()) {
            throw new Exception("INI File '{$file}' doesn't exist");
        }
        return $ini;
    }

    /**
     * To convert an array into a properly formatted INI file
     * @param  array   $iniArray
     * @param  int     $indent
     * @return String. A string to be saved as INI file
     */
    public static function arrayToINI(Array $iniArray, $indent = 0)
    {
        foreach ($iniArray as $k => $v) {
            if (is_array($v)) {
                $ini .= str_repeat(" ", $indent * 5);
                $ini .= "[$k] \r\n";
                $ini .= self::arrayToINI($v, $indent + 1);
            } else {
                $ini .= str_repeat(" ", $indent * 5);
                $v = (is_string($v)) ? "\"$v\"" : $v;
                $ini .= "$k = $v \r\n";
            }
        }
        return $ini;
    }
}