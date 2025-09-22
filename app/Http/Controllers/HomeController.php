<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PhpParser\Node\Scalar\MagicConst\Dir;

class HomeController extends Controller
{

    //定義三個屬性來存放目錄路徑、文件路徑與圖片，放在類別層級，讓其他在此類別中的方法也能使用
    protected $basePath;
    protected $directories = [];
    protected $files = [];
    protected $images = [];

    /**
     * 顯示content-page目錄下的資料夾結構與文件列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 嘗試從快取中取得解析結果
        $cacheKey = 'content_structure';
        $cacheTime = 60; // 快取 60 分鐘

        $cachedData = Cache::remember($cacheKey, $cacheTime, function () {
            return $this->parseContentDirectory();
        });

        // 將快取的資料設定到類別屬性
        $this->directories = $cachedData['directories'];
        $this->files = $cachedData['files'];
        $this->images = $cachedData['images'];

        // index() 的主要工作是初始化並解析目錄結構，然後調用 listDirectoriesAndFiles() 來處理和顯示目錄與文件的列表
        return $this->listDirectoriesAndFiles();
    }

    /**
     * 解析內容目錄並回傳結構化資料
     * 這個方法會被快取，減少重複的檔案系統操作
     */
    protected function parseContentDirectory()
    {
        $directories = [];
        $files = [];
        $images = [];

        //建立三個變數來存放目錄路徑、文件路徑與圖片
        $basePath = public_path('content-pages');

        if (!is_dir($basePath)) {
            // 目錄不存在，回傳空資料
            return [
                'directories' => [],
                'files' => [],
                'images' => []
            ];
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
                        //排除images目錄後,檢查路徑是否己存在陣列中，若不存在則存入目錄陣列，確保資料夾名稱不重複
                        if(!in_array($relativePath, $directories)){

                            $directories[] = $relativePath;
                        }
                } else if(explode(DIRECTORY_SEPARATOR, $relativePath)[0] === 'images' && $item->isFile()){
                    //將images中的檔案另外存放,只存圖片檔名不存路徑
                    if(!in_array($relativePath, $images)){
                        $images[] = explode(DIRECTORY_SEPARATOR, $relativePath)[1];
                    }
                }else if($item->isFile()){
                    //其他的文件存入文件陣列
                    if(!in_array($relativePath, $files)){
                        $files[] = $relativePath;
                    }
                }
            }
        } catch (\Exception $e) {
            // 錯誤時回傳空資料
            return [
                'directories' => [],
                'files' => [],
                'images' => []
            ];
        }

        // 回傳解析結果供快取使用
        return [
            'directories' => $directories,
            'files' => $files,
            'images' => $images
        ];
    }

    /*
     * 列出指定目錄下的所有子目錄與文件
     * $directoryName 目錄名稱,預設為空字串,表示只列出basePath下的第一層目錄與文件
     */
function listDirectoriesAndFiles($directoryName=""){
    // 確保有資料可用
    $this->ensureContentDataLoaded();

    //如果沒有指定目錄名稱,則列出basePath下的第一層目錄與文件
    if($directoryName===""){
        $dirs=[];
        foreach($this->directories as $dir){
            //使用strpos檢查目錄名稱中是否包含目錄分隔符號,若不包含表示是第一層目錄,則存入陣列
            if(strpos($dir, DIRECTORY_SEPARATOR)===false){
                $dirs[]=$dir;
            }
        }
        

        $files=[];
        foreach($this->files as $file){
            //使用strpos檢查文件名稱中是否包含目錄分隔符號,若不包含表示是第一層文件,則存入陣列
            if(strpos($file, DIRECTORY_SEPARATOR)===false){
                $files[]=$file;
            }
        }

        //dd($dirs,$files);
    }else{
        //若有指定目錄名稱,則列出該目錄下的子目錄與文件
        $dirs=[];
        dd($directoryName,$this->directories);
        foreach($this->directories as $dir){
            //使用strpos檢查目錄名稱中是否包含指定的目錄名稱與目錄分隔符號,若包含表示是該目錄下的子目錄,則存入陣列
            echo $dir."<br>";
            if(strpos($dir, $directoryName.DIRECTORY_SEPARATOR)===0){
                //使用substr來去掉前面的目錄名稱與目錄分隔符號
                $subDir=substr($dir, strlen($directoryName.DIRECTORY_SEPARATOR));
                //再使用strpos檢查子目錄名稱中是否還包含目錄分隔符號,若不包含表示是該目錄下的第一層子目錄,則存入陣列
                if(strpos($subDir, DIRECTORY_SEPARATOR)===false){
                    $dirs[]=$subDir;
                }
            }
        }
        $files=[];
        foreach($this->files as $file){
            //使用strpos檢查文件名稱中是否包含指定的目錄名稱與目錄分隔符號,若包含表示是該目錄下的文件,則存入陣列
            if(strpos($file, $directoryName.DIRECTORY_SEPARATOR)===0){
                //使用substr來去掉前面的目錄名稱與目錄分隔符號
                $subFile=substr($file, strlen($directoryName.DIRECTORY_SEPARATOR));
                //再使用strpos檢查子文件名稱中是否還包含目錄分隔符號,若不包含表示是該目錄下的第一層文件,則存入陣列
                if(strpos($subFile, DIRECTORY_SEPARATOR)===false){
                    $files[]=$subFile;
                }
            }
        }
        dd($dirs,$files);
    }
        sort($dirs); //將目錄名稱進行排序
        rsort($files); //將文件名稱進行反向排序,讓最新的文件顯示在最前面
        return view('home', ['directories' => $dirs, 'files' => $files]);

    //return view('home', ['directories' => $this->directories, 'files' => $this->files,'images'=>$this->images]);


}
function handleHeritages($path = null){
    // 確保有目錄資料可用
    $this->ensureContentDataLoaded();

    //先判斷$path是否為空,若為空表示是heritages目錄,則列出該目錄下的子目錄與文件
    if(empty($path)){
        return $this->listDirectoriesAndFiles();
    }else{
        //根$path有沒有包含副檔名,若有包含表示是文件,若沒有包含表示是目錄
        if(pathinfo($path, PATHINFO_EXTENSION)!==""){
            //表示是文件,則讀取該文件內容並顯示
            dd("讀取文件內容:".$path);
        }else{
            //表示是目錄,則列出該目錄下的子目錄與文件
           // dd($path);
            return $this->listDirectoriesAndFiles($path);
        }
    }
}

