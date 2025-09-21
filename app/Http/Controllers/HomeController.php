<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpParser\Node\Scalar\MagicConst\Dir;

class HomeController extends Controller
{
    /**
     * 顯示content-page目錄下的資料夾結構與文件列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //建立三個變數來存放目錄路徑、文件路徑與圖片
        $directories = [];
        $files = [];
        $images = [];

        $basePath = public_path('content-pages');
        //dd($basePath);
        if (!is_dir($basePath)) {
            // 目錄不存在，回傳錯誤訊息
            return response('content-page 目錄不存在', 404);
        }

        try {
            /*
             * 使用 RecursiveDirectoryIterator 與 RecursiveIteratorIterator 來遞迴讀取目錄與文件
             * RecursiveDirectoryIterator 會遞迴讀取目錄與文件，並且可以設定一些選項，
             * 例如 SKIP_DOTS 來忽略 . 與 .. 這兩個特殊目錄
             * RecursiveIteratorIterator 會將 RecursiveDirectoryIterator 產生的目錄與文件進行迭代，
             * 並且可以設定一些選項，例如 SELF_FIRST 來先處理目錄，再處理文件
             */            
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($basePath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            // 將解析出來的$iterator進行foreach迴圈，並將目錄與文件分別存入不同的陣列
            foreach ($iterator as $item) {

                // 取得相對於 basePath 的路徑,替換掉前面的basePath('public/content-pages/')部分
                $relativePath = ltrim(str_replace($basePath, '', $item->getPathname()), DIRECTORY_SEPARATOR);
                // 判斷是目錄還是文件，並分別存入不同的陣列,另外將images中的檔案另外存放
                if ($item->isDir() && $item->getFilename()!=='images') {
                        //排除images目錄後存入目錄陣列
                        $directories[] = $relativePath;
                } else if(explode(DIRECTORY_SEPARATOR, $relativePath)[0] === 'images' && $item->isFile()){
                    //將images中的檔案另外存放,只存圖片檔名不存路徑
                    $images[] = explode(DIRECTORY_SEPARATOR, $relativePath)[1];
                }else if($item->isFile()){
                    //其他的文件存入文件陣列
                    $files[] = $relativePath;
                }
            }
        } catch (\Exception $e) {
            return response('目錄讀取失敗: ' . $e->getMessage(), 500);
        }

        //dd($directories, $files,$images);  //查看解析出來的目錄與文件是否正確
        
        /* 
         * compact()函數將變數轉換為關聯陣列，並傳遞給視圖
         * 比如 compact('directories', 'files','images') 會產生以下的陣列
         * [
         *   'directories' => $directories,
         *   'files' => $files,
         *   'images' => $images
         * ]
         */
        //將$files陣列進行反向排序，讓最新的文件顯示在最前面
        rsort($files);
        return view('home', compact('directories', 'files','images'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
