<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
    <link rel="stylesheet" href="{{mix('css/home.css')}}">
    <script src="{{ mix('js/home.js') }}"></script>
</head>

<body>
    <div class="container">
        <!-- Content here -->
        <button type="button" class="btn btn-primary">上传</button>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>文件名称</th>
                    <th>大小</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($fileList as $fileItem)
                <tr>
                    <td>{{$fileItem['fileName']}}</td>
                    <td>{{$fileItem['size']}}</td>
                    <td>
                        <button type="button" class="btn btn-default">下载</button>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger">删除</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</body>

</html>