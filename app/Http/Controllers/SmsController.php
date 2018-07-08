<?php

namespace App\Http\Controllers;

use Overtrue\EasySms\EasySms;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\Storage;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use EasyExcel\Read\ExcelToArray;
use EasyExcel\Read\ChunkReadFilter;
use App\Models\PhoneNumber;

class SmsController extends Controller
{
    public function readfile()
    {
    }

    public function index()
    {
        return view('gjj/smsShow');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function send()
    {
    }
    
    public function excelToolUp(Request $request)
    {
        if ($request->isMethod('POST')) {	//判断是否是POST传值

            $file = $request->file('myfile');	//接文件

            //文件是否上传成功
                if ($file->isValid()) {	//判断文件是否上传成功
                    $originalName = $file->getClientOriginalName(); //源文件名

                    $ext = $file->getClientOriginalExtension();    //文件拓展名

                    $type = $file->getClientMimeType(); //文件类型

                    $realPath = $file->getRealPath();   //临时文件的绝对路径

                    $fileName = date('Y-m-d-H-i-s').'-'.uniqid().'.'.$ext;  //新文件名

                    if (($ext == 'xls') || ($ext == 'xlsx')) {
                        $bool = Storage::disk('local')->put($fileName, file_get_contents($realPath));   //传成功返回bool值
                        if (1) {
                            $filePath = '/home/vagrant/Code/MongoliaTranslate/'.'storage/app/'.$fileName;
                           
                            //分批获取Excel的数据（防止内存泄漏）
                            // $chunk = new ChunkReadFilter();
                            $config = ['firstRowAsIndex' => true];

                            //$chunk->setRows(100, 1);
                            $getData = new ExcelToArray($filePath, $config);
                            $getData->load();

                            // dd($getData->getData());
                            // $data= $data->loadByChunk($chunk)->getData();

                            //$tablName = 'db_' . date('YmdHis') . mt_rand(100, 999);
                            // $result = $this->create_table($tablName, );
                            //  $data = DB::table($tablName)->simplePaginate(15);
                            $s = array('code' => '1', 'body' => $getData->getData(), 'message' => '查询成功');

                            if (1) {
                                $str = json_encode($s);
                                header('Content-type:text/json');
                                exit($str);
                            }
                        } else {
                            return response()->json(['code' => '1', 'body' => '上传失败']);
                        }
                    } else {
                        return response()->json(['code' => '1', 'body' => '文件类型错误']);
                    }
                }
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function state()
    {
        $filePath = '/home/vagrant/Code/MongoliaTranslate/storage/app/' . '1234.xlsx';
        $config = ['firstRowAsIndex' => true];
          
        //分批获取Excel的数据（防止内存泄漏）
        $ss=null;
        $chunk = new ChunkReadFilter();
       // for ($x=0; $x<=10; $x++) {
            $chunk->setRows(5000, 1);
            $data = new ExcelToArray($filePath, $config);
            dd($data->loadByChunk($chunk)->getData());
            foreach ($data->loadByChunk($chunk)->getData() as $user) {
                //dd($user['phone']. $user['nr']);
                $this.store1($user['phone'], $user['nr']);
            }
        //}
       
        $s = array('code' => '1', 'body' => 'ok' , 'message' => '查询成功');

        $str = json_encode($s);
        header('Content-type:text/json');
        exit($str);
    }
    public function store1($phone, $body)
    {
        try {
            $result = $easySms->send($phone, [
                'data'  =>  "【呼市人社局】".$body
            ]);
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
            $message = $exception->getException('submail')->getMessage();
            return response()->json([
                'isok' => 'no',
                'message' => $message ?? '短信发送异常'
            ]);
        }


        return response()->json([
            'isok' => 'ok',
            'message'=>$phone
        ]);
    }
    public function store(Request $request, EasySms $easySms)
    {
        $phone = $request->phone;
        $body=$request->body;
        // 生成4位随机数，左侧补0

        try {
            $result = $easySms->send($phone, [
                'data'  =>  "【呼市人社局】".$body
            ]);
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
            $message = $exception->getException('submail')->getMessage();
            return response()->json([
                'isok' => 'no',
                'message' => $message ?? '短信发送异常'
            ]);
        }


        return response()->json([
            'isok' => 'ok',
            'message'=>$phone
        ]);
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
