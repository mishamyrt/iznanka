<?php
$config = include('config.php');
$db = null;

define('IZNANKA_ROOT', getcwd());
define('IZNANKA_VERSION', '3.0.0a');
define('IZNANKA_TPL', IZNANKA_ROOT . '/system/tpl/');
define('IZNANKA_CACHE', IZNANKA_ROOT . '/caches/');
define('IZNANKA_MODULES', IZNANKA_ROOT . '/system/modules/');

class View
{
    protected static $var = array();
    protected static $patterns;
    protected static $values;
    protected static $caching;
    protected static $cacheLifetime;

    protected static $dict = array(
        '/include\s\((.[^\s\?]*)\)/'                    => 'self::display($1)',
        '/anticache\s\(\'(.[^\s\?]*)\'\)/'              => 'self::anticache(\'$1\')',
        '/if \((.*)\)/'                                 => 'if ($1){',
        '/else/'                                        => '}else{',
        '/end/'                                         => '}',
        '/#/'                                           => ' echo ',
        '/for \((.*)=(.*) to (.*)\)/'                   => 'for ($1=$2; $1 < $3; ++$1){',
        '/{{(.*) as ([^\s]+)}}/'                        => '{{foreach ($1 as &$2){}}',
        '/@([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+)/' => 'self::get(\'$1\')'
    );

    public static function init($caching, $cacheLifetime)
    {
        self::$caching = $caching;
        self::$cacheLifetime = $cacheLifetime;
        self::$patterns = array_keys(self::$dict);
        self::$values = array_values(self::$dict);
    }
    
    public static function set($name, $value)
    {
        self::$var[$name] = $value;
    }

    public static function get($name)
    {
        if (isset(self::$var[$name])) {
            return self::$var[$name];
        }
        return '';
    }

    private static function anticache($filename)
    {
        $file = IZNANKA_ROOT . $filename;
        if (file_exists($file)) {
            return $filename . '?' . filemtime($file);
        } else {
            return $filename;
        }
    }
    public static function display(string $tplPath)
    {
        $file = IZNANKA_TPL . $tplPath;
        if (file_exists($file)) {
            return self::render(file_get_contents($file), $tplPath);
        } else {
            die('There is no template '. $file);
        }
    }
    public static function render(string $template, $filename) : string
    {

        $filename = str_replace('/', '.', $filename);
        if (self::$caching){
            $cachename = IZNANKA_CACHE . $filename;
            if (file_exists($cachename) && (time() - filemtime($cachename)) <= self::$cacheLifetime)
                $code = file_get_contents($cachename);
            else {
                $code = self::codify($template);
                file_put_contents($cachename, $code);
            }
        }
        else
            $code = self::codify($template);
        ob_start();
        eval('?> ' . $code);
        return ob_get_clean();
    }

    private static function codify($content)
    {
        $lines = explode("\n", $content);
        $linesmap = array();
        $size = sizeof($lines);
        for ($j = 0; $j < $size; ++$j) {
            if (strpos($lines[$j], '}}')) {
                preg_match_all('/{{(.[^}]*)}}/', $lines[$j], $blocks);
                $bsize = sizeof($blocks[0]);
                for ($i=0; $i < $bsize; ++$i) {
                    $lines[$j] = str_replace($blocks[0][$i], preg_replace(self::$patterns, self::$values, $blocks[0][$i]), $lines[$j]);
                }
                $lines[$j] = preg_replace('/{{([^}\s]+)}}/', '<?php echo $1 ?>', $lines[$j]);
                $lines[$j] = preg_replace('/{{(.[^}]*)}}/', '<?php $1 ?>', $lines[$j]);
                if (trim(preg_replace('/<\?php(.+?)\?>/', '', $lines[$j])) === '') { //Ищем строку в которой только ПХП
                    if (strpos($lines[$j], 'echo') !== false) {
                        $lines[$j] = $lines[$j] . ' '; //В строке есть вывод, экранируем перенос строки пробелом
                    } else {
                        $lines[$j] = trim($lines[$j]); //Вывода нет, смело обрезаем
                    }
                } else {
                    $lines[$j] .= ' '; //В строке есть ХТМЛ, экранируем
                }
            }
        }
        $content = implode("\n", array_filter($lines)); //Собираем, удаляя пустые строки
        return $content;
    }
}

function connect()
{
    global $db, $config;
    if (!$db) {
        $db = new mysqli('localhost', $config['datebase-username'], $config['datebase-password'], $config['datebase-name']);
        $db->set_charset('utf8');
        if ($db->connect_errno) {
            die('Can\'t connect to datebase: ' . $db->connect_error . "\n");
        }
    }
}

function throw404()
{
    header('HTTP/1.0 404 Not Found');
    View::set('template', '404.tpl');
    View::set('title', '404');
}

function addRoute($route, $callback)
{
    global $staticRoutes, $dynamicRoutes;
    if (substr($route, -1) !== '/') {
        $route .= '/';
    }
    //TODO: Cache generated regex
    if (strpos($route, '*')) {
        $route = str_replace('/', '\/', $route);
        $route = str_replace('*', '(.*)', $route);
        $dynamicRoutes[] = array('/^'.$route.'/', $callback);
    } else {
        $staticRoutes[] = array($route, $callback);
    }
}
function addRoutes()
{
    $_routes = func_get_args();
    $size = sizeof($_routes);
    for ($i = 0; $i < $size; ++$i) {
        addRoute(array_keys($_routes[$i])[0], array_values($_routes[$i])[0]);
    }
}

function runModule($module)
{
    global $db;
    require_once IZNANKA_MODULES . $module . '.php';
}

global $db, $config, $staticRoutes, $dynamicRoutes;
if ($config['user-session'])
    session_start();
View::init($config['cache-enabled'], $config['cache-lifetime']);
$uri = $_SERVER['REQUEST_URI'];
if (substr($uri, -1) !== '/') {
    $uri .= '/';
}
define('path', explode('/', $uri));
define('uri', $uri);
unset($uri);
$includes = glob(IZNANKA_ROOT . '/system/includes/' . '*.php');
$size = sizeof($includes);
for ($i = 0; $i < $size; ++$i) {
    require ($includes[$i]);
}
unset($includes);
$size = sizeof($staticRoutes);
for ($i = 0; $i < $size; ++$i) {
    if (uri === $staticRoutes[$i][0]) {
        call_user_func($staticRoutes[$i][1]);
    }
}
if (View::get('template') === '') {
    $size = sizeof($dynamicRoutes);
    for ($i = 0; $i < $size; ++$i) {
        preg_match($dynamicRoutes[$i][0], uri, $match);
        if ($match) {
            call_user_func($dynamicRoutes[$i][1]);
        }
    }
}
if (View::get('template') === '') {
    if (uri === '/') {
        View::set('template', $config['templates-default']);
        View::set('title', $config['templates-title']);
    } else {
        throw404();
    }
}

header('X-Powered-By: Iznanka v' . IZNANKA_VERSION);
    print View::display('index.tpl');
if ($db) {
    $db->close();
}
