<?php

namespace App\Traits;



trait Permissions
{

    protected function isLoanOfficer($user): bool
    {
        return !empty($user) ? $user->tokenCan('loan-officer') : false;
    }
    protected function isBranchManager($user): bool
    {
        return !empty($user) ? $user->tokenCan('branch-manager') : false;
    }
    protected function isCashier($user): bool
    {
        return !empty($user) ? $user->tokenCan('cashier') : false;
    }
    protected function isAdmin($user): bool
    {
        return !empty($user) ? $user->tokenCan('super-admin') : false;
    }

}
