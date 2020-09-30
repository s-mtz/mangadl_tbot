<?php

class I18n
{
    /**
     * Fa_ir
     * En_us
     *
     * @var [type]
     */
    private static $lang;
    private static $messages;

    public static function set_language($_lang)
    {
        self::$lang = $_lang;
        switch ($_lang) {
            case 'En_us':
                include ABSPATH . "i18n/En_us/messages_En_us.php";
                self::$messages = $messages;
                break;

            case 'Fa_ir':
                include ABSPATH . "i18n/Fa_ir/messages_Fa_ir.php";
                self::$messages = $messages;
                break;

            default:
                self::$messages = [];
                break;
        }
    }

    public static function get($_key)
    {
        if (isset(self::$messages[$_key])) {
            return self::$messages[$_key];
        }
        return "Undefined";
    }
}
