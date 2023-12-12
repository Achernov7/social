<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Components\SharedServices\getNameWithSurnameFromSearchingUser;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'surname',
        'birthdayDate',
        'town',
        'gender',
        'familyStatus',
        'about',
        'stop_check_chat_messages'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = ['last_activity'];


    public function subscribeToUser() 
    {
        return $this->belongsToMany(User::class, 'friend_users', 'user_id', 'friend_id');
    }

    public function requestToBeFriend()
    {
        return $this->belongsToMany(User::class, 'friend_users', 'friend_id', 'user_id');
    }

    public function realFriends()
    {
        return $this->SubscribeToUser()->wherePivot('accepted', 1)->get()->merge($this->RequestToBeFriend()->wherePivot('accepted', 1)->get());
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id');
    }

    public function music()
    {
        return $this->belongsToMany(Music::class, 'music_users', 'user_id', 'music_id');
    }


    /**
     * Scope a query to only include with additional params for users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchWithAdditionalParams($query, $params)
    {
        foreach ($params as $param) {

            if (isset($param['nameOfUserToSearch'])) {

                if (str_contains($param['nameOfUserToSearch'], ' ') || str_contains($param['nameOfUserToSearch'], '&nbsp;')){

                    $nameOrSuraname = getNameWithSurnameFromSearchingUser::getNameSurname($param['nameOfUserToSearch']);
                    
                    if (gettype($nameOrSuraname) == 'string'){
                        
                        $query = $query->where(function ($query) use ($nameOrSuraname) {
                            $query->where('name', 'like', '%' . $nameOrSuraname . '%')->orWhere('surname', 'like', '%' . $nameOrSuraname . '%');
                        });

                    } else if (gettype($nameOrSuraname) == 'array'){
                        
                        $query = $query->where(function ($query) use ($nameOrSuraname) {
                            $query->where(function ($query) use ($nameOrSuraname) {
                                $query->where('name', 'like', '%' . $nameOrSuraname[0] . '%')->where('surname', 'like', '%' . $nameOrSuraname[1] . '%');
                            })->orWhere(function ($query) use ($nameOrSuraname) {
                                $query->where('name', 'like', '%' . $nameOrSuraname[1] . '%')->where('surname', 'like', '%' . $nameOrSuraname[0] . '%');
                            });
                        });

                    }  
                } else {

                    $query = $query->where(function ($query) use ($param) {
                        $query->where('name', 'like', '%' . $param['nameOfUserToSearch'] . '%')
                            ->orWhere('surname', 'like','%' . $param['nameOfUserToSearch'] . '%');
                    });

                }
            }

            if (isset($param['town'])) {
                $query = $query->where('town', 'like', '%' . $param['town'] . '%');
            }

            if (isset($param['gender'])) {
                if ($param['gender'] != 'any'){
                    $query = $query->where('gender', $param['gender']);
                }
            }

            if (isset($param['ageFrom'])) {
                $query = $query->where(DB::raw("cast(strftime('%Y %m %d', 'now') - strftime('%Y %m %d', substr(birthdayDate, 7, 4) || '-' || substr(birthdayDate, 4,2) || '-' || substr(birthdayDate, 1,2)) as int)"), '>=', $param['ageFrom']);
            }

            if (isset($param['ageTo'])) {
                $query = $query->where(DB::raw("cast(strftime('%Y %m %d', 'now') - strftime('%Y %m %d', substr(birthdayDate, 7, 4) || '-' || substr(birthdayDate, 4,2) || '-' || substr(birthdayDate, 1,2)) as int)"), '<=', $param['ageTo']);
            }

            if (isset($param['familystatus'])) {
                if ($param['familystatus'] != 'additional.Choose_status'){
                    $query = $query->where('familyStatus', $param['familystatus']);
                }
            }

        }

        return $query;
    }
}
