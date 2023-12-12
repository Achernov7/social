<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ClearLastActivityCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onlineUserCacheClear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use this command to clear last activity cache';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $generator = $this->scanAllForMatch('laravel_database_usersLastOnline:*');
        foreach ($generator as $user) {
            list(, $userId) = preg_split("/laravel_database_usersLastOnline:/", $user);

            if (Carbon::now()->gt( Carbon::parse(Redis::get('usersLastOnline:'.$userId))->addMinutes(30)) ) {
                User::where('id', $userId)->update([
                    'last_activity' => Carbon::parse(Redis::get('usersLastOnline:'.$userId)),
                ]);
                Redis::del('usersLastOnline:'.$userId);
            }
        }
    }

    protected function scanAllForMatch($pattern)
    {
        $cursor = 0;
        do {
            list($cursor, $keys) = Redis::scan($cursor, 'match', $pattern);
            foreach ($keys as $key) {
                yield $key;
            }
        } while ($cursor);
    }
}