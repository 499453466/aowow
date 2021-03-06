<?php

if (!defined('AOWOW_REVISION'))
    die('illegal access');


class AjaxHandler
{
    protected $validParams = [];
    protected $params      = [];
    protected $handler;

    protected $contentType = 'application/x-javascript; charset=utf-8';

    protected $_post       = [];
    protected $_get        = [];

    public    $doRedirect = false;

    public function __construct(array $params)
    {
        $this->params = $params;

        foreach ($this->_post as $k => &$v)
            $v = isset($_POST[$k]) ? filter_input(INPUT_POST, $k, $v[0], $v[1]) : null;

        foreach ($this->_get  as $k => &$v)
            $v = isset($_GET[$k])  ? filter_input(INPUT_GET,  $k, $v[0], $v[1]) : null;
    }

    public function handle(&$out)
    {
        if (!$this->handler)
            return false;

        if ($this->validParams)
        {
            if (count($this->params) != 1)
                return false;

            if (!in_array($this->params[0], $this->validParams))
                return false;
        }

        $h   = $this->handler;
        $out = (string)$this->$h();

        return true;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    protected function checkEmptySet($val)
    {
        return $val === '';                                 // parameter is expected to be empty
    }

    protected function checkLocale($val)
    {
        if (preg_match('/^'.implode('|', array_keys(array_filter(Util::$localeStrings))).'$/', $val))
            return intval($val);

        return null;
    }

    protected function checkInt($val)
    {
        if (preg_match('/^-?\d+$/', $val))
            return intval($val);

        return null;
    }

    protected function checkIdList($val)
    {
        if (preg_match('/^-?\d+(,-?\d+)*$/', $val))
            return array_map('intval', explode(',', $val));

        return null;
    }

    protected function checkFulltext($val)
    {
        // trim non-printable chars
        return preg_replace('/[\p{C}]/ui', '', $val);
    }
}
?>
