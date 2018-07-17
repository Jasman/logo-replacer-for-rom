<?php

/**
 * @author Jasman
 * @copyright Ihsana IT Solution 2018
 */
 
$rom_sumber = "K5-MODERN-MT-2018-06-25.bin";
$gambar_pengganti = "hasil.jpg";
// outputnya hasil.bin

$rom_img_header = array('ff',  'd8', 'ff' ,'e0');
//$rom_img_header = array('ff',  'd8', 'ff' ,'e1');

//$rom_img_offset =  array( '00','00' ,'00','00' ,'01' ,'b3');
$rom_img_offset =  array('00','00' ,'01' ,'b3');

function hexbin($arr){
    $str ='';
    for($i=0;$i<count($arr);$i++){
        $str .= chr( hexdec($arr[$i]));
    }
	return $str;
}

$jpg_awal = hexbin($rom_img_header);
$jpg_akhir = hexbin($rom_img_offset);

 if(!file_exists('tmp')){
    @mkdir('tmp');
}
 

if(!file_exists($rom_sumber)){
    die("File ".$rom_sumber." tidak ada.");
}
if(!file_exists($gambar_pengganti)){
    die("File ".$gambar_pengganti." tidak ada.");
}
echo 'Ukuran Firmware: ' . filesize($rom_sumber) . '<br/>';
echo 'Sha1 Firmware: ' . sha1_file($rom_sumber) . '<br/>';

$bin = file_get_contents($rom_sumber);
$pisah = explode($jpg_awal, $bin);

file_put_contents("tmp/rom0.bin", $pisah[0]); //tanpa header

$zpisah = explode($jpg_akhir, $pisah[1]);
file_put_contents("tmp/rom2.bin", $zpisah[1]); //tanpa header


$gambar = file_put_contents("tmp/bootlogo.jpg", $jpg_awal . $zpisah[0] . $jpg_akhir);

$ukuran_asli = filesize("tmp/bootlogo.jpg");
echo 'Ukuran Gambar Maksimun: ' . $ukuran_asli . '<br/>';

$ukuran_penganti = filesize($gambar_pengganti);
echo 'Ukuran Gambar Penganti: ' . $ukuran_penganti . '<br/>';
$gambar = file_get_contents($gambar_pengganti);

$panjang_null = $ukuran_asli - $ukuran_penganti;
if ($panjang_null < 2)
{
    die("Gambar " . $gambar_pengganti . " terlalu besar, ukuran harus kurang dari " . $ukuran_asli);
}
$nullbyte = null;
for ($i = 0; $i < ($panjang_null - 2); $i++)
{
    $nullbyte .= hexbin(array('ff'));
}
$nullbyte .=  hexbin(array('01'));
$nullbyte .=  hexbin(array('b3'));

$fix_nullbyte = $gambar . $nullbyte ; 
file_put_contents('tmp/rom1.bin', $fix_nullbyte);

$binner = file_get_contents('tmp/rom0.bin');
$binner .= file_get_contents('tmp/rom1.bin');
$binner .= file_get_contents('tmp/rom2.bin');
file_put_contents("hasil.bin", $binner);

echo 'Ukuran Firmware Hasil: ' . filesize('hasil.bin') . '<br/>';
echo 'Sha1 Firmware Hasil: ' . sha1_file('hasil.bin') . '<br/>';
echo '<hr/>';
echo '<img src="tmp/bootlogo.jpg" width="380" height="200" />';
echo ' =&raquo; <img src="hasil.jpg" width="380" height="200" />';
?>
