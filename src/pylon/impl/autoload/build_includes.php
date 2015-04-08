<?php

/**\addtogroup Autoload
 * @{
 */
if($argc == 4 )
{
    $a = new PYLAssemblyBuilder();
    $a->run($argv[1],$argv[2],$argv[3]);
}
else
{
    echo "useage: php build_includes  <root_path> <search_path> <outfiles> \n";
}
define( "FILE_DEFAULT_DIRECTORY_CREATION_MODE", 0755 );
class PYLFile
{

    var $_fileName;
    var $_handler;
    var $_mode;

    function __construct( $fileName )
    {
        //$this->Object();

        $this->_fileName = $fileName;
    }

    function open( $mode = "r" )
    {
        $this->_handler = fopen( $this->_fileName, $mode );

        $this->_mode = $mode;

        return $this->_handler;
    }

    /**
     * Closes the stream currently being held by this object
     *
     * @return nothing
     */
    function close()
    {
        fclose( $this->_handler );
    }

    /**
     * Reads the whole file and put it into an array, where every position
     * of the array is a line of the file (new-line characters not
     * included)
     *
     * @return An array where every position is a line from the file.
     */
    function readFile()
    {
        $contents = Array();

        $contents = file( $this->_fileName );

        for( $i = 0; $i < count( $contents ); $i++ )
            $contents[$i] = trim( $contents[$i] );

        return $contents;
    }

    /**
     * Reads bytes from the currently opened file
     *
     * @param size Amount of bytes we'd like to read from the file. It is 
     * set to 4096 by default.
     * @return Returns the read contents
     */
    function read( $size = 4096 )
    {
        return( fread( $this->_handler, $size ));
    }

    /**
     * checks whether we've reached the end of file
     *
     * @return True if we reached the end of the file or false otherwise
     */
    function eof()
    {
        return feof( $this->_handler );
    }

    /**
     * Writes data to disk
     *
     * @param data The data that we'd like to write to disk
     * @return returns the number of bytes written, or false otherwise
     */
    function write( $data )
    {
        return fwrite( $this->_handler, $data );
    }

    /**
     * truncates the currently opened file to a given length
     *
     * @param length Lenght at which we'd like to truncate the file
     * @return true if successful or false otherwise
     */
    function truncate( $length = 0 )
    {
        return ftruncate( $this->_handler, $length );
    }

    /**
     * Writes an array of text lines to the file.
     *
     * @param lines The array with the text.
     * @return Returns true if successful or false otherwise.
     */
    function writeLines( $lines )
    {
        // truncate the file to remove the old contents
        $this->truncate();

        foreach( $lines as $line ) {
            //print("read: \"".htmlentities($line)."\"<br/>");
            if( !$this->write( $line, strlen($line))) {
                return false;
            }
                 /*else
                 print("written: \"".htmlentities($line)."\"<br/>");*/
        }

        return true;
    }

    /**
     * Returns true wether the file is a directory. See
     * http://fi.php.net/manual/en/function.is-dir.php for more details.
     *
     * @param file The filename we're trying to check. If omitted, the
     * current file will be used (note that this function can be used as
     * static as long as the file parameter is provided)
     * @return Returns true if the file is a directory.
     */
    function isDir( $file = null )
    {
        if( $file == null )
            $file = $this->_fileName;

        return is_dir( $file );
    }

    /**
     * Returns true if the file is writable by the current user.
     * See http://fi.php.net/manual/en/function.is-writable.php for more 
     * details.
     *
     * @param file The filename we're trying to check. If omitted, the
     * current file will be used (note that this function can be used as
     * static as long as the file parameter is provided)
     * @return Returns true if the file is writable, or false otherwise.
     */
    function isWritable( $file = null )
    {
        if( $file == null )
            $file = $this->_fileName;

        return is_writable( $file );
    }

    /**
     * returns true if the file is readable. Can be used as static if a
     * filename is provided
     *
     * @param if provided, this method can be used as an static method and
     * it will check for the readability status of the file
     * @return true if readable or false otherwise
     */
    function isReadable( $file = null )
    {
        if( $file == null )
            $file = $this->_fileName;

        clearstatcache();

        return is_readable( $file );
    }

    /**
     * removes a file. Can be used as static if a filename is provided. 
     * Otherwise it will remove the file that was given in the constructor
     *
     * @param optionally, name of the file to delete
     * @return True if successful or false otherwise
     */
    function delete( $file = null )
    {
        if( $file == null )
            $file = $this->_fileName;

        if( !PYLFile::isReadable( $file ))
            return false;

        if( PYLFile::isDir( $file ))
            $result = rmdir( $file );
        else
            $result = unlink( $file );

        return $result;
    }

    /**
     * removes a directory, optinally in a recursive fashion
     *
     * @param dirName
     * @param recursive Whether to recurse through all subdirectories that
     * are within the given one and remove them.
     * @param onlyFiles If the recursive mode is enabled, setting this to 'true' will
     * force the method to only remove files but not folders. The directory will not be
     * removed but all the files included it in (and all subdirectories) will be.
     * @return True if successful or false otherwise
     * @static
     */
    function deleteDir( $dirName, $recursive = false, $onlyFiles = false )
    {
        // if the directory can't be read, then quit with an error
        if( !PYLFile::isReadable( $dirName ) || !PYLFile::exists( $dirName )) {
            return false;
        }

        // if it's not a file, let's get out of here and transfer flow
        // to the right place...
        if( !PYLFile::isDir( $dirName )) {
            return PYLFile::delete( $dirName );
        }

        // Glob::myGlob is easier to use than Glob::glob, specially when 
        // we're relying on the native version... This improved version 
        // will automatically ignore things like "." and ".." for us, 
        // making it much easier!
        $files = Glob::myGlob( $dirName, "*" );
        foreach( $files as $file ) {
            if( PYLFile::isDir( $file )) {
                // perform a recursive call if we were allowed to do so
                if( $recursive ) 
                    PYLFile::deleteDir( $file, $recursive, $onlyFiles );
            }

            // PYLFile::delete can remove empty folders as well as files
            if( PYLFile::isReadable( $file ))
                PYLFile::delete( $file );			}

            // finally, remove the top-level folder but only in case we
            // are supposed to!
            if( !$onlyFiles )
                PYLFile::delete( $dirName );

            return true;
    }

    /**
     * Creates a new folder. If the folder name is /a/b/c and neither 
     * /a or /a/b exist, this method will take care of creating the 
     * whole folder structure automatically.
     *
     * @static
     * @param dirName The name of the new folder
     * @param mode Attributes that will be given to the folder
     * @return Returns true if no problem or false otherwise.
     */
    function createDir( $dirName, 
        $mode = FILE_DEFAULT_DIRECTORY_CREATION_MODE )
    {
        if(PYLFile::exists($dirName)) return true;

        if(substr($dirName, strlen($dirName)-1) == "/" ){
            $dirName = substr($dirName, 0,strlen($dirName)-1);
        }

        // for example, we will create dir "/a/b/c"
        // $firstPart = "/a/b"
        $firstPart = substr($dirName,0,strrpos($dirName, "/" ));           

        if(file_exists($firstPart)){
            if(!mkdir($dirName,$mode)) return false;
            chmod( $dirName, $mode );
        } else {
            PYLFile::createDir($firstPart,$mode);
            if(!mkdir($dirName,$mode)) return false;
            chmod( $dirName, $mode );
        }

        return true;
    }


    /**
     * returns a temporary filename in a pseudo-random manner
     *
     * @return a temporary name
     */
    function getTempName()
    {
        return md5(microtime());
    }

    /**
     * Returns the size of the file.
     *
     * @param string fileName An optional parameter specifying the name 
     * of the file. If omitted, we will use the file that we have 
     * currently opened. Please note that this function can
     * be used as static if a file name is specified.
     * @return An integer specifying the size of the file.
     */
    function getSize( $fileName = null )
    {
        if( $fileName == null )
            $fileName = $this->_fileName;

        $size = filesize( $fileName );
        if( !$size )
            return -1;
        else
            return $size;
    }

    /**
     * renames a file
     *
     * http://www.php.net/manual/en/function.rename.php
     *
     * This function can be used as static if inFile and outFile are both 
     * not empty. if outFile is empty, then the internal file of the
     * current object will be used as the input file and the first 
     * parameter of this method will become the destination file name.
     *
     * @param inFile Original file
     * @param outFile Destination file.
     * @return Returns true if file was renamed ok or false otherwise.
     */
    function rename( $inFile, $outFile = null )
    {
        // check how many parameters we have
        if( $outFile == null ) {
            $outFile = $inFile;
            $inFile  = $this->_fileName;
        }

        // In order to work around the bug in php versions older
        // than 4.3.3, where rename will not work across different
        // partitions, this will be a copy and delete of the original file

        // copy the file to the new location
        if (!copy($inFile, $outFile)) {
            // The copy failed, return false
            return FALSE;
        }

        // Now delete the old file
        // NOTICE, we are not checking the error here.  It is possible 
        // the the original file will remain and the copy will exist.
        //
        // One way to potentially fix this is to look at the result of
        // unlink, and then delete the copy if unlink returned FALSE, 
        // but this call to unlink could just as easily fail
        unlink( $inFile );

        return TRUE;
    }

    /**
     * copies a file from one place to another.
     * This method is always static
     *
     * @param inFile
     * @param destFile
     * @return True if successful or false otherwise
     * @static
     */
    function copy( $inFile, $outFile )
    {
        return @copy( $inFile, $outFile );
    }

    /**
     * changes the permissions of a file, via PHP's chmod() function
     *
     * @param inFile The name of the file whose mode we'd like to change
     * @param mode The new mode, or if none provided, it will default 
     * to 0644
     * @return true if successful or false otherwise 
     * @static
     */
    function chMod( $inFile, $mode = 0644 )
    {
        return chmod( $inFile, $mode );
    }

