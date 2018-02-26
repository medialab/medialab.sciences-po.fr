<?

//echo file_get_contents("http://tools.medialab.sciences-po.fr/manylines/meta.json");

$jsoncontext = stream_context_create(array('http'=>array('timeout' => 1)));
$meta = file_get_contents("http://10.35.1.155/source/index.json", 0, $jsoncontext, null);
if( $meta== "")
	$meta=file_get_contents("index.json");
echo $meta;
?>
