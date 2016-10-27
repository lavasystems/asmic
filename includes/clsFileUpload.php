<?php

class clsFileUpload
{
    var $strTempName;
    var $strFileName;
    var $strFileType;
    var $strFileSize;
    var $strErrorCode;
    var $strNewFileName;
    var $strKey;

    function clsFileUpload( $k )
    {
        $this->strTempName = '';
        $this->strFileName = '';
        $this->strFileType = '';
        $this->strFileSize = '';
        $this->strErrorCode = '';
        $this->strNewFileName='';

        $this->setKey( $k );

        return true;
    }

    function loadData()
    {
        $this->strTempName = $this->getTempName();
        $this->strFileName = $this->getFileName();
        $this->strFileSize = $this->getFileSize();
        $this->strFileType = $this->getFileType();
    }

    function setKey( $k='' )
    {
        if ( $k=='' )
        {
            echo "error: empty key given - upload";
            exit;
        }

        $this->strKey = $k;
        $this->loadData();
    }

    function isValidExtension( $arrValidator )
    {

        $strExt = $this->getExtension();

        foreach ( $arrValidator as $key => $value )
        {
            if ( $value == $strExt )
            {
                return true;
            }
        }
        return false;
    }

    function getExtension()
    {
//        return substr( $this->strFileName , strpos( $this->strFileName, '.' )+1, strlen( $this->strFileName ) );
		// modified by Malik
		// replaced strpos with strrpos function to fix multiple dots in filename
        return substr( $this->strFileName , strrpos( $this->strFileName, '.' )+1, strlen( $this->strFileName ) );
    }

    function getFileType()
    {
        return $_FILES[ $this->strKey ][ 'type' ];
    }


    function getErrorCode()
    {
        return $_FILES[ $this->strKey ][ 'error' ];
    }

    function getFileSize()
    {
        return $_FILES[ $this->strKey ][ 'size' ];
    }

    function getFileName()
    {
        return $_FILES[ $this->strKey ][ 'name' ];
    }

    function getTempName()
    {
        return $_FILES[ $this->strKey ][ 'tmp_name' ];
    }

    function generateFileName()
    {
        return time() . rand( 1000, 9999 );
    }

    function moveFile( $strDestination, $strNewFileName=false )
    {
        
        if ( $strNewFileName == false )
        {
            $strNewFileName = $this->generateFileName() . '.' . $this->getExtension();
        }
	
        if ( move_uploaded_file( $this->strTempName, $strDestination . $strNewFileName) )
        {
            $this->strNewFileName = $strNewFileName;
            return true;
        }
        else
        {
            return false;
        }
    }
    
    function getNewFileName()
    {
        return $this->strNewFileName;
    }
}
?>