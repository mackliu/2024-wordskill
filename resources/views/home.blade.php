<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>
    <link rel="stylesheet" href="{{ asset('public/css/bootstrap.css') }}">
</head>
<body>
    <h1 class="text-center">List Page Layout</h1>
    @if(count($directories) > 0)
        <ul>
            @foreach($directories as $directory)       
                <li>
                    <a href='/01_module_c/heritages/{{$directory}}/'>{{ $directory }}</a>
                </li>   
            @endforeach
        </ul>
    @endif

    @if(count($files) > 0)
        <ul>
            @foreach($files as $file)

                <!--使用mb_substr來去掉檔名前面的日期部分-->
                <li>
                    <a href='/01_module_c/heritages/{{$file}}'>{{ mb_substr($file,11) }}</a>
                </li>

            @endforeach
        </ul>
    @endif
</body>
</html>