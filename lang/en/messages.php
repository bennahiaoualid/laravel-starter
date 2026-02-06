<?php

return [

    /*
    |--------------------------------------------------------------------------
    | validation Language Lines
    |--------------------------------------------------------------------------
    |
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    'validation' => [
        'success' => [
            'saved' => 'data have been saved',
            'updated' => 'data have been updated',
            'deleted' => 'data have been deleted',
            'restored' => 'data have been restored',
        ],
        'fail' => [
            'saved' => 'something went wrong while saving',
            'updated' => 'something went wrong while updating',
            'deleted' => 'something went wrong while deleting',
            'restored' => 'something went wrong while restoring',
        ],
        'info' => [
            'saved' => 'save operation is in progress',
            'updated' => 'update operation is in progress',
            'deleted' => 'delete operation is in progress',
            'restored' => 'restore operation is in progress',
            'nothing_to_update' => 'there is nothing to be updated',
        ],
        '404' => [
            'user' => 'user not found',
        ],
        'error' => [
            'somthing_went_wrong' => 'Somthing went wrong , please contact adminstrator',
        ],
        'not_allow' => [
            'not_authorized' => 'you are not authorized to proceed this action',
        ],
    ],
    'alert' => [
        'type' => [
            'success' => 'success',
            'error' => 'error',
            'warning' => 'warning',
            'info' => 'info',
            'danger' => 'danger',
        ],
    ],
    'details' => 'Details',
    'global' => [
        'and' => 'and',
        'id' => 'ID',
        'num' => 'N°',
        'yes' => 'Yes',
        'no' => 'No',
        'see_all' => 'see all',
        'see_more' => 'see more',
        'details' => 'details',
        'no_records' => 'No Records Found',
        'select_lang' => 'select a language',
        'action' => 'action',
        'choose' => 'choose',
        'description' => 'Description',
        'name' => 'Name',
        'number' => 'Number',
        'created_at' => 'Created At',
        'user' => 'User',
        'errors' => 'Errors',
        'reason' => 'Reason',
    ],
    'mail' => [],
    'permissions' => [
        'assigned_successfully' => 'Permissions have been assigned successfully',
        'revoked_successfully' => 'Permissions have been revoked successfully',
        'assignment_failed' => 'Failed to assign permissions. Please try again.',
        'revocation_failed' => 'Failed to revoke permissions. Please try again.',
        'unauthorized' => 'You are not authorized to perform this action.',
        'no_permissions_selected' => 'No permissions were selected.',
    ],
];
