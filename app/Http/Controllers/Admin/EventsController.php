<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    const DATETIME_FORMAT = 'd.m.Y H:i';
    
    private function getCategories() 
    {
        return Category::with(['categories'])
            ->whereNull('parent_id')
            ->where('is_active', 1)
            ->get();
    }
    
    private function getUsers() 
    {
        return User::all();
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $events = Event::with(['category', 'user'])
                ->where('name', 'LIKE', "%$keyword%")
                ->orWhere('event_location_human', 'LIKE', "%$keyword%")
                ->orderBy('date', 'DESC')
                ->paginate();
        } else {
            $events = Event::orderBy('date', 'DESC')->paginate($perPage);
        }

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = $this->getCategories();
        $users = $this->getUsers();
        return view('admin.events.create', compact('categories', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Requests\EventSaveRequest $request)
    {
        $requestData = $request->all();
        $requestData['date'] = Carbon::createFromFormat(self::DATETIME_FORMAT, $request->date)->toDateTimeString();
        Event::create($requestData);

        return redirect('admin/events')->with('success', 'Event added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);

        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $event->date = Carbon::parse($event->date)->format(self::DATETIME_FORMAT);
        $categories = $this->getCategories();
        $users = $this->getUsers();

        return view('admin.events.edit', compact('event', 'categories', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Requests\EventSaveRequest $request, $id)
    {
        $requestData = $request->all();
        $requestData['date'] = Carbon::createFromFormat(self::DATETIME_FORMAT, $request->date)->toDateTimeString();
        $event = Event::findOrFail($id);
        $event->update($requestData);

        return redirect('admin/events')->with('success', 'Event updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Event::destroy($id);

        return redirect('admin/events')->with('info', 'Event deleted!');
    }
}
