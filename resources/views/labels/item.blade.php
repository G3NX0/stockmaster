<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 5px;
            text-align: center;
        }
        .container {
            width: 100%;
            height: 80pt;
            padding: 3px;
            box-sizing: border-box;
        }
        .title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
        }
        .sku {
            font-size: 8px;
            color: #666;
            margin-bottom: 5px;
        }
        .barcode {
            margin: 5px 0;
        }
        .barcode img {
            width: 100%;
            height: 35px;
        }
        .footer {
            font-size: 7px;
            margin-top: 2px;
            border-top: 0.5px solid #eee;
            padding-top: 2px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">{{ $item->nama_barang }}</div>
        <div class="sku">{{ $item->kode_barang }}</div>
        
        <div class="barcode">
            @php
                $barcode = \App\Helpers\BarcodeGenerator::getCode128B($item->kode_barang);
                $width = 100 / strlen($barcode);
            @endphp
            <div style="width: 100%; height: 35px; background: white; white-space: nowrap; overflow: hidden; display: block; line-height: 0; font-size: 0;">
                @foreach(str_split($barcode) as $bar)<div style="display: inline-block; width: {{ $width }}%; height: 35px; background: {{ $bar == '1' ? 'black' : 'white' }}; margin: 0; padding: 0;"></div>@endforeach
            </div>
        </div>

        <div class="footer">
            {{ $item->category?->name }} | {{ $item->unit?->name }}
        </div>
    </div>
</body>
</html>