    /**
     * returns true if the file exists.
     *
     * Can be used as an static method if a file name is provided as a
     *  parameter
     * @param fileName optinally, name of the file whose existance we'd
     * like to check
     * @return true if successful or false otherwise
     */
    function exists( $fileName = null ) 
    {
        if( $fileName == null )
            $fileName = $this->_fileName;

        clearstatcache();

        return file_exists( $fileName );
    } 

    /** 
     * returns true if the file could be touched
     *
     * Can be used to create a file or to reset the timestamp.
     * @return true if successful or false otherwise
     * @see PHP Function touch()
     *
     */
    function touch( $fileName = null )
    {
        if( $fileName == null )
            return false;

        return touch($fileName);
    }
}

    
class PYLAssemblyBuilder
{
    private static $_paths = array();
    private static $_skipFolders = array('web-inf', 'tmp', '.svn',  'logs', 'project','personal');
    private static $_skipFiles= array('qmb_client_.*\.php');
    private static $_fileNameTemplate = array('php');

    public function run($srcRoot,$rootPath,$outfiles)
    {
        $classes = array();
        self::$_paths = explode(":",$rootPath);
        $outfileArr = explode(":",$outfiles);
        foreach (self::$_paths as $path)
        {
            $find = $this->getClassPos($path);
            $classes = array_merge($classes , $find);
        }
        self::generatorAssemblyFile($classes,$srcRoot,$outfileArr);
    
        echo "\ngenerator assembly file successed!\n";
    }
    public function getClassPos($path)
    {
        $classes = array();
        $files = self::findFiles($path);
        foreach (self::findClasses($files) as $class => $filename)
        {
            if (empty($classes[$class]))
                $classes[$class] = $filename;
            else
                echo "Repeatedly Class $class in file $filename\n";
        }
        return $classes;
    }

    static private function generatorAssemblyFile($classes,$srcRoot,$outFileArr)
    {
        

        $arrayCode = "";
        foreach ($classes as $key => $value)
        {
            $value = str_replace($srcRoot,"\$ROOT.'",$value);

            $arrayCode  .= "\t\t\t'$key' => \t\t\t$value',\n";
        }
        foreach($outFileArr as $item)
        {
            $datafile= new PYLFile($item);
            $datafile->open("w+");
            $datafile->write("<?php \n\$data = array( \n $arrayCode 'ok'=>'ok'); \n?>");
        }
        
    }

    
    static private function findClasses($files)
    {
        $classes = array();
	    foreach ($files as $file)
	    {
	        foreach (self::findClassFromAFile($file) as $class)
	        {
	            if (empty($classes[$class]))
	                $classes[$class] = $file;
	            else
                    echo "Repeatedly Class $class in file $file\n";
	        }
	    }
	    return $classes;
    }
    static private function findClassFromAFile($file)
    {
        $classes = array();
        $lines = file($file);
        if(! is_array($lines)) return $classes;
        foreach ($lines as $line)
        {
            if (preg_match("/^\s*class\s+(\S+)\s*/", $line, $match))
            {
                $classes[] = $match[1];
            }
            if (preg_match("/^\s*abstract\s*class\s+(\S+)\s*/", $line, $match))
            {
                $classes[] = $match[1];
            }
            if (preg_match("/^\s*interface\s+(\S+)\s*/", $line, $match))
            {
                $classes[] = $match[1];
            }

        }
        return $classes;
    }

    static private function skipFiles($file)
    {
        foreach(self::$_skipFiles as $fileRule)
        {
//            var_dump($fileRule);
            if(preg_match("/$fileRule/i",$file))
                return ;
        }
        $suffix = self::getFileSuffix($file);
        return ( false == in_array($suffix, self::$_fileNameTemplate) 
            || (1 == preg_match("/\.svn/", $file)) || (0 == preg_match("/.+\.php/", $file)) ); 
    }

    static private function isInFileTemplates($file)
    {
    }

    static private function isSkipFolders($file)
    {

        foreach (self::$_skipFolders as $skip)
        {
            $skip = quotemeta($skip);
            if (1 == preg_match("/$skip/", $file))
            {
                return true;
            }
        }
    }
    
    static private function findFiles($dirname)
    {
         $filelist = array();
         $currentfilelist = scandir($dirname);
         if(is_array($currentfilelist ))
         foreach ($currentfilelist as $file)
         {
             if ($file == "." || $file == ".." || self::isSkipFolders($file))
             {
                 continue;
             }

             $file = "$dirname/$file";

             if (is_dir($file))
             {
                 foreach (self::findFiles($file) as $tmpFile)
                 {
                     $filelist[] = $tmpFile;
                 }
                 continue;
             }

             if (false == self::skipFiles($file))
             {
                echo $file."\n";
                $filelist[] = $file;
             }
         }
         return $filelist;
    }

    static private function getFileSuffix($fileName)
    {
        $pointPos = strrpos($fileName, "."); 
         
        if ($pointPos)
        {
            return substr($fileName, $pointPos+1, strlen($fileName) - $pointPos); 
        }
        return;
    }
}

/** 
 *  @}
 */
?>
