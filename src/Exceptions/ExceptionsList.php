<?php
/**
 * Doctors Verification
 */
namespace Chirontex\DocsVer\Exceptions;

class ExceptionsList
{

    const COMMON = [
        '-1' => [
            'message' => 'User is not authorized.',
            'code' => -1
        ],
        '-2' => [
            'message' => 'User ID cannot be lesser than 1.',
            'code' => -2
        ]
    ];

    const PROVIDERS = [
        '-11' => [
            'message' => 'Table creation failure.',
            'code' => -11
        ],
        '-12' => [
            'message' => 'Selection query returns false.',
            'code' => -12
        ],
        '-13' => [
            'message' => 'Entry insertion failure.',
            'code' => -13
        ]
    ];

}
