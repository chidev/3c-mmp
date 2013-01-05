 <? 
/*******************************************************************************************
                            Script: CLASS debugTool v.1.0
                            Date relased: 08/05/2008                                      
                            Developed by Andreas Christodoulou                         
                            Description: The aim of this class is to speed up the 
                                         process of the development part by having a debuging
                                         tool on the fly for checking up different variables
                                         and more.         
*********************************************************************************************/
class debugTools
{
    private $menuElements = array();
    private $menuElementsName = array();
    private $menuContent = array();
    private $elementPrefix = NULL;

    public function debugTools($pre = "el_")
    {
        if ($_GET['ca']) $this->clearall();
        if ($_GET['cs']) $this->clearSessions();
        if ($_GET['cp']) $this->clearRequests();
        if ($_GET['cc']) $this->clearCookies();
        
        $cs  = "<a href='" . basename( $_SERVER['PHP_SELF'] ) . "?ca=1'>CLEAR ALL</a> | ";
        $cs .= "<a href='" . basename( $_SERVER['PHP_SELF'] ) . "?cs=1'>CLEAR SESSIONS</a> | ";
        $cs .= "<a href='" . basename( $_SERVER['PHP_SELF'] ) . "?cp=1'>CLEAR REQUESTS</a> |";
        $cs .= "<a href='" . basename( $_SERVER['PHP_SELF'] ) . "?cc=1'>CLEAR COOKIES</a>";
        
        $dc = get_defined_constants(true);
        $uc = $dc['user'];
        
        $uf = get_defined_functions();
        $uf = $uf['user'];
    
        $this->setElementPrefix($pre);
        $this->setMenuElement("TOOLS", "11", $cs);
        $this->setMenuElement("SERVER"    , "7"    , $_SERVER);
        $this->setMenuElement("SESSIONS", "1"    , $_SESSION);    
        $this->setMenuElement("REQUESTS", "2"    , $_REQUEST);
        $this->setMenuElement("GET"        , "3"     , $_GET);
        $this->setMenuElement("POST"    , "4"    , $_POST);
        $this->setMenuElement("COOKIES"    , "5"    , $_COOKIE);
        $this->setMenuElement("FILES"    , "6"    , $_FILES);
        $this->setMenuElement("ENV"        , "8"    , $_ENV);
        $this->setMenuElement("REQUIRED & INCLUDE", "9", get_included_files());    
        $this->setMenuElement("DEFINE CONSTANTS", "10", $uc);    
        //$this->setMenuElement("USER FUNCTIONS", "12", $uf);    
        $this->showDebugTools(); 
    }
    
    public function getSessions() { return $_SESSION; }
    public function getRequests() { return $_REQUEST; }
    public function getPrefix()   { return $this->elementPrefix; }
    public function getElementName($val) { return $this->menuElementsName[$val]; }
    
    public function setElementPrefix($pre) { $this->elementPrefix = $pre; }
    
    public function setMenuElement($name, $id, $content) 
    {
        $val = "<a href=\"#\" onclick=\"Effect.toggle('" . $this->elementPrefix . $id . "','slide'); return false;\" style='text-decoration: none'>$name</a>";
        
        $this->menuElementsName[$id] = $name;
        $this->menuElements[$id] = $val;
        
        $this->setMenuContent($id, $content);
    }
    
    public function setMenuContent($id, $content)
    {
        $values = "";
        $count  = 0;
        
        if ( is_array($content) )
        {
            $count = sizeof($content);
            
            foreach($content as $k => $v)
            {
                if (is_array($k))
                {
                    foreach($content as $k1 => $v1)
                    {
                        $v1 = $this->viewSource($v1);
                        $values .= "&nbsp;&nbsp;&raquo; <b>" . $k1 . "</b> => " . $v1 . "<br />";
                    }
                }
                else
                {
                    $v = $this->viewSource($v);
                    $values .= "&raquo; <b>" . $k . "</b> => " . $v . "<br />";
                }
            }
            $content = $values;
        }
        else if (strstr($content, "form")) {$content = $content; }
        else if (is_file($content)) { $content = $this->viewSource($content); }
        else { if ($content) $content = "&raquo; " . $content; }
        
        $count = ($count) ? $count : 0;

        $val  = "<div id=\"" . $this->elementPrefix . $id . "\" style=\"display:none; margin-left: 10px;\">\n";
        $val .= "<div style=\"background-color: #DEEBED; border:1px solid #ddd; padding:10px; color: #333 \">\n
                    <div style='width:400px; color: blue; font-size: 14px; font-family: Verdana;'>\n
                    <b><a href=\"#\" onmouseover=\"Tip('hide me')\" onclick=\"Effect.toggle('" . $this->elementPrefix . $id . "','slide'); return false;\" style='text-decoration: none'>" . $this->getElementName($id) . " (" . $count . ")</a></b>
                    </div>\n<br />$content</div>\n";
        $val .= "</div>\n";
        $this->menuContent[$id] = $val;
        
    }
    
