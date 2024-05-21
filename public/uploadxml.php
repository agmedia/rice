<?php
$conn = mysqli_connect("127.0.0.1", "root", "bakanal", "ricekakis");

$affectedRow = 0;

$xml = simplexml_load_file("rice.xml") or die("Error: Cannot create object");

foreach ($xml->children() as $row) {
    $sku = $row->ID;

if (!empty($row->Stock) ) {
    $quantity = $row->Stock;
    }



    if ( !empty($row->ProductAttributes)) {

        $attributes = unserialize($row->ProductAttributes);
        if (isset($attributes['podaci-o-prehrani']['value'])) {
            $podaci = $attributes['podaci-o-prehrani']['value'];
        }

        if (isset($attributes['sastojci']['value'])) {
            $sastojci = $attributes['sastojci']['value'];
        }


    } else{
        $podaci ='';
        $sastojci = '';
    }



    $sql = "INSERT INTO `temp_products` ( `sku`,   `quantity`,  `podaci`, `sastojci`)
                                 VALUES ('" . $sku . "','" . $quantity . "','" . $podaci . "','" . $sastojci . "' )";




    $result = mysqli_query($conn, $sql);

    if (! empty($result)) {
        $affectedRow ++;
    } else {
        $error_message = mysqli_error($conn) . "\n";
    }
}



function categoryList(SimpleXmlElement $categories){
    $cats = array();
    foreach($categories as $category){
        $cats[] = (string) $category;
    }

    return implode(', ', $cats);

}



?>
<h2>Insert XML Data to MySql Table Output</h2>
<?php
if ($affectedRow > 0) {
    $message = $affectedRow . " records inserted";
} else {
    $message = "No records inserted";
}

?>
<style>
    body {
        max-width:550px;
        font-family: Arial;
    }
    .affected-row {
        background: #cae4ca;
        padding: 10px;
        margin-bottom: 20px;
        border: #bdd6bd 1px solid;
        border-radius: 2px;
        color: #6e716e;
    }
    .error-message {
        background: #eac0c0;
        padding: 10px;
        margin-bottom: 20px;
        border: #dab2b2 1px solid;
        border-radius: 2px;
        color: #5d5b5b;
    }
</style>
<div class="affected-row"><?php  echo $message; ?></div>
<?php if (! empty($error_message)) { ?>
    <div class="error-message"><?php echo nl2br($error_message); ?></div>
<?php } ?>

/*  CREATE TABLE `temp_products` (
`id` bigint UNSIGNED NOT NULL,
`sku` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
`price` decimal(15,4) NOT NULL DEFAULT '0.0000',
`quantity` int UNSIGNED NOT NULL DEFAULT '0',
`images` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
`category` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL,
`description` text COLLATE utf8mb4_unicode_ci,
`podaci` text COLLATE utf8mb4_unicode_ci,
`sastojci` text COLLATE utf8mb4_unicode_ci,
`meta_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`meta_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; */
