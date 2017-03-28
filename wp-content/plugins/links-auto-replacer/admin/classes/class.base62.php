<?php
/**
 * Base 62 Encoder / Decoder Class
 * (c) Andy Huang, 2009; All rights reserved
 *
 * This code is not distributed under any specific license, 
 * as I do not believe in them, but it is distributed under
 * these terms outlined below:
 * - You may use these code as part of your application, even if it is a commercial product
 * - You may modify these code to suite your application, even if it is a commercial product
 * - You may sell your commercial product derived from these code
 * - You may donate to me if you are some how able to get a hold of me, but that's not required
 * - You may link back to the original article for reference, but do not hotlink the source file
 * - This line is intentionally added to differentiate from LGPL, or other similar licensing terms
 * - You must at all time retain this copyright message and terms in your code
 */
class base62
{
    static $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    static $base = 62;

    public static function encode($var) 
    {   
        if(!function_exists('bccomp')){
            return self::base62encode( $var );
        }

        $stack = array();
        while (bccomp($var, 0) != 0)
        {
            $remainder = bcmod($var, self::$base);
            $var = bcdiv( bcsub($var, $remainder), self::$base );

            array_push($stack, self::$characters[$remainder]);
        }

        return implode('', array_reverse($stack));
    }

    public static function decode($var) 
    {
        if(!function_exists('bcadd')){
            return self::base62decode( $var );
        }
        $length = strlen($var);
        $result = 0;
        for($i=0; $i<$length; $i++) 
        {
            $result = bcadd($result, bcmul(self::get_digit($var[$i]), bcpow(self::$base, ($length-($i+1)))));
        }

        return $result;
    }

    private function get_digit($var) 
    {
        if(ereg('[0-9]', $var))
        {
            return (int)(ord($var) - ord('0'));
        }
        else if(ereg('[A-Z]', $var))
        {
            return (int)(ord($var) - ord('A') + 10);
        }
        else if(ereg('[a-z]', $var))
        {
            return (int)(ord($var) - ord('a') + 36);
        }
        else
        {
            return $var;
        }
    }

    public static function base62decode($data) {
        if(!function_exists('gmp_strval')){
            return self::native_convert($data, 62, 10);
        }
        $outstring = '';
        $l = strlen($data);
        for ($i = 0; $i < $l; $i += 11) {
            $chunk = substr($data, $i, 11);
            $outlen = floor((strlen($chunk) * 6)/8); //6bit/char in, 8bits/char out, round down
            $y = gmp_strval(gmp_init(ltrim($chunk, '0'), 62), 16); //gmp doesn't like leading 0s
            $pad = str_pad($y, $outlen * 2, '0', STR_PAD_LEFT); //double output length as as we're going via hex (4bits/char)
            $outstring .= pack('H*', $pad); //same as hex2bin
        }
        return $outstring;
    }

    public static function base62encode($data) {
        if(!function_exists('gmp_strval')){
            return self::native_convert($data, 10, 62);
        }
        $outstring = '';
        $l = strlen($data);
        for ($i = 0; $i < $l; $i += 8) {
            $chunk = substr($data, $i, 8);
            $outlen = ceil((strlen($chunk) * 8)/6); //8bit/char in, 6bits/char out, round up
            $x = bin2hex($chunk);  //gmp won't convert from binary, so go via hex
            $w = gmp_strval(gmp_init(ltrim($x, '0'), 16), 62); //gmp doesn't like leading 0s
            $pad = str_pad($w, $outlen, '0', STR_PAD_LEFT);
            $outstring .= $pad;
        }
        return $outstring;
    }

    public static function  native_convert($number, $from_base=10, $to_base=62) {
        
        //OPTIMIZATION: no need to convert 0
        if("{$number}" === '0') {
            return 0;
        }

        //OPTIMIZATION: if to and from base are same.
        if($from_base == $to_base){
            return $number;
        }

        //OPTIMIZATION: if base is lower than 36, use PHP internal function
        if($from_base <= 36 && $to_base <= 36) {
            // for lower base, use the default PHP function for faster results
            return base_convert($number, $from_base, $to_base);
        }

        // char list starts from 0-9 and then small alphabets and then capital alphabets
        // to make it compatible with eixisting base_convert function
        $charlist = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        if($from_base < $to_base) {
            // if converstion is from lower base to higher base
            // first get the number into decimal and then convert it to higher base from decimal;

            if($from_base != 10){
                $decimal = self::convert($number, $from_base, 10);
            } else {
                $decimal = intval($number);
            }

            //get the list of valid characters
            $charlist = substr($charlist, 0, $to_base);

            if($number == 0) {
                return 0;
            }
            $converted = '';
            while($number > 0) {
                $converted = $charlist{($number % $to_base)} . $converted;
                $number = floor($number / $to_base);
            }
            return $converted;
        } else {
            // if conversion is from higher base to lower base;
            // first convert it into decimal and the convert it to lower base with help of same function.
            $number = "{$number}";
            $length = strlen($number);
            $decimal = 0;
            $i = 0;
            while($length > 0) {
                $char = $number{$length-1};
                $pos = strpos($charlist, $char);
                if($pos === false){
                    trigger_error("Invalid character in the input number: ".($char), E_USER_ERROR);
                }
                $decimal += $pos * pow($from_base, $i);
                $length --;
                $i++;
            }
            return self::convert($decimal, 10, $to_base);
        }
    }

}