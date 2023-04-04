<?php

// $permitted_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
  
// function generate_string($input, $strength = 10) {
//     $input_length = strlen($input);
//     $random_string = '';
//     for($i = 0; $i < $strength; $i++) {
//         $random_character = $input[mt_rand(0, $input_length - 1)];
//         $random_string .= $random_character;
//     }
  
//     return $random_string;
// }
 
// $image = imagecreatetruecolor(200, 50);
 
// imageantialias($image, true);
 
// $colors = [];
 
// $red = rand(125, 175);
// $green = rand(125, 175);
// $blue = rand(125, 175);
 
// for($i = 0; $i < 5; $i++) {
//   $colors[] = imagecolorallocate($image, $red - 20*$i, $green - 20*$i, $blue - 20*$i);
// }
 
// imagefill($image, 0, 0, $colors[0]);
 
// for($i = 0; $i < 10; $i++) {
//   imagesetthickness($image, rand(2, 10));
//   $line_color = $colors[rand(1, 4)];
//   imagerectangle($image, rand(-10, 190), rand(-10, 10), rand(-10, 190), rand(40, 60), $line_color);
// }
 
// $black = imagecolorallocate($image, 0, 0, 0);
// $white = imagecolorallocate($image, 255, 255, 255);
// $textcolors = [$black, $white];
 
// $fonts = [dirname(__FILE__).'\fonts\Acme.ttf', dirname(__FILE__).'\fonts\Ubuntu.ttf', dirname(__FILE__).'\fonts\Merriweather.ttf', dirname(__FILE__).'\fonts\PlayfairDisplay.ttf'];
 
// $string_length = 6;
// $captcha_string = generate_string($permitted_chars, $string_length);
 
// $_SESSION['captcha_text'] = $captcha_string;
 
// for($i = 0; $i < $string_length; $i++) {
//   $letter_space = 170/$string_length;
//   $initial = 15;
   
//   imagettftext($image, 24, rand(-15, 15), $initial + $i*$letter_space, rand(25, 45), $textcolors[rand(0, 1)], $fonts[array_rand($fonts)], $captcha_string[$i]);
// }
 
// header('Content-type: image/png');
// imagepng($image);
// imagedestroy($image);

// header("Content-Type: image/png");
// $im = @imagecreate(110, 20)
//     or die("Cannot Initialize new GD image stream");
// $background_color = imagecolorallocate($im, 0, 0, 0);
// $text_color = imagecolorallocate($im, 233, 14, 91);
// imagestring($im, 1, 5, 5,  "A Simple Text String", $text_color);
// imagepng($im);
// imagedestroy($im);

    ob_start();
    header("Content-Type: image/jpeg"); 

     $md5_hash = md5(rand(0,999)); 
    //We don't need a 32 character long string so we trim it down to 5 
    $security_code = substr($md5_hash, 15, 5); 

    //Set the session to store the security code


    //Set the image width and height
    $width = 100;
    $height = 20; 

    //Create the image resource 
    $image = ImageCreate($width, $height);  

    //We are making three colors, white, black and gray
    $white = ImageColorAllocate($image, 255, 255, 255);
    $black = ImageColorAllocate($image, 0, 0, 0);
    $grey = ImageColorAllocate($image, 204, 204, 204);

    //Make the background black 
    ImageFill($image, 0, 0, $black); 

    //Add randomly generated string in white to the image
    ImageString($image, 3, 30, 3, $security_code, $white); 

    //Throw in some lines to make it a little bit harder for any bots to break 
    ImageRectangle($image,0,0,$width-1,$height-1,$grey); 
    imageline($image, 0, $height/2, $width, $height/2, $grey); 
    imageline($image, $width/2, 0, $width/2, $height, $grey);


    ImageJpeg($image);
    $img = ob_get_clean();

    ImageDestroy($image);
    return base64_encode($img)