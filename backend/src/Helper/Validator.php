<?php

namespace App\Helper;

use App\Core\Entity\Entity;
use Exception;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Validator osztály
 *
 */
class Validator
{

    protected $post;
    protected $rules = array();
    protected $filters = array();
    protected $defaultRules = array();
    protected $defaultFilters = array();
    protected $cleanedValues;
    protected $listOfErrors;
    protected $defaultErrors = array();

    /**
     * __construct
     *
     * @param  mixed $post
     * @return void
     */
    public function __construct(array $post = null)
    {
        if (!empty($post)) {
            $this->post = $post;
        }
    }

    /**
     * create - Gyártó metódus
     *
     * @param  mixed $post
     * @return void
     */
    public static function create(array $post = null)
    {
        return new Validator($post);
    }

    /**
     * addRule - A validáláshoz szükséges adatok megadása
     *
     * @param  mixed $fieldName
     * @param  mixed $ruleName
     * @param  mixed $params
     * @return \Validator
     */
    public function addRule($fieldName, $ruleName, $params = array())
    {
        $this->rules[$fieldName][$ruleName] = $params;
        return $this;
    }

    /**
     * A szûréshez szükséges adatok megadása
     *
     * @param string $fieldName
     * @param string $filterName
     * @param array $params
     * @return \Validator
     */
    public function addFilter($fieldName, $filterName, $params = array())
    {
        $this->filters[$fieldName][$filterName] = $params;
        return $this;
    }

    /**
     * equalsPassword - Ellenõrzi, hogy a mezõ megegyezik-e a paraméterben szereplõvel jelszó
     *
     * @param  mixed $value
     * @param  mixed $required
     * @return boolean
     */
    public static function equalsPassword($value, $required)
    {
        return ($value === $required);
    }

    /**
     * taxnumber
     *
     * @param  mixed $taxnumber
     * @return void
     */
    public static function taxnumber($taxnumber)
    {
        $value = substr($taxnumber, 0, 8);
        if (strlen($value) != 8) {
            return false;
        }
        $invalidTaxNo = ['11111111', '22222222', '33333333', '44444444', '55555555', '66666666', '77777777', '88888888', '99999999', '00000000'];
        if (in_array($value, $invalidTaxNo)) {
            return false;
        }
        $l_sum = 9 * (int) $value[0] + 7 * $value[1] + 3 * $value[2] + 1 * $value[3] + 9 * $value[4] + 7 * $value[5] + 3 * $value[6];
        $mod = ($l_sum % 10);
        if ($mod > 0) {
            $mod = 10 - $mod;
        }
        if ($value[7] != $mod) {
            return false;
        }
        return true;
    }

    /**
     * A behelyezett szabályok eltávolítása!
     * Eltávolításkor az adott mezõnévhez tartozó
     * összes meghatározás törlésre kerül!
     *
     * @param string $fieldName
     * @return \Validator
     */
    public function removeRule($fieldName)
    {
        if (isset($this->rules[$fieldName])) {
            unset($this->rules[$fieldName]);
            return $this;
        } else {
            throw new Exception("Nincs ilyen Rule!");
        }
    }

    /**
     * Egy adott mezõhöz rendelt filter eltávolítása!
     *
     * @param string $fieldName
     * @return \Validator
     */
    public function removeFilter($fieldName)
    {
        if (isset($this->filters[$fieldName])) {
            unset($this->filters[$fieldName]);
            return $this;
        } else {
            throw new Exception("Nincs ilyen Filter!");
        }
    }

    /**
     * addDefaultRule
     *
     * @param  mixed $ruleName
     * @param  mixed $params
     * @return void
     */
    public function addDefaultRule($ruleName, $params = array())
    {
        $this->defaultRules[$ruleName] = $params;
        return $this;
    }

    /**
     * A default filter megadása, mely minden ellenõrízendõ
     * mezõn értelmezve lesz!
     *
     * @param strgin $filterName
     * @param array $params
     * @return \Validator
     */
    public function addDefaultFilter($filterName, $params = array())
    {
        $this->defaultFilters[$filterName] = $params;
        return $this;
    }

    /**
     * removeDefaultRule
     *
     * @param  mixed $ruleName
     * @return void
     */
    public function removeDefaultRule($ruleName)
    {
        if (isset($this->defaultRules[$ruleName])) {
            unset($this->defaultRules[$ruleName]);
            return $this;
        } else {
            throw new Exception("Nincs ilyen defaultRule!");
        }
    }

