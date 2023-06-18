<?php

namespace App\Http\Controllers;

use App\User;
use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Kutia\Larafirebase\Facades\Larafirebase;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
   
        $notificationsQuery = $request->user()->notifications()->orderBy('created_at', 'desc');
        if(!getsetting('is_ideations_enabled'))
            $notificationsQuery->where('notifiable_type', '!=', 'App\Ideation');

        //gets rid of different notifications for the same notifiable object
    	$groupedNotifications = $notificationsQuery->get()->groupBy(['notifiable_type', 'notifiable_id']);

        $notifications = collect([]);

        //group notifications together and set a priority for unviewed notifications
        foreach($groupedNotifications as $notifiableTypes)
        {
            foreach($notifiableTypes as $notifiables)
            {
                if($notifiables->where('action', 'Post Reported')->count())
                {
                    foreach($notifiables as $notifiable)
                        $notifications->push($notifiable);
                }
                elseif($notifiables->count() > 1)
                {  
                    if($notifiables->whereNull('viewed_at')->count())
                        $notifications->push($notifiables->whereNull('viewed_at')->first());
                    else
                        $notifications->push($notifiables->first());
                }
                else
                    $notifications->push($notifiables->first());
            }
        }

        $request->user()->notifications()->where('action', 'Ideation Not Accepted')->update(['viewed_at' => \Carbon\Carbon::now()]);
        $notifications = Notification::whereIn('id', $notifications->pluck('id'));
       
        if ($request->ajax()) {
          
           // $notifications = $notifications->orderBy('created_at', 'desc')->paginate(10);
            $html = view('notifications.load-notification')->with([
                'notifications' => $notifications->orderBy('created_at', 'desc')->paginate(10),
            ])->render();
           //dd($request->);
          
            $response = [
              //  'nextPageUrl' =>  URL::current().'?page='.$request->page,
                'html' => $html
            ];
            return $response;
        }
    	return view('notifications.index')->with([
    		'notifications' => $notifications->orderBy('created_at', 'desc')->paginate(10),
    	]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->notifications()->update(['viewed_at' => \Carbon\Carbon::now()]);

        return redirect('/notifications');
    }
}
