<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order - {{ $poNumber }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; font-size: 12px; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; }
        .po-title { font-size: 24px; font-weight: bold; color: #4f46e5; margin: 0; }
        .company-info { float: left; width: 50%; }
        .po-info { float: right; width: 40%; text-align: right; }
        .clear { clear: both; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #94a3b8; margin-bottom: 5px; }
        .supplier-info { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f8fafc; color: #475569; text-align: left; padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 10px; text-transform: uppercase; }
        td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; }
        .total-section { margin-top: 30px; float: right; width: 250px; }
        .total-row { padding: 5px 0; border-bottom: 1px solid #f1f5f9; }
        .grand-total { font-size: 16px; font-weight: bold; color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-top: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; color: #94a3b8; font-size: 10px; padding-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h1 class="po-title">STOCKMASTER</h1>
            <p>Enterprise Inventory Solutions<br>Jl. Digital Tech No. 101<br>Jakarta, Indonesia</p>
        </div>
        <div class="po-info">
            <h2 style="margin:0; font-size: 18px;">PURCHASE ORDER</h2>
            <p>No: {{ $poNumber }}<br>Date: {{ $date }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="supplier-info">
        <div class="section-title">Supplier</div>
        <p><strong>{{ $supplier->name }}</strong><br>
           {{ $supplier->email }}<br>
           {{ $supplier->phone }}<br>
           {{ $supplier->address }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Description</th>
                <th>SKU</th>
                <th style="text-align: right;">Quantity</th>
                <th style="text-align: right;">Unit Price</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($items as $item)
            @php $grandTotal += $item->total; @endphp
            <tr>
                <td>{{ $item->name }}</td>
                <td style="font-family: monospace; color: #64748b;">{{ $item->code }}</td>
                <td style="text-align: right;">{{ $item->quantity }} {{ $item->unit }}</td>
                <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td style="text-align: right; font-weight: bold;">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span style="float:left;">Subtotal</span>
            <span style="float:right;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            <div class="clear"></div>
        </div>
        <div class="total-row">
            <span style="float:left;">Tax (0%)</span>
            <span style="float:right;">Rp 0</span>
            <div class="clear"></div>
        </div>
        <div class="total-row grand-total">
            <span style="float:left;">GRAND TOTAL</span>
            <span style="float:right;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            <div class="clear"></div>
        </div>
    </div>
    <div class="clear"></div>

    <div style="margin-top: 50px;">
        <div class="section-title">Terms & Conditions</div>
        <p style="font-size: 9px; color: #64748b;">
            1. Harap sertakan nomor PO ini pada invoice.<br>
            2. Pengiriman dilakukan dalam waktu 7 hari kerja.<br>
            3. Pembayaran dilakukan 14 hari setelah barang diterima.
        </p>
    </div>

    <div class="footer">
        Generated automatically by StockMaster Enterprise AI
    </div>
</body>
</html>
