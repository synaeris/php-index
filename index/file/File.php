<?php

namespace malkusch\index;

/**
 * Wrapper for file operations and informations
 *
 * @author   Markus Malkusch <markus@malkusch.de>
 * @link     https://github.com/malkusch/php-index
 */
class File
{
    
    const
    /**
     * Sector size
     */
    DEFAULT_BLOCK_SIZE = 512;
    
    private
    /**
     * @var int
     */
    $_blocksize = self::DEFAULT_BLOCK_SIZE,
    /**
     * @var String
     */
    $_path = "",
    /**
     * @var int
     */
    $_size = 0,
    /**
     * @var resource
     */
    $_filePointer;
    
    /**
     * Sets the index file and opens a file pointer
     *
     * @param string $path Index file
     *
     * @throws IndexException_IO_FileExists
     * @throws IndexException_IO
     */
    public function __construct($path)
    {
        $this->_path = $path;
        
        // Open the file
        $this->_filePointer = @\fopen($path, "rb");
        if (! \is_resource($this->_filePointer)) {
            if (! \file_exists($path)) {
                throw new IndexException_IO_FileExists(
                    "'$path' doesn't exist."
                );

            }
            $errors = \error_get_last();
            throw new IndexException_IO($errors["message"]);

        }
        
        // Read the filesystem's blocksize
        $stat = \stat($path);
        if (\is_array($stat)
            && isset($stat["blksize"])
            && $stat["blksize"] > 0
        ) {
            $this->_blocksize = $stat["blksize"];
            
        }
        
        // Read the size
        $this->_size = \filesize($path);
        if ($this->_size === false) {
            throw new IndexException_IO("Can't read size of '$path'");
            
        }
    }
    
    /**
     * Returns an open file pointer for reading in binary mode
     *
     * @return resource
     */
    public function getFilePointer()
    {
        return $this->_filePointer;
    }
    
    /**
     * Returns the path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }
    
    /**
     * Returns the file size
     * 
     * @return int 
     */
    public function getFileSize()
    {
        return $this->_size;
    }
    
    /**
     * Returns the blocksize of the file's filesystem
     * 
     * @return int 
     */
    public function getBlockSize()
    {
        return $this->_blocksize;
    }
    
}