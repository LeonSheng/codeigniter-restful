<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CaptchaUtils
{
    /**
     * generate a captcha image of calculating two numbers with plus or minus operator
     * @param string $result the calculation result
     * @return string base64 encoded string of a PNG image
     */
    public static function generateCalculate(string &$result)
    {
        $imageWidth = 100;
        $imageHeight = 30;
        $image = imagecreatetruecolor($imageWidth, $imageHeight);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgColor);

        $fontSize = 6;
        $fontColor = imagecolorallocate($image, 0, 0, 0);
        //random1
        $number1 = rand(0, 20);
        $x = rand(5, 10);
        $y = rand(5, 10);
        imagestring($image, $fontSize, $x, $y, $number1, $fontColor);

        //+ or -
        $operator = rand(0, 1) === 1 ? '+' : '-';
        $x = ($imageWidth/5) + rand(5, 10);
        $y = rand(5, 10);
        imagestring($image, $fontSize, $x, $y, $operator, $fontColor);

        //random2
        $number2 = rand(0, 20);
        $x = ($imageWidth*2/5) + rand(5, 10);
        $y = rand(5, 10);
        imagestring($image, $fontSize, $x, $y, $number2, $fontColor);

        $equal = '=';
        $x = ($imageWidth*3/5) + rand(5, 10);
        $y = rand(5, 10);
        imagestring($image, $fontSize, $x, $y, $equal, $fontColor);

        $questionMark = '?';
        $x = ($imageWidth*4/5) + rand(5, 10);
        $y = rand(5, 10);
        imagestring($image, $fontSize, $x, $y, $questionMark, $fontColor);

        //add some dots and lines
        for ($i=0; $i < 200; $i++) {
            $pointColor = imagecolorallocate($image,rand(50, 200),rand(50, 200),rand(50, 200));
            imagesetpixel($image, rand(1, 99), rand(1, 29), $pointColor);
        }
        for ($i=0; $i < 3; $i++) {
            $lineColor = imagecolorallocate($image,rand(80, 220),rand(80, 220),rand(80, 220));
            imageline($image, rand(1, 99), rand(1, 29),rand(1, 99), rand(1, 29) ,$lineColor);
        }

        //calculation result
        $result = (string)($operator === '+' ? $number1 + $number2 : $number1 - $number2);
        ob_start();
        imagepng($image);
        $buffer = base64_encode(ob_get_clean());
        ob_end_clean();
        return "data:image/png;base64,${buffer}";
    }
}
