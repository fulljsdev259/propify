<?php

namespace App\Console\Commands;

use App\Models\Request;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendActiveRemainderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-active-remainder-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send active remainder notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $requests = Request::where('due_date', '>=', now()->format('Y-m-d'))
            ->where('active_reminder', 1) // @TODO need use class constant or not
          //  ->whereRaw("sent_reminder_user_ids not like CONCAT('%', reminder_user_ids, '%') ") // each user send only one time
            ->whereColumn('days_left_due_date', '=', DB::raw('DATEDIFF(due_date, CURDATE())'))
            ->get(['id', 'reminder_user_ids', 'sent_reminder_user_ids']);

        $userIds = $requests->pluck('reminder_user_ids')->collapse()->unique()->all();
        $users = User::whereIn('id', $userIds)->get();

        // @TODO correct
        foreach ($requests as $request) {
            $remainderUsers = $users->whereIn('id', $request->reminder_user_ids);

            if ($remainderUsers->isEmpty()) {
                // check if no users that mean users can deleted or invalid
                $request->reminder_user_ids = [];
                $request->save();
                continue;
            }

            $sentReminderUserIds = $request->sent_reminder_user_ids ?? [];
            foreach ($remainderUsers as $user) {

                if (in_array($user->id, $sentReminderUserIds)) {
                    // already sent that user in past
                    continue;
                }

                $user->notify(new \App\Notifications\SendActiveRemainderNotification($request));
                $sentReminderUserIds[] = $user->id;
            }

            $request->sent_reminder_user_ids = $sentReminderUserIds;
            $request->save();
        }
    }
}
