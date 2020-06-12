<?php

namespace App\Http\Controllers\Api;

use App\File;
use App\Http\Controllers\Controller;
use App\Mail\MailOrderAccept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $files = File::query()
            ->select('filename', 'id', 'filemail')
            ->where('state', '=', 'noTranslate')
            ->get();

        return response()->json(['files' => $files]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fileName' => 'required',
            'fileMail' => 'required',
            'file' => 'required',
            'targetLang' => 'required',
            'sourceLang' => 'required',
            'fileType' => 'required',
        ]);

        $validatedData['contentToTranslate'] = file_get_contents($validatedData['file']);
        $file = File::create($validatedData);

        Mail::to($validatedData['fileMail'])->send(new MailOrderAccept($file->id));

        return response()->json(['result' => $file]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $file = File::find($id);
        return response()->json(['file' => $file]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTranslation(Request $request, $id)
    {
        $validatedData = $request->validate([
            'file' => 'required'
        ]);

        $file = File::find($id)->update(['contentTranslate' => $validatedData['file']]);

        return response()->json(['file' => $file]);

    }

    public function updateStatus(Request $request, $id)
    {
        $file = File::find($id)->update(['translator' => auth()->user()->id, 'state' => 'running']);
        return response()->json(['file' => $file]);
    }

    public function getMyRunningTranslation() {
        $files = File::query()
            ->select('filename', 'id', 'filemail')
            ->whereRaw('translator = ? AND state = ?', [auth()->user()->id, "running"])
            ->get();
        return response()->json(['files' => $files, 'id' => auth()->user()->id]);
    }

    public function getMyEffectedTranslation() {
        $files = File::query()
            ->select('filename', 'id', 'filemail')
            ->whereRaw('translator = ? AND state = ?', [auth()->user()->id, "effected"])
            ->get();
        return response()->json(['files' => $files]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
