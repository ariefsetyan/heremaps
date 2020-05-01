<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Space;
use Illuminate\Support\Facades\Storage;

class SpaceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $spaces = Space::orderBy('created_at', 'DESC')->paginate(4);
        return view('space.index',compact('spaces'));
    }

    public function browse(){
        return view('space.browse');
    }

    public function create()
    {
        return view('space.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        $this->validate($request, [
//            'title' => ['required', 'min:3'],
//            'address' => ['required', 'min:5'],
//            'description' => ['required', 'min:10'],
//            'latitude' => ['required'],
//            'longitude' => ['required'],
//            'photo' => ['required'],
//            'photo.*' => ['mimes:jpg,png'],
//        ]);

        $space = $request->user()->spaces()->create($request->except('photo'));
        $spacesPhoto = [];
        foreach ($request->file('photo') as $file){
            $path = Storage::disk('public')->putFile('spaces',$file);
            $spacesPhoto[] = [
                'space_id' => $space->id,
                'path' => $path
            ];
        }
        $space->photos()->insert($spacesPhoto);

        return redirect()->route('space.index')->with('status','Space Create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $space = Space::findOrFail($id);
        return view('space.show',compact('space'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $space = Space::findOrFail($id);
        if ($space->user_id != request()->user()->id) {
            return redirect()->back();
        }
        return view('space.edit', compact('space'));
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
        $space = Space::findOrFail($id);
        if ($space->user_id != request()->user()->id) {
            return redirect()->back();
        }
        $this->validate($request, [
            'title' => ['required', 'min:3'],
            'address' => ['required', 'min:5'],
            'description' => ['required', 'min:10'],
            'latitude' => ['required'],
            'longitude' => ['required'],
        ]);
        $space->update($request->all());
        return redirect()->route('space.index')->with('status', 'Space updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $space = Space::findOrFail($id);
//        dd($space->photos);
        if ($space->user_id != request()->user()->id) {
            return redirect()->back();
        }
        foreach ($space->photos as $photo){
            Storage::delete('public/'.$photo->path);
        }
        $space->delete();
        return redirect()->route('space.index')->with('status', 'Space Delete!');
    }
}
