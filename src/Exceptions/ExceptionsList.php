<?php
/**
 * Doctors Verification
 */
namespace Chirontex\DocsVer\Exceptions;

class ExceptionsList
{

    const COMMON = [
        'user_not_authorized' => [
            'message' => 'User is not authorized.',
            'code' => -1
        ],
        'invalid_user_id' => [
            'message' => 'User ID cannot be lesser than 1.',
            'code' => -2
        ]
    ];

    const PROVIDERS = [
        'table_creation_failure' => [
            'message' => 'Table creation failure.',
            'code' => -11
        ],
        'select_failure' => [
            'message' => 'Selection query returns false.',
            'code' => -12
        ]
    ];

}
