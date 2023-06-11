<?php

namespace system\core\abstracts\mvc;

use system\core\abstracts\traits\patterns\Singleton;
use system\core\abstracts\traits\TraitSetGetForClass;

/**
 * Class View
 *
 * @property string text
 *
 * @package system\core\abstracts\mvc
 */
class View
{
    use Singleton;
    use TraitSetGetForClass;

    public function setText(string $text = ''): self
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): string
    {
        return ((!empty($this->text)) ? $this->text : '');
    }

    public static function set(string $text = '')
    {
        /** @var View $view */
        $view = View::getInstance();

        $view->setText($text);
    }

    public static function get(): string
    {
        /** @var View $view */
        $view = View::getInstance();

        return $view->getText();
    }
}