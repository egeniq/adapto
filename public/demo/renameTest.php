<?php
include('atk.inc');

$callback = new renameAtk();
$traverser = new Adapto_DirectoryTraverser();
$traverser->addCallbackObject($callback);

$traverser->traverse('atk');

class renameAtk
{
    public $_newDir = "Adapto_new"; // defaulted to public
    public $_compatDir = "Adapto_compatibility"; // defaulted to public
    public $_renameRules = array("DG", 'cache_'); // defaulted to public

    public $_ignoredMethods = array("atkconfig","atkMetaEntity"," // defaulted to public

    public $_refactoredClassNames = array( // defaulted to public
    "Adapto_menuinterface" => "Adapto_Menu_Interface",
    "MenuInterface" => "Adapto_Menu_Interface",
    "Adapto_Menu_Adapto_menuinterface" => "Adapto_Menu_Interface",
    "Adapto_menuinterface" => "Adapto_Menu_Interface",
    "Adapto_Menu_menuinterface" => "Adapto_Menu_Interface",
    "menuinterface" => "Adapto_Menu_Interface",
    "Adapto_PlainMenu" =>"Adapto_Menu_Plain",
    "Adapto_Relation_Attribute" => "Adapto_Attribute"
    );

    public $_classType; // defaulted to public


    public function __construct()
    {
        //Create new and compat dir when they're not there
        if(!is_dir($this->_newDir) || !is_dir($this->_compatDir))
        {
            mkdir($this->_newDir, 0777);
            mkdir($this->_compatDir, 0777);
        }
    }

    /*
     * Convert fullpath to :
     * 2 - New dir
     * 3 - Compat dir
     * 4 - Include dir
     * 5 - Name of class
     */
    public function convertDir($fullPath, $mode)
    {
        //Get parts of path
        $fullPathParts = explode("/", $fullPath);
        $fullPathParts[0] = "Atk";

        switch($mode)
        {
            case 2:
                //Get path to new dir
                $fullPathParts[0] = $this->_newDir;
                if(strstr(end($fullPathParts),".")){
                    unset($fullPathParts[count($fullPathParts) -1] );
                }
                return implode("/", $this->cleanDirName($fullPathParts));
                break;
            case 3:
                //Get path to compat dir
                $fullPathParts[0] = $this->_compatDir;
                return implode("/", $fullPathParts);
                break;
            case 4:
                //Get path to Include dir
                unset($fullPathParts[count($fullPathParts) -1]);

                $fullPathParts = array_map(
                function($item) { return ucfirst($item); },
                $fullPathParts
                );

                return implode("/", $this->cleanDirName($fullPathParts));
                break;
            case 5:
                unset($fullPathParts[count($fullPathParts) -1]);

                $fullPathParts = array_map(
                function($item) { return ucfirst($item); },
                $fullPathParts
                );

                $className = implode("_", $this->cleanDirName($fullPathParts));
                //                echo $className ."\n";

                return $className;
                break;
        }
    }

    public function cleanDirName($fullPathParts)
    {
        $lastItem = ucfirst(end($fullPathParts));
        //Simple hack to convert names like 'attributes' to 'Attribute'
        if(substr($lastItem, strlen($lastItem) -1, strlen($lastItem)) == "s")
        {
            $lastItem = substr($lastItem, 0, strlen($lastItem)  -1);
        }

        $fullPathParts[count($fullPathParts) -1] = $lastItem;

        return $fullPathParts;
    }

    /**
     * Rename all visited directory's to ATK style names if name not is existing
     *
     * @param string $fullPath
     */
    public function visitDir($fullPath)
    {
        //Get path to compat dir
        $compatDir = trim($this->convertDir($fullPath, 3));

        //Get path to new dir
        $newDir = trim($this->convertDir($fullPath, 2));

        //if folder in compat dir not exists create dir
        if(!is_dir($compatDir)){
            mkdir( $compatDir, 0777);
        }

        //if folder in new dir not exists, create dir
        if(! is_dir($newDir) &&
        ($this->_containsFilesWithExt($this->getDirContents($fullPath), "php")
        || $this->_containsFilesWithExt($this->getDirContents($fullPath), "inc")))
        {
            mkdir($newDir, 0777);
        }
    }

    public function visitFile($fullPath)
    {
        $folder= explode("/",trim($fullPath));

        if(preg_match('/.(?P<filename>atk.+)(?P<suffix>.inc)/', $fullPath, $matches)
        && preg_match('/.(?P<prefix>class.)/', $fullPath, $classmatches))
        {
            //If true this is an ATK class
            /*Example of input:
             /atk/ui/class.atkpage.inc
             */
            $oldClassName = $this->extractClassname($fullPath);
            $newDir = $this->convertDir($fullPath, 2);
            $newClassName = str_replace("atk","", $oldClassName);
            if($oldClassName == "atkStatement"){
                $this->_classType = "abstract";
            }
            $newClassName = $this->generateClassName($newClassName, $newDir);
            $compatDir = $this->convertDir($fullPath, 3);

            if(!strstr(strtolower(basename($newDir)), strtolower($newClassName)) == false && count(explode("/",$newDir)) < 3)
            {
                $newDir = $this->_newDir . "/";
            }
            else if(!strstr(strtolower(basename($newDir)), strtolower($newClassName)) == false && count(explode("/",$newDir)) > 2)
            {
                $dirParts = explode("/",$newDir);
                $newDir = $this->_newDir . "/" . $dirParts[1] ."/";

            }

            $this->createCompatStyleClass($oldClassName, $newClassName, $compatDir, $newDir);
            $this->createNewStyleClass($oldClassName,$newClassName, $fullPath, $newDir);

        }
        else if(substr( $fullPath, strlen( $fullPath ) - 4) == ".php" && in_array(strtolower($folder[count($folder)-2]), array("ui","helper","exceptions","placeholder")))
        {
            $newClassName = end($folder);
            $newDir = $this->convertDir($fullPath, 2) . "/" . $newClassName;
            $this->copyFile($fullPath,$newDir);
        }
        else
        {
            $compatDir = $this->convertDir($fullPath, 3);
            $this->copyFile($fullPath,$compatDir);
            //            echo "No match on filename " . $fullPath . "\n";
        }
    }

    public function copyFile($oldLocation, $newLocation)
    {
        $content = $this->getFileContents($oldLocation);
        $this->createFile($content, $newLocation);
    }

    public function extractClassname($fullPath)
    {
        try
        {
            $className = "";
            $sData = file_get_contents($fullPath);

            if(preg_match('/.(\sclass+)\s(atk.+)\s(extends|implements.+)/', $sData, $matches)
            || preg_match('/.(\sabstract class|\sclass|\sinterface+)\s(atk.+)/', $sData, $matches)
            || preg_match('/.(class+)\s(atk.+)\s(extends|implements.+)/', $sData, $matches)
            || preg_match('/.(class|interface+)\s(atk.+)/', $sData, $matches))
            {
                $this->_classType= $matches[0];
                $className = trim($matches[2]);
            }
            else
            {
                echo "No match: " . $fullPath . "\n";
            }
        }
        catch(Exception $e)
        {
            die( "Exception: ". $e->getMessage());
        }

        return $className;
    }

    public function generateClassName($className, $location)
    {
        $dir = end(explode("/", $location));
        if(! strstr($dir,".php") == false){
           $tmpLocation = explode("/", $location);
           $dir =  $tmpLocation[sizeof($tmpLocation) -2];
        }
        if(count(explode("_",$className)) > 1)
        {
            return $className;
        }

        foreach($this->_renameRules as $rule){
            $className = str_replace($rule,"",$className);
        }

        if(strlen(str_replace($dir,"",$className)) > 0){
            $className = str_replace($dir,"",$className);
        }

        return $className;
    }

    public function generateFileLocation($className, $location)
    {
		if(count(explode("_", $className)) < 1){
			$className = $this->generateClassName($className, $location);
		}
		else
		{
			$className = end(explode("_",$className));
		}
        //Simple hack to convert names like 'attributes' to 'Attribute'
        if(substr($location, strlen($location) -1, strlen($location)) == "s")
        {
            $location = substr($location, 0, strlen($location)  -1);
        }

        $fileLocation = $location . "/" . $className . ".php";

        return $fileLocation;
    }

    /**
     * Read all the entries of a directory.
     * @param String $path The path to read the contents from.
     * @return array Array containing the contents of the directory.
     */
    function getDirContents($path)
    {
        $result = array();
        $dir = @opendir($path);
        while (($file = @readdir($dir)) !== false)
        {
            $result[] = $file;
        }
        @closedir($dir);
        sort($result);
        return $result;
    }

    public function getFileContents($fullPath)
    {
        try
        {
            $content = file_get_contents($fullPath);
        }
        catch(Exception $e)
        {
            die( "Exception: ". $e->getMessage());
        }

        return $content;
    }

    public function createNewStyleClass($oldClassName, $newClassName, $oldLocation, $newLocation)
    {
        //TODO a test to match the constructor /\batkBlaat\b/
        $content = $this->getFileContents($oldLocation);
        if(array_key_exists($newClassName, $this->_refactoredClassNames))
        {
            $newClassName = $this->_refactoredClassNames[$newClassName];
        }
        else
        {
            if(strstr(strtolower($this->convertDir($oldLocation, 5)), strtolower($newClassName)) == false)
            {
                $newClassName = $this->convertDir($oldLocation, 5) . "_" . $newClassName;
            }
            else
            {
                $newClassName = $this->convertDir($oldLocation, 5);
            }
        }
        
        $newLocation= $this->generateFileLocation($newClassName, $newLocation);//$newLocation . "/" . $newClassName . ".php";

        //        $content = preg_replace("/\batk[A-Z].\b/", bst)
        if(!in_array($oldClassName,$this->_ignoredMethods) && !in_array(strtolower($oldClassName),$this->_ignoredMethods) ){
            $content  = preg_replace('/(public\sfunction\s|protected\sfunction\s|function\s)('.$oldClassName.'\()/', "public function __construct(",$content);
            $content  = preg_replace('/(public\sfunction\s|function\s)('.strtolower($oldClassName).'\()/', "public function __construct(",$content);
        }

        $content = preg_replace('/\b'.$oldClassName.'\b/', $newClassName,$content);
        
        //Covers current class constructor
        $content  = preg_replace('/(this->)('.$oldClassName.')(\()/', "\1__construct",$content);
        //Covers parent class constructor
        if(preg_match('/.(extends|implements).+/', $content, $matches))
        {
            $extendedClass = end(explode(" ",trim($matches[0])));
            $content = preg_replace('/(\$this->)('.$extendedClass.')/', "parent::__construct", $content);
//
//            $extendedClassNew = str_replace("atk","",$extendedClass);
//            $tmpExtClass = $this->convertDir($oldLocation, 5) . "_" . $this->generateClassName($extendedClassNew, $newLocation);
//            $extendedClassNew = str_replace("atk","Adapto_",$extendedClass);
//            
//            $boolExtClass = strstr($tmpExtClass,"__");
//            if($boolExtClass == false && count(explode("/",$oldLocation)) > 2){
//                $extendedClassNew = $tmpExtClass;
//            }
//            if(array_key_exists($extendedClassNew, $this->_refactoredClassNames))
//            {
//                $extendedClassNew = $this->_refactoredClassNames[$extendedClassNew];
//            }
//             
//            $extendedClassNew = preg_replace("/(_)$/","",$extendedClassNew);
//            $extendedClassNew = implode("_",array_unique(explode("_", $extendedClassNew)));
//            
//            //fix for relations classes
//            if(preg_match('/(Meta)(.*?)(Relation)/', $extendedClassNew,$matches )
//            && !preg_match('/(Meta)(.*?)(Relation)(.*?)(Meta.+)/',$extendedClassNew,$matchesTwo))
//            {
//               $extendedClassNew = preg_replace("/(Meta_)/","",$extendedClassNew);
////               echo $extendedClassNew . "\n";
//            }
//            
//            $extendedClassNew = preg_replace("/(_)$/","",$extendedClassNew);
//            $content = preg_replace('/\b'.$extendedClass.'\b/', $extendedClassNew, $content);
        }
        
            
 

        if(!$this->createFile($content, $newLocation))
        {
            echo "Something went wrong while writing this file" . $location. "\n";
        }

    }

    public function createCompatStyleClass($oldStyleName, $newStyleName, $oldLocation, $newLocation)
    {
        $newClassLocation = $this->convertDir($oldLocation, 4) . "/". $this->generateClassName($newStyleName, $newLocation) .".php";
        if($newLocation == $this->_newDir . "/"){
            $newClassLocation = "Atk" ."/" . $this->generateClassName($newStyleName, $newLocation) .".php";
        }

        if(strstr(strtolower($this->convertDir($oldLocation, 5)), strtolower($newStyleName)) == false){
            $newStyleName = $this->convertDir($oldLocation, 5) . "_" . $newStyleName;
        }
        else
        {
            $newStyleName = $this->convertDir($oldLocation, 5);
        }

        if(array_key_exists($newStyleName, $this->_refactoredClassNames))
        {
            $newStyleName = $this->_refactoredClassNames[$newStyleName];
            $newClassLocation = str_replace("_","/", $newStyleName) .".php";
        }
        
        $newClassLocation = str_replace("UI","Ui", $newClassLocation);
        $newClassLocation = str_replace("DataGrid","Datagrid", $newClassLocation);
        $newClassLocation = str_replace("RecordList","Recordlist", $newClassLocation);

        //Create an compat class with provided template
        $type = $this->_getClassType();
        extract(array($newClassLocation, $oldStyleName, $newStyleName));

        ob_start();
        switch($type)
        {
            case 'interface':
                include 'compatInterfaceTemplate.php';
                break;
            case 'abstract':
                include 'compatAbstractClassTemplate.php';
                break;
            default:
            case 'class':
                include 'compatClassTemplate.php';
                break;
        }

        $content =  ob_get_clean();
        ob_end_clean();

        if(!$this->createFile($content, $oldLocation))
        {
            echo "Something went wrong while writing this file" . $oldLocation. "\n";
        }
    }

    public function createFile($content, $location)
    {
        try
        {
            $fp = fopen($location, 'w');
            fwrite($fp, $content);
            fclose($fp);
        }
        catch(Exception $e)
        {
            echo "Write exception: " . $e->getMessage();
            return false;
        }

        return true;
    }

    private function _getClassType()
    {
        if(!strstr($this->_classType, "abstract")==false)
        {
            return "abstract";
             
        }
        else if(!strstr($this->_classType, "interface")==false && strstr($this->_classType, "menuinterface")==false )
        {
            return "interface";
        }

        return "class";
    }

    private function _containsFilesWithExt(array $dirContents, $ext)
    {
        foreach($dirContents as $file){
            $pathParts = pathinfo($file);
            if($pathParts['extension'] == $ext){
                return true;
            }
        }

        return false;

    }
}

?>