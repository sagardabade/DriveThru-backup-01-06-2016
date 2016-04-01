<?php

    // Connect to the database
   // mysql_connect('localhost', 'root', '');
   // mysql_select_db('test');

   header('Content-Type: application/json; charset=utf-8');
   
   include("DB_config.php");
   connectDB();

    echo '<pre>';

    $categories = Category::getTopCategories();
   // print_r($categories);
   
deliver_response($categories);
    echo '</pre>';
    
       function deliver_response($arr)
{
$response['products']=$arr;
$json_response=json_encode($response, JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK);
echo $json_response;
};

class Category
{
    /**
     * The information stored in the database for each category
     */
    public $id;
    public $parent;
    public $name;

    // The child categories
    public $children;

    public function __construct()
    {
        // Get the child categories when we get this category
        $this->getChildCategories();
    }

    /**
     * Get the child categories
     * @return array
     */
    public function getChildCategories()
    {
        if ($this->children) {
            return $this->children;
        }
        return $this->childrenX = self::getCategories("parent = {$this->id}");
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * The top-level categories (i.e. no parent)
     * @return array
     */
    public static function getTopCategories()
    {
        return self::getCategories('parent = 0');
    }

    /**
     * Get categories from the database.
     * @param string $where Conditions for the returned rows to meet
     * @return array
     */
    public static function getCategories($where = '')
    {
        if ($where) $where = " WHERE $where";
        
		
        $result = mysql_query("SELECT * FROM categories$where");
        

        $categories = array();
        while ($category = mysql_fetch_object($result, 'Category'))
            $categories[] = $category;

        mysql_free_result($result);
        
        return $categories;
    }
    
  
}