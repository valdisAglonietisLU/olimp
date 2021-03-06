<?php

namespace App\Http\Controllers;

use App\Olympiad;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class OlympiadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('olympiad.index');
        return view('olympiad.index', ['olympiads' => Olympiad::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('olympiad.create');
        return view('olympiad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('olympiad.create');
        $this->validate($request, [
            'name'       => 'required',
            'date'       => 'required|date',
        ]);

        Olympiad::create($request->only(['name', 'date']));
        Session::flash('msg', trans('messages.olympiad_added', ['name' => $request->name]));
        return redirect()->route('olympiads.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('olympiad.edit');
        return view('olympiad.edit', ['olympiad' => Olympiad::findOrFail($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('olympiad.edit');

        $rules = array(
            'name'       => 'required',
            'date'       => 'required|date',
        );
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('olympiads/'.$id.'/edit')
                ->withErrors($validator)
                ->withInput();
        } else {
            $oly = Olympiad::find($id);
            $oly->name       = $request->get('name');
            $oly->date       = $request->get('date');
            $oly->save();

            Session::flash('message', $oly->name.' updated');
            return Redirect::to('olympiads');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }


    /**
     * Mark as active
     *
     * @param $id
     */
    public function select(Olympiad $olympiad) {
        Auth::user()->activeOlympiad()->associate($olympiad)->save();
        return back();
    }
}
