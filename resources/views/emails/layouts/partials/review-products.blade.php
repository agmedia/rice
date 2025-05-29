@push('css')
    <style>
        #products, #totals {
            /*font-family: "Roboto", Arial, Helvetica, sans-serif;*/
            font-size: 13px;
            border-collapse: collapse;
            width: 100%;
        }

        #products td, #products th {
            border: 1px solid #fff;
            padding: 8px;
        }

        #totals td, #totals th {
            border: 1px solid #fff;
            padding: 6px 8px;
        }

        #products tr:nth-child(even){background-color: #fff;}

        /*#products tr:hover {background-color: #ddd;}*/

        #products th {
            font-size: 15px;
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #EF4D48;
            color: white;
            border:none;
        }
    </style>
@endpush
<table id="products">
    @foreach ($order->products()->get() as $product)
        <tr>
            <td style="width:150px"><img width="150" src="{{$product->real->thumb}}" alt="{{ $product->name }}"/></td>
            <td>
                <strong>{{ $product->name }}</strong>
                @if ($product->combo())
                    <br>
                    @foreach ($product->combo() as $combo_product)
                        <span class="font-size-sm font-weight-light">{{ $combo_product->translation->name }}</span>
                    @endforeach
                @endif
                <br>  <br>
                <a href="{{url($product->real->url)}}" style="background-color: #EF4D48;color:#fff; padding:6px 12px;border-radius:0.3125rem"> Ocijeni proizvod</a>
            </td>
        </tr>
    @endforeach
</table>




