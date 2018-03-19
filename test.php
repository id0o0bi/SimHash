<?php


$scws = scws_new();
$dict = '/usr/local/scws/etc/dict.utf8.xdb';           # 词典路径设置
$scws->set_charset('utf8');                            # 设定字符编码
$scws->set_ignore(true);                               # 忽略标点符号
$scws->set_dict($dict);                                # 词典文件路径

include_once "SimHash.php";

$SimHash = new SimHash();

$hashes = array();

$docs = 12;

for ($i = 1;$i <= $docs;$i++) 
{
    $file = str_pad($i, 3, '0', STR_PAD_LEFT);
    $content = file_get_contents("docs/{$file}.txt");
    
    /* 获取文本关键词
    * exclude: a 形容词 i 成语 m 数词 y 语气词 
    *          r 代词 t 时间词 ad 副形词 an 名形词
    */
    $scws->send_text($content);
    $words = $scws->get_tops(10, '~a,i,m,y,r,t,ad,an');    # 过滤
    $vectors = array();
    foreach ($words as $word) 
    {
        $string = $word['word'];
        $weight = $word['weight'];
        $binary = $SimHash->getStringHash($string);
    
        $vector = array(
            'binary' => $binary, 
            'weight' => $weight
        );
    
        array_push($vectors, $vector);
    }
    $hash = $SimHash->getSimHash(array_values($vectors));

    array_push($hashes, $hash);
}

foreach ($hashes as $i => $t) {
    for ($j = $i+1;$j <= $docs-1;$j++) {
        $dis = $SimHash->hammingDistance($t, $hashes[$j]);
        echo sprintf("%03d-%03d: %02d  ", $i+1, $j+1, $dis);
    }
    echo "\n";
}

$scws->close();

// print_r(hash_algos());
// print_r($words);