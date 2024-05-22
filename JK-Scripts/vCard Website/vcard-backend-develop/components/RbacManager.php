<?php


namespace app\components;


use app\models\ext\User;
use vr\rbac\BaseRbacManager;

/**
 * Class RbacManager
 * @package app\components
 */
class RbacManager extends BaseRbacManager
{
    /**
     * @var string
     */
    public string $identityClass = User::class;

    /**
     * @return array
     */
    public function roles(): array
    {
        return [
            self::ROLE_GUEST,
            User::ROLE_LOCATION => self::ROLE_GUEST,
            User::ROLE_TECHNICIAN => self::ROLE_GUEST,
            User::ROLE_ROUTEMAN => self::ROLE_GUEST,
            User::ROLE_ADMIN => self::ROLE_GUEST,
        ];
    }

    /**
     * @return array
     */
    public function permissions(): array
    {
        return [
            'app/account/sign-in' => self::ROLE_GUEST,
            'app/misc/*' => self::ROLE_GUEST,


            'app/account/*' => [User::ROLE_LOCATION, User::ROLE_TECHNICIAN, User::ROLE_ADMIN],
            'app/account/get' => User::ROLE_ROUTEMAN,

            'app/locations/list' => [User::ROLE_ROUTEMAN, User::ROLE_TECHNICIAN],
            'app/locations/list+all' => [User::ROLE_LOCATION, User::ROLE_ADMIN],
            'app/locations/list+test' => [User::ROLE_LOCATION, User::ROLE_ADMIN, User::ROLE_TECHNICIAN],

            'app/locations/get' => [User::ROLE_ROUTEMAN, User::ROLE_TECHNICIAN],
            'app/locations/get+all' => [User::ROLE_ROUTEMAN, User::ROLE_LOCATION, User::ROLE_ADMIN],
            'app/locations/get+test' => [User::ROLE_TECHNICIAN, User::ROLE_LOCATION, User::ROLE_ADMIN],

            'app/locations/update' => User::ROLE_TECHNICIAN,
            'app/locations/export' => [User::ROLE_ROUTEMAN, User::ROLE_TECHNICIAN],
            'app/locations/service-request' => User::ROLE_ROUTEMAN,

            'app/terminals/list' => [User::ROLE_TECHNICIAN, User::ROLE_ROUTEMAN],
            'app/terminals/get' => [User::ROLE_TECHNICIAN, User::ROLE_ROUTEMAN],

            'app/clerks/list' => [User::ROLE_ROUTEMAN, User::ROLE_TECHNICIAN],

            'app/invoices/list' => User::ROLE_ROUTEMAN,
            'app/invoices/view' => User::ROLE_ROUTEMAN,
            'app/invoices/get' => User::ROLE_ROUTEMAN,

            'app/emails/list' => User::ROLE_TECHNICIAN,
            'app/categories/list' => User::ROLE_TECHNICIAN,

            'app/locations/revenue' => [User::ROLE_TECHNICIAN, User::ROLE_ROUTEMAN],
            'app/charts/revenue' => [User::ROLE_TECHNICIAN, User::ROLE_ROUTEMAN],
            'app/dashboard/index' => [User::ROLE_TECHNICIAN, User::ROLE_ROUTEMAN],

            'app/software/list' => [User::ROLE_ROUTEMAN, User::ROLE_TECHNICIAN],
            'app/software/get' => [User::ROLE_ROUTEMAN, User::ROLE_TECHNICIAN],

            'reports/inventory/count' => User::ROLE_ROUTEMAN,
            'reports/inventory/details' => User::ROLE_ROUTEMAN,
            'reports/inventory/earners' => User::ROLE_ROUTEMAN,
            'reports/inventory/reconcile' => User::ROLE_ROUTEMAN,
            'reports/locations/remains' => User::ROLE_ROUTEMAN,
            'reports/locations/week-to-date' => User::ROLE_ROUTEMAN,
            'reports/locations/all-locations-standard' => User::ROLE_ROUTEMAN,
            'reports/locations/redemptions' => User::ROLE_ROUTEMAN,
            'reports/locations/location-standard' => User::ROLE_ROUTEMAN,
            'reports/locations/location-daily' => User::ROLE_ROUTEMAN,
            'reports/locations/location-invoiced' => User::ROLE_ROUTEMAN,
            'reports/locations/device-performance' => User::ROLE_ROUTEMAN,

            'app/*' => User::ROLE_LOCATION,
            'app/password/change' => [User::ROLE_LOCATION, User::ROLE_TECHNICIAN, User::ROLE_ROUTEMAN, User::ROLE_ADMIN],
            '*' => User::ROLE_ADMIN
        ];
    }
}