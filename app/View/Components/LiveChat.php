<?php

namespace App\View\Components;

use App\ChatRoom;
use Illuminate\View\Component;

class LiveChat extends Component
{

    public $room;
    public $type;
    public $video;
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($room, $type, $video)
    {
        $this->room = $room;
        $this->type = $type;
        $this->video = $video;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.live-chat');
    }
}
