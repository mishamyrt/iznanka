<?php
define("ROOT_DIR", getcwd());
define("iznanka_version", '2.2');

class view
{
    private $_path = ROOT_DIR . '/system/tpl/';
    private $_template;
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
        $content = file_get_contents($this->_path . $template);
        eval('?>' . $this->_render($content));
    }

    private function _anticache($filename)
    {
        $file = ROOT_DIR . $filename;
        if (file_exists($file))
            echo $filename . '?' . filemtime($file);
        else
            echo $filename;
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
        $this->_template = $this->_path . $template;
        if (!file_exists($this->_template)) {
            die('Шаблона ' . $this->_template . ' не существует!');
        }
        $content = $this->_compile(file_get_contents($this->_template));
        echo $content;
    }
}

$config = include('config.php');
$db = null;
$view = null;

function connect()
{
    global $db, $config;
    $db = new mysqli("localhost", $config['dbusername'], $config['dbpass'], $config['dbname']);
    $db->set_charset("utf8");
    if ($db->connect_errno) {
        die("Не удалось подключиться: " . $db->connect_error . "\n");
    }
}
function iznanka()
{
    session_start();
    global $db, $config, $view;
    $view = new view(false);
    $view->set('template', ' ');
    $view->set('path', explode("/", $_SERVER["REQUEST_URI"]));
    $view->set('uri', $_SERVER['REQUEST_URI']);
    foreach (glob(ROOT_DIR . '/system/includes/' . "*.php") as $php_file) {
        include_once ($php_file);
    }
    if ($view->uri == '/' && $view->template == ' ') {
        $view->set('template', $config['deftemplate']);
        $view->set('title', $config['title']);
    } elseif ($view->template == ' ') {
        header("HTTP/1.0 404 Not Found");
        $view->set('template', '404.tpl');
        $view->set('title', '404');
        $view->set('error', true);
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
