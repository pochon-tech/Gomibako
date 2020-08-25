<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
use App\Http\Requests\ContactInputPost; 

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = Contact::latest('id')->paginate(3);
        return view('contacts.index', compact('contacts'))->with('i', (request()->input('page', 1) - 1) * 3);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contacts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ContactInputPost  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactInputPost $request)
    {
        Contact::create($request->all());
        return redirect()->route('contacts.index')->with('success','登録完了');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contact  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        return view('contacts.show',compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Contact  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        return view('contacts.edit',compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\ContactInputPost  $request
     * @param  \App\Contact  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ContactInputPost $request, Contact $contact)
    {
        $contact->update($request->all());
        return redirect()->route('contacts.index')->with('success','編集完了');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success','削除完了しました');
    }
}
