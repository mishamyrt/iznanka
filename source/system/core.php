<?php
define("ROOT_DIR", getcwd());
define("iznanka_version", '2.2');
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
        eval('?>' . $this->_render($content));
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

    public function compile($path)
    {
        ob_start();
        $content = file_get_contents($path);
        eval('?> ' . $this->_render($content));
        return ob_get_clean();
    }

    private function _compile($content)
    {
        ob_start();
        eval('?> ' . $this->_render($content));
        return ob_get_clean();
    }
    private function _render($content)
    {
        $patterns = array_keys($this->_dict);
        $values = array_values($this->_dict);
        preg_match_all("/{{(.[^}]*)}}/", $content, $blocks);
        foreach ($blocks[0] as $block) {
            $content = str_replace($block, preg_replace($patterns, $values, $block), $content);
        }
        $content = preg_replace('/{{([^}\s]+)}}/', '<?php echo $1 ?>', $content);
        $content = preg_replace('/{{(.[^}]*)}}/', "<?php $1 ?>", $content);
        return $content;
    }

    public function display($template)
    {
        $file = $this->_path . $template;
        if (!file_exists($file)) {
            die('There is no template at '. $file);
        }
        $content = $this->_compile(file_get_contents($file));
        echo $content;
    }
}

function connect()
{
    global $db, $config;
    $db = new mysqli("localhost", $config['dbusername'], $config['dbpass'], $config['dbname']);
    $db->set_charset("utf8");
    if ($db->connect_errno) {
        die("Can't connect to datebase: " . $db->connect_error . "\n");
    }
}
function throw404()
{
    header("HTTP/1.0 404 Not Found");
    $view->set('template', '404.tpl');
    $view->set('title', '404');
    $view->set('error', true);
}
function iznanka()
{
    session_start();
    global $db, $config, $view;
    $view = new View();
    $view->set('template', ' ');
    $view->set('path', explode("/", $_SERVER["REQUEST_URI"]));
    $view->set('uri', $_SERVER['REQUEST_URI']);
    $includes = glob(ROOT_DIR . '/system/includes/' . '*.php');
    for ($i=0; $i < sizeof($includes); $i++) {
        include_once ($includes[$i]);
    }
    if ($view->uri == '/' && $view->template == ' ') {
        $view->set('template', $config['deftemplate']);
        $view->set('title', $config['title']);
    } elseif ($view->template == ' ') {
    }
    header('X-Powered-By: Iznanka '.iznanka_version);
    $view->display('index.tpl');
    if ($db) {
        $db->close();
    }
}
function runModule($module)
{
    global $db, $view;
    include_once ROOT_DIR . '/system/modules/' . $module . '.php';
}
