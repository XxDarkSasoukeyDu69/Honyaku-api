<?php

namespace App\Http\Controllers\Api;

use App\File;
use App\Http\Controllers\Controller;
use App\Mail\MailFileTranslateFinished;
use App\Mail\MailOrderAccept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            ->select('fileName', 'id', 'fileMail')
            ->whereRaw('state = ? and automatic = false', ['noTranslate'])
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
            'fileName'      => 'required',
            'fileMail'      => 'required',
            'file'          => 'required',
            'targetLang'    => 'required',
            'sourceLang'    => 'required',
            'fileType'      => 'required',
        ]);

        $file = null;

        if($validatedData['fileType'] === 'json') {
            $validatedData['contentToTranslate'] = file_get_contents($validatedData['file']);
            $file = File::create($validatedData);
        } else if ($validatedData['fileType'] === 'txt') {

            $validatedData['contentToTranslate'] = $this->convertTextToJsonObject(file_get_contents($validatedData['file']));

            $file = File::create($validatedData);

        } else if ($validatedData['fileType'] === 'docx') {

            $striped_content = $this->convertDocxToText($validatedData['file']);

            $validatedData['contentToTranslate'] = $this->convertTextToJsonObject($striped_content);
            $file = File::create($validatedData);
        }

        if(!is_null($file)) {
            Mail::to($validatedData['fileMail'])->send(new MailOrderAccept($file->id));
        }

        return response()->json(['result' => $file, 'test'=> !is_null($file)]);
    }


    public function convertTextToJsonObject($text) {
        $strs = preg_split('/(\.|\?|\!)(\s)/',$text);

        $arr = array();
        foreach ($strs as $str) {
            $uuid = (string) Str::uuid();
            $arr[$uuid] = $str;
        }

        return json_encode($arr, JSON_FORCE_OBJECT);
    }

    public function convertDocxToText($file) {
        $content = '';
        $zip = zip_open($file);

        if (!$zip || is_numeric($zip)) return false;

        while ($zip_entry = zip_read($zip)) {
            if (zip_entry_open($zip, $zip_entry) == false) continue;
            if (zip_entry_name($zip_entry) != "word/document.xml") continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
            zip_entry_close($zip_entry);
        }

        zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
         return strip_tags($content);
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrder($id)
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

    public function setRunning(Request $request, $id)
    {
        $file = File::find($id)->update(['translator' => auth()->user()->id, 'state' => 'running']);
        return response()->json(['file' => $file]);
    }

    public function setFinish(Request $request, $id) {

        $file = File::find($id);
        $file->update(['state' => 'effected']);

        if($file['fileType'] === 'json') {
            $fp = json_decode($file['contentToTranslate'], JSON_PRETTY_PRINT);
            Mail::to($file['fileMail'])->send(new MailFileTranslateFinished($request['messageContent'], json_encode($fp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $file,  'json'));
        }
        else {
            $fp = json_decode($file['contentToTranslate'], JSON_PRETTY_PRINT);
            Mail::to($file['fileMail'])->send(new MailFileTranslateFinished($request['messageContent'], $this->loop($fp, ""), $file,  'txt'));
        }

        return response()->json(['file' => $file['fileType']]);

    }

    public function loop($obj, $str) {
        foreach ($obj as $f => $f_item) {
            if(is_array($obj[$f])) {
                return $this->loop($obj[$f], $str);
            }
            $str .= $obj[$f];
        }
        return $str;
    }

    public function getMyRunningTranslation() {
        $files = File::query()
            ->select('fileName', 'id', 'fileMail')
            ->whereRaw('translator = ? AND state = ? and automatic = false', [auth()->user()->id, "running"])
            ->get();
        return response()->json(['files' => $files, 'id' => auth()->user()->id]);
    }

    public function getMyEffectedTranslation() {
        $files = File::query()
            ->select('fileName', 'id', 'fileMail')
            ->whereRaw('translator = ? AND state = ? and automatic = false', [auth()->user()->id, "effected"])
            ->get();
        return response()->json(['files' => $files]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $file = File::find($id);
        $file->delete();

        return response()->json(['status'=>'ok']);
    }
}
