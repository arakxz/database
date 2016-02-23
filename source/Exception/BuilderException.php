<?php
namespace Arakxz\Database\Exception;

class BuilderException extends \RuntimeException
{

    /**
     * @param string     $message
     * @param integer    $code
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
