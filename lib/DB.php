<?php

/**
 * Class DB (https://github.com/nikic/DB)
 * Simple database wrapper for PDO
 * example
 * DB::q(
 * 'SELECT * FROM user WHERE group = ?s AND points > ?i AND id IN ?a',
 * 'user', 7000, [2, 3] //           ^^              ^^           ^^
 * )->fetchAll(PDO::FETCH_ASSOC)
 */
class DB
{
    protected static $instance = null;

    private function __construct() {}
    private function __clone() {}

    /**
     * @param bool $exception
     * @return PDO|PDOException
     */
    public static function instance($exception = false)
    {
        if (self::$instance === null) {
            try {
                $dsn = defined('DB_DSN')
                    ? DB_DSN
                    : 'mysql:host=' . DB_HOST
                    . (defined('DB_NAME') ? ';dbname=' . DB_NAME : '')
                    . (defined('DB_CHAR') ? ';charset=' . DB_CHAR : '');
                self::$instance = new PDO($dsn, DB_USER, DB_PASS);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                if ($exception) return $e;
                die('Database connection could not be established.' . 'mysql:host=' . DB_HOST
                    . (defined('DB_NAME') ? ';dbname=' . DB_NAME : ''));
            }
        }

        return self::$instance;
    }

    /**
     * @return PDOStatement
     */
    public static function q($query)
    {
        if (func_num_args() == 1) {
            return self::instance()->query($query);
        }

        $args = func_get_args();
        return self::instance()->query(self::autoQuote(array_shift($args), $args));
    }

    public static function x($query)
    {
        if (func_num_args() == 1) {
            return self::instance()->exec($query);
        }

        $args = func_get_args();
        return self::instance()->exec(self::autoQuote(array_shift($args), $args));
    }

    public static function autoQuote($query, array $args)
    {
        $i = strlen($query) - 1;
        $c = count($args);

        while ($i--) {
            if ('?' === $query[$i] && false !== $type = strpos('sia', $query[$i + 1])) {
                if (--$c < 0) {
                    throw new InvalidArgumentException('Too little parameters.');
                }

                if (0 === $type) {
                    $replace = self::instance()->quote($args[$c]);
                } elseif (1 === $type) {
                    $replace = intval($args[$c]);
                } elseif (2 === $type) {
                    foreach ($args[$c] as &$value) {
                        $value = self::instance()->quote($value);
                    }
                    $replace = '(' . implode(',', $args[$c]) . ')';
                }

                $query = substr_replace($query, $replace, $i, 2);
            }
        }

        if ($c > 0) {
            throw new InvalidArgumentException('Too many parameters.');
        }

        return $query;
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }
}
