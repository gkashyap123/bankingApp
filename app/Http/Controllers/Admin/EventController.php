<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Customer;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('customer')->orderBy('event_date')->paginate(15);

        return view('events.index', compact('events'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();

        return view('events.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|string|max:255',
            'event_date' => 'required|date',
            'note' => 'nullable|string',
            'notified' => 'sometimes|boolean',
        ]);

        $data['notified'] = $request->has('notified') ? (bool)$request->input('notified') : false;

        Event::create($data);

        return redirect()->route('events.index')->with('success', 'Event created.');
    }

    public function show(Event $event)
    {
        $event->load('customer');

        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $customers = Customer::orderBy('name')->get();

        return view('events.edit', compact('event', 'customers'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|string|max:255',
            'event_date' => 'required|date',
            'note' => 'nullable|string',
            'notified' => 'sometimes|boolean',
        ]);

        $data['notified'] = $request->has('notified') ? (bool)$request->input('notified') : false;

        $event->update($data);

        return redirect()->route('events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted.');
    }
}
