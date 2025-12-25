<?php

use App\Models\RolePermission;
use App\Models\ModuleFeature;

if (!function_exists('can')) {
   function can($featureCode, $action = 'read')
   {
    $user = auth()->user();
    if (!$user) return false;

    $feature = ModuleFeature::where('code', $featureCode)->first();
    if (!$feature) return false;

    $permission = RolePermission::where('role_id', $user->role_id)
                    ->where('feature_id', $feature->id)
                    ->where('company_id', $user->company_id)
                    ->first();

    if (!$permission) return false;

    return match($action){
        'read' => $permission->can_read,
        'create' => $permission->can_create,
        'update' => $permission->can_update,
        'delete' => $permission->can_delete,
        default => false,
    };
   }

}
