<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <title>RiceKakis Merchant Feed</title>
        <link>http://www.ricekakis.com</link>
        <description>
            RiceKakis Merchant Feed
        </description>

    @foreach ($items as $item)


        <item>
            <g:id>{{ $item['id'] }}</g:id>
            <g:title><![CDATA[{{ $item['name'] }}]]></g:title>
            <g:description>
                <![CDATA[ {{ $item['description'] }}]]>
            </g:description>
            <g:link>
                {{ $item['slug'] }}
            </g:link>
            <g:brand>{{ $item['brand'] }}</g:brand>
            <g:image_link><![CDATA[{{ $item['image'] }}]]></g:image_link>
            <g:condition>new</g:condition>
            <g:availability>in stock</g:availability>
            <g:price>{{ $item['price'] }} EUR</g:price>

            <g:google_product_category><![CDATA[Food & Drink > Food > Asian Food]]></g:google_product_category>

        </item>
    @endforeach
    </channel>
</rss>
