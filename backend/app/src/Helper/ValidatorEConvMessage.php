<?php

namespace App\Helper;

/**
 * ValidatorEConvMessage
 *
 */
class ValidatorEConvMessage
{

    protected $containerHead = '';
    protected $containerFoot = '';
    protected $messages = array(
        "notEmpty" => "Field cannot be empty!",
        "maxLength" => "Field value is too long!",
        "minLength" => "Password should be at least 8 characters long!",
        "number" => "Field can only contain integers!",
        "isDouble" => "Field can only contain real numbers!",
        "isInt" => "Field can only contain integers!",
        "date" => "Invalid date!",
        "email" => "Invalid email address!",
        "equalsPassword" => "Passwords do not match!",
        "alphaNumeric" => "Field can only contain letters and numbers!",
        "equals" => "Passwords provided do not match!"
    );

    /**
     * Osztály inicializálása
     */
    public function __construct()
    {
    }

    /**
     * A kapott tömbben lévő hibakódokat cseréli, érhetőbb
     * hibaüzenetekre.
     *
     * @param array $errors
     * @return array
     */
    public function replaceErrorToMessage($errors)
    {

        $ret = array();

        foreach ((array) $errors as $index => $errorCode) {
            if (is_array($errorCode)) {
                $errorCodeValues = array_values($errorCode);
                $errorCode = array_shift($errorCodeValues);
            }
            if (!isset($ret[$index])) {
                $ret[$index] = "";
            }
            $ret[$index] .= $this->containerHead;

            if (isset($this->messages[$errorCode])) {
                $ret[$index] .= $this->messages[$errorCode];
            } else {
                $ret[$index] .= $errorCode;
            }

            $ret[$index] .= $this->containerFoot;
        }

        return $ret;
    }

    /**
     * A beépített hibaüzeneteken kívűl lehetőség van új megadására adott példányon
     * belül.
     *
     * @param array $array
     * @throws Exception
     */
    public function addNewErrorMessages($array)
    {
        if (is_array($array)) {
            $this->messages = array_merge($this->messages, $array);
        } else {
            throw new \Exception("Wrong data! Is not array!");
        }
    }
}
