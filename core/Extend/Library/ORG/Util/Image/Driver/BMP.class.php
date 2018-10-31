<?php

/**
 * Created by PhpStorm.
 * User: wl
 * Date: 14-9-2
 * Time: 上午10:00
 */
class BMP
{
    private $img;
    private $filename;

    public function __construct($filename=null){
        $this->filename = $filename;
    }

    public function create(){
        $this->img = $this->imagecreatefrombmp($this->filename);
        return $this->img;
    }

    public function save(){
        return $this->imagebmp($this->img, $this->filename);
    }

    function imagebmp($im, $fn = false)
    {
        if (!$im) return false;

        if ($fn === false) $fn = 'php://output';
        $f = fopen($fn, "w");
        if (!$f) return false;

        //Image dimensions
        $biWidth = imagesx($im);
        $biHeight = imagesy($im);
        $biBPLine = $biWidth * 3;
        $biStride = ($biBPLine + 3) & ~3;
        $biSizeImage = $biStride * $biHeight;
        $bfOffBits = 54;
        $bfSize = $bfOffBits + $biSizeImage;

        //BITMAPFILEHEADER
        fwrite($f, 'BM', 2);
        fwrite($f, pack('VvvV', $bfSize, 0, 0, $bfOffBits));

        //BITMAPINFO (BITMAPINFOHEADER)
        fwrite($f, pack('VVVvvVVVVVV', 40, $biWidth, $biHeight, 1, 24, 0, $biSizeImage, 0, 0, 0, 0));

        $numpad = $biStride - $biBPLine;
        for ($y = $biHeight - 1; $y >= 0; --$y) {
            for ($x = 0; $x < $biWidth; ++$x) {
                $col = imagecolorat($im, $x, $y);
                fwrite($f, pack('V', $col), 3);
            }
            for ($i = 0; $i < $numpad; ++$i)
                fwrite($f, pack('C', 0));
        }
        fclose($f);
        return true;
    }

    function imagecreatefrombmp($filename)
    {
        if (!$f1 = fopen($filename, "rb")) return FALSE;

        $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
        if ($FILE['file_type'] != 19778) return FALSE;

        $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' .
            '/Vcompression/Vsize_bitmap/Vhoriz_resolution' .
            '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
        $BMP['colors'] = pow(2, $BMP['bits_per_pixel']);

        if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
        $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
        $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
        $BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal'] -= floor($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal'] = 4 - (4 * $BMP['decal']);
        if ($BMP['decal'] == 4) $BMP['decal'] = 0;

        $PALETTE = array();
        if ($BMP['colors'] < 16777216 && $BMP['colors'] != 65536) {
            $PALETTE = unpack('V' . $BMP['colors'], fread($f1, $BMP['colors'] * 4));
        }

        $IMG = fread($f1, $BMP['size_bitmap']);
        $VIDE = chr(0);

        $res = imagecreatetruecolor($BMP['width'], $BMP['height']);
        $P = 0;
        $Y = $BMP['height'] - 1;
        while ($Y >= 0) {
            $X = 0;
            while ($X < $BMP['width']) {
                if ($BMP['bits_per_pixel'] == 24)
                    $COLOR = unpack("V", substr($IMG, $P, 3) . $VIDE);
                elseif ($BMP['bits_per_pixel'] == 16) {
                    $COLOR = unpack("v", substr($IMG, $P, 2));
                    $blue = ($COLOR[1] & 0x001f) << 3;
                    $green = ($COLOR[1] & 0x07e0) >> 3;
                    $red = ($COLOR[1] & 0xf800) >> 8;
                    $COLOR[1] = $red * 65536 + $green * 256 + $blue;
                } elseif ($BMP['bits_per_pixel'] == 8) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, $P, 1));
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 4) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 2) % 2 == 0) $COLOR[1] = ($COLOR[1] >> 4); else $COLOR[1] = ($COLOR[1] & 0x0F);
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 1) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 8) % 8 == 0) $COLOR[1] = $COLOR[1] >> 7;
                    elseif (($P * 8) % 8 == 1) $COLOR[1] = ($COLOR[1] & 0x40) >> 6;
                    elseif (($P * 8) % 8 == 2) $COLOR[1] = ($COLOR[1] & 0x20) >> 5;
                    elseif (($P * 8) % 8 == 3) $COLOR[1] = ($COLOR[1] & 0x10) >> 4;
                    elseif (($P * 8) % 8 == 4) $COLOR[1] = ($COLOR[1] & 0x8) >> 3;
                    elseif (($P * 8) % 8 == 5) $COLOR[1] = ($COLOR[1] & 0x4) >> 2;
                    elseif (($P * 8) % 8 == 6) $COLOR[1] = ($COLOR[1] & 0x2) >> 1;
                    elseif (($P * 8) % 8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } else
                    return FALSE;

                imagesetpixel($res, $X, $Y, $COLOR[1]);

                $X++;
                $P += $BMP['bytes_per_pixel'];
            }
            $Y--;
            $P += $BMP['decal'];
        }
        fclose($f1);
        return $res;
    }
}