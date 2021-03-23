<?php
include 'UUID.php';

// use with http://homesteadswitchesmx.test/import.php?option=X
$option = isset( $_REQUEST['option'] ) ? $_REQUEST['option']  : '';

function removeLinkBreaksReplaceSingleQuote( $string ) {
  // Remove linebreaks
  $string = preg_replace( "/\r|\n/", "", $string );

  // Sanitise quotes
  $string = str_replace("'", "''", $string);

  return $string;
}

if( 'brand' == $option ) {
    // **********************************************
    //
    // brand
    // - These go in /content/taxonomies/brands/
    //
    // **********************************************

    $str = file_get_contents('https://switches.mx/wp-json/wp/v2/brand?per_page=100');
    $json = json_decode($str, true); // decode the JSON into an associative array
    // var_dump( $json );

    foreach ($json as $brand) {
        $output = '';
        $path = 'imported/brands/';
        $date = new DateTime($brand['date']);
        $output .= "title: " . $brand['title']['rendered'] . "\n";
        $output .= "updated_by: 346c3162-6b01-4097-b7ee-8c4482d3ec52\n";
        $output .= "updated_at: " . date_timestamp_get($date) . "\n";
        $output .= "blueprint: brands\n";

        // write to file
        file_put_contents($path . $brand['slug'] . ".yaml", $output);
        echo 'File written: ' . $brand['slug'] . 'yaml<br />';
    }
}

if( 'manufacturer' == $option ) {
    // **********************************************
    //
    // manufacturer
    // - These go in /content/taxonomies/manufacturers/
    //
    // **********************************************

    $str = file_get_contents('https://switches.mx/wp-json/wp/v2/manufacturer?per_page=100');
    $json = json_decode($str, true); // decode the JSON into an associative array

    foreach ($json as $brand) {
        $output = '';
        $path = 'imported/manufacturer/';
        $date = new DateTime($brand['date']);
        $output .= "title: " . $brand['title']['rendered'] . "\n";
        $output .= "website: '" . $brand['acf']['website'] . "'\n";
        $output .= "updated_by: 346c3162-6b01-4097-b7ee-8c4482d3ec52\n";
        $output .= "updated_at: " . date_timestamp_get($date) . "\n";
        $output .= "blueprint: manufacturers\n";

        // write to file
        file_put_contents($path . $brand['slug'] . ".yaml", $output);
        echo 'File written: ' . $brand['slug'] . '.yaml<br />';
    }
}

if( 'vendor' == $option ) {
    // **********************************************
    //
    // vendor
    // - These go in /content/collections/vendors/
    //
    // **********************************************

    $str = file_get_contents('https://switches.mx/wp-json/wp/v2/vendor?per_page=100');
    $json1 = json_decode($str, true); // decode the JSON into an associative array

    $str2 = file_get_contents('https://switches.mx/wp-json/wp/v2/vendor?per_page=100&page=2');
    $json2 = json_decode($str2, true); // decode the JSON into an associative array

    $json = array_merge( $json1, $json2);

    $filesWritten = 0;

    foreach ($json as $item) {
        $output = '';
        $path = 'imported/vendors/';
        $date = new DateTime($item['date']);

        $output .= "title: '" . $item['title']['rendered'] . "'\n";
        $output .= "country: " . $item['acf']['country'] . "\n";
        $output .= "currency: " . $item['acf']['currency'] . "\n";
        $output .= "website: '" . $item['acf']['website'] . "'\n";

        // Favicon
        $vendorIcon = 'https://switches.mx/img/vendors/vendor-' . $item['id'] . '.png';
        $vendorIconSize = getimagesize( $vendorIcon );

        if( $vendorIconSize && is_array( $vendorIconSize ) ) {
            // if vendor image exists in codebase
            $faviconNewFilename = $item['id'] . '-' . $item['slug'] . '.png';
            copy($vendorIcon, 'imported/images/vendors/favicon/' . $faviconNewFilename);
            $output .= "favicon: vendors/favicons/" . $faviconNewFilename . "\n";
        } else {
            if( isset( $item['acf']['favicon'] ) && $item['acf']['favicon'] &&  $item['acf']['favicon']['url'] ) {
                // if an image is uploaded then use that
                $favicon = $item['acf']['favicon']['url'];
                $faviconNewFilename = $item['id'] . '-' . $item['acf']['favicon']['filename'];
                copy($favicon, 'imported/images/vendor/favicon/' . $faviconNewFilename);
                $output .= "favicon: vendors/favicons/" . $faviconNewFilename . "\n";
            }
        }

        // Social
        if( isset( $item['acf']['social'] ) && is_array( $item['acf']['social'] ) && count( $item['acf']['social'] ) > 0 ) {
            $output .= "social:\n";
            foreach ($item['acf']['social'] as $social) {
                $output .= "  -\n";
                $output .= "    network: " . $social['network'] . "\n";
                $output .= "    item_url: '" . $social['url'] . "'\n";
                $output .= "    type: new_set\n";
                $output .= "    enabled: true\n";
            }
        }
        // favicon: vendors/favicons/vendor-280.png
        $output .= "updated_by: 346c3162-6b01-4097-b7ee-8c4482d3ec52\n";
        $output .= "updated_at: " . date_timestamp_get($date) . "\n";
        $output .= "blueprint: vendors\n";

        // write to file
        file_put_contents($path . $item['slug'] . ".yaml", $output);
        // echo '<p>File written: ' . $item['slug'] . '.yaml</p>';
        $filesWritten++;
    }
    echo "<h3>" . $filesWritten . " files written</h3>";
}

if( 'currency' == $option ) {
    $data = array (
        'USD' =>
        array (
          'symbol' => '$',
          'name' => 'US Dollar',
          'symbol_native' => '$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'USD',
          'name_plural' => 'US dollars',
        ),
        'CAD' =>
        array (
          'symbol' => 'CA$',
          'name' => 'Canadian Dollar',
          'symbol_native' => '$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'CAD',
          'name_plural' => 'Canadian dollars',
        ),
        'EUR' =>
        array (
          'symbol' => '€',
          'name' => 'Euro',
          'symbol_native' => '€',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'EUR',
          'name_plural' => 'euros',
        ),
        'AED' =>
        array (
          'symbol' => 'AED',
          'name' => 'United Arab Emirates Dirham',
          'symbol_native' => 'د.إ.‏',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'AED',
          'name_plural' => 'UAE dirhams',
        ),
        'AFN' =>
        array (
          'symbol' => 'Af',
          'name' => 'Afghan Afghani',
          'symbol_native' => '؋',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'AFN',
          'name_plural' => 'Afghan Afghanis',
        ),
        'ALL' =>
        array (
          'symbol' => 'ALL',
          'name' => 'Albanian Lek',
          'symbol_native' => 'Lek',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'ALL',
          'name_plural' => 'Albanian lekë',
        ),
        'AMD' =>
        array (
          'symbol' => 'AMD',
          'name' => 'Armenian Dram',
          'symbol_native' => 'դր.',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'AMD',
          'name_plural' => 'Armenian drams',
        ),
        'ARS' =>
        array (
          'symbol' => 'AR$',
          'name' => 'Argentine Peso',
          'symbol_native' => '$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'ARS',
          'name_plural' => 'Argentine pesos',
        ),
        'AUD' =>
        array (
          'symbol' => 'AU$',
          'name' => 'Australian Dollar',
          'symbol_native' => '$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'AUD',
          'name_plural' => 'Australian dollars',
        ),
        'AZN' =>
        array (
          'symbol' => 'man.',
          'name' => 'Azerbaijani Manat',
          'symbol_native' => 'ман.',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'AZN',
          'name_plural' => 'Azerbaijani manats',
        ),
        'BAM' =>
        array (
          'symbol' => 'KM',
          'name' => 'Bosnia-Herzegovina Convertible Mark',
          'symbol_native' => 'KM',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'BAM',
          'name_plural' => 'Bosnia-Herzegovina convertible marks',
        ),
        'BDT' =>
        array (
          'symbol' => 'Tk',
          'name' => 'Bangladeshi Taka',
          'symbol_native' => '৳',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'BDT',
          'name_plural' => 'Bangladeshi takas',
        ),
        'BGN' =>
        array (
          'symbol' => 'BGN',
          'name' => 'Bulgarian Lev',
          'symbol_native' => 'лв.',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'BGN',
          'name_plural' => 'Bulgarian leva',
        ),
        'BHD' =>
        array (
          'symbol' => 'BD',
          'name' => 'Bahraini Dinar',
          'symbol_native' => 'د.ب.‏',
          'decimal_digits' => 3,
          'rounding' => 0,
          'code' => 'BHD',
          'name_plural' => 'Bahraini dinars',
        ),
        'BIF' =>
        array (
          'symbol' => 'FBu',
          'name' => 'Burundian Franc',
          'symbol_native' => 'FBu',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'BIF',
          'name_plural' => 'Burundian francs',
        ),
        'BND' =>
        array (
          'symbol' => 'BN$',
          'name' => 'Brunei Dollar',
          'symbol_native' => '$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'BND',
          'name_plural' => 'Brunei dollars',
        ),
        'BOB' =>
        array (
          'symbol' => 'Bs',
          'name' => 'Bolivian Boliviano',
          'symbol_native' => 'Bs',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'BOB',
          'name_plural' => 'Bolivian bolivianos',
        ),
        'BRL' =>
        array (
          'symbol' => 'R$',
          'name' => 'Brazilian Real',
          'symbol_native' => 'R$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'BRL',
          'name_plural' => 'Brazilian reals',
        ),
        'BWP' =>
        array (
          'symbol' => 'BWP',
          'name' => 'Botswanan Pula',
          'symbol_native' => 'P',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'BWP',
          'name_plural' => 'Botswanan pulas',
        ),
        'BYN' =>
        array (
          'symbol' => 'Br',
          'name' => 'Belarusian Ruble',
          'symbol_native' => 'руб.',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'BYN',
          'name_plural' => 'Belarusian rubles',
        ),
        'BZD' =>
        array (
          'symbol' => 'BZ$',
          'name' => 'Belize Dollar',
          'symbol_native' => '$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'BZD',
          'name_plural' => 'Belize dollars',
        ),
        'CDF' =>
        array (
          'symbol' => 'CDF',
          'name' => 'Congolese Franc',
          'symbol_native' => 'FrCD',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'CDF',
          'name_plural' => 'Congolese francs',
        ),
        'CHF' =>
        array (
          'symbol' => 'CHF',
          'name' => 'Swiss Franc',
          'symbol_native' => 'CHF',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'CHF',
          'name_plural' => 'Swiss francs',
        ),
        'CLP' =>
        array (
          'symbol' => 'CL$',
          'name' => 'Chilean Peso',
          'symbol_native' => '$',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'CLP',
          'name_plural' => 'Chilean pesos',
        ),
        'CNY' =>
        array (
          'symbol' => 'CN¥',
          'name' => 'Chinese Yuan',
          'symbol_native' => 'CN¥',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'CNY',
          'name_plural' => 'Chinese yuan',
        ),
        'COP' =>
        array (
          'symbol' => 'CO$',
          'name' => 'Colombian Peso',
          'symbol_native' => '$',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'COP',
          'name_plural' => 'Colombian pesos',
        ),
        'CRC' =>
        array (
          'symbol' => '₡',
          'name' => 'Costa Rican Colón',
          'symbol_native' => '₡',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'CRC',
          'name_plural' => 'Costa Rican colóns',
        ),
        'CVE' =>
        array (
          'symbol' => 'CV$',
          'name' => 'Cape Verdean Escudo',
          'symbol_native' => 'CV$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'CVE',
          'name_plural' => 'Cape Verdean escudos',
        ),
        'CZK' =>
        array (
          'symbol' => 'Kč',
          'name' => 'Czech Republic Koruna',
          'symbol_native' => 'Kč',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'CZK',
          'name_plural' => 'Czech Republic korunas',
        ),
        'DJF' =>
        array (
          'symbol' => 'Fdj',
          'name' => 'Djiboutian Franc',
          'symbol_native' => 'Fdj',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'DJF',
          'name_plural' => 'Djiboutian francs',
        ),
        'DKK' =>
        array (
          'symbol' => 'Dkr',
          'name' => 'Danish Krone',
          'symbol_native' => 'kr',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'DKK',
          'name_plural' => 'Danish kroner',
        ),
        'DOP' =>
        array (
          'symbol' => 'RD$',
          'name' => 'Dominican Peso',
          'symbol_native' => 'RD$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'DOP',
          'name_plural' => 'Dominican pesos',
        ),
        'DZD' =>
        array (
          'symbol' => 'DA',
          'name' => 'Algerian Dinar',
          'symbol_native' => 'د.ج.‏',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'DZD',
          'name_plural' => 'Algerian dinars',
        ),
        'EEK' =>
        array (
          'symbol' => 'Ekr',
          'name' => 'Estonian Kroon',
          'symbol_native' => 'kr',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'EEK',
          'name_plural' => 'Estonian kroons',
        ),
        'EGP' =>
        array (
          'symbol' => 'EGP',
          'name' => 'Egyptian Pound',
          'symbol_native' => 'ج.م.‏',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'EGP',
          'name_plural' => 'Egyptian pounds',
        ),
        'ERN' =>
        array (
          'symbol' => 'Nfk',
          'name' => 'Eritrean Nakfa',
          'symbol_native' => 'Nfk',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'ERN',
          'name_plural' => 'Eritrean nakfas',
        ),
        'ETB' =>
        array (
          'symbol' => 'Br',
          'name' => 'Ethiopian Birr',
          'symbol_native' => 'Br',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'ETB',
          'name_plural' => 'Ethiopian birrs',
        ),
        'GBP' =>
        array (
          'symbol' => '£',
          'name' => 'British Pound Sterling',
          'symbol_native' => '£',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'GBP',
          'name_plural' => 'British pounds sterling',
        ),
        'GEL' =>
        array (
          'symbol' => 'GEL',
          'name' => 'Georgian Lari',
          'symbol_native' => 'GEL',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'GEL',
          'name_plural' => 'Georgian laris',
        ),
        'GHS' =>
        array (
          'symbol' => 'GH₵',
          'name' => 'Ghanaian Cedi',
          'symbol_native' => 'GH₵',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'GHS',
          'name_plural' => 'Ghanaian cedis',
        ),
        'GNF' =>
        array (
          'symbol' => 'FG',
          'name' => 'Guinean Franc',
          'symbol_native' => 'FG',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'GNF',
          'name_plural' => 'Guinean francs',
        ),
        'GTQ' =>
        array (
          'symbol' => 'GTQ',
          'name' => 'Guatemalan Quetzal',
          'symbol_native' => 'Q',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'GTQ',
          'name_plural' => 'Guatemalan quetzals',
        ),
        'HKD' =>
        array (
          'symbol' => 'HK$',
          'name' => 'Hong Kong Dollar',
          'symbol_native' => '$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'HKD',
          'name_plural' => 'Hong Kong dollars',
        ),
        'HNL' =>
        array (
          'symbol' => 'HNL',
          'name' => 'Honduran Lempira',
          'symbol_native' => 'L',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'HNL',
          'name_plural' => 'Honduran lempiras',
        ),
        'HRK' =>
        array (
          'symbol' => 'kn',
          'name' => 'Croatian Kuna',
          'symbol_native' => 'kn',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'HRK',
          'name_plural' => 'Croatian kunas',
        ),
        'HUF' =>
        array (
          'symbol' => 'Ft',
          'name' => 'Hungarian Forint',
          'symbol_native' => 'Ft',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'HUF',
          'name_plural' => 'Hungarian forints',
        ),
        'IDR' =>
        array (
          'symbol' => 'Rp',
          'name' => 'Indonesian Rupiah',
          'symbol_native' => 'Rp',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'IDR',
          'name_plural' => 'Indonesian rupiahs',
        ),
        'ILS' =>
        array (
          'symbol' => '₪',
          'name' => 'Israeli New Sheqel',
          'symbol_native' => '₪',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'ILS',
          'name_plural' => 'Israeli new sheqels',
        ),
        'INR' =>
        array (
          'symbol' => 'Rs',
          'name' => 'Indian Rupee',
          'symbol_native' => 'টকা',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'INR',
          'name_plural' => 'Indian rupees',
        ),
        'IQD' =>
        array (
          'symbol' => 'IQD',
          'name' => 'Iraqi Dinar',
          'symbol_native' => 'د.ع.‏',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'IQD',
          'name_plural' => 'Iraqi dinars',
        ),
        'IRR' =>
        array (
          'symbol' => 'IRR',
          'name' => 'Iranian Rial',
          'symbol_native' => '﷼',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'IRR',
          'name_plural' => 'Iranian rials',
        ),
        'ISK' =>
        array (
          'symbol' => 'Ikr',
          'name' => 'Icelandic Króna',
          'symbol_native' => 'kr',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'ISK',
          'name_plural' => 'Icelandic krónur',
        ),
        'JMD' =>
        array (
          'symbol' => 'J$',
          'name' => 'Jamaican Dollar',
          'symbol_native' => '$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'JMD',
          'name_plural' => 'Jamaican dollars',
        ),
        'JOD' =>
        array (
          'symbol' => 'JD',
          'name' => 'Jordanian Dinar',
          'symbol_native' => 'د.أ.‏',
          'decimal_digits' => 3,
          'rounding' => 0,
          'code' => 'JOD',
          'name_plural' => 'Jordanian dinars',
        ),
        'JPY' =>
        array (
          'symbol' => '¥',
          'name' => 'Japanese Yen',
          'symbol_native' => '￥',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'JPY',
          'name_plural' => 'Japanese yen',
        ),
        'KES' =>
        array (
          'symbol' => 'Ksh',
          'name' => 'Kenyan Shilling',
          'symbol_native' => 'Ksh',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'KES',
          'name_plural' => 'Kenyan shillings',
        ),
        'KHR' =>
        array (
          'symbol' => 'KHR',
          'name' => 'Cambodian Riel',
          'symbol_native' => '៛',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'KHR',
          'name_plural' => 'Cambodian riels',
        ),
        'KMF' =>
        array (
          'symbol' => 'CF',
          'name' => 'Comorian Franc',
          'symbol_native' => 'FC',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'KMF',
          'name_plural' => 'Comorian francs',
        ),
        'KRW' =>
        array (
          'symbol' => '₩',
          'name' => 'South Korean Won',
          'symbol_native' => '₩',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'KRW',
          'name_plural' => 'South Korean won',
        ),
        'KWD' =>
        array (
          'symbol' => 'KD',
          'name' => 'Kuwaiti Dinar',
          'symbol_native' => 'د.ك.‏',
          'decimal_digits' => 3,
          'rounding' => 0,
          'code' => 'KWD',
          'name_plural' => 'Kuwaiti dinars',
        ),
        'KZT' =>
        array (
          'symbol' => 'KZT',
          'name' => 'Kazakhstani Tenge',
          'symbol_native' => 'тңг.',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'KZT',
          'name_plural' => 'Kazakhstani tenges',
        ),
        'LBP' =>
        array (
          'symbol' => 'LB£',
          'name' => 'Lebanese Pound',
          'symbol_native' => 'ل.ل.‏',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'LBP',
          'name_plural' => 'Lebanese pounds',
        ),
        'LKR' =>
        array (
          'symbol' => 'SLRs',
          'name' => 'Sri Lankan Rupee',
          'symbol_native' => 'SL Re',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'LKR',
          'name_plural' => 'Sri Lankan rupees',
        ),
        'LTL' =>
        array (
          'symbol' => 'Lt',
          'name' => 'Lithuanian Litas',
          'symbol_native' => 'Lt',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'LTL',
          'name_plural' => 'Lithuanian litai',
        ),
        'LVL' =>
        array (
          'symbol' => 'Ls',
          'name' => 'Latvian Lats',
          'symbol_native' => 'Ls',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'LVL',
          'name_plural' => 'Latvian lati',
        ),
        'LYD' =>
        array (
          'symbol' => 'LD',
          'name' => 'Libyan Dinar',
          'symbol_native' => 'د.ل.‏',
          'decimal_digits' => 3,
          'rounding' => 0,
          'code' => 'LYD',
          'name_plural' => 'Libyan dinars',
        ),
        'MAD' =>
        array (
          'symbol' => 'MAD',
          'name' => 'Moroccan Dirham',
          'symbol_native' => 'د.م.‏',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'MAD',
          'name_plural' => 'Moroccan dirhams',
        ),
        'MDL' =>
        array (
          'symbol' => 'MDL',
          'name' => 'Moldovan Leu',
          'symbol_native' => 'MDL',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'MDL',
          'name_plural' => 'Moldovan lei',
        ),
        'MGA' =>
        array (
          'symbol' => 'MGA',
          'name' => 'Malagasy Ariary',
          'symbol_native' => 'MGA',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'MGA',
          'name_plural' => 'Malagasy Ariaries',
        ),
        'MKD' =>
        array (
          'symbol' => 'MKD',
          'name' => 'Macedonian Denar',
          'symbol_native' => 'MKD',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'MKD',
          'name_plural' => 'Macedonian denari',
        ),
        'MMK' =>
        array (
          'symbol' => 'MMK',
          'name' => 'Myanma Kyat',
          'symbol_native' => 'K',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'MMK',
          'name_plural' => 'Myanma kyats',
        ),
        'MOP' =>
        array (
          'symbol' => 'MOP$',
          'name' => 'Macanese Pataca',
          'symbol_native' => 'MOP$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'MOP',
          'name_plural' => 'Macanese patacas',
        ),
        'MUR' =>
        array (
          'symbol' => 'MURs',
          'name' => 'Mauritian Rupee',
          'symbol_native' => 'MURs',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'MUR',
          'name_plural' => 'Mauritian rupees',
        ),
        'MXN' =>
        array (
          'symbol' => 'MX$',
          'name' => 'Mexican Peso',
          'symbol_native' => '$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'MXN',
          'name_plural' => 'Mexican pesos',
        ),
        'MYR' =>
        array (
          'symbol' => 'RM',
          'name' => 'Malaysian Ringgit',
          'symbol_native' => 'RM',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'MYR',
          'name_plural' => 'Malaysian ringgits',
        ),
        'MZN' =>
        array (
          'symbol' => 'MTn',
          'name' => 'Mozambican Metical',
          'symbol_native' => 'MTn',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'MZN',
          'name_plural' => 'Mozambican meticals',
        ),
        'NAD' =>
        array (
          'symbol' => 'N$',
          'name' => 'Namibian Dollar',
          'symbol_native' => 'N$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'NAD',
          'name_plural' => 'Namibian dollars',
        ),
        'NGN' =>
        array (
          'symbol' => '₦',
          'name' => 'Nigerian Naira',
          'symbol_native' => '₦',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'NGN',
          'name_plural' => 'Nigerian nairas',
        ),
        'NIO' =>
        array (
          'symbol' => 'C$',
          'name' => 'Nicaraguan Córdoba',
          'symbol_native' => 'C$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'NIO',
          'name_plural' => 'Nicaraguan córdobas',
        ),
        'NOK' =>
        array (
          'symbol' => 'Nkr',
          'name' => 'Norwegian Krone',
          'symbol_native' => 'kr',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'NOK',
          'name_plural' => 'Norwegian kroner',
        ),
        'NPR' =>
        array (
          'symbol' => 'NPRs',
          'name' => 'Nepalese Rupee',
          'symbol_native' => 'नेरू',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'NPR',
          'name_plural' => 'Nepalese rupees',
        ),
        'NZD' =>
        array (
          'symbol' => 'NZ$',
          'name' => 'New Zealand Dollar',
          'symbol_native' => '$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'NZD',
          'name_plural' => 'New Zealand dollars',
        ),
        'OMR' =>
        array (
          'symbol' => 'OMR',
          'name' => 'Omani Rial',
          'symbol_native' => 'ر.ع.‏',
          'decimal_digits' => 3,
          'rounding' => 0,
          'code' => 'OMR',
          'name_plural' => 'Omani rials',
        ),
        'PAB' =>
        array (
          'symbol' => 'B/.',
          'name' => 'Panamanian Balboa',
          'symbol_native' => 'B/.',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'PAB',
          'name_plural' => 'Panamanian balboas',
        ),
        'PEN' =>
        array (
          'symbol' => 'S/.',
          'name' => 'Peruvian Nuevo Sol',
          'symbol_native' => 'S/.',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'PEN',
          'name_plural' => 'Peruvian nuevos soles',
        ),
        'PHP' =>
        array (
          'symbol' => '₱',
          'name' => 'Philippine Peso',
          'symbol_native' => '₱',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'PHP',
          'name_plural' => 'Philippine pesos',
        ),
        'PKR' =>
        array (
          'symbol' => 'PKRs',
          'name' => 'Pakistani Rupee',
          'symbol_native' => '₨',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'PKR',
          'name_plural' => 'Pakistani rupees',
        ),
        'PLN' =>
        array (
          'symbol' => 'zł',
          'name' => 'Polish Zloty',
          'symbol_native' => 'zł',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'PLN',
          'name_plural' => 'Polish zlotys',
        ),
        'PYG' =>
        array (
          'symbol' => '₲',
          'name' => 'Paraguayan Guarani',
          'symbol_native' => '₲',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'PYG',
          'name_plural' => 'Paraguayan guaranis',
        ),
        'QAR' =>
        array (
          'symbol' => 'QR',
          'name' => 'Qatari Rial',
          'symbol_native' => 'ر.ق.‏',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'QAR',
          'name_plural' => 'Qatari rials',
        ),
        'RON' =>
        array (
          'symbol' => 'RON',
          'name' => 'Romanian Leu',
          'symbol_native' => 'RON',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'RON',
          'name_plural' => 'Romanian lei',
        ),
        'RSD' =>
        array (
          'symbol' => 'din.',
          'name' => 'Serbian Dinar',
          'symbol_native' => 'дин.',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'RSD',
          'name_plural' => 'Serbian dinars',
        ),
        'RUB' =>
        array (
          'symbol' => 'RUB',
          'name' => 'Russian Ruble',
          'symbol_native' => '₽.',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'RUB',
          'name_plural' => 'Russian rubles',
        ),
        'RWF' =>
        array (
          'symbol' => 'RWF',
          'name' => 'Rwandan Franc',
          'symbol_native' => 'FR',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'RWF',
          'name_plural' => 'Rwandan francs',
        ),
        'SAR' =>
        array (
          'symbol' => 'SR',
          'name' => 'Saudi Riyal',
          'symbol_native' => 'ر.س.‏',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'SAR',
          'name_plural' => 'Saudi riyals',
        ),
        'SDG' =>
        array (
          'symbol' => 'SDG',
          'name' => 'Sudanese Pound',
          'symbol_native' => 'SDG',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'SDG',
          'name_plural' => 'Sudanese pounds',
        ),
        'SEK' =>
        array (
          'symbol' => 'Skr',
          'name' => 'Swedish Krona',
          'symbol_native' => 'kr',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'SEK',
          'name_plural' => 'Swedish kronor',
        ),
        'SGD' =>
        array (
          'symbol' => 'S$',
          'name' => 'Singapore Dollar',
          'symbol_native' => '$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'SGD',
          'name_plural' => 'Singapore dollars',
        ),
        'SOS' =>
        array (
          'symbol' => 'Ssh',
          'name' => 'Somali Shilling',
          'symbol_native' => 'Ssh',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'SOS',
          'name_plural' => 'Somali shillings',
        ),
        'SYP' =>
        array (
          'symbol' => 'SY£',
          'name' => 'Syrian Pound',
          'symbol_native' => 'ل.س.‏',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'SYP',
          'name_plural' => 'Syrian pounds',
        ),
        'THB' =>
        array (
          'symbol' => '฿',
          'name' => 'Thai Baht',
          'symbol_native' => '฿',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'THB',
          'name_plural' => 'Thai baht',
        ),
        'TND' =>
        array (
          'symbol' => 'DT',
          'name' => 'Tunisian Dinar',
          'symbol_native' => 'د.ت.‏',
          'decimal_digits' => 3,
          'rounding' => 0,
          'code' => 'TND',
          'name_plural' => 'Tunisian dinars',
        ),
        'TOP' =>
        array (
          'symbol' => 'T$',
          'name' => 'Tongan Paʻanga',
          'symbol_native' => 'T$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'TOP',
          'name_plural' => 'Tongan paʻanga',
        ),
        'TRY' =>
        array (
          'symbol' => 'TL',
          'name' => 'Turkish Lira',
          'symbol_native' => 'TL',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'TRY',
          'name_plural' => 'Turkish Lira',
        ),
        'TTD' =>
        array (
          'symbol' => 'TT$',
          'name' => 'Trinidad and Tobago Dollar',
          'symbol_native' => '$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'TTD',
          'name_plural' => 'Trinidad and Tobago dollars',
        ),
        'TWD' =>
        array (
          'symbol' => 'NT$',
          'name' => 'New Taiwan Dollar',
          'symbol_native' => 'NT$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'TWD',
          'name_plural' => 'New Taiwan dollars',
        ),
        'TZS' =>
        array (
          'symbol' => 'TSh',
          'name' => 'Tanzanian Shilling',
          'symbol_native' => 'TSh',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'TZS',
          'name_plural' => 'Tanzanian shillings',
        ),
        'UAH' =>
        array (
          'symbol' => '₴',
          'name' => 'Ukrainian Hryvnia',
          'symbol_native' => '₴',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'UAH',
          'name_plural' => 'Ukrainian hryvnias',
        ),
        'UGX' =>
        array (
          'symbol' => 'USh',
          'name' => 'Ugandan Shilling',
          'symbol_native' => 'USh',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'UGX',
          'name_plural' => 'Ugandan shillings',
        ),
        'UYU' =>
        array (
          'symbol' => '$U',
          'name' => 'Uruguayan Peso',
          'symbol_native' => '$',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'UYU',
          'name_plural' => 'Uruguayan pesos',
        ),
        'UZS' =>
        array (
          'symbol' => 'UZS',
          'name' => 'Uzbekistan Som',
          'symbol_native' => 'UZS',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'UZS',
          'name_plural' => 'Uzbekistan som',
        ),
        'VEF' =>
        array (
          'symbol' => 'Bs.F.',
          'name' => 'Venezuelan Bolívar',
          'symbol_native' => 'Bs.F.',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'VEF',
          'name_plural' => 'Venezuelan bolívars',
        ),
        'VND' =>
        array (
          'symbol' => '₫',
          'name' => 'Vietnamese Dong',
          'symbol_native' => '₫',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'VND',
          'name_plural' => 'Vietnamese dong',
        ),
        'XAF' =>
        array (
          'symbol' => 'FCFA',
          'name' => 'CFA Franc BEAC',
          'symbol_native' => 'FCFA',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'XAF',
          'name_plural' => 'CFA francs BEAC',
        ),
        'XOF' =>
        array (
          'symbol' => 'CFA',
          'name' => 'CFA Franc BCEAO',
          'symbol_native' => 'CFA',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'XOF',
          'name_plural' => 'CFA francs BCEAO',
        ),
        'YER' =>
        array (
          'symbol' => 'YR',
          'name' => 'Yemeni Rial',
          'symbol_native' => 'ر.ي.‏',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'YER',
          'name_plural' => 'Yemeni rials',
        ),
        'ZAR' =>
        array (
          'symbol' => 'R',
          'name' => 'South African Rand',
          'symbol_native' => 'R',
          'decimal_digits' => 2,
          'rounding' => 0,
          'code' => 'ZAR',
          'name_plural' => 'South African rand',
        ),
        'ZMK' =>
        array (
          'symbol' => 'ZK',
          'name' => 'Zambian Kwacha',
          'symbol_native' => 'ZK',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'ZMK',
          'name_plural' => 'Zambian kwachas',
        ),
        'ZWL' =>
        array (
          'symbol' => 'ZWL$',
          'name' => 'Zimbabwean Dollar',
          'symbol_native' => 'ZWL$',
          'decimal_digits' => 0,
          'rounding' => 0,
          'code' => 'ZWL',
          'name_plural' => 'Zimbabwean Dollar',
        ),
      );

    $filesWritten = 0;

    foreach ($data as $item) {
        $output = '';
        $path = 'imported/currency/';

        // Data
        $output .= "title: '" . $item['name'] . "'\n";
        $output .= "symbol: " . $item['symbol'] ."\n";
        $output .= "symbol_native: " . $item['symbol_native'] ."\n";
        $output .= "decimal_digits: " . $item['decimal_digits'] ."\n";
        $output .= "code: " . $item['code'] ."\n";
        $output .= "title_plural: '" . $item['name_plural'] ."'\n";
        $output .= "updated_by: 346c3162-6b01-4097-b7ee-8c4482d3ec52\n";
        $output .= "updated_at: 1613398632\n";
        $output .= "blueprint: currencies\n";

        // write to file
        file_put_contents($path . strtolower( $item['code'] ) . ".yaml", $output);
        $filesWritten++;
    }
    echo "<h3>" . $filesWritten . " files written</h3>";
}


