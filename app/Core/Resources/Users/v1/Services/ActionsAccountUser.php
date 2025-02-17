<?php

namespace App\Core\Resources\Users\v1\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActionsAccountUser
{
    public static function deleteUser ($user) {
        if ( !($user instanceof User) ) {
            $user = User::query()->findOrFail($user);
        }

        DB::table('personal_access_tokens')->where('tokenable_id', '=', $user->getRouteKey())->delete();

        DB::select(
            "CALL delete_user_data_test(?)",
            array($user->id)
        );

        $user->delete();

        return $user;
    }
    public static function disableAccountUser ($user) {

        if ( !($user instanceof User) ) {
            $user = User::query()->findOrFail($user);
            DB::table('personal_access_tokens')->where('tokenable_id', '=', $user->getRouteKey())->delete();
        }

        DB::table('personal_access_tokens')->where('tokenable_id', '=', $user->getRouteKey())->delete();

        $user->state = 'disable';
        $user->save();
        $user->refresh();

        /*$user->delete(); // Soft Delete
        $user->refresh();*/

        return $user;
    }
    public static function enableAccountUser ($user) {

        if ( !($user instanceof User) ) {
            $user = User::query()->findOrFail($user);
            DB::table('personal_access_tokens')->where('tokenable_id', '=', $user->getRouteKey())->delete();
        }

        $user->state = 'enable';
        $user->save();
        $user->refresh();

        /*$user->restore(); // Soft Delete
        $user->refresh();*/

        return $user;
    }
}
