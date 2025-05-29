@extends('emails.layouts.base')
@section('content')
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr><td class="ag-mail-tableset">
                <h3>Bok {{ $order->payment_fname }}</h3>
                <p>Vaše mišljenje nam puno znači.<br>
                Svaki komentar i ocjena pomažu nam da budemo još bolji i da zajedno s vama gradimo Rice Kakis.</p>
                <p>Zato vas molimo – odvojite samo 30 sekundi i recite nam kako vam se svidjela ova narudžba:</
            </td>
        </tr>
        <tr>
            <td class="ag-mail-tableset">
                @include('emails.layouts.partials.review-products', ['order' => $order])
            </td>
        </tr>
        <tr>
            <td class="ag-mail-tableset">
                <h3>Zahvala za vaš trud:</h3>
                <p>Za svaku recenziju dodjeljujemo vam <strong>1 loyalty bod.</strong><br><br>
                    🔸 100 bodova = 5 € popusta<br>
                    🔸 200 bodova = 12 € popusta<br><br>

                    Detalje možete vidjeti ovdje:<br>
                   <a style="color:#EF4D48" href="https://www.ricekakis.com/info/loyalty-club">🔗 www.ricekakis.com/loyalty-program </a>
            </td>
        </tr>

    </table>
@endsection
