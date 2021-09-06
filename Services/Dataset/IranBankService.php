<?php

namespace Services\Dataset;

use XUA\Service;

abstract class IranBankService extends Service
{
    public static function getBankFromIban(string $iban): ?array
    {
        if (!self::validateIban($iban)) {
            return null;
        }

        return self::BANKS_BY_IBAN_ID[substr($iban,4, 3)] ?? null;
    }

    public static function getBankAccountNoFromIban(string $iban): ?string
    {
        $bank = self::getBankFromIban($iban);
        if (!$bank) {
            return null;
        }

        $pos = 7;
        $accountNoParts = [];
        foreach ($bank[self::BANK_FIELD_ACCOUNT_NO_FORMAT] as $length) {
            $accountNoParts[] = ltrim(substr($iban,$pos, $length), '0');
            $pos += $length;
        }
        return implode('-', $accountNoParts);
    }

    public static function validateIban(string $iban): bool
    {
        if(strlen($iban) < 2) {
            return false;
        }
        $country = substr($iban,0, 2);

        $lengths = ['AL' => 28, 'AD' => 24, 'AT' => 20, 'AZ' => 28, 'BH' => 22, 'BE' => 16, 'BA' => 20, 'BR' => 29, 'BG' => 22, 'CR' => 21, 'HR' => 21, 'CY' => 28, 'CZ' => 24, 'DK' => 18, 'DO' => 28, 'EE' => 20, 'FO' => 18, 'FI' => 18, 'FR' => 27, 'GE' => 22, 'DE' => 22, 'GI' => 23, 'GR' => 27, 'GL' => 18, 'GT' => 28, 'HU' => 28, 'IS' => 26, 'IE' => 22, 'IL' => 23, 'IT' => 27, 'JO' => 30, 'KZ' => 20, 'KW' => 30, 'LV' => 21, 'LB' => 28, 'LI' => 21, 'LT' => 20, 'LU' => 20, 'MK' => 19, 'MT' => 31, 'MR' => 27, 'MU' => 30, 'MC' => 27, 'MD' => 24, 'ME' => 22, 'NL' => 18, 'NO' => 15, 'PK' => 24, 'PS' => 29, 'PL' => 28, 'PT' => 25, 'QA' => 29, 'RO' => 24, 'SM' => 27, 'SA' => 24, 'RS' => 22, 'SK' => 24, 'SI' => 19, 'ES' => 24, 'SE' => 24, 'CH' => 21, 'TN' => 24, 'TR' => 26, 'AE' => 23, 'GB' => 22, 'VG' => 24, 'DZ' => 26, 'AO' => 25, 'BJ' => 28, 'BF' => 28, 'BI' => 16, 'CV' => 25, 'CM' => 27, 'CF' => 27, 'TD' => 27, 'KM' => 27, 'CG' => 27, 'CI' => 28, 'DJ' => 27, 'GQ' => 27, 'GA' => 27, 'GW' => 25, 'HN' => 28, 'IR' => 26, 'MG' => 27, 'ML' => 28, 'MA' => 28, 'MZ' => 25, 'NI' => 32, 'NE' => 28, 'SN' => 28, 'TG' => 28];

        if (!isset($lengths[$country]) or $lengths[$country] != strlen($iban)) {
            return false;
        }

        $chars = str_split(substr($iban, 4) . substr($iban,0,4));
        $checkString = "";
        foreach($chars AS $char){
            if(is_numeric($char)){
                $checkString .= $char;
            } else {
                $checkString .= ord($char) - 55;
            }
        }

        if(bcmod($checkString, '97') != 1) {
            return false;
        }

        return true;
    }

    // Fields
    const BANK_FIELD_NAME = 'name';
    const BANK_FIELD_TITLE = 'title';
    const BANK_FIELD_SHORT_TITLE = 'shortTitle';
    const BANK_FIELD_IBAN_ID = 'ibanId';
    const BANK_FIELD_ACCOUNT_NO_FORMAT = 'accountNumberFormats';
    const BANK_FIELD_CARD_PREFIXES = 'cardPrefixes';
    const BANK_FIELD_ACTIVE = 'active';