    public function showDebugTools()
    {
        if (!file_exists("images/show.png")) $dir = "debugTool/";
        
        echo "<script type=\"text/javascript\" src=\"" . $dir . "js/wz_tooltip.js\"></script>";
        
        echo "<div id=\"revertbox1\" class=\"box1\" style='position: absolute; top: 2px; left: 2px; z-index: 1000; background-color: #ffffcc; padding: 5px; padding-top: 10px; filter:alpha(opacity=90);-moz-opacity:.90; opacity:.90; border: 1px solid #ddd;'>
        
                <span id=\"handle1\" style='border: 1px solid #ddd; padding-top:2px; padding-left:2px;'>
                    <input type=\"hidden\" id=\"shouldrevert1\"/>
                    <img src='".$dir."images/move_arrow.png' border='0'  />
                </span>
                
                <a href=\"#\" onclick=\"Effect.Appear('onoff'); return false;\" style='border: 1px solid #ddd; text-decoration:none; padding-top:2px; padding-left:2px;'>
                    <img src='".$dir."images/show.png' border='0' />
                </a>
                
                <a href=\"#\" onclick=\"Effect.Fade('onoff'); return false;\" style='border: 1px solid #ddd; text-decoration:none; padding-top:2px; padding-left:2px;'>
                    <img src='".$dir."images/hide.png' border='0' />
                </a>
                
                <div id='onoff' style=\"float: left; display: none;  filter:alpha(opacity=90);-moz-opacity:.90;opacity:.90; font-size: 12px; 
                                        font-family: Verdana; margin-top: 5px;\">";
                
                echo "<ul style='float: left; margin-left: 1px; background-color: #DEEBED; padding: 10px; list-style:none;'>";    
                
                foreach($this->menuElements as $k => $v)
                {
                    echo "<li>&raquo;&nbsp;" . $v . "</li>";
                }
                
                //echo "<li>&raquo;&nbsp;<a href=\"#\" style='text-decoration: none;'
                //onclick=\"Effect.toggle('keywordFinder','slide'); return false;\">KEYWORD FINDER</a></li>";
                
                echo "</ul>";
                
                echo "<div id='onoff' style='float: left; padding-right: 10px; padding-bottom: 10px;'>";
                
                foreach($this->menuContent as $k => $v)
                {
                     echo $v;
                }
                
                //echo "<div id='keywordFinder' style='display:none; margin-left: 10px;'><div>";
                //include ("keywordFinder/index.php");
                
                echo "</div></div>";
                echo "</div></div></div>";
        ?>
        <script type="text/javascript" language="javascript" charset="utf-8">
        // <![CDATA[
          new Draggable('revertbox1',{scroll:window,handle:'handle1',revert:function(element){return ($('shouldrevert1').checked)}});
        // ]]>
        </script>
        <?
    }
    
    public function viewSource($value)
    {
        $max = 512000; // 1 kb = 1024 bytes
        $ext = array("php", "inc","txt", "html");
            
        if ( is_file($value) && filesize($value) < $max)  
        {    
            $fext = substr($value, -3);
        
            if ( in_array($fext, $ext) )
            {
                if (file_exists("view_sourcecode.php")) $dir = "";
                else $dir = "debugTool/";    
                $val = "<a href='" . $dir . "view_sourcecode.php?filename=" . $value . "' target='_blank'>$value</a>";
            }
            else $val = $value;
            
        }
        else $val = strip_tags($value);
        
        return $val;
    }

    public function clearall() 
    {
        $this->clearSessions();
        $this->clearRequests();
        $this->clearCookies();
    }
    public function clearSessions(){ unset($_SESSION); }
    public function clearRequests(){ unset($_REQUEST); }
    public function clearCookies(){ unset($_COOKIE); }

}

$dt = new debugTools();

?> 