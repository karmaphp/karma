<?php namespace Karma\Service;

class SmartyService
{
    /**
     * @var \Smarty
     */
    protected $smarty;

    public function __construct()
    {
        $this->smarty = new \Smarty();
    }

    public function fetch($template, array $params = [])
    {
        return $this->smarty
            ->assign($params)
            ->fetch($template);
    }
}
