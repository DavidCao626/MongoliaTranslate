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
    public function create_table($table_name, $arr_field)
    {

        $tmp = $table_name;
        $va = $arr_field;
        Schema::create($tmp, function (Blueprint $table) use ($tmp, $va) {
            $fields = $va[0];  //列字段
            //$fileds_count =  0; //列数
            $table->increments('id');//主键
            foreach ($fields as $key => $value) {
                if ($key == 0) {
                    $table->string($fields[$key]);//->unique(); 唯一
                } else {
                    $table->string($fields[$key]);
                }
                //$fileds_count = $fileds_count + 1;
            }
        });
       // dd(21);
        $value_str = array();
        $id = 1;
        foreach ($va as $key => $value) {
            if ($key != 0) {

                $content = implode(",", $value);
                $content2 = explode(",", $content);
                foreach ($content2 as $key => $val) {
                    $value_str[] = "'$val'";
                }
                $news = implode(",", $value_str);
                $news = "$id," . $news;
               
                DB::insert("insert into $tmp values ($news)");
              
                //$value_str = '';
                $value_str = array();
                $id = $id + 1;
            }
        }
        return 1;
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

                     $bool = Storage::disk('local')->put($fileName,file_get_contents($realPath));   //传成功返回bool值
                        if (1) {
                            $filePath = 'storage/app/'.$fileName;
                           
                          //分批获取Excel的数据（防止内存泄漏）
                        $chunk = new ChunkReadFilter();
                        $chunk->setRows(100, 1);
                        $data = new ExcelToArray('/home/vagrant/Code/MongoliaTranslate/'.$filePath);
                        
                       // var_dump();
                            $tablName = 'db_' . date('YmdHis') . mt_rand(100, 999);
                                $result = $this->create_table($tablName, $data->loadByChunk($chunk)->getData());
                                $data = DB::table($tablName)->simplePaginate(15);
                                $s = array('code' => '1', 'body' => $data, 'message' => '查询成功');

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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
