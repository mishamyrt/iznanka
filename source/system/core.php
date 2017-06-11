<?php
define("ROOT_DIR", getcwd());
define("iznanka_version", '2.3');
$config = include('config.php');
$db = null;
$view = null;

class View
{
    private $_path = ROOT_DIR . '/system/tpl/';
    private $_var = array();

    protected $_dict = array(
        "/include file='(.*)'/"       => '$this->_include("$1")',
        "/anticache file='(.*)'/"     => '$this->_anticache("$1")',
        "/if \((.*)\)/"               => 'if ($1){',
        "/else/"                      => '}else{',
        "/end/"                       => '}',
        "/#/"                         => ' echo ',
        '/for \((.*)=(.*) to (.*)\)/' => 'for ($1=$2; $1 < $3; ++$1){',
        "/{{(.*) as ([^\s]+)}}/"      => '{{foreach ($1 as $2){}}',
        "/@/"                         => '$this->'
    );

    public function set($name, $value)
    {
        $this->_var[$name] = $value;
    }

    public function __get($name)
    {
        if (isset($this->_var[$name])) {
            return $this->_var[$name];
        }
        return '';
    }

    private function _include($template)
    {
        $file = $this->_path . $template;
        if (!file_exists($file)) {
            die('There is no template at ' . $file);
        }
        $content = file_get_contents($file);
        echo $this->compile($content);
    }

    private function _anticache($filename)
    {
        $file = ROOT_DIR . $filename;
        if (file_exists($file)) {
            echo $filename . '?' . filemtime($file);
        } else {
            echo $filename;
        }
    }

    private function compile($content)
    {
        ob_start();
        eval('?> ' . $this->_render($content));
        return ob_get_clean();
    }

    private function _render($content)
    {
        $lines = explode("\n", $content);
        $linesmap = array();
        $patterns = array_keys($this->_dict);
        $values = array_values($this->_dict);
        for ($j = 0; $j < sizeof($lines); $j++) {
            preg_match_all("/{{(.[^}]*)}}/", $lines[$j], $blocks);
            if (sizeof($blocks[0]) > 0) {
                for ($i=0; $i < sizeof($blocks[0]); $i++) {
                    $lines[$j] = str_replace($blocks[0][$i], preg_replace($patterns, $values, $blocks[0][$i]), $lines[$j]);
                }
                $lines[$j] = preg_replace('/{{([^}\s]+)}}/', '<?php echo $1 ?>', $lines[$j]);
                $lines[$j] = preg_replace('/{{(.[^}]*)}}/', "<?php $1 ?>", $lines[$j]) . ' ';
                $linesmap[$j] = true;
            } else {
                $linesmap[$j] = false;
            }
        }
        for ($i=0; $i < sizeof($lines); $i++) {
            if ($linesmap[$i] && trim(preg_replace('/<\?php(.[^\>]*)?>/', '', $lines[$i])) == '') {
                unset ($lines[$i]);
            }
        }
        $content = implode("\n", array_filter($lines));
        return $content;
    }

    public function display($template)
    {
        $file = $this->_path . $template;
        if (!file_exists($file)) {
            die('There is no template at '. $file);
        }
        $content = $this->compile(file_get_contents($file));
        echo $content;
    }
}

function connect()
{
    global $db, $config;
    $db = new mysqli('localhost', $config['dbusername'], $config['dbpass'], $config['dbname']);
    $db->set_charset('utf8');
    if ($db->connect_errno) {
        die('Can\'t connect to datebase: ' . $db->connect_error . "\n");
    }
}

function throw404()
{
    global $view;
    header('HTTP/1.0 404 Not Found');
    $view->set('template', '404.tpl');
    $view->set('title', '404');
}

function runModule($module)
{
    global $db, $view;
    include_once ROOT_DIR . '/system/modules/' . $module . '.php';
}

function iznanka()
{
    session_start();
    global $db, $config, $view;
    $view = new View();
    define('path', explode("/", $_SERVER['REQUEST_URI']));
    define('uri', $_SERVER['REQUEST_URI']);
    $includes = glob(ROOT_DIR . '/system/includes/' . '*.php');
    for ($i=0; $i < sizeof($includes); $i++) {
        include_once ($includes[$i]);
    }
    if ($view->template === '') {
        if (uri === '/') {
            $view->set('template', $config['deftemplate']);
            $view->set('title', $config['title']);
        } else {
            throw404();
        }
    }
    header('X-Powered-By: Iznanka ' . iznanka_version);
    $view->display('index.tpl');
    if ($db) {
        $db->close();
    }
}
