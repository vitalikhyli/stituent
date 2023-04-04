<?php

namespace App\Traits;

use Faker\Factory as Faker;

trait CFCaptchaTrait
{
    public function CFCaptcha()
    {
        $faker = Faker::create();

        $security_code = substr(strtoupper($faker->firstname), 0, 12).' '.rand(100, 999);

        $captcha_code = $this->secretEncode($security_code);

        $width = 150;
        $height = 40;
        $im = imagecreate($width, $height);
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        $grey = imagecolorallocate($im, 204, 204, 204);
        $blue = imagecolorallocate($im, 00, 00, 204);
        $red = imagecolorallocate($im, 204, 00, 00);
        $cf_blue = imagecolorallocate($im, 43, 108, 176);
        imagefill($im, 0, 0, $cf_blue);

        $x = 4;
        $y = 4;
        for ($i = 0; $i <= strlen($security_code); $i++) {
            $line = str_repeat(' ', $i);
            $line .= substr($security_code, $i, 1);
            $line .= str_repeat(' ', strlen($security_code) - $i);

            // $x += rand(1,4);
            $y += rand(-3, 3);
            imagestring($im, 5, $i + $x, $i + $y, $line, $white);
        }

        imagerectangle($im, 0, 0, $width - 1, $height - 1, $grey);
        imageline($im, 0, $height / 3, $width, $height / 2, $grey);
        imageline($im, $width / 2, 0, $width / 3, $height, $grey);
        imageline($im, $width / 4, 0, $width / 2, $height, $red);

        ob_start();
        imagepng($im);

        $captcha_image = base64_encode(ob_get_clean());
        imagedestroy($im);

        return ['image' => $captcha_image, 'code' => $captcha_code];
    }

    public function secretEncode($c)
    {
        return base64_encode(base64_encode(base64_encode($c)));
    }

    public function secretDecode($c)
    {
        return base64_decode(base64_decode(base64_decode($c)));
    }

    public function CFCaptchaCheck($actual, $submitted)
    {
        $captcha_code_actual = strtoupper($this->secretDecode($actual));
        $captcha_code_submit = strtoupper($submitted);

        if ($captcha_code_submit != $captcha_code_actual) {
            return false;
        } else {
            return true;
        }
    }
}