/**
 * 確保內容資料已載入
 * 如果類別屬性為空，從快取中重新載入
 */
protected function ensureContentDataLoaded()
{
    if (empty($this->directories) && empty($this->files) && empty($this->images)) {
        $cacheKey = 'content_structure';
        $cachedData = Cache::get($cacheKey);

        if (!$cachedData) {
            // 如果快取也沒有資料，重新解析
            $cachedData = Cache::remember($cacheKey, 60, function () {
                return $this->parseContentDirectory();
            });
        }

        $this->directories = $cachedData['directories'] ?? [];
        $this->files = $cachedData['files'] ?? [];
        $this->images = $cachedData['images'] ?? [];
    }
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

    /**
     * 清除內容結構快取
     * 當檔案系統有變更時可以呼叫此方法
     */
    public function clearCache()
    {
        Cache::forget('content_structure');
        return response()->json(['message' => '快取已清除']);
    }
    /**
     * 解析網頁內容並提取所需資料。
     *
     * 此函式會執行以下步驟：
     * 1. 從指定的網址取得 HTML 內容。
     * 2. 使用 DOMDocument 解析 HTML 結構。
     * 3. 利用 DOMXPath 查詢特定的節點或元素。
     * 4. 將查詢到的資料進行整理與格式化，並存入陣列或物件中。
     * 5. 回傳整理後的資料結果。
     *
     * @param string $url 網頁的網址
     * @return array 解析後的資料陣列
     */

        // 手動解析檔案內容中的 front matter（前置資料區塊）
        function parsePage($page){
            // 定義分隔符號，通常 front matter 會用三個連字號作為區塊分隔
            $delimiter = '---';
            // 以分隔符號將內容切成三個部分：前置資料、分隔符號、主體內容
            $parts = explode($delimiter, $content, 3);

            // 如果沒有找到完整的 front matter，則直接回傳全部內容作為 body
            if (count($parts) < 3) {
                return [
                    'meta' => [], // meta 為空陣列
                    'body' => $content // body 為原始內容
                ];
            }

            // 取得 front matter 區塊，並以換行符號分割成多行
            $metaLines = explode("\n", trim($parts[1]));
            $meta = [];

            // 逐行解析 front matter，每行格式通常為 key: value
            foreach ($metaLines as $line) {
                // 跳過空行
                if (empty(trim($line))) continue;

                // 以冒號分割成鍵值對
                $pair = explode(':', $line, 2);
                $key = trim($pair[0]); // 取得 key
                $value = trim($pair[1] ?? ''); // 取得 value，若沒值則設為空字串

                // 如果 key 是 tags，則以逗號分割成陣列
                if ($key === 'tags') {
                    $meta[$key] = array_map('trim', explode(',', $value));
                } else {
                    $meta[$key] = $value; // 其他 key 直接存入 meta 陣列
                }
            }

            // 取得主體內容並去除前後空白
            $body = trim($parts[2]);

            // 回傳解析後的 meta 資料與主體內容
            return [
                'meta' => $meta,
                'body' => $body
            ];
        }

    // 取得指定頁面內容，並解析 front matter 與主體內容
    public function getPage($page)
    {
        // 組合檔案路徑，指向 public/heritages 資料夾下的指定檔案
        $filePath = public_path('heritages/' . $page);

        // 檢查檔案是否存在
        if (File::exists($filePath)) {
            // 讀取檔案內容
            $content = File::get($filePath);

            // 呼叫輔助函式解析 front matter 與主體內容
            $parsedContent = $this->parseFrontMatter($content);

            // 將解析後的資料傳給 welcome 視圖
            return view('welcome', [
                'meta' => $parsedContent['meta'], // 前置資料
                'body' => $parsedContent['body']  // 主體內容
            ]);
        } else {
            // 若檔案不存在則回傳 404 錯誤
            abort(404, 'File not found');
        }
    }
}
