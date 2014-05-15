<?php
$dbname = 'cntr_licence_glo';

$link=mysql_connect('localhost', 'cntr_licence_glo', 'JprfJXePmhTTNSHL');
if (!$link) {
   echo 'Impossible de se connecter à MySQL';
   exit;
}
mysql_select_db($dbname,$link);
$sql = "SHOW TABLES;";
$tables_result = mysql_query($sql);

if (!$tables_result) {
   echo "Erreur DB, impossible de lister les tables\n";
   echo 'Erreur MySQL : ' . mysql_error();
   exit;
}

while ($row = mysql_fetch_row($tables_result)) {
    $table=$row[0];
    echo "Table : {$table}<br>";
    $result = mysql_query('SELECT * FROM '.$table);
    $num_fields = mysql_num_fields($result);
    
    $return.= 'DROP TABLE IF EXISTS '.$table.';';
    $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
    $return.= "\n\n".$row2[1].";\n\n";
    
    for ($i = 0; $i < $num_fields; $i++) 
    {
        while($row = mysql_fetch_row($result))
        {
            $return.= 'INSERT INTO '.$table.' VALUES(';
            for($j=0; $j<$num_fields; $j++) 
            {
                $row[$j] = addslashes($row[$j]);
                $row[$j] = ereg_replace("\n","\\n",$row[$j]);
                if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                if ($j<($num_fields-1)) { $return.= ','; }
            }
            $return.= ");\n";
        }
    }
    $return.="\n\n\n";
}
echo $return;
//save file
$handle = fopen('db-backup-'.time().'-'.$dbname.'.sql','w+');
fwrite($handle,$return);
fclose($handle);


mysql_free_result($result);
mysql_free_result($tables_result);
?>