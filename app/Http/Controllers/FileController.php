<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileManager;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FileManager $filemanager)
    {
        //
        $data = $filemanager->get(['id', 'fileName', 'size']);
        $fileList = $data->toArray();
        return view('home', compact('fileList'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download(FileManager $filemanager, Request $request, $id)
    {
        //
        global $file;
        $pwd = $request->input('pwd');
        $data = $filemanager->where('id', '=', $id)->get()->toArray();
        $fileNameMd = $data[0]['fileNameMd'];
        if ($data[0]['pwd'] != $pwd) {

            return ['retcode' => -1];
        }
        $file = public_path() . Storage::url($fileNameMd);
        // return ['url' => $file];
        // return response()->download($file, $data[0]['fileName'], $headers);
        return response()->streamDownload(function () {
            echo file_get_contents($GLOBALS['file']);
        });
    }

    /**
     * 文件上传
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function uploadFile(Request $request, FileManager $filemanager)
    {
        //获取formdata中名字为file的文件
        $file = $request->file('file');
        $fileName = $request->input('newfilename');
        $password = $request->input('pwd');
        // 判断文件合法性
        if (!$file->isValid()) {
            return ['retcode'=>-1,'msg'=>$file->getErrorMessage()];
        }
        // 判断文件大小是否超过10M
        $tmpFile = $file->getRealPath();
        if (filesize($tmpFile) > 20971500) {
            return ['retcode'=>-1,'msg'=>'文件过大'];
        }
        // 4.是否是通过http请求表单提交的文件
        if (!is_uploaded_file($tmpFile)) {
            return false;
        }
        // 存储文件
        $fileExtension = $file->getClientOriginalExtension();
        $fileNameMd = md5(time()) . mt_rand(0, 99999) . '.' . $fileExtension;
        if (Storage::disk('public')->put($fileNameMd, file_get_contents($tmpFile))) {
            //数据库记录
            $data = [
                'pwd' => $password,
                'fileName' => $file->getClientOriginalName(),
                'size' => filesize($tmpFile),
                'fileNameMd' => $fileNameMd,
            ];
            // dump($data);
            $filemanager->create($data);
            return Storage::url($fileName);
            $data = $filemanager->get(['id', 'fileName', 'size']);
            $fileList = $data->toArray();
            return view('home', compact('fileList'));
        }
    }

    /**
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function create(FileManager $filemanager)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(FileManager $filemanager)
    {
        //
        $data = $filemanager->get(['id', 'fileName', 'size']);
        $fileList = $data->toArray();
        return $fileList;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * 删除文件.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(FileManager $filemanager, Request $request, $id)
    {
        //

        $all = $request->all();
        dump($all);
        $pwd = $request->input('pwd');
        $data = $filemanager->where('id', '=', $id)->get()->toArray();
        $fileNameMd = $data[0]['fileNameMd'];
        if ($data[0]['pwd'] != $pwd) {
            return ['retcode' => -1, 'pwd' => $data[0]['pwd']];
        }
        Storage::disk('public')->delete($fileNameMd);
        $filemanager->where('id', $id)->delete();
        return ['retcode' => 0];
    }
}
