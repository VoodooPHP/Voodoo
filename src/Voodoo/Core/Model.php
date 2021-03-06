<?php
/**
 * -----------------------------------------------------------------------------
 * VoodooPHP
 * -----------------------------------------------------------------------------
 * @author      Mardix (http://twitter.com/mardix)
 * @github      https://github.com/mardix/Voodoo
 * @package     VoodooPHP
 *
 * @copyright   (c) 2012 Mardix (http://github.com/mardix)
 * @license     MIT
 * -----------------------------------------------------------------------------
 *
 * @name        Voodooo\Model
 * @desc        The abstract class for models
 *              This class is extended by VoodOrm. All the public VoodOrm methods
 *              can be accessed in this class
 *
 * Quick Tips
 * - Relationship
 * Sometimes you will need relationship in your model
 *
 * Let's say you have the tables: book and author
 *
 * We can create a method in Model/Book to get author from the Model/Author
 *
 * class Book extends Voodoo\Core\Model
  {
   // blah blah code here

    // For One to One. Will get only one entry
    public function getAuthor(){
        $table = (new Author)->getTableName();
         return $this->{$table}(self::REL_HASONE, function($res){
                    return Author::create($res);
                });

    }

    // For One to Many. Will get all the tags entry
    public function getTags(){
        $table = (new Tags)->getTableName();
         return $this->{$table}(self::REL_HASMANY, function($res){
                    return Tags::create($res);
                });

    }
 }

 * Now that's how you access them
   $book = (new Model\Book)->findOne(1234);
   $author = $book->getAuthor()->name;
   $tags = $book->getTags();

 *
 */

namespace Voodoo\Core;

use Voodoo\VoodOrm,
    Closure,
    PDO;

abstract class Model extends VoodOrm
{
  /**
   * The table name
   * @var type
   */
  protected $tableName = null;

  /**
   * The primary ke name
   * @var string
   */
  protected $primaryKeyName = "id";

  /**
   * The foreign key name for one to many
   * @var string
   */
  protected $foreignKeyName = "%s_id";

  /**
   * The DB Alias to use. It is saved in App/Config/DB.ini
   * @var string
   */
  protected $dbAlias = "";

 /*******************************************************************************/

  /**
   * Create a new instance
   *
   * @param mixed $obj
   * @return Model
   */
    public static function create($obj = null)
    {
        if(is_array($obj)) { // fill the object with new data
            return (new static)->fromArray($obj);
        } else {
            return new static;
        }
    }

    /**
     * The constructor
     *
     * @param PDO $pdo
     * @throws Exception
     */
    public function __construct(PDO $pdo = null)
    {
        if(! $this->tableName){
            throw new Exception\Model("TableName is null in ".get_called_class());
        }
        if (! $this->primaryKeyName){
            throw new Exception\Model("PrimaryKeyName is null in ".get_called_class());
        }

        if (! $pdo) {
            if (! $this->dbAlias){
                throw new Exception\Model("DB Alias is missing in ".get_called_class());
            }
            $pdo =  ConnectionManager::connect($this->dbAlias);
        }

        parent::__construct($pdo, $this->primaryKeyName, $this->foreignKeyName);

        $instance = parent::table($this->tableName);

        $this->table_name = $instance->getTablename();
    }


}
