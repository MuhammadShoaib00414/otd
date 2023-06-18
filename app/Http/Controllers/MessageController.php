<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use App\User;
use App\Group;
use App\Message;
use Carbon\Carbon;
use App\Introduction;
use App\Notification;
use App\MessageThread;
use App\ReportedUsers;
use App\Mail\NewMessage;
use App\Jobs\SendMessages;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class MessageController extends Controller
{

    public $exts = [
        "image" => ["jpg", "jpeg", "png", "gif", 'webp',"jfif",],
        "excel" => ["xls", "xlsx"],
        "powerpoint" => ["ppt", "pptx"],
        "word" => ["doc", "docx"],
        "pdf" => ["pdf"],
        "video" => [ "mkv","webm", "mpg", "mp2", "mpeg", "mpe", "mpv", "ogg", "mp4", "m4p", "m4v", "avi", "wmv", "mov", "qt", "flv", "swf", "avchd"],
        "audio" => ["mp3", "wav", "wma", "ogg", "aac"],
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
       
        Artisan::call('cache:clear');
        $threads = $request->user()
                    ->threads()
                    ->orderBy('updated_at', 'desc')
                    ->simplePaginate(15);
                    
        Artisan::call('cache:clear');
        return view('messages.index')->with([
            'threads' => $threads,
          
        ]);
    }

    public function create(Request $request)
    {
       
        if ($request->has('user'))
      
            $users = collect([User::find($request->input('user'))]);
        else if($request->has('users'))
            $users = User::whereIn('id', $request->input('users'))->orderBy('name', 'desc')->get();
        else if($request->has('group'))
            $users = collect(Group::find($request->group)->admins()->get());
        else

            $users = null;
           
        return view('messages.create')->with([
            'recipients' => $users
        ]);
    }

    public function send(Request $request)
    {
        \Log::info('send meesage.', ['users' => $request ]);

        $test = "test";
        if($request->has('createIndividually') && $request->createIndividually)
        {
            if(count($request->recipients) > 50)
            {
                SendMessages::dispatch($request->recipients, $request->user(), $request->all());
                return redirect('/messages')->withErrors(['message' => 'Your messages are being sent. This could take a minute.']);
            }
            else
            {
                foreach($request->recipients as $recipient)
                    $thread = $this->createThread(collect([$recipient]), $request);
            }
        }
        else
            $thread = $this->createThread(collect($request->recipients), $request);

        return redirect('/messages' . ($request->has('createIndividually') && $request->createIndividually ? '' : '/' . $thread->id));
    }

    private function createThread($users, $request)
    {
       
        $user = $request->user();
        $recipients = collect([$user->id])->merge($users)->unique();

        $recipients = $recipients->map(function ($recipientId) {
            return User::find($recipientId);
        });
        // Check if there's an existing thread for these participants
        $participantIds = $recipients->pluck('id');

        $threads = $request->user()->threads()->wherePivotIn('user_id', $participantIds)->has('participants', count($participantIds))->get();

        if ($threads->count())
        {
            $threads = $threads->filter(function ($thread) use ($recipients) {
                return $thread->participants()->pluck('user_id')->sort()->values() == $recipients->pluck('id')->sort()->values();
            });
            $thread = $threads->last();
            if ($thread) {
                if($request->has('event'))
                {
                    $thread = MessageThread::create([
                        'event_id' => $request->event,
                        'type' => $request->type,
                    ]); 
                }
            } else {
                $thread = MessageThread::create(); 
                $thread->participants()->saveMany($recipients);
            }
        } else {
            $thread = MessageThread::create();
            $thread->participants()->saveMany($recipients);
        }
        if ($request->has('message') && $request->get('message') != null) {
            $message = $thread->messages()->create([
                'sending_user_id' => $user->id,
                'message' => $request->input('message'),
                'subject' => $request->input('subject'),
            ]);
        }
        if ($request->has('attachments')) {
            $fileMessage = $this->uploadAttachments($request->file('attachments'), $thread, $user);
        }
        if (!isset($message)) {
            $message = $fileMessage;
        }

        if(Introduction::participants($request->recipients)->count())
        {
            $introduction = Introduction::participants($request->recipients)->first();
            $are_messages_sent = json_decode($introduction->are_messages_sent, true);
            $are_messages_sent[$user->id] = 1;
            $introduction->update(['are_messages_sent' => json_encode($are_messages_sent)]);
        }

        if($users->count() == 1 && $request->has('is_from_mentor') && !$request->user()->points()->where('key', 'find-mentor')->count())
        {
            $request->user()->awardPoint('find-mentor');
        }

        //for good measure
        $thread->touch();
        event(new MessageSent($user, $thread, $message));

        if($request->user()->threads()->count() >= 5)
            $request->user()->badges()->syncWithoutDetaching(4);

        return $thread;
    }

    public function show(Request $request, $id)
    {
    
        $blockedUsers = ReportedUsers::where('reported_by', $request->user()->id)->where('status', 'blocked')->pluck('user_id')->toArray();
        $whoBlockedMe = ReportedUsers::where('user_id', $request->user()->id)->where('status', 'blocked')->pluck('reported_by')->toArray();
        $blockedUsersIds = array_merge($blockedUsers, $whoBlockedMe);
        $thread = MessageThread::with(['participants', 'messages'])->find($id);
        
        $subject = Message::where('message_thread_id', '=', $thread->id)->where('subject','!=',null)->first();
       
        $thread->messages()
               ->where('sending_user_id', '!=', $request->user()->id)
               ->update(['recipient_read_at' => Carbon::now()]);
        $userId = $thread->participants->where('id', '=', $request->user()->id)->first()->id;
        if (in_array($userId, $blockedUsersIds))
            $blocked = true;
        else
            $blocked = false;
        if ($thread->participants->contains($request->user())) {
            event(new \App\Events\MessageViewed($request->user(), $thread));
            return view('messages.show')->with([
                'thread' => $thread,
                'blocked' => $blocked,
                'subject' => $subject
            ]);
        }
    }

    public function reply(Request $request, $id)
    {

        $thread = MessageThread::with(['participants', 'messages'])->find($id);
        if ($thread->participants->contains($request->user())) {
            $thread->touch();
            $user = $request->user();

            if ($request->has('message') && $request->get('message') != null) {
                $message = $thread->messages()->create([
                    'sending_user_id' => $user->id,
                    'message' => $request->input('message'),
                ]);
            }


            


            // Un-archive this message if any 
            // participants have previously archived
            DB::table('message_participants')
              ->where('message_thread_id', '=', $id)
              ->update(['deleted_at' => null]);
            $thread->load('messages');
            if ($request->has('attachments')) {
                $fileMessage = $this->uploadAttachments($request->file('attachments'), $thread, $user);
            }
            if (!isset($message)) {
                $message = $fileMessage;
            }
            event(new MessageSent($user, $thread, $message));

            return redirect('/messages/'.$id);
        }
    }

    public function uploadAttachments($attachments, $thread, $user) {
        $my_path = "':path'";
        $attachmentMessage = '<div><div class="p-1 attachment-wrapper" title=":filename"><div class="bg-lightest-brand attachment-box"><a href=":path" onclick="showpop(event,'.$my_path.',this)">:content<span style="font-size:12px">:shortname</span></a></div></div></div>';
        $messageHTML = ''; $isAudio = false;
        foreach ($attachments as $attachment) {
            // upload the attachment to public folder public/uploads/messages/attachments
            $fileExt = $attachment->getClientOriginalExtension();
           
            $imgStyle = 'class="popup-preview" style="max-width: 100%; height: 170px;"/><br/>';
            $filename = $attachment->getClientOriginalName();
            $shortFilename = strlen($filename) > 15 ? substr($filename, 0, 15) . '....' . $fileExt : $filename;
            $folder = '/uploads/messages/attachments/' . $thread->id . '/';
            $filepath = $attachment->move(public_path($folder), $filename);
            $cols = (sizeof($attachments) > 1) ? 'col-6 col-md-3' : '';
            if (in_array($fileExt, $this->exts['image'])) {
                $fileInfo = getimagesize($filepath);
                $isLandscape = $fileInfo[0] > $fileInfo[1] ? true : false;
                if ($isLandscape) {
                    $content = '<div class="popup-preview"  style="max-width:100%; height: 170px; background: url(\'' . $folder . $filename . '\'); background-size: cover;"></div>';
                } else {
                    $content = '<img src="' . $folder . $filename . '" ' . $imgStyle;
                }
            } else if (in_array($fileExt, $this->exts['pdf'])) {
                $content = '<img src="/images/pdf.png" ' . $imgStyle;
            } else if (in_array($fileExt, $this->exts['word'])) {
                $content = '<img src="/images/doc.png" ' . $imgStyle;
            } else if (in_array($fileExt, $this->exts['excel'])) {
                $content = '<img src="/images/xls.png" ' . $imgStyle;
            } else if (in_array($fileExt, $this->exts['powerpoint'])) {
                $content = '<img src="/images/ppt.png" ' . $imgStyle;
            } else if (in_array($fileExt, $this->exts['video'])) {
                $content = '<img src="/images/video.png" ' . $imgStyle;
            } else if (in_array($fileExt, $this->exts['audio'])) {
                $isAudio = true;
                $content = '<audio controls><source src="' . $folder . $filename . '" type="audio/mpeg"></audio><br/>';
            } else {
                $content = '<img src="/images/file.png" ' . $imgStyle;
            }
            if ($isAudio) {
                $tempHTML = str_replace(['attachment-wrapper', 'attachment-box', 'bg-lightest-brand'], ['', '', ''], $attachmentMessage);
            } else {
                $tempHTML = $attachmentMessage;
            }
            $messageHTML .= str_replace([':filename', ':path', ':content', ':shortname'], [$filename, $folder . $filename, $content, $shortFilename], $tempHTML);
            $isAudio = false;
        }
        return $thread->messages()->create([
            'sending_user_id' => $user->id,
            'message' => $messageHTML,
        ]);
    }

    public function delete(Request $request, $id)
    {
        DB::table('message_participants')
          ->where('message_thread_id', '=', $id)
          ->where('user_id', '=', $request->user()->id)
          ->update(['left_at' => Carbon::now()]);
        $thread = MessageThread::find($id);
        if($thread->has_active_members)
        {
            $thread->notificationsFor($request->user()->id)->delete();
            \Session::flash('message', "Message deleted");
            \Session::flash('id', "$id");
        }
        else
        {
            $thread->participants()->sync([]);
            $thread->messages()->delete();
            $thread->notifications()->delete();
            $thread->delete();
            $files = glob(public_path('uploads/messages/attachments/' . $id . '/*'));
            array_map('unlink', $files);
        }
        $thread->touch();
        Artisan::call('cache:clear');
        return redirect('/messages');
    }

    public function undoDelete(Request $request, $id)
    {
        Artisan::call('cache:clear');
        DB::table('message_participants')
          ->where('message_thread_id', '=', $id)
          ->where('user_id', '=', $request->user()->id)
          ->update(['left_at' => null]);
          $thread = MessageThread::find($id);
          $thread->notifications()->delete();
          $thread->touch();
          Artisan::call('cache:clear');
          return redirect('/messages');
    }

}