    // Iran Banks Data
    const BANK_MELLI = [
        self::BANK_FIELD_NAME              => 'melli',
        self::BANK_FIELD_TITLE             => 'بانک ملی',
        self::BANK_FIELD_SHORT_TITLE       => 'ملی',
        self::BANK_FIELD_IBAN_ID           => '017',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['603799'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_SADERAT = [
        self::BANK_FIELD_NAME              => 'saderat',
        self::BANK_FIELD_TITLE             => 'بانک صادرات',
        self::BANK_FIELD_SHORT_TITLE       => 'صادرات',
        self::BANK_FIELD_IBAN_ID           => '019',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['603769', '903769'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_KESHAVARZI = [
        self::BANK_FIELD_NAME              => 'keshavarzi',
        self::BANK_FIELD_TITLE             => 'بانک کشاورزی',
        self::BANK_FIELD_SHORT_TITLE       => 'کشاورزی',
        self::BANK_FIELD_IBAN_ID           => '016',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['603770', '639217'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_MELLAT = [
        self::BANK_FIELD_NAME              => 'mellat',
        self::BANK_FIELD_TITLE             => 'بانک ملت',
        self::BANK_FIELD_SHORT_TITLE       => 'ملت',
        self::BANK_FIELD_IBAN_ID           => '012',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [16],
        self::BANK_FIELD_CARD_PREFIXES     => ['610433', '991975'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_REFAHKARGARAN = [
        self::BANK_FIELD_NAME              => 'refahkargaran',
        self::BANK_FIELD_TITLE             => 'بانک رفاه',
        self::BANK_FIELD_SHORT_TITLE       => 'رفاه',
        self::BANK_FIELD_IBAN_ID           => '013',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [17],
        self::BANK_FIELD_CARD_PREFIXES     => ['589463'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_TEJARAT = [
        self::BANK_FIELD_NAME              => 'tejarat',
        self::BANK_FIELD_TITLE             => 'بانک تجارت',
        self::BANK_FIELD_SHORT_TITLE       => 'تجارت',
        self::BANK_FIELD_IBAN_ID           => '018',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['627353', '585983'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_SEPAH = [
        self::BANK_FIELD_NAME              => 'sepah',
        self::BANK_FIELD_TITLE             => 'بانک سپه',
        self::BANK_FIELD_SHORT_TITLE       => 'سپه',
        self::BANK_FIELD_IBAN_ID           => '015',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [10, 9],
        self::BANK_FIELD_CARD_PREFIXES     => ['589210', '604932'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_POSTBANK = [
        self::BANK_FIELD_NAME              => 'postbank',
        self::BANK_FIELD_TITLE             => 'پست بانک',
        self::BANK_FIELD_SHORT_TITLE       => 'پست بانک',
        self::BANK_FIELD_IBAN_ID           => '021',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [7, 11],
        self::BANK_FIELD_CARD_PREFIXES     => ['627760'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_PASARGAD = [
        self::BANK_FIELD_NAME              => 'pasargad',
        self::BANK_FIELD_TITLE             => 'بانک پاسارگاد',
        self::BANK_FIELD_SHORT_TITLE       => 'پاسارگاد',
        self::BANK_FIELD_IBAN_ID           => '057',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 3, 8, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['502229', '639347'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_MASKAN = [
        self::BANK_FIELD_NAME              => 'maskan',
        self::BANK_FIELD_TITLE             => 'بانک مسکن',
        self::BANK_FIELD_SHORT_TITLE       => 'مسکن',
        self::BANK_FIELD_IBAN_ID           => '014',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [16],
        self::BANK_FIELD_CARD_PREFIXES     => ['628023'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_SHAHR = [
        self::BANK_FIELD_NAME              => 'shahr',
        self::BANK_FIELD_TITLE             => 'بانک شهر',
        self::BANK_FIELD_SHORT_TITLE       => 'شهر',
        self::BANK_FIELD_IBAN_ID           => '061',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['502806', '504706'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_ANSAR = [
        self::BANK_FIELD_NAME              => 'ansar',
        self::BANK_FIELD_TITLE             => 'بانک انصار',
        self::BANK_FIELD_SHORT_TITLE       => 'انصار',
        self::BANK_FIELD_IBAN_ID           => '063',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 3, 8, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['627381'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_RESALATGH = [
        self::BANK_FIELD_NAME              => 'resalatgh',
        self::BANK_FIELD_TITLE             => 'قرض الحسنه رسالت',
        self::BANK_FIELD_SHORT_TITLE       => 'رسالت',
        self::BANK_FIELD_IBAN_ID           => '070',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 7, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['504172'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_PARSIAN = [
        self::BANK_FIELD_NAME              => 'parsian',
        self::BANK_FIELD_TITLE             => 'بانک پارسیان',
        self::BANK_FIELD_SHORT_TITLE       => 'پارسیان',
        self::BANK_FIELD_IBAN_ID           => '054',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 3, 8, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['622106', '639194', '627884'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_MEHRIRAN = [
        self::BANK_FIELD_NAME              => 'mehriran',
        self::BANK_FIELD_TITLE             => 'قرض الحسنه مهر ایران',
        self::BANK_FIELD_SHORT_TITLE       => 'مهر ایران',
        self::BANK_FIELD_IBAN_ID           => '060',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 3, 8, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['606373'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_GHAVAMIN = [
        self::BANK_FIELD_NAME              => 'ghavamin',
        self::BANK_FIELD_TITLE             => 'بانک قوامین',
        self::BANK_FIELD_SHORT_TITLE       => 'قوامین',
        self::BANK_FIELD_IBAN_ID           => '052',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['639599'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_AYANDEH = [
        self::BANK_FIELD_NAME              => 'ayandeh',
        self::BANK_FIELD_TITLE             => 'بانک آینده',
        self::BANK_FIELD_SHORT_TITLE       => 'آینده',
        self::BANK_FIELD_IBAN_ID           => '062',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['636214'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_SAMAN = [
        self::BANK_FIELD_NAME              => 'saman',
        self::BANK_FIELD_TITLE             => 'بانک سامان',
        self::BANK_FIELD_SHORT_TITLE       => 'سامان',
        self::BANK_FIELD_IBAN_ID           => '056',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 3, 8, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['621986'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_SINA = [
        self::BANK_FIELD_NAME              => 'sina',
        self::BANK_FIELD_TITLE             => 'بانک سینا',
        self::BANK_FIELD_SHORT_TITLE       => 'سینا',
        self::BANK_FIELD_IBAN_ID           => '059',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 3, 8, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['639346'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_TOSEETAAVON = [
        self::BANK_FIELD_NAME              => 'toseetaavon',
        self::BANK_FIELD_TITLE             => 'بانک توسعه تعاون',
        self::BANK_FIELD_SHORT_TITLE       => 'توسعه تعاون',
        self::BANK_FIELD_IBAN_ID           => '022',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 4, 7, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['502908'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_EGHTESADNOVIN = [
        self::BANK_FIELD_NAME              => 'eghtesadnovin',
        self::BANK_FIELD_TITLE             => 'بانک اقتصاد نوین',
        self::BANK_FIELD_SHORT_TITLE       => 'اقتصاد نوین',
        self::BANK_FIELD_IBAN_ID           => '055',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 3, 8, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['627412'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_MEHREGHTESAD = [
        self::BANK_FIELD_NAME              => 'mehreghtesad',
        self::BANK_FIELD_TITLE             => 'بانک مهر اقتصاد',
        self::BANK_FIELD_SHORT_TITLE       => 'مهر اقتصاد',
        self::BANK_FIELD_IBAN_ID           => '079',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 3, 8, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['639370', '606737'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_DEY = [
        self::BANK_FIELD_NAME              => 'dey',
        self::BANK_FIELD_TITLE             => 'بانک دی',
        self::BANK_FIELD_SHORT_TITLE       => 'دی',
        self::BANK_FIELD_IBAN_ID           => '066',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['502938'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_KOSARFCI = [
        self::BANK_FIELD_NAME              => 'kosarfci',
        self::BANK_FIELD_TITLE             => 'موسسه مالی و اعتباری کوثر',
        self::BANK_FIELD_SHORT_TITLE       => 'موسسه کوثر',
        self::BANK_FIELD_IBAN_ID           => '073',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [9, 2],
        self::BANK_FIELD_CARD_PREFIXES     => ['505801'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_IRANZAMIN = [
        self::BANK_FIELD_NAME              => 'iranzamin',
        self::BANK_FIELD_TITLE             => 'بانک ایران زمین',
        self::BANK_FIELD_SHORT_TITLE       => 'ایران زمین',
        self::BANK_FIELD_IBAN_ID           => '069',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 3, 8, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['505785'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_HEKMATIRANIAN = [
        self::BANK_FIELD_NAME              => 'hekmatiranian',
        self::BANK_FIELD_TITLE             => 'بانک حکمت ایرانیان',
        self::BANK_FIELD_SHORT_TITLE       => 'حکمت ایرانیان',
        self::BANK_FIELD_IBAN_ID           => '065',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 3, 8, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['636949'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_KARAFARIN = [
        self::BANK_FIELD_NAME              => 'karafarin',
        self::BANK_FIELD_TITLE             => 'بانک کارآفرین',
        self::BANK_FIELD_SHORT_TITLE       => 'کارآفرین',
        self::BANK_FIELD_IBAN_ID           => '053',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['627488', '502910'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_SARMAYE = [
        self::BANK_FIELD_NAME              => 'sarmaye',
        self::BANK_FIELD_TITLE             => 'بانک سرمایه',
        self::BANK_FIELD_SHORT_TITLE       => 'سرمایه',
        self::BANK_FIELD_IBAN_ID           => '058',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 3, 8, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['639607'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_NOORFCI = [
        self::BANK_FIELD_NAME              => 'noorfci',
        self::BANK_FIELD_TITLE             => 'موسسه اعتباری نور',
        self::BANK_FIELD_SHORT_TITLE       => 'موسسه نور',
        self::BANK_FIELD_IBAN_ID           => '080',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['507677'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_GARDESHGARI = [
        self::BANK_FIELD_NAME              => 'gardeshgari',
        self::BANK_FIELD_TITLE             => 'بانک گردشگری',
        self::BANK_FIELD_SHORT_TITLE       => 'گردشگری',
        self::BANK_FIELD_IBAN_ID           => '064',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 3, 8, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['505416'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_MELALFCI = [
        self::BANK_FIELD_NAME              => 'melalfci',
        self::BANK_FIELD_TITLE             => 'موسسه اعتباری ملل (عسکریه)',
        self::BANK_FIELD_SHORT_TITLE       => 'موسسه ملل',
        self::BANK_FIELD_IBAN_ID           => '075',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [4, 2, 3, 9],
        self::BANK_FIELD_CARD_PREFIXES     => ['606256'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_MARKAZI = [
        self::BANK_FIELD_NAME              => 'markazi',
        self::BANK_FIELD_TITLE             => 'بانک مرکزی جمهوری اسلامی ایران',
        self::BANK_FIELD_SHORT_TITLE       => 'مرکزی',
        self::BANK_FIELD_IBAN_ID           => '010',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['636795'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_TOSEESADERAT = [
        self::BANK_FIELD_NAME              => 'toseesaderat',
        self::BANK_FIELD_TITLE             => 'بانک توسعه صادرات',
        self::BANK_FIELD_SHORT_TITLE       => 'توسعه صادرات',
        self::BANK_FIELD_IBAN_ID           => '020',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['627648', '207177'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_MIDDLEEASTKHAVARMIANEH = [
        self::BANK_FIELD_NAME              => 'middleeastkhavarmianeh',
        self::BANK_FIELD_TITLE             => 'بانک خاورميانه',
        self::BANK_FIELD_SHORT_TITLE       => 'خاورميانه',
        self::BANK_FIELD_IBAN_ID           => '078',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 2, 3, 9],
        self::BANK_FIELD_CARD_PREFIXES     => ['505809', '585947'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_SANATMADAN = [
        self::BANK_FIELD_NAME              => 'sanatmadan',
        self::BANK_FIELD_TITLE             => 'بانک صنعت و معدن',
        self::BANK_FIELD_SHORT_TITLE       => 'صنعت و معدن',
        self::BANK_FIELD_IBAN_ID           => '011',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['627961'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_TOSEEFCI = [
        self::BANK_FIELD_NAME              => 'toseefci',
        self::BANK_FIELD_TITLE             => 'موسسه اعتباری توسعه',
        self::BANK_FIELD_SHORT_TITLE       => 'موسسه توسعه',
        self::BANK_FIELD_IBAN_ID           => '051',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [5, 3, 8, 3],
        self::BANK_FIELD_CARD_PREFIXES     => ['628157'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];
    const BANK_IRANVENEZUELA = [
        self::BANK_FIELD_NAME              => 'iranvenezuela',
        self::BANK_FIELD_TITLE             => 'بانک مشترک ایران ونزوئلا',
        self::BANK_FIELD_SHORT_TITLE       => 'ایران ونزوئلا',
        self::BANK_FIELD_IBAN_ID           => '095',
        self::BANK_FIELD_ACCOUNT_NO_FORMAT => [19],
        self::BANK_FIELD_CARD_PREFIXES     => ['581874'],
        self::BANK_FIELD_ACTIVE            => 1,
    ];

    const BANKS_BY_NAME = [
        self::BANK_MELLI                 [self::BANK_FIELD_NAME] => self::BANK_MELLI,
        self::BANK_SADERAT               [self::BANK_FIELD_NAME] => self::BANK_SADERAT,
        self::BANK_KESHAVARZI            [self::BANK_FIELD_NAME] => self::BANK_KESHAVARZI,
        self::BANK_MELLAT                [self::BANK_FIELD_NAME] => self::BANK_MELLAT,
        self::BANK_REFAHKARGARAN         [self::BANK_FIELD_NAME] => self::BANK_REFAHKARGARAN,
        self::BANK_TEJARAT               [self::BANK_FIELD_NAME] => self::BANK_TEJARAT,
        self::BANK_SEPAH                 [self::BANK_FIELD_NAME] => self::BANK_SEPAH,
        self::BANK_POSTBANK              [self::BANK_FIELD_NAME] => self::BANK_POSTBANK,
        self::BANK_PASARGAD              [self::BANK_FIELD_NAME] => self::BANK_PASARGAD,
        self::BANK_MASKAN                [self::BANK_FIELD_NAME] => self::BANK_MASKAN,
        self::BANK_SHAHR                 [self::BANK_FIELD_NAME] => self::BANK_SHAHR,
        self::BANK_ANSAR                 [self::BANK_FIELD_NAME] => self::BANK_ANSAR,
        self::BANK_RESALATGH             [self::BANK_FIELD_NAME] => self::BANK_RESALATGH,
        self::BANK_PARSIAN               [self::BANK_FIELD_NAME] => self::BANK_PARSIAN,
        self::BANK_MEHRIRAN              [self::BANK_FIELD_NAME] => self::BANK_MEHRIRAN,
        self::BANK_GHAVAMIN              [self::BANK_FIELD_NAME] => self::BANK_GHAVAMIN,
        self::BANK_AYANDEH               [self::BANK_FIELD_NAME] => self::BANK_AYANDEH,
        self::BANK_SAMAN                 [self::BANK_FIELD_NAME] => self::BANK_SAMAN,
        self::BANK_SINA                  [self::BANK_FIELD_NAME] => self::BANK_SINA,
        self::BANK_TOSEETAAVON           [self::BANK_FIELD_NAME] => self::BANK_TOSEETAAVON,
        self::BANK_EGHTESADNOVIN         [self::BANK_FIELD_NAME] => self::BANK_EGHTESADNOVIN,
        self::BANK_MEHREGHTESAD          [self::BANK_FIELD_NAME] => self::BANK_MEHREGHTESAD,
        self::BANK_DEY                   [self::BANK_FIELD_NAME] => self::BANK_DEY,
        self::BANK_KOSARFCI              [self::BANK_FIELD_NAME] => self::BANK_KOSARFCI,
        self::BANK_IRANZAMIN             [self::BANK_FIELD_NAME] => self::BANK_IRANZAMIN,
        self::BANK_HEKMATIRANIAN         [self::BANK_FIELD_NAME] => self::BANK_HEKMATIRANIAN,
        self::BANK_KARAFARIN             [self::BANK_FIELD_NAME] => self::BANK_KARAFARIN,
        self::BANK_SARMAYE               [self::BANK_FIELD_NAME] => self::BANK_SARMAYE,
        self::BANK_NOORFCI               [self::BANK_FIELD_NAME] => self::BANK_NOORFCI,
        self::BANK_GARDESHGARI           [self::BANK_FIELD_NAME] => self::BANK_GARDESHGARI,
        self::BANK_MELALFCI              [self::BANK_FIELD_NAME] => self::BANK_MELALFCI,
        self::BANK_MARKAZI               [self::BANK_FIELD_NAME] => self::BANK_MARKAZI,
        self::BANK_TOSEESADERAT          [self::BANK_FIELD_NAME] => self::BANK_TOSEESADERAT,
        self::BANK_MIDDLEEASTKHAVARMIANEH[self::BANK_FIELD_NAME] => self::BANK_MIDDLEEASTKHAVARMIANEH,
        self::BANK_SANATMADAN            [self::BANK_FIELD_NAME] => self::BANK_SANATMADAN,
        self::BANK_TOSEEFCI              [self::BANK_FIELD_NAME] => self::BANK_TOSEEFCI,
        self::BANK_IRANVENEZUELA         [self::BANK_FIELD_NAME] => self::BANK_IRANVENEZUELA,
    ];

    const BANKS_BY_IBAN_ID = [
        self::BANK_MELLI                 [self::BANK_FIELD_IBAN_ID] => self::BANK_MELLI,
        self::BANK_SADERAT               [self::BANK_FIELD_IBAN_ID] => self::BANK_SADERAT,
        self::BANK_KESHAVARZI            [self::BANK_FIELD_IBAN_ID] => self::BANK_KESHAVARZI,
        self::BANK_MELLAT                [self::BANK_FIELD_IBAN_ID] => self::BANK_MELLAT,
        self::BANK_REFAHKARGARAN         [self::BANK_FIELD_IBAN_ID] => self::BANK_REFAHKARGARAN,
        self::BANK_TEJARAT               [self::BANK_FIELD_IBAN_ID] => self::BANK_TEJARAT,
        self::BANK_SEPAH                 [self::BANK_FIELD_IBAN_ID] => self::BANK_SEPAH,
        self::BANK_POSTBANK              [self::BANK_FIELD_IBAN_ID] => self::BANK_POSTBANK,
        self::BANK_PASARGAD              [self::BANK_FIELD_IBAN_ID] => self::BANK_PASARGAD,
        self::BANK_MASKAN                [self::BANK_FIELD_IBAN_ID] => self::BANK_MASKAN,
        self::BANK_SHAHR                 [self::BANK_FIELD_IBAN_ID] => self::BANK_SHAHR,
        self::BANK_ANSAR                 [self::BANK_FIELD_IBAN_ID] => self::BANK_ANSAR,
        self::BANK_RESALATGH             [self::BANK_FIELD_IBAN_ID] => self::BANK_RESALATGH,
        self::BANK_PARSIAN               [self::BANK_FIELD_IBAN_ID] => self::BANK_PARSIAN,
        self::BANK_MEHRIRAN              [self::BANK_FIELD_IBAN_ID] => self::BANK_MEHRIRAN,
        self::BANK_GHAVAMIN              [self::BANK_FIELD_IBAN_ID] => self::BANK_GHAVAMIN,
        self::BANK_AYANDEH               [self::BANK_FIELD_IBAN_ID] => self::BANK_AYANDEH,
        self::BANK_SAMAN                 [self::BANK_FIELD_IBAN_ID] => self::BANK_SAMAN,
        self::BANK_SINA                  [self::BANK_FIELD_IBAN_ID] => self::BANK_SINA,
        self::BANK_TOSEETAAVON           [self::BANK_FIELD_IBAN_ID] => self::BANK_TOSEETAAVON,
        self::BANK_EGHTESADNOVIN         [self::BANK_FIELD_IBAN_ID] => self::BANK_EGHTESADNOVIN,
        self::BANK_MEHREGHTESAD          [self::BANK_FIELD_IBAN_ID] => self::BANK_MEHREGHTESAD,
        self::BANK_DEY                   [self::BANK_FIELD_IBAN_ID] => self::BANK_DEY,
        self::BANK_KOSARFCI              [self::BANK_FIELD_IBAN_ID] => self::BANK_KOSARFCI,
        self::BANK_IRANZAMIN             [self::BANK_FIELD_IBAN_ID] => self::BANK_IRANZAMIN,
        self::BANK_HEKMATIRANIAN         [self::BANK_FIELD_IBAN_ID] => self::BANK_HEKMATIRANIAN,
        self::BANK_KARAFARIN             [self::BANK_FIELD_IBAN_ID] => self::BANK_KARAFARIN,
        self::BANK_SARMAYE               [self::BANK_FIELD_IBAN_ID] => self::BANK_SARMAYE,
        self::BANK_NOORFCI               [self::BANK_FIELD_IBAN_ID] => self::BANK_NOORFCI,
        self::BANK_GARDESHGARI           [self::BANK_FIELD_IBAN_ID] => self::BANK_GARDESHGARI,
        self::BANK_MELALFCI              [self::BANK_FIELD_IBAN_ID] => self::BANK_MELALFCI,
        self::BANK_MARKAZI               [self::BANK_FIELD_IBAN_ID] => self::BANK_MARKAZI,
        self::BANK_TOSEESADERAT          [self::BANK_FIELD_IBAN_ID] => self::BANK_TOSEESADERAT,
        self::BANK_MIDDLEEASTKHAVARMIANEH[self::BANK_FIELD_IBAN_ID] => self::BANK_MIDDLEEASTKHAVARMIANEH,
        self::BANK_SANATMADAN            [self::BANK_FIELD_IBAN_ID] => self::BANK_SANATMADAN,
        self::BANK_TOSEEFCI              [self::BANK_FIELD_IBAN_ID] => self::BANK_TOSEEFCI,
        self::BANK_IRANVENEZUELA         [self::BANK_FIELD_IBAN_ID] => self::BANK_IRANVENEZUELA,
    ];
}