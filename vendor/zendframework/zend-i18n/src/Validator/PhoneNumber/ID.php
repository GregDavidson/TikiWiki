<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'code' => '62',
    'patterns' => [
        'national' => [
            'general' => '/^[1-9]\\d{6,10}$/',
            'fixed' => '/^2(?:1(?:[0-8]\\d{6,7}|9\\d{6})|[24]\\d{7,8})|(?:2(?:[35][1-4]|6[0-8]|7[1-6]|8\\d|9[1-8])|3(?:1|2[1-578]|3[1-68]|4[1-3]|5[1-8]|6[1-3568]|7[0-46]|8\\d)|4(?:0[1-589]|1[01347-9]|2[0-36-8]|3[0-24-68]|5[1-378]|6[1-5]|7[134]|8[1245])|5(?:1[1-35-9]|2[25-8]|3[1246-9]|4[1-3589]|5[1-46]|6[1-8])|6(?:19?|[25]\\d|3[1-469]|4[1-6])|7(?:1[1-46-9]|2[14-9]|[36]\\d|4[1-8]|5[1-9]|7[0-36-9])|9(?:0[12]|1[013-8]|2[0-479]|5[125-8]|6[23679]|7[159]|8[01346]))\\d{5,8}$/',
            'mobile' => '/^(?:2(?:1(?:3[145]|4[01]|5[1-469]|60|8[0359]|9\\d)|2(?:88|9[1256])|3[1-4]9|4(?:36|91)|5(?:1[349]|[2-4]9)|6[0-7]9|7(?:[1-36]9|4[39])|8[1-5]9|9[1-48]9)|3(?:19[1-3]|2[12]9|3[13]9|4(?:1[69]|39)|5[14]9|6(?:1[69]|2[89])|709)|4[13]19|5(?:1(?:19|8[39])|4[129]9|6[12]9)|6(?:19[12]|2(?:[23]9|77))|7(?:1[13]9|2[15]9|419|5(?:1[89]|29)|6[15]9|7[178]9))\\d{5,6}|8[1-35-9]\\d{7,9}$/',
            'tollfree' => '/^177\\d{6,8}|800\\d{5,7}$/',
            'premium' => '/^809\\d{7}$/',
            'emergency' => '/^11[02389]$/',
        ],
        'possible' => [
            'general' => '/^\\d{5,11}$/',
            'fixed' => '/^\\d{5,10}$/',
            'mobile' => '/^\\d{9,11}$/',
            'tollfree' => '/^\\d{8,11}$/',
            'premium' => '/^\\d{10}$/',
            'emergency' => '/^\\d{3}$/',
        ],
    ],
];