    /**
     * removeDefaultFilter
     *
     * @param  mixed $filterName
     * @return void
     */
    public function removeDefaultFilter($filterName)
    {
        if (isset($this->defaultFilters[$filterName])) {
            unset($this->defaultFilters[$filterName]);
            return $this;
        } else {
            throw new Exception("Nincs ilyen defaultFilter!");
        }
    }

    /**
     * Az filter által validálásra küldött adatok
     * lekérése!
     *
     * @return array
     */
    public function getCleanedValues()
    {
        return $this->cleanedValues;
    }

    /**
     * Validálás során keletkezett hibák lekérése!
     *
     * @return array
     */
    public function getListOfErrors()
    {
        return $this->listOfErrors;
    }

    /**
     * Adatok slashelése mysql_real_escape_string függvénnyel
     * @param mixed $str Sima string vagy tömb
     * @return mixed
     */
    public static function addslashes($str)
    {
        if (is_array($str)) {
            foreach ((array) $str as $k => $v) {
                $str[$k] = self::addslashes($v);
            }
            return $str;
        }

        return addslashes($str);
    }

    /**
     * Adathalmazból tagek eltávolítása.
     *
     * @param mixed $str string vagy array
     * @return mixed
     */
    public static function stripTags($str)
    {
        if (is_array($str)) {
            foreach ((array) $str as $k => $v) {
                $str[$k] = self::stripTags($v);
            }
            return $str;
        }
        return strip_tags($str);
    }

    /**
     * Felesleges szóközkarakterek eldobása
     * @param mixed $str string vagy array
     * @return mixed
     */
    public static function trim($str)
    {
        if (is_array($str)) {
            foreach ((array) $str as $k => $v) {
                $str[$k] = self::trim($v);
            }
            return $str;
        }
        return trim($str);
    }

    /**
     * Ellenõrzi, hogy egy mezõ nem üres
     *
     * @return  boolean
     */
    public static function notEmpty($value)
    {
        if (is_object($value) and $value instanceof \ArrayObject) {
            // Kérjük a tömböt az ArrayObjectbõl
            $value = $value->getArrayCopy();
        }

        // Érték nem lehet NULL, FALSE, '', vagy üres tömb
        return !in_array($value, array(null, false, '', array()), true);
    }

    /**
     * CSV ben érkező számoknál használt , cseréje . ra
     * @param string $value
     * @return string
     */
    public static function unFormatCSVPrice($value)
    {
        $value = str_replace(' ', '', $value);
        return str_replace(',', '.', $value);
    }

    /**
     * Adott változó értéke üres
     *
     * Empty függvény aliasa
     *
     * @param mixed $value
     * @return boolean
     */
    public static function isEmpty($value)
    {
        return empty($value);
    }

    /**
     * Adott string adott hosszúságó e!
     *
     * @param string $value
     * @param int $length
     * @return bool
     */
    public static function maxLength($value, $length)
    {
        return mb_strlen($value, 'UTF-8') <= $length;
    }

    /**
     * Adott srting megfeleõen hosszú e!
     *
     * @param string $value
     * @param int $length
     * @return bool
     */
    public static function minLength($value, $length)
    {
        return mb_strlen($value, 'UTF-8') >= $length;
    }

    /**
     * exactLength - Ellenõrzi, hogy a mezõ karakterhossza megegyezik-e a paraméteben szereplõvel
     *
     * @param  mixed $value
     * @param  mixed $length
     * @return void
     */
    public static function exactLength($value, $length)
    {
        return strlen($value) === $length;
    }

    /**
     * equals - Ellenõrzi, hogy a mezõ megegyezik-e a paraméterben szereplõvel
     *
     * @param  mixed $value
     * @param  mixed $required
     * @return void
     */
    public static function equals($value, $required)
    {
        return ($value === $required);
    }

    /**
     * regex - Regex-el ellenõrzi a mezõt
     *
     * @param  mixed $value
     * @param  mixed $expression
     * @return void
     */
    public static function regex($value, $expression)
    {
        return (bool) preg_match($expression, (string) $value);
    }

    /**
     * msn
     *
     * @param  mixed $value
     * @return void
     */
    public static function msn($value)
    {
        return (bool) preg_match('/(^\w[\-\.\w]*@([\w-]+\.)*\w+[\w-]*\.([a-zA-Z]{2,4})$|^$)/', $value);
    }

    /**
     * email
     *
     * @param  mixed $email
     * @param  mixed $strict
     * @param  mixed $checkDNS
     * @return void
     */
    public static function email($email, $strict = false, $checkDNS = true)
    {
        if ($strict === true) {
            $qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
            $dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
            $atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
            $pair = '\\x5c[\\x00-\\x7f]';

            $domain_literal = "\\x5b($dtext|$pair)*\\x5d";
            $quoted_string = "\\x22($qtext|$pair)*\\x22";
            $sub_domain = "($atom|$domain_literal)";
            $word = "($atom|$quoted_string)";
            $domain = "$sub_domain(\\x2e$sub_domain)*";
            $local_part = "$word(\\x2e$word)*";

            $expression = "/^$local_part\\x40$domain$/D";
        } else {
            $expression = '/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD';
        }

        $emailParts = explode("@", $email);
        $domain = array_pop($emailParts);
        return (bool) preg_match($expression, (string) $email) && (!$checkDNS || checkdnsrr($domain, "MX"));
    }

    /**
     * url
     *
     * @param  mixed $url
     * @return void
     */
    public static function url($url)
    {
        // http://www.apps.ietf.org/rfc/rfc1738.html#sec-5
        if (!preg_match(
            '~^

				# scheme
				[-a-z0-9+.]++://

				# username:password (optional)
				(?:
						[-a-z0-9$_.+!*\'(),;?&=%]++   # username
					(?::[-a-z0-9$_.+!*\'(),;?&=%]++)? # password (optional)
					@
				)?

				(?:
					# ip address
					\d{1,3}+(?:\.\d{1,3}+){3}+

					| # or

					# hostname (captured)
					(
								(?!-)[-a-z0-9]{1,63}+(?<!-)
						(?:\.(?!-)[-a-z0-9]{1,63}+(?<!-)){0,126}+
					)
				)

				# port (optional)
				(?::\d{1,5}+)?

				# path (optional)
				(?:/.*)?

				$~iDx',
            $url,
            $matches
        )) {
            return false;
        }

        if (!isset($matches[1])) {
            return true;
        }

        // http://en.wikipedia.org/wiki/Domain_name#cite_note-0
        if (strlen($matches[1]) > 253) {
            return false;
        }

        $tld = ltrim(substr($matches[1], (int) strrpos($matches[1], '.')), '.');
        return ctype_alpha($tld[0]);
    }

    /**
     * phone
     *
     * @param  mixed $number
     * @param  mixed $lengths
     * @return void
     */
    public static function phone($number, $lengths = null)
    {
        if (!is_array($lengths)) {
            $lengths = array(7, 8, 9, 10, 11);
        }

        $number = preg_replace('/\D+/', '', $number);

        return in_array(strlen($number), $lengths);
    }

    /**
     * date - Dátum validálás
     *
     * @param  mixed $str
     * @return void
     */
    public static function date($str)
    {

        if ($str === '') {
            return true;
        }

        return (strtotime($str) !== false);
    }

    /**
     * alpha - Ellenõrzi, hogy a string csak betûket tartalmaz
     *
     * @param  mixed $str
     * @param  mixed $utf8
     * @return void
     */
    public static function alpha($str, $utf8 = false)
    {
        $str = (string) $str;

        if ($utf8 === true) {
            return (bool) preg_match('/^\pL++$/uD', $str);
        } else {
            return ctype_alpha($str);
        }
    }

    /**
     * alphaNumeric - Ellenõrzi, hogy a string csak betûket ,számokat tartalmaz
     *
     * @param  mixed $str
     * @param  mixed $utf8
     * @return void
     */
    public static function alphaNumeric($str, $utf8 = false)
    {
        if ($utf8 === true) {
            return (bool) preg_match('/^[\pL\pN]++$/uD', $str);
        } else {
            return ctype_alnum($str);
        }
    }

    /**
     * alphaDash - Ellenõrzi, hogy a string csak betûket, számokat, aláhúzás vonalat, kötõjelet tartalmaz
     *
     * @param  mixed $str
     * @param  mixed $utf8
     * @return void
     */
    public static function alphaDash($str, $utf8 = false)
    {
        if ($utf8 === true) {
            $regex = '/^[-\pL\pN_]++$/uD';
        } else {
            $regex = '/^[-a-z0-9_]++$/iD';
        }

        return (bool) preg_match($regex, $str);
    }

    /**
     * number - Ellenõrzi, hogy a string csak számokat tartalmaz
     *
     * @param  mixed $str
     * @param  mixed $utf8
     * @return void
     */
    public static function number($str, $utf8 = false)
    {
        if ($utf8 === true) {
            return (bool) preg_match('/^\pN++$/uD', $str);
        } else {
            return (is_int($str) and $str >= 0) or ctype_digit($str);
        }
    }

    /**
     * range - A szám a megadott intervallumon belül van
     *
     * @param  mixed $number
     * @param  mixed $min
     * @param  mixed $max
     * @return void
     */
    public static function range($number, $min, $max)
    {
        return ($number >= $min and $number <= $max);
    }

    /**
     * Ellenõrzi, hogy a string valid html szín-e.,  Nem szükséges a # prefixum, illetve támogatja a 3 számjegyes megadási formátumot is a 6 hrlyett
     *
     * @param  mixed $str
     * @return void
     */
    public static function color($str)
    {
        return (bool) preg_match('/^#?+[0-9a-f]{3}(?:[0-9a-f]{3})?$/iD', $str);
    }

    /**
     * creditCard - Bank kártya szám validálás, Luhn algoritmussal
     *
     * @param  mixed $number
     * @param  mixed $type
     * @return void
     */
    public static function creditCard($number, $type = null)
    {
        // Remeljük az összes nem decimális karaktert
        if (($number = preg_replace('/\D+/', '', $number)) === '') {
            return false;
        }

        if ($type == null) {
            // Default típust használjuk
            $type = 'default';
        } elseif (is_array($type)) {
            foreach ($type as $t) {
                // Ellenõrízzük az összes típus validitását
                if (self::creditCard($number, $t)) {
                    return true;
                }
            }

            return false;
        }

        //TODO :: kártyák típusok behúzása configból
        $cards = array(
            'default' => array(
                'length' => '13,14,15,16,17,18,19',
                'prefix' => '',
                'luhn' => true,
            ),
            'american express' => array(
                'length' => '15',
                'prefix' => '3[47]',
                'luhn' => true,
            ),
            'maestro' => array(
                'length' => '16,18',
                'prefix' => '50(?:20|38)|6(?:304|759)',
                'luhn' => true,
            ),
            'mastercard' => array(
                'length' => '16',
                'prefix' => '5[1-5]',
                'luhn' => true,
            )
        );

        // Kártya típus ellenõrzés
        $type = strtolower($type);

        if (!isset($cards[$type])) {
            return false;
        }

        // Kártyaszám hossz ellenõrzés
        $length = strlen($number);

        // Kártyaszám hossz validálás kártyatípus szerint
        if (!in_array($length, preg_split('/\D+/', $cards[$type]['length']))) {
            return false;
        }

        // Kártya szám prefix ellenõrzés
        if (!preg_match('/^' . $cards[$type]['prefix'] . '/', $number)) {
            return false;
        }

        // Nincs szükség Luhn ellenõrzésre
        if ($cards[$type]['luhn'] == false) {
            return true;
        }

        return self::luhn($number);
    }

    /**
     * luhn - Validálás Luhn algoritmussal [Luhn](http://en.wikipedia.org/wiki/Luhn_algorithm) (mod10) formula.
     *
     * @param  mixed $number
     * @return void
     */
    public static function luhn($number)
    {
        // TypeCast-olunk stringre, hogy lehessen string függvényeket használni
        $number = (string) $number;

        if (!ctype_digit($number)) {
            // Luhn - t csak számokkal lehet használni
            return false;
        }

        $length = strlen($number);

        $checksum = 0;

        for ($i = $length - 1; $i >= 0; $i -= 2) {
            $checksum += substr($number, $i, 1);
        }

        for ($i = $length - 2; $i >= 0; $i -= 2) {
            $double = substr($number, $i, 1) * 2;

            $checksum += ($double >= 10) ? ($double - 9) : $double;
        }

        return ($checksum % 10 === 0);
    }

    /**
     * Annak ellenőrzése, hogy a kapott érték double érték e
     *
     * @param double $number
     * @return boolean
     */
    public static function isDouble($number)
    {

        if ($number === '') {
            return true;
        }
        if (filter_var($number, FILTER_VALIDATE_FLOAT) !== false) {
            return true;
        }
        return false;
    }

    /**
     * Annak ellenőrzése, hogy a kapott érték egész szám e
     *
     * @param double $number
     * @return boolean
     */
    public static function isInt($number)
    {

        if ($number === '') {
            return true;
        }

        if (filter_var($number, FILTER_VALIDATE_INT) !== false) {
            return true;
        }
        return false;
    }

    /**
     * isDate - Annak ellenőrzése, hogy a kapott érték egész szám e
     *
     * @param  mixed $date
     * @param  mixed $format
     * @return void
     */
    public static function isDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * ip - IP validálás
     *
     * @param  mixed $ip
     * @param  mixed $allow_private
     * @return void
     */
    public static function ip($ip, $allow_private = true)
    {
        $flags = FILTER_FLAG_NO_RES_RANGE;

        if ($allow_private === false) {
            $flags = $flags | FILTER_FLAG_NO_PRIV_RANGE;
        }

        return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flags);
    }

    /**
     * A filtert és a validálást meghívó metódus!
     *
     * @param array $values
     * @return bool
     */
    public function clearAndValidate(array $values = null)
    {
        $this->cleanedValues = array();
        $this->listOfErrors = $this->defaultErrors;

        if (empty($values)) {
            $values = $this->post;
        }

        $this->cleanedValues = $this->filterChecker($values);
        return $this->validationChecker($this->cleanedValues);
    }

    /**
     * validationChecker - Ez a metódus végig megy a megadott szabályokon melyek adott indexhez vannak rendelve a kapott értéktömbben!
     *
     * @param  mixed $values
     * @return void
     */
    protected function validationChecker($values)
    {

        if (count($this->rules) == 0 && count($this->defaultRules) == 0) {
            return true;
        }

        foreach ((array) $values as $index => $value) {
            if (isset($this->rules[$index]) && is_array((array) $this->defaultRules)) {
                $fullRules = array_merge($this->rules[$index], $this->defaultRules);
            } elseif (isset($this->rules[$index]) && !isset($this->rules[$index])) {
                $fullRules = $this->rules[$index];
            } else {
                $fullRules = $this->defaultRules;
            }

            foreach ($fullRules as $ruleName => $params) {
                array_unshift($params, $values[$index]);
                if (!$this->methodCaller($ruleName, $params)) {
                    $this->listOfErrors[$index][] = $ruleName;
                    break;
                }
            }
        }

        $diff = array_diff(array_keys($this->rules), array_keys((array) $values));

        if (count($diff) > 0) {
            foreach ($diff as $index) {
                $this->listOfErrors[$index][] = key($this->rules[$index]);
            }
        }

        if (count($this->listOfErrors) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * A validátorhoz pluszba hibák bepakolása
     * Pl az olyan hibák mint foglalt email. Nem a validátor ellenőrzi,
     * de a hibák egységes kezelése miatt ide bepakolja a hibát!
     */
    public function putDefaultError($index, $message)
    {
        $this->defaultErrors[$index][] = $message;
    }

    /**
     * filterChecker
     * Adott érték tömbön végig megy és minden egyes értékén végrehajta a kiválasztott mûveletett!
     *
     * Ha van megadva default és normal filter, akkor ezeket egy tömbbe pakolva, majd a tömmbben lévõ
     * minden egyes metódusut behívva végrehajtja a kiválaszott mûveletet az adott értéken!
     *
     * Ha csak normál filter van, akkor akkor az adott mezõhöz hozzá rendelt filtereket futtatja!
     *
     * Ha csak defaultFilter van akkor minden értéken végrehajta a defaultFiltereket!
     *
     * @param  mixed $values
     * @return void
     */
    protected function filterChecker($values)
    {

        if (count($this->filters) == 0 && count($this->defaultFilters) == 0) {
            return $values;
        }

        foreach ($values as $index => $value) {
            if (isset($this->filters[$index]) && is_array($this->defaultFilters)) {
                $fullFilter = array_merge($this->filters[$index], $this->defaultFilters);
            } elseif (isset($this->filters[$index]) && !isset($this->filters[$index])) {
                $fullFilter = $this->filters[$index];
            } else {
                $fullFilter = $this->defaultFilters;
            }

            foreach ($fullFilter as $filterName => $params) {
                array_unshift($params, $values[$index]);
                $values[$index] = $this->methodCaller($filterName, $params);
            }
        }

        return $values;
    }

    /**
     * methodCaller
     *
     * @param  mixed $methodName
     * @param  mixed $params
     * @return void
     */
    protected function methodCaller($methodName, $params)
    {

        if (method_exists($this, $methodName)) {
            $method = new ReflectionMethod($this, $methodName);
            if ($method->isStatic()) {
                return $method->invokeArgs(null, $params);
            } else {
                return call_user_func_array(array($this, $methodName), $params);
            }
            //Egyedi metódus / függvény hívás (akár más osztályból is)
        } elseif (strpos($methodName, '::') === false) {
            $function = new ReflectionFunction($methodName);
            return $function->invokeArgs($params);
        } else {
            list($class, $method) = explode('::', $methodName, 2);
            $method = new ReflectionMethod($class, $method);

            // $Class::$method($this[$field], $params, ...) Reflection
            return $method->invokeArgs(null, $params);
        }
    }

    /**
     * __call overload
     *
     * @param  mixed $name
     * @param  mixed $args
     * @return void
     */
    public function __call($name, $args)
    {
        throw new Exception("nincs [$name(" . var_export($args, true) . ")] metódus!");
    }
}