if( 'switch' == $option ) {
  // **********************************************
  //
  // switch
  // - These go in /content/collections/switches/
  //
  // **********************************************

  $str = file_get_contents('https://switches.mx/wp-json/wp/v2/switch?per_page=100');
  $json = json_decode($str, true); // decode the JSON into an associative array

  $filesWritten = 0;

  // Data maps
  $dataMap = [
    'factory_lubed' => [
      0 => 'no',
      1 => 'slight',
      2 => 'significant',
    ],
    'box_stem' => [
      1 => 'yes',
      0 => 'no',
    ],
    'limited_run' => [
      1 => 'yes',
      0 => 'no',
    ],
    'manufacturer' => [
      389 => 'kaicheng',
      340 => 'keebwerk',
      273 => 'yok',
      272 => 'ttc',
      271 => 'mod',
      270 => 'bsun',
      269 => 'razer',
      268 => 'originative-co',
      267 => 'greetech',
      266 => 'outemu',
      265 => 'jwk',
      264 => 'durock',
      263 => 'everglide',
      262 => 'kailh',
      250 => 'gateron',
      249 => 'cherry',
    ],
    'brand' => [
      840 => 'wuque-studio',
      838 => '43studio',
      832 => 'matrix-labs',
      809 => 'homerow-co',
      806 => 'gazzew',
      790 => 'keyfirst',
      739 => 'sp-star',
      714 => 'hhhh',
      528 => 'invyr',
      420 => 'massdrop-x-invyr',
      419 => 'rara',
      418 => 'daily-clack',
      417 => 'domikey',
      416 => 'keebwerk',
      415 => 'invyr',
      414 => 'c3',
      413 => 'thic-thock',
      412 => 'bsun',
      411 => 'yok',
      410 => 'ttc',
      409 => 'mod',
      408 => 'gsus',
      407 => 'fei',
      406 => 'aliaz',
      405 => 'kbdfans',
      404 => 'novelkeys',
      403 => 'input-club',
      402 => 'outemu',
      401 => 'durock',
      400 => 'everglide',
      399 => 'zeal',
      398 => 'kailh',
      397 => 'gateron',
      396 => 'cherry-mx',
      1023 => 'cannonkeys',
    ],
    'vendor' => [
      1055 => 'kinetic-labs',
      1053 => 'keychron',
      964 => 'fancy-customs',
      1016 => '415keys',
      988 => 'rebult',
      986 => 'teal-technik',
      984 => 'mechs-and-co',
      983 => 'kwertie-keys',
      981 => 'lets-get-it',
      980 => 'infinity-key',
      978 => 'glorious-pc-gaming-race',
      976 => 'holyswitch-co',
      974 => 'ashkeebs',
      972 => 'salvun',
      970 => 'keyboard-treehouse',
      968 => 'aeboards',
      967 => 'kingly-keys',
      956 => 'mekanisk',
      954 => 'little-keyboards',
      952 => 'velocifire',
      951 => 'boardsource',
      949 => 'angry-miao',
      947 => 'upgrade-keyboards',
      944 => 'mech-supply',
      943 => 'uk-keycaps',
      942 => 'switchmod-net',
      941 => 'meckey-alpha',
      924 => 'sprit-designs',
      922 => 'zfrontier',
      921 => 'vintkeys',
      919 => 'the-keyboard-project',
      918 => 'the-keyboard-company',
      916 => 'ringer-keys',
      915 => 'rgbkb',
      914 => 'prototypist',
      913 => 'pohjola-works',
      912 => 'play-keyboard',
      911 => 'pimp-my-keyboard',
      910 => 'mykeys',
      909 => 'melgeek',
      908 => 'mechanicalkeyboards-co-id',
      907 => 'mechanical-keyboards-etc',
      906 => 'makeyboard',
      905 => 'keyboardio',
      904 => 'keyboard-market',
      903 => 'keebio',
      902 => 'keeb-me-up',
      901 => 'justins-mechanical-keyboards',
      900 => 'iron-meets-wood',
      899 => 'g-heavy-industries',
      898 => 'firefly-keyboards',
      897 => 'eunbu',
      896 => 'endgame-keys',
      895 => 'elecshopper',
      894 => 'deskhero',
      893 => 'clawsome-boards',
      892 => 'bolsa-keyboard-supply',
      891 => 'be-your-style',
      890 => 'divinikey',
      846 => 'mech-land',
      844 => 'wuque-studio',
      843 => 'yushakobo',
      841 => 'monstargear',
      833 => 'malvix-studio',
      829 => 'keys',
      825 => 'dangkeebs',
      823 => 'mkultra',
      805 => '3dkeebs',
      804 => 'keyspresso',
      803 => 'crafting-keyboards',
      801 => 'lwkeyboards',
      770 => 'monokei',
      710 => 'thic-thock',
      709 => 'flashquark',
      708 => 'dixie-mech',
      682 => 'mechbox',
      681 => 'taobao',
      680 => 'apexkeyboards',
      657 => 'keygem',
      571 => 'ilumkb',
      500 => 'overclockers',
      352 => 'desk-candy',
      339 => 'keebwerk',
      337 => 'optic-boards',
      336 => 'homerow-co',
      335 => 'switchkeys',
      334 => 'wasd-keyboards',
      333 => 'originative-co',
      329 => 'switchtop',
      328 => 'cannon-keys',
      327 => 'project-keyboard',
      326 => 'kebo',
      325 => 'prime-keyboards',
      324 => '1up-keyboards',
      323 => 'kono',
      322 => 'spacecat',
      321 => 'mechboards',
      316 => 'splitkb',
      314 => 'the-key-dot-co',
      313 => 'keycapsss',
      312 => 'mehkee',
      291 => 'mechanicalkeyboards-com',
      290 => 'candy-keys',
      281 => 'aliexpress',
      279 => 'daily-clack',
      277 => 'auramech',
      276 => 'kbdfans',
      275 => 'novelkeys',
      278 => 'zeal',
      282 => 'kprepublic',
      280 => 'mykeyboard-eu',
      274 => 'drop',
    ]
  ];

  foreach ($json as $item) {
      $output = '';
      $path = 'imported/switches/';
      $date = new DateTime($item['date']);
      $id = UUID::v4();

      // Map testing
      // var_dump( $dataMap['manufacturer'] );
      // var_dump( $item['acf']['info']['manufacturer'] );
      // var_dump( $dataMap['manufacturer'][ $item['acf']['info']['manufacturer'] ] );
      // die;

      // Info
      // -------------------------------------------------------------
      $output .= "---\n";
      $output .= "title: '" . $item['title']['rendered'] . "'\n";
      $output .= "volume: " . $item['acf']['info']['volume'] . "\n";
      $output .= "volume_notes: '" . $item['acf']['info']['volume_notes'] . "'\n";

      if( isset( $item['acf']['info']['factory-lubed'] ) ) {
        $output .= "factory_lubed: " . $dataMap['factory_lubed'][ $item['acf']['info']['factory-lubed'] ] . "\n";
      } else {
        $output .= "factory_lubed: no\n";
      }

      $output .= "lubrication_notes: '" . $item['acf']['info']['lube_notes'] . "'\n";
      $output .= "film: " . $item['acf']['info']['film'] . "\n";
      if( isset( $item['acf']['info']['film_notes'] ) ) {
        $output .= "film_notes: '" . $item['acf']['info']['film_notes'] . "'\n";
      } else {
        $output .= "film_notes: ''\n";
      }
      $output .= "notes: |-\n";
      $output .= "  " . removeLinkBreaksReplaceSingleQuote( $item['acf']['info']['notes'] ) . "\n";
      $output .= "manufacturer: " . $dataMap['manufacturer'][ $item['acf']['info']['manufacturer'] ] . "\n";
      if( isset( $dataMap['brand'][ $item['acf']['info']['brand'] ] ) ) {
        $output .= "brand: " . $dataMap['brand'][ $item['acf']['info']['brand'] ] . "\n";
      } else {
        echo 'Brand not found: ';
        var_dump( $item['acf']['info']['brand'] );
        var_dump( $item['title']['rendered'] );
        die;
      }
      $output .= "switch_type: " . $item['acf']['info']['switch-type'] . "\n";
      $output .= "mount: '" . $item['acf']['info']['mount'] . "'\n";

      if( isset( $item['acf']['info']['box-stem'] ) ) {
        $output .= "stem_construction: '" . $dataMap['box_stem'][ $item['acf']['info']['box-stem'] ] . "'\n";
      } else {
        $output .= "stem_construction: 'standard'\n";
      }

      if( isset( $item['acf']['info']['limited-run'] ) ) {
        $output .= "limited_run: '" . $dataMap['limited_run'][ $item['acf']['info']['limited-run'] ] . "'\n";
      } else {
        $output .= "limited_run: 'no'\n";
      }

      // Specs
      // -------------------------------------------------------------
      if( isset( $item['acf']['specs'] ) && is_array( $item['acf']['specs'] ) && count( $item['acf']['specs'] ) > 0 ) {
        $output .= "specs:\n";
        foreach ($item['acf']['specs'] as $spec) {
          $output .= "  -\n";
          $output .= "    name: " . $spec['name'] . "\n";
          $output .= "    description: '" . removeLinkBreaksReplaceSingleQuote( $spec['description'] ) . "'\n";
          $output .= "    actuation: " . $spec['actuation'] . "\n";
          $output .= "    bottom-out: " . $spec['bottom-out'] . "\n";
          $output .= "    pre-travel: " . $spec['pre-travel'] . "\n";
          $output .= "    total-travel: " . $spec['total-travel'] . "\n";
          if( isset( $item['acf']['spring'] ) ) {
            $output .= "    spring: " . $item['acf']['spring']['spring'] . "\n";
            $output .= "    spring_color: '" . $item['acf']['spring']['spring-color'] . "'\n";
            $output .= "    spring_color_info: '" . $item['acf']['spring']['spring-color-info'] . "'\n";
            $output .= "    spring_swap: '" . $item['acf']['spring']['spring-swap'] . "'\n";
          }
          $output .= "    stem_type: " . $spec['stem']['type'] . "\n";
          $output .= "    stem_color: '" . $spec['stem']['color'] . "'\n";
          $output .= "    stem_material: " . $spec['stem']['material'] . "\n";
          $output .= "    stem_custom_material_notes: '" . $spec['stem']['material-notes'] . "'\n";
          $output .= "    housing_top_type: " . $spec['housing-top']['type'] . "\n";
          $output .= "    housing_top_color: '" . $spec['housing-top']['color'] . "'\n";
          $output .= "    housing_top_material: " . $spec['housing-top']['material'] . "\n";
          $output .= "    housing_top_custom_material_notes: '" . $spec['housing-top']['material-notes'] . "'\n";
          $output .= "    housing_bottom_type: " . $spec['housing-bottom']['type'] . "\n";
          $output .= "    housing_bottom_color: '" . $spec['housing-bottom']['color'] . "'\n";
          $output .= "    housing_bottom_material: " . $spec['housing-bottom']['material'] . "\n";
          $output .= "    housing_bottom_custom_material_notes: '" . $spec['housing-bottom']['material-notes'] . "'\n";

          // Force Graph
          if( isset( $spec['force-graph'] ) && $spec['force-graph'] ) {
            // Download file and rename
            $forceGraphFileUrl = explode( '/', $spec['force-graph'] );
            $forceGraphFilename = end( $forceGraphFileUrl );
            copy( $spec['force-graph'], 'imported/images/switches/force-graphs/' . $forceGraphFilename );

            // Output
            $output .= "    force_graph:\n";
            $output .= "      - switches/force-graphs/" . $forceGraphFilename . "\n";
          } else {
            $output .= "    force_graph: {  }\n";
          }

          $output .= "    led_support: " . $spec['led-support'] . "\n";
        }
      }

      // Photos
      // -------------------------------------------------------------
      if( isset( $item['acf']['photos'] ) && is_array( $item['acf']['photos'] ) && count( $item['acf']['photos'] ) > 0 ) {
        $output .= "photos:\n";
        foreach ($item['acf']['photos'] as $photo) {
          // Download file and rename
          $imageUrl = $photo['url'];
          $imageFilenameArray = explode( '/', $imageUrl );
          $imageFilename = $item['slug'] . '-' . end( $imageFilenameArray );

          // Let's only download all the images once because it takes a while
          // copy( $imageUrl, 'imported/images/switches/photos/' . $imageFilename );

          // Output
          $output .= "  - switches/photos/" . $imageFilename . "\n";
        }
      }

      // Videos
      // -------------------------------------------------------------
      if( isset( $item['acf']['videos'] ) && is_array( $item['acf']['videos'] ) && count( $item['acf']['videos'] ) > 0 ) {
        $output .= "videos:\n";
        foreach ($item['acf']['videos'] as $video) {
          $youTubeRegex = preg_match_all('/(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+/', $video['url'], $matches);

          // Get last match
          $youTubeID = $matches[ array_key_last( $matches ) ];

          // Convert to string
          $youTubeID = implode( $youTubeID );

          // Strip '?feature=oembed' off the end
          $youTubeID = str_replace( '?feature=oembed', '', $youTubeID);

          $youTubeIDUrl = 'https://www.youtube.com/watch?v=' . $youTubeID;

          $output .= "  -\n";
          $output .= "    item_url: '" . $youTubeIDUrl . "'\n";
          $output .= "    description: '" . removeLinkBreaksReplaceSingleQuote( $video['description'] ) . "'\n";
        }
      }

      // Links
      // -------------------------------------------------------------
      if( isset( $item['acf']['links'] ) && is_array( $item['acf']['links'] ) && count( $item['acf']['links'] ) > 0 ) {
        $output .= "related_links:\n";
        foreach ($item['acf']['links'] as $link) {
          $output .= "  -\n";
          $output .= "    item_url: '" . $link['url'] . "'\n";
          $output .= "    description: '" . removeLinkBreaksReplaceSingleQuote( $link['description'] ) . "'\n";
        }
      }

      // Quotes
      // -------------------------------------------------------------
      if( isset( $item['acf']['quotes'] ) && is_array( $item['acf']['quotes'] ) && count( $item['acf']['quotes'] ) > 0 ) {
        $output .= "quotes:\n";
        foreach ($item['acf']['quotes'] as $quote) {
          $output .= "  -\n";
          $output .= "    title: '" . removeLinkBreaksReplaceSingleQuote( $quote['title'] ) . "'\n";
          $output .= "    source: '" . $quote['source'] . "'\n";
          $output .= "    text: '" . removeLinkBreaksReplaceSingleQuote( $quote['text'] ) . "'\n";
        }
      }

      // Prices
      // -------------------------------------------------------------
      if( isset( $item['acf']['prices'] ) && is_array( $item['acf']['prices'] ) && count( $item['acf']['prices'] ) > 0 ) {
        $output .= "prices:\n";
        foreach ($item['acf']['prices'] as $price) {
          $priceDate = DateTime::createFromFormat('d/m/Y', $price['date']);

          $output .= "  -\n";
          $output .= "    price: '" . $price['price'] . "'\n";
          if( isset( $price['extra_text'] ) && strlen( $price['extra_text'] ) > 0 ) {
            $output .= "    extra_text: '" . $price['extra_text'] . "'\n";
          } else {
            $output .= "    extra_text: null\n";
          }
          $output .= "    source: '" . $price['source'] . "'\n";
          $output .= "    vendor: " . $dataMap['vendor'][ $price['vendor'] ] . "\n";
          $output .= "    datecheck: '" . $priceDate->format('Y-m-d') . "'\n"; // eg. 2020-02-04
          $output .= "    in_stock: true\n";
        }
      }

      // Meta info
      // -------------------------------------------------------------
      $output .= "updated_by: 346c3162-6b01-4097-b7ee-8c4482d3ec52\n";
      $output .= "updated_at: " . date_timestamp_get($date) . "\n";
      $output .= "id: "  . $id . "\n";
      $output .= "---\n";

      // Write to file
      // -------------------------------------------------------------
      file_put_contents($path . $item['slug'] . ".md", $output);
      echo '<p>File written: ' . $item['slug'] . '.md</p>';
      $filesWritten++;

      // Only loop once
      // die;
  }
  echo "<h3>" . $filesWritten . " files written</h3>";
}
?>

<html>
    <head>
        <title>Importer</title>
    </head>
    <body>
        <ul>
            <li><a href="?option=brand">Brands</a></li>
            <li><a href="?option=manufacturer">Manufacturers</a></li>
            <li><a href="?option=vendor">Vendors</a></li>
            <li><a href="?option=currency">Currency</a></li>
            <li><a href="?option=switch">Switches</a></li>
        </ul>

        <h4>Copy created files from import</h4>
        <pre><code>cd /mnt/c/Users/Kieran/Sites/HomesteadSites/switchesmx;
cp public/imported/vendor/* content/collections/vendors;
cp public/imported/images/vendor/favicon/* public/assets/vendors/favicons</code></pre>
    </body>
</html>