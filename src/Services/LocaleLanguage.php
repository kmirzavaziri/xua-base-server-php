<?php /** @noinspection SpellCheckingInspection */

namespace Xua\Core\Services;

use Xua\Core\Eves\Service;

final class LocaleLanguage extends Service
{
    private static string $locale;
    private static string $language;
    private static string $timezone;
    private static string $calendar;

    private function __construct() {}

    protected static function _init(): void
    {
        self::$locale = ConstantService::get('config', 'services.ll.locale');
        self::$language = ConstantService::get('config', 'services.ll.language');
        self::$timezone = ConstantService::get('config', 'services.ll.timezone') ?? date_default_timezone_get();
        self::$calendar = ConstantService::get('config', 'services.ll.calendar');
    }

    /**
     * @return string
     */
    public static function getLocale(): string
    {
        return self::$locale;
    }

    /**
     * @return string
     */
    public static function getLanguage(): string
    {
        return self::$language;
    }

    /**
     * @return string
     */
    public static function getTimezone(): string
    {
        return self::$timezone;
    }

    /**
     * @return string
     */
    public static function getCalendar(): string
    {
        return self::$calendar;
    }

    /**
     * @param string|null $language
     * @return bool
     */
    public static function isRtl(?string $language = null): bool
    {
        return in_array($language ?? self::$language, [
            self::LANG_AR,
//            self::LANG_ARC,
//            self::LANG_CKB,
            self::LANG_DV,
            self::LANG_FA,
//            self::LANG_HA,
            /* self::LANG_HE, */
//            self::LANG_KHW,
//            self::LANG_KS,
            self::LANG_PS,
//            self::LANG_SD,
            self::LANG_UR,
//            self::LANG_UZ_AF,
//            self::LANG_YI,
        ]);
    }

    /**
     * @param string $language
     * @return void
     */
    public static function setLanguage(string $language): void
    {
        self::$language = $language;
    }

    /**
     * @param string $timezone
     * @return void
     */
    public static function setTimezone(string $timezone): void
    {
        self::$timezone = $timezone;
    }

    /**
     * @param string $calendar
     * @return void
     */
    public static function setCalendar(string $calendar): void
    {
        self::$calendar = $calendar;
    }

    public static function localeToLang(?string $locale): string
    {
        return match ($locale) {
//            self::LOC_DJ, self::LOC_ER, self::LOC_ET => self::LANG_AA,
            self::LOC_SD, self::LOC_AE, self::LOC_BH, self::LOC_DZ, self::LOC_EG, self::LOC_IQ, self::LOC_JO, self::LOC_KW, self::LOC_LB, self::LOC_LY, self::LOC_MA, self::LOC_OM, self::LOC_QA, self::LOC_SA, self::LOC_SY, self::LOC_TN, self::LOC_YE => self::LANG_AR,
            self::LOC_AZ => self::LANG_AZ,
            self::LOC_BY => self::LANG_BE,
            self::LOC_BG => self::LANG_BG,
//            self::LOC_BD => self::LANG_BN,
            self::LOC_BA => self::LANG_BS,
            self::LOC_CZ => self::LANG_CS,
            self::LOC_DK => self::LANG_DA,
            self::LOC_AT, self::LOC_CH, self::LOC_DE, self::LOC_LU => self::LANG_DE,
            self::LOC_MV => self::LANG_DV,
//            self::LOC_BT => self::LANG_DZ,
            self::LOC_GR => self::LANG_EL,
            self::LOC_AG, self::LOC_AI, self::LOC_AQ, self::LOC_AS, self::LOC_BB, self::LOC_BW, self::LOC_NG, self::LOC_ZM, self::LOC_AU, self::LOC_CA, self::LOC_GB, self::LOC_IE, self::LOC_KE, self::LOC_NZ, self::LOC_PH, self::LOC_SG, self::LOC_US, self::LOC_ZA, self::LOC_ZW => self::LANG_EN,
            self::LOC_AD, self::LOC_CU, self::LOC_AR, self::LOC_BO, self::LOC_CL, self::LOC_CO, self::LOC_CR, self::LOC_DO, self::LOC_EC, self::LOC_ES, self::LOC_GT, self::LOC_HN, self::LOC_MX, self::LOC_NI, self::LOC_PA, self::LOC_PE, self::LOC_PR, self::LOC_PY, self::LOC_SV, self::LOC_UY, self::LOC_VE => self::LANG_ES,
            self::LOC_EE => self::LANG_ET,
            self::LOC_IR => self::LANG_FA,
            self::LOC_FI => self::LANG_FI,
            self::LOC_FO => self::LANG_FO,
            self::LOC_SN, self::LOC_BE, self::LOC_FR => self::LANG_FR,
            /* self::LOC_IL => self::LANG_HE, */
            self::LOC_IN => self::LANG_HI,
            self::LOC_HR => self::LANG_HR,
//            self::LOC_HT => self::LANG_HT,
            self::LOC_HU => self::LANG_HU,
            self::LOC_AM => self::LANG_HY,
            self::LOC_ID => self::LANG_ID,
            self::LOC_IS => self::LANG_IS,
            self::LOC_IT => self::LANG_IT,
            self::LOC_JP => self::LANG_JA,
            self::LOC_GE => self::LANG_KA,
            self::LOC_KZ => self::LANG_KK,
//            self::LOC_GL => self::LANG_KL,
//            self::LOC_KH => self::LANG_KM,
            self::LOC_KR, /* self::LOC_KP, */ => self::LANG_KO,
            self::LOC_KG => self::LANG_KY,
//            self::LOC_UG => self::LANG_LG,
//            self::LOC_LA => self::LANG_LO,
            self::LOC_LT => self::LANG_LT,
            self::LOC_LV => self::LANG_LV,
//            self::LOC_MG => self::LANG_MG,
            self::LOC_MK => self::LANG_MK,
            self::LOC_MN => self::LANG_MN,
            self::LOC_MY => self::LANG_MS,
            self::LOC_MT => self::LANG_MT,
//            self::LOC_MM => self::LANG_MY,
//            self::LOC_NP => self::LANG_NE,
            self::LOC_AW, self::LOC_NL => self::LANG_NL,
//            self::LOC_NO => self::LANG_NO,
            self::LOC_PL => self::LANG_PL,
            self::LOC_AF => self::LANG_PS,
            self::LOC_AO, self::LOC_BR, self::LOC_PT => self::LANG_PT,
            self::LOC_RO => self::LANG_RO,
            self::LOC_RU, self::LOC_UA => self::LANG_RU,
//            self::LOC_RW => self::LANG_RW,
            self::LOC_AX => self::LANG_SE,
            self::LOC_SK => self::LANG_SK,
            self::LOC_SI => self::LANG_SL,
//            self::LOC_SO => self::LANG_SO,
            self::LOC_AL, self::LOC_XK => self::LANG_SQ,
            self::LOC_ME, self::LOC_RS => self::LANG_SR,
            self::LOC_SE => self::LANG_SV,
            self::LOC_TZ => self::LANG_SW,
            self::LOC_LK => self::LANG_TA,
//            self::LOC_TJ => self::LANG_TG,
            self::LOC_TH => self::LANG_TH,
//            self::LOC_TM => self::LANG_TK,
            self::LOC_CY, self::LOC_TR => self::LANG_TR,
            self::LOC_PK => self::LANG_UR,
            self::LOC_UZ => self::LANG_UZ,
            self::LOC_VN => self::LANG_VI,
            self::LOC_CN, self::LOC_HK, self::LOC_TW => self::LANG_ZH,
            default => ConstantService::get('config', 'services.ll.language'),
        };
    }

    public static function natToLocale(?string $nat): ?string
    {
        return match ($nat) {
            self::NAT_AFGHAN => self::LOC_AF,
            self::NAT_ALBANIAN => self::LOC_AL,
            self::NAT_ALGERIAN => self::LOC_DZ,
            self::NAT_AMERICAN => self::LOC_US,
            self::NAT_ANDORRAN => self::LOC_AD,
            self::NAT_ANGOLAN => self::LOC_AO,
            self::NAT_ANGUILLAN => self::LOC_AI,
            self::NAT_ARGENTINE => self::LOC_AR,
            self::NAT_ARMENIAN => self::LOC_AM,
            self::NAT_AUSTRALIAN => self::LOC_AU,
            self::NAT_AUSTRIAN => self::LOC_AT,
            self::NAT_AZERBAIJANI => self::LOC_AZ,
            self::NAT_BAHAMIAN => self::LOC_BS,
            self::NAT_BAHRAINI => self::LOC_BH,
            self::NAT_BANGLADESHI => self::LOC_BD,
            self::NAT_BARBADIAN => self::LOC_BB,
            self::NAT_BELARUSIAN => self::LOC_BY,
            self::NAT_BELGIAN => self::LOC_BE,
            self::NAT_BELIZEAN => self::LOC_BZ,
            self::NAT_BENINESE => self::LOC_BJ,
            self::NAT_BERMUDIAN => self::LOC_BM,
            self::NAT_BHUTANESE => self::LOC_BT,
            self::NAT_BOLIVIAN => self::LOC_BO,
            self::NAT_BOTSWANAN => self::LOC_BW,
            self::NAT_BRAZILIAN => self::LOC_BR,
            self::NAT_BRITISH, self::NAT_ENGLISH, self::NAT_NORTHERN_IRISH, self::NAT_SCOTTISH, self::NAT_WELSH => self::LOC_GB,
            self::NAT_BRITISH_VIRGIN_ISLANDER => self::LOC_VG,
            self::NAT_BRUNEIAN => self::LOC_BN,
            self::NAT_BULGARIAN => self::LOC_BG,
            self::NAT_BURKINAN => self::LOC_BF,
            self::NAT_BURMESE => self::LOC_MM,
            self::NAT_BURUNDIAN => self::LOC_BI,
            self::NAT_CAMBODIAN => self::LOC_KH,
            self::NAT_CAMEROONIAN => self::LOC_CM,
            self::NAT_CANADIAN => self::LOC_CA,
            self::NAT_CAPE_VERDEAN => self::LOC_CV,
            self::NAT_CAYMAN_ISLANDER => self::LOC_KY,
            self::NAT_CENTRAL_AFRICAN => self::LOC_CF,
            self::NAT_CHADIAN => self::LOC_TD,
            self::NAT_CHILEAN => self::LOC_CL,
            self::NAT_CHINESE => self::LOC_CN,
            self::NAT_CITIZEN_OF_ANTIGUA_AND_BARBUDA => self::LOC_AG,
            self::NAT_CITIZEN_OF_BOSNIA_AND_HERZEGOVINA => self::LOC_BA,
            self::NAT_CITIZEN_OF_GUINEABISSAU => self::LOC_GW,
            self::NAT_CITIZEN_OF_KIRIBATI => self::LOC_KI,
            self::NAT_CITIZEN_OF_SEYCHELLES => self::LOC_SC,
            self::NAT_CITIZEN_OF_THE_DOMINICAN_REPUBLIC => self::LOC_DO,
            self::NAT_CITIZEN_OF_VANUATU => self::LOC_VU,
            self::NAT_COLOMBIAN => self::LOC_CO,
            self::NAT_COMORAN => self::LOC_KM,
            self::NAT_CONGOLESE_CONGO => self::LOC_CG,
            self::NAT_CONGOLESE_DRC => self::LOC_CD,
            self::NAT_COOK_ISLANDER => self::LOC_CK,
            self::NAT_COSTA_RICAN => self::LOC_CR,
            self::NAT_CROATIAN => self::LOC_HR,
            self::NAT_CUBAN => self::LOC_CU,
//            self::NAT_CYMRAES => ?, @TODO
//            self::NAT_CYMRO => ?, @TODO
            self::NAT_CYPRIOT => self::LOC_CY,
            self::NAT_CZECH => self::LOC_CZ,
            self::NAT_DANISH => self::LOC_DK,
            self::NAT_DJIBOUTIAN => self::LOC_DJ,
            self::NAT_DOMINICAN => self::LOC_DM,
            self::NAT_DUTCH => self::LOC_NL,
            self::NAT_EAST_TIMORESE => self::LOC_TL,
            self::NAT_ECUADOREAN => self::LOC_EC,
            self::NAT_EGYPTIAN => self::LOC_EG,
            self::NAT_EMIRATI => self::LOC_AE,
            self::NAT_EQUATORIAL_GUINEAN => self::LOC_GQ,
            self::NAT_ERITREAN => self::LOC_ER,
            self::NAT_ESTONIAN => self::LOC_EE,
            self::NAT_ETHIOPIAN => self::LOC_ET,
            self::NAT_FAROESE => self::LOC_FO,
            self::NAT_FIJIAN => self::LOC_FJ,
            self::NAT_FILIPINO => self::LOC_PH,
            self::NAT_FINNISH => self::LOC_FI,
            self::NAT_FRENCH => self::LOC_FR,
            self::NAT_GABONESE => self::LOC_GA,
            self::NAT_GAMBIAN => self::LOC_GM,
            self::NAT_GEORGIAN => self::LOC_GE,
            self::NAT_GERMAN => self::LOC_DE,
            self::NAT_GHANAIAN => self::LOC_GH,
            self::NAT_GIBRALTARIAN => self::LOC_GI,
            self::NAT_GREEK => self::LOC_GR,
            self::NAT_GREENLANDIC => self::LOC_GL,
            self::NAT_GRENADIAN => self::LOC_GD,
            self::NAT_GUAMANIAN => self::LOC_GU,
            self::NAT_GUATEMALAN => self::LOC_GT,
            self::NAT_GUINEAN => self::LOC_GN,
            self::NAT_GUYANESE => self::LOC_GY,
            self::NAT_HAITIAN => self::LOC_HT,
            self::NAT_HONDURAN => self::LOC_HN,
            self::NAT_HONG_KONGER => self::LOC_HK,
            self::NAT_HUNGARIAN => self::LOC_HU,
            self::NAT_ICELANDIC => self::LOC_IS,
            self::NAT_INDIAN => self::LOC_IN,
            self::NAT_INDONESIAN => self::LOC_ID,
            self::NAT_IRANIAN => self::LOC_IR,
            self::NAT_IRAQI => self::LOC_IQ,
            self::NAT_IRISH => self::LOC_IE,
            /* self::NAT_ISRAELI  => self::LOC_IL, */
            self::NAT_ITALIAN => self::LOC_IT,
            self::NAT_IVORIAN => self::LOC_CI,
            self::NAT_JAMAICAN => self::LOC_JM,
            self::NAT_JAPANESE => self::LOC_JP,
            self::NAT_JORDANIAN => self::LOC_JO,
            self::NAT_KAZAKH => self::LOC_KZ,
            self::NAT_KENYAN => self::LOC_KE,
            self::NAT_KITTITIAN => self::LOC_KN,
            self::NAT_KOSOVAN => self::LOC_XK,
            self::NAT_KUWAITI => self::LOC_KW,
            self::NAT_KYRGYZ => self::LOC_KG,
            self::NAT_LAO => self::LOC_LA,
            self::NAT_LATVIAN => self::LOC_LV,
            self::NAT_LEBANESE => self::LOC_LB,
            self::NAT_LIBERIAN => self::LOC_LR,
            self::NAT_LIBYAN => self::LOC_LY,
            self::NAT_LIECHTENSTEIN_CITIZEN => self::LOC_LI,
            self::NAT_LITHUANIAN => self::LOC_LT,
            self::NAT_LUXEMBOURGER => self::LOC_LU,
            self::NAT_MACANESE => self::LOC_MO,
            self::NAT_MACEDONIAN => self::LOC_MK,
            self::NAT_MALAGASY => self::LOC_MG,
            self::NAT_MALAWIAN => self::LOC_MW,
            self::NAT_MALAYSIAN => self::LOC_MY,
            self::NAT_MALDIVIAN => self::LOC_MV,
            self::NAT_MALIAN => self::LOC_ML,
            self::NAT_MALTESE => self::LOC_MT,
            self::NAT_MARSHALLESE => self::LOC_MH,
            self::NAT_MARTINIQUAIS => self::LOC_MQ,
            self::NAT_MAURITANIAN => self::LOC_MR,
            self::NAT_MAURITIAN => self::LOC_MU,
            self::NAT_MEXICAN => self::LOC_MX,
            self::NAT_MICRONESIAN => self::LOC_FM,
            self::NAT_MOLDOVAN => self::LOC_MD,
            self::NAT_MONEGASQUE => self::LOC_MC,
            self::NAT_MONGOLIAN => self::LOC_MN,
            self::NAT_MONTENEGRIN => self::LOC_ME,
            self::NAT_MONTSERRATIAN => self::LOC_MS,
            self::NAT_MOROCCAN => self::LOC_MA,
            self::NAT_MOSOTHO => self::LOC_LS,
            self::NAT_MOZAMBICAN => self::LOC_MZ,
            self::NAT_NAMIBIAN => self::LOC_NA,
            self::NAT_NAURUAN => self::LOC_NR,
            self::NAT_NEPALESE => self::LOC_NP,
            self::NAT_NEW_ZEALANDER => self::LOC_NZ,
            self::NAT_NICARAGUAN => self::LOC_NI,
            self::NAT_NIGERIAN => self::LOC_NG,
            self::NAT_NIGERIEN => self::LOC_NE,
            self::NAT_NIUEAN => self::LOC_NU,
            /* self::NAT_NORTH_KOREAN => self::LOC_KP, */
            self::NAT_NORWEGIAN => self::LOC_NO,
            self::NAT_OMANI => self::LOC_OM,
            self::NAT_PAKISTANI => self::LOC_PK,
            self::NAT_PALAUAN => self::LOC_PW,
            self::NAT_PALESTINIAN => self::LOC_PS,
            self::NAT_PANAMANIAN => self::LOC_PA,
            self::NAT_PAPUA_NEW_GUINEAN => self::LOC_PG,
            self::NAT_PARAGUAYAN => self::LOC_PY,
            self::NAT_PERUVIAN => self::LOC_PE,
            self::NAT_PITCAIRN_ISLANDER => self::LOC_PN,
            self::NAT_POLISH => self::LOC_PL,
            self::NAT_PORTUGUESE => self::LOC_PT,
//            self::NAT_PRYDEINIG => ?, @TODO
            self::NAT_PUERTO_RICAN => self::LOC_PR,
            self::NAT_QATARI => self::LOC_QA,
            self::NAT_ROMANIAN => self::LOC_RO,
            self::NAT_RUSSIAN => self::LOC_RU,
            self::NAT_RWANDAN => self::LOC_RW,
            self::NAT_SALVADOREAN => self::LOC_SV,
            self::NAT_SAMMARINESE => self::LOC_SM,
            self::NAT_SAMOAN => self::LOC_WS,
            self::NAT_SAO_TOMEAN => self::LOC_ST,
            self::NAT_SAUDI_ARABIAN => self::LOC_SA,
            self::NAT_SENEGALESE => self::LOC_SN,
            self::NAT_SERBIAN => self::LOC_RS,
            self::NAT_SIERRA_LEONEAN => self::LOC_SL,
            self::NAT_SINGAPOREAN => self::LOC_SG,
            self::NAT_SLOVAK => self::LOC_SK,
            self::NAT_SLOVENIAN => self::LOC_SI,
            self::NAT_SOLOMON_ISLANDER => self::LOC_SB,
            self::NAT_SOMALI => self::LOC_SO,
            self::NAT_SOUTH_AFRICAN => self::LOC_ZA,
            self::NAT_SOUTH_KOREAN => self::LOC_KR,
            self::NAT_SOUTH_SUDANESE => self::LOC_SS,
            self::NAT_SPANISH => self::LOC_ES,
            self::NAT_SRI_LANKAN => self::LOC_LK,
            self::NAT_ST_HELENIAN, self::NAT_TRISTANIAN => self::LOC_SH,
            self::NAT_ST_LUCIAN => self::LOC_LC,
            self::NAT_STATELESS => null,
            self::NAT_SUDANESE => self::LOC_SD,
            self::NAT_SURINAMESE => self::LOC_SR,
            self::NAT_SWAZI => self::LOC_SZ,
            self::NAT_SWEDISH => self::LOC_SE,
            self::NAT_SWISS => self::LOC_CH,
            self::NAT_SYRIAN => self::LOC_SY,
            self::NAT_TAIWANESE => self::LOC_TW,
            self::NAT_TAJIK => self::LOC_TJ,
            self::NAT_TANZANIAN => self::LOC_TZ,
            self::NAT_THAI => self::LOC_TH,
            self::NAT_TOGOLESE => self::LOC_TG,
            self::NAT_TONGAN => self::LOC_TO,
            self::NAT_TRINIDADIAN => self::LOC_TT,
            self::NAT_TUNISIAN => self::LOC_TN,
            self::NAT_TURKISH => self::LOC_TR,
            self::NAT_TURKMEN => self::LOC_TM,
            self::NAT_TURKS_AND_CAICOS_ISLANDER => self::LOC_TC,
            self::NAT_TUVALUAN => self::LOC_TV,
            self::NAT_UGANDAN => self::LOC_UG,
            self::NAT_UKRAINIAN => self::LOC_UA,
            self::NAT_URUGUAYAN => self::LOC_UY,
            self::NAT_UZBEK => self::LOC_UZ,
            self::NAT_VATICAN_CITIZEN => self::LOC_VA,
            self::NAT_VENEZUELAN => self::LOC_VE,
            self::NAT_VIETNAMESE => self::LOC_VN,
            self::NAT_VINCENTIAN => self::LOC_VC,
            self::NAT_WALLISIAN => self::LOC_WF,
            self::NAT_YEMENI => self::LOC_YE,
            self::NAT_ZAMBIAN => self::LOC_ZM,
            self::NAT_ZIMBABWEAN => self::LOC_ZW,
            default => null,
        };
    }

    public static function natToLang(?string $nat): string {
        return self::localeToLang(self::natToLocale($nat));
    }

    public static function computeDirection(?string $text): string
    {
        // @TODO persian/arabic digits must be evaluated to ltr
        if (!$text) {
            return 'ltr';
        }
        $ltrChars = "A-Za-z\u{00C0}-\u{00D6}\u{00D8}-\u{00F6}\u{00F8}-\u{02B8}\u{0300}-\u{0590}\u{0800}-\u{1FFF}\u{2C00}-\u{FB1C}\u{FDFE}-\u{FE6F}\u{FEFD}-\u{FFFF}";
        $rtlChars = "\u{0591}-\u{07FF}\u{FB1D}-\u{FDFD}\u{FE70}-\u{FEFC}";
        return preg_match("/^[^$ltrChars]*[$rtlChars]/", $text) ? 'rtl' : 'ltr';
    }

    const LOC_AF = 'AF'; const LOC_AX = 'AX'; const LOC_AL = 'AL'; const LOC_DZ = 'DZ'; const LOC_AS = 'AS'; const LOC_AD = 'AD'; const LOC_AO = 'AO'; const LOC_AI = 'AI'; const LOC_AQ = 'AQ'; const LOC_AG = 'AG'; const LOC_AR = 'AR'; const LOC_AM = 'AM'; const LOC_AW = 'AW'; const LOC_AU = 'AU'; const LOC_AT = 'AT'; const LOC_AZ = 'AZ'; const LOC_BS = 'BS'; const LOC_BH = 'BH'; const LOC_BD = 'BD'; const LOC_BB = 'BB'; const LOC_BY = 'BY'; const LOC_BE = 'BE'; const LOC_BZ = 'BZ'; const LOC_BJ = 'BJ'; const LOC_BM = 'BM'; const LOC_BT = 'BT'; const LOC_BO = 'BO'; const LOC_BQ = 'BQ'; const LOC_BA = 'BA'; const LOC_BW = 'BW'; const LOC_BV = 'BV'; const LOC_BR = 'BR'; const LOC_IO = 'IO'; const LOC_BN = 'BN'; const LOC_BG = 'BG'; const LOC_BF = 'BF'; const LOC_BI = 'BI'; const LOC_CV = 'CV'; const LOC_KH = 'KH'; const LOC_CM = 'CM'; const LOC_CA = 'CA'; const LOC_KY = 'KY'; const LOC_CF = 'CF'; const LOC_TD = 'TD'; const LOC_CL = 'CL'; const LOC_CN = 'CN'; const LOC_CX = 'CX'; const LOC_CC = 'CC'; const LOC_CO = 'CO'; const LOC_KM = 'KM'; const LOC_CD = 'CD'; const LOC_CG = 'CG'; const LOC_CK = 'CK'; const LOC_CR = 'CR'; const LOC_CI = 'CI'; const LOC_HR = 'HR'; const LOC_CU = 'CU'; const LOC_CW = 'CW'; const LOC_CY = 'CY'; const LOC_CZ = 'CZ'; const LOC_DK = 'DK'; const LOC_DJ = 'DJ'; const LOC_DM = 'DM'; const LOC_DO = 'DO'; const LOC_EC = 'EC'; const LOC_EG = 'EG'; const LOC_SV = 'SV'; const LOC_GQ = 'GQ'; const LOC_ER = 'ER'; const LOC_EE = 'EE'; const LOC_SZ = 'SZ'; const LOC_ET = 'ET'; const LOC_FK = 'FK'; const LOC_FO = 'FO'; const LOC_FJ = 'FJ'; const LOC_FI = 'FI'; const LOC_FR = 'FR'; const LOC_GF = 'GF'; const LOC_PF = 'PF'; const LOC_TF = 'TF'; const LOC_GA = 'GA'; const LOC_GM = 'GM'; const LOC_GE = 'GE'; const LOC_DE = 'DE'; const LOC_GH = 'GH'; const LOC_GI = 'GI'; const LOC_GR = 'GR'; const LOC_GL = 'GL'; const LOC_GD = 'GD'; const LOC_GP = 'GP'; const LOC_GU = 'GU'; const LOC_GT = 'GT'; const LOC_GG = 'GG'; const LOC_GN = 'GN'; const LOC_GW = 'GW'; const LOC_GY = 'GY'; const LOC_HT = 'HT'; const LOC_HM = 'HM'; const LOC_VA = 'VA'; const LOC_HN = 'HN'; const LOC_HK = 'HK'; const LOC_HU = 'HU'; const LOC_IS = 'IS'; const LOC_IN = 'IN'; const LOC_ID = 'ID'; const LOC_IR = 'IR'; const LOC_IQ = 'IQ'; const LOC_IE = 'IE'; const LOC_IM = 'IM'; /* const LOC_IL = 'IL'; */ const LOC_IT = 'IT'; const LOC_JM = 'JM'; const LOC_JP = 'JP'; const LOC_JE = 'JE'; const LOC_JO = 'JO'; const LOC_KZ = 'KZ'; const LOC_KE = 'KE'; const LOC_KI = 'KI'; /* const LOC_KP = 'KP'; */ const LOC_KR = 'KR'; const LOC_KW = 'KW'; const LOC_KG = 'KG'; const LOC_LA = 'LA'; const LOC_LV = 'LV'; const LOC_LB = 'LB'; const LOC_LS = 'LS'; const LOC_LR = 'LR'; const LOC_LY = 'LY'; const LOC_LI = 'LI'; const LOC_LT = 'LT'; const LOC_LU = 'LU'; const LOC_MO = 'MO'; const LOC_MK = 'MK'; const LOC_MG = 'MG'; const LOC_MW = 'MW'; const LOC_MY = 'MY'; const LOC_MV = 'MV'; const LOC_ML = 'ML'; const LOC_MT = 'MT'; const LOC_MH = 'MH'; const LOC_MQ = 'MQ'; const LOC_MR = 'MR'; const LOC_MU = 'MU'; const LOC_YT = 'YT'; const LOC_MX = 'MX'; const LOC_FM = 'FM'; const LOC_MD = 'MD'; const LOC_MC = 'MC'; const LOC_MN = 'MN'; const LOC_ME = 'ME'; const LOC_MS = 'MS'; const LOC_MA = 'MA'; const LOC_MZ = 'MZ'; const LOC_MM = 'MM'; const LOC_NA = 'NA'; const LOC_NR = 'NR'; const LOC_NP = 'NP'; const LOC_NL = 'NL'; const LOC_NC = 'NC'; const LOC_NZ = 'NZ'; const LOC_NI = 'NI'; const LOC_NE = 'NE'; const LOC_NG = 'NG'; const LOC_NU = 'NU'; const LOC_NF = 'NF'; const LOC_MP = 'MP'; const LOC_NO = 'NO'; const LOC_OM = 'OM'; const LOC_PK = 'PK'; const LOC_PW = 'PW'; const LOC_PS = 'PS'; const LOC_PA = 'PA'; const LOC_PG = 'PG'; const LOC_PY = 'PY'; const LOC_PE = 'PE'; const LOC_PH = 'PH'; const LOC_PN = 'PN'; const LOC_PL = 'PL'; const LOC_PT = 'PT'; const LOC_PR = 'PR'; const LOC_QA = 'QA'; const LOC_RE = 'RE'; const LOC_RO = 'RO'; const LOC_RU = 'RU'; const LOC_RW = 'RW'; const LOC_BL = 'BL'; const LOC_SH = 'SH'; const LOC_KN = 'KN'; const LOC_LC = 'LC'; const LOC_MF = 'MF'; const LOC_PM = 'PM'; const LOC_VC = 'VC'; const LOC_WS = 'WS'; const LOC_SM = 'SM'; const LOC_ST = 'ST'; const LOC_SA = 'SA'; const LOC_SN = 'SN'; const LOC_RS = 'RS'; const LOC_SC = 'SC'; const LOC_SL = 'SL'; const LOC_SG = 'SG'; const LOC_SX = 'SX'; const LOC_SK = 'SK'; const LOC_SI = 'SI'; const LOC_SB = 'SB'; const LOC_SO = 'SO'; const LOC_ZA = 'ZA'; const LOC_GS = 'GS'; const LOC_SS = 'SS'; const LOC_ES = 'ES'; const LOC_LK = 'LK'; const LOC_SD = 'SD'; const LOC_SR = 'SR'; const LOC_SJ = 'SJ'; const LOC_SE = 'SE'; const LOC_CH = 'CH'; const LOC_SY = 'SY'; const LOC_TW = 'TW'; const LOC_TJ = 'TJ'; const LOC_TZ = 'TZ'; const LOC_TH = 'TH'; const LOC_TL = 'TL'; const LOC_TG = 'TG'; const LOC_TK = 'TK'; const LOC_TO = 'TO'; const LOC_TT = 'TT'; const LOC_TN = 'TN'; const LOC_TR = 'TR'; const LOC_TM = 'TM'; const LOC_TC = 'TC'; const LOC_TV = 'TV'; const LOC_UG = 'UG'; const LOC_UA = 'UA'; const LOC_AE = 'AE'; const LOC_GB = 'GB'; const LOC_UM = 'UM'; const LOC_US = 'US'; const LOC_UY = 'UY'; const LOC_UZ = 'UZ'; const LOC_VU = 'VU'; const LOC_VE = 'VE'; const LOC_VN = 'VN'; const LOC_VG = 'VG'; const LOC_VI = 'VI'; const LOC_WF = 'WF'; const LOC_EH = 'EH'; const LOC_YE = 'YE'; const LOC_XK = 'XK'; const LOC_ZM = 'ZM'; const LOC_ZW = 'ZW';
    const LOC_ = [self::LOC_AF, self::LOC_AX, self::LOC_AL, self::LOC_DZ, self::LOC_AS, self::LOC_AD, self::LOC_AO, self::LOC_AI, self::LOC_AQ, self::LOC_AG, self::LOC_AR, self::LOC_AM, self::LOC_AW, self::LOC_AU, self::LOC_AT, self::LOC_AZ, self::LOC_BS, self::LOC_BH, self::LOC_BD, self::LOC_BB, self::LOC_BY, self::LOC_BE, self::LOC_BZ, self::LOC_BJ, self::LOC_BM, self::LOC_BT, self::LOC_BO, self::LOC_BQ, self::LOC_BA, self::LOC_BW, self::LOC_BV, self::LOC_BR, self::LOC_IO, self::LOC_BN, self::LOC_BG, self::LOC_BF, self::LOC_BI, self::LOC_CV, self::LOC_KH, self::LOC_CM, self::LOC_CA, self::LOC_KY, self::LOC_CF, self::LOC_TD, self::LOC_CL, self::LOC_CN, self::LOC_CX, self::LOC_CC, self::LOC_CO, self::LOC_KM, self::LOC_CD, self::LOC_CG, self::LOC_CK, self::LOC_CR, self::LOC_CI, self::LOC_HR, self::LOC_CU, self::LOC_CW, self::LOC_CY, self::LOC_CZ, self::LOC_DK, self::LOC_DJ, self::LOC_DM, self::LOC_DO, self::LOC_EC, self::LOC_EG, self::LOC_SV, self::LOC_GQ, self::LOC_ER, self::LOC_EE, self::LOC_SZ, self::LOC_ET, self::LOC_FK, self::LOC_FO, self::LOC_FJ, self::LOC_FI, self::LOC_FR, self::LOC_GF, self::LOC_PF, self::LOC_TF, self::LOC_GA, self::LOC_GM, self::LOC_GE, self::LOC_DE, self::LOC_GH, self::LOC_GI, self::LOC_GR, self::LOC_GL, self::LOC_GD, self::LOC_GP, self::LOC_GU, self::LOC_GT, self::LOC_GG, self::LOC_GN, self::LOC_GW, self::LOC_GY, self::LOC_HT, self::LOC_HM, self::LOC_VA, self::LOC_HN, self::LOC_HK, self::LOC_HU, self::LOC_IS, self::LOC_IN, self::LOC_ID, self::LOC_IR, self::LOC_IQ, self::LOC_IE, self::LOC_IM, /* self::LOC_IL, */ self::LOC_IT, self::LOC_JM, self::LOC_JP, self::LOC_JE, self::LOC_JO, self::LOC_KZ, self::LOC_KE, self::LOC_KI, /* self::LOC_KP, */ self::LOC_KR, self::LOC_KW, self::LOC_KG, self::LOC_LA, self::LOC_LV, self::LOC_LB, self::LOC_LS, self::LOC_LR, self::LOC_LY, self::LOC_LI, self::LOC_LT, self::LOC_LU, self::LOC_MO, self::LOC_MK, self::LOC_MG, self::LOC_MW, self::LOC_MY, self::LOC_MV, self::LOC_ML, self::LOC_MT, self::LOC_MH, self::LOC_MQ, self::LOC_MR, self::LOC_MU, self::LOC_YT, self::LOC_MX, self::LOC_FM, self::LOC_MD, self::LOC_MC, self::LOC_MN, self::LOC_ME, self::LOC_MS, self::LOC_MA, self::LOC_MZ, self::LOC_MM, self::LOC_NA, self::LOC_NR, self::LOC_NP, self::LOC_NL, self::LOC_NC, self::LOC_NZ, self::LOC_NI, self::LOC_NE, self::LOC_NG, self::LOC_NU, self::LOC_NF, self::LOC_MP, self::LOC_NO, self::LOC_OM, self::LOC_PK, self::LOC_PW, self::LOC_PS, self::LOC_PA, self::LOC_PG, self::LOC_PY, self::LOC_PE, self::LOC_PH, self::LOC_PN, self::LOC_PL, self::LOC_PT, self::LOC_PR, self::LOC_QA, self::LOC_RE, self::LOC_RO, self::LOC_RU, self::LOC_RW, self::LOC_BL, self::LOC_SH, self::LOC_KN, self::LOC_LC, self::LOC_MF, self::LOC_PM, self::LOC_VC, self::LOC_WS, self::LOC_SM, self::LOC_ST, self::LOC_SA, self::LOC_SN, self::LOC_RS, self::LOC_SC, self::LOC_SL, self::LOC_SG, self::LOC_SX, self::LOC_SK, self::LOC_SI, self::LOC_SB, self::LOC_SO, self::LOC_ZA, self::LOC_GS, self::LOC_SS, self::LOC_ES, self::LOC_LK, self::LOC_SD, self::LOC_SR, self::LOC_SJ, self::LOC_SE, self::LOC_CH, self::LOC_SY, self::LOC_TW, self::LOC_TJ, self::LOC_TZ, self::LOC_TH, self::LOC_TL, self::LOC_TG, self::LOC_TK, self::LOC_TO, self::LOC_TT, self::LOC_TN, self::LOC_TR, self::LOC_TM, self::LOC_TC, self::LOC_TV, self::LOC_UG, self::LOC_UA, self::LOC_AE, self::LOC_GB, self::LOC_UM, self::LOC_US, self::LOC_UY, self::LOC_UZ, self::LOC_VU, self::LOC_VE, self::LOC_VN, self::LOC_VG, self::LOC_VI, self::LOC_WF, self::LOC_EH, self::LOC_XK, self::LOC_YE, self::LOC_ZM, self::LOC_ZW];

    const LANG_AF = 'af'; const LANG_AR = 'ar'; const LANG_AZ = 'az'; const LANG_BE = 'be'; const LANG_BG = 'bg'; const LANG_BS = 'bs'; const LANG_CA = 'ca'; const LANG_CS = 'cs'; const LANG_CY = 'cy'; const LANG_DA = 'da'; const LANG_DE = 'de'; const LANG_DV = 'dv'; const LANG_EL = 'el'; const LANG_EN = 'en'; const LANG_EO = 'eo'; const LANG_ES = 'es'; const LANG_ET = 'et'; const LANG_EU = 'eu'; const LANG_FA = 'fa'; const LANG_FI = 'fi'; const LANG_FO = 'fo'; const LANG_FR = 'fr'; const LANG_GL = 'gl'; const LANG_GU = 'gu'; /* const LANG_HE = 'he'; */ const LANG_HI = 'hi'; const LANG_HR = 'hr'; const LANG_HU = 'hu'; const LANG_HY = 'hy'; const LANG_ID = 'id'; const LANG_IS = 'is'; const LANG_IT = 'it'; const LANG_JA = 'ja'; const LANG_KA = 'ka'; const LANG_KK = 'kk'; const LANG_KN = 'kn'; const LANG_KO = 'ko'; const LANG_KOK = 'kok'; const LANG_KY = 'ky'; const LANG_LT = 'lt'; const LANG_LV = 'lv'; const LANG_MI = 'mi'; const LANG_MK = 'mk'; const LANG_MN = 'mn'; const LANG_MR = 'mr'; const LANG_MS = 'ms'; const LANG_MT = 'mt'; const LANG_NB = 'nb'; const LANG_NL = 'nl'; const LANG_NN = 'nn'; const LANG_NS = 'ns'; const LANG_PA = 'pa'; const LANG_PL = 'pl'; const LANG_PS = 'ps'; const LANG_PT = 'pt'; const LANG_QU = 'qu'; const LANG_RO = 'ro'; const LANG_RU = 'ru'; const LANG_SA = 'sa'; const LANG_SE = 'se'; const LANG_SK = 'sk'; const LANG_SL = 'sl'; const LANG_SQ = 'sq'; const LANG_SR = 'sr'; const LANG_SV = 'sv'; const LANG_SW = 'sw'; const LANG_SYR = 'syr'; const LANG_TA = 'ta'; const LANG_TE = 'te'; const LANG_TH = 'th'; const LANG_TL = 'tl'; const LANG_TN = 'tn'; const LANG_TR = 'tr'; const LANG_TT = 'tt'; const LANG_TS = 'ts'; const LANG_UK = 'uk'; const LANG_UR = 'ur'; const LANG_UZ = 'uz'; const LANG_VI = 'vi'; const LANG_XH = 'xh'; const LANG_ZH = 'zh'; const LANG_ZU = 'zu';
    const LANG_ = [self::LANG_AF, self::LANG_AR, self::LANG_AZ, self::LANG_BE, self::LANG_BG, self::LANG_BS, self::LANG_CA, self::LANG_CS, self::LANG_CY, self::LANG_DA, self::LANG_DE, self::LANG_DV, self::LANG_EL, self::LANG_EN, self::LANG_EO, self::LANG_ES, self::LANG_ET, self::LANG_EU, self::LANG_FA, self::LANG_FI, self::LANG_FO, self::LANG_FR, self::LANG_GL, self::LANG_GU, /* self::LANG_HE, */ self::LANG_HI, self::LANG_HR, self::LANG_HU, self::LANG_HY, self::LANG_ID, self::LANG_IS, self::LANG_IT, self::LANG_JA, self::LANG_KA, self::LANG_KK, self::LANG_KN, self::LANG_KO, self::LANG_KOK, self::LANG_KY, self::LANG_LT, self::LANG_LV, self::LANG_MI, self::LANG_MK, self::LANG_MN, self::LANG_MR, self::LANG_MS, self::LANG_MT, self::LANG_NB, self::LANG_NL, self::LANG_NN, self::LANG_NS, self::LANG_PA, self::LANG_PL, self::LANG_PS, self::LANG_PT, self::LANG_QU, self::LANG_RO, self::LANG_RU, self::LANG_SA, self::LANG_SE, self::LANG_SK, self::LANG_SL, self::LANG_SQ, self::LANG_SR, self::LANG_SV, self::LANG_SW, self::LANG_SYR, self::LANG_TA, self::LANG_TE, self::LANG_TH, self::LANG_TL, self::LANG_TN, self::LANG_TR, self::LANG_TT, self::LANG_TS, self::LANG_UK, self::LANG_UR, self::LANG_UZ, self::LANG_VI, self::LANG_XH, self::LANG_ZH, self::LANG_ZU];

    const NAT_AFGHAN = "afghan"; const NAT_ALBANIAN = "albanian"; const NAT_ALGERIAN = "algerian"; const NAT_AMERICAN = "american"; const NAT_ANDORRAN = "andorran"; const NAT_ANGOLAN = "angolan"; const NAT_ANGUILLAN = "anguillan"; const NAT_ARGENTINE = "argentine"; const NAT_ARMENIAN = "armenian"; const NAT_AUSTRALIAN = "australian"; const NAT_AUSTRIAN = "austrian"; const NAT_AZERBAIJANI = "azerbaijani"; const NAT_BAHAMIAN = "bahamian"; const NAT_BAHRAINI = "bahraini"; const NAT_BANGLADESHI = "bangladeshi"; const NAT_BARBADIAN = "barbadian"; const NAT_BELARUSIAN = "belarusian"; const NAT_BELGIAN = "belgian"; const NAT_BELIZEAN = "belizean"; const NAT_BENINESE = "beninese"; const NAT_BERMUDIAN = "bermudian"; const NAT_BHUTANESE = "bhutanese"; const NAT_BOLIVIAN = "bolivian"; const NAT_BOTSWANAN = "botswanan"; const NAT_BRAZILIAN = "brazilian"; const NAT_BRITISH = "british"; const NAT_BRITISH_VIRGIN_ISLANDER = "british_virgin_islander"; const NAT_BRUNEIAN = "bruneian"; const NAT_BULGARIAN = "bulgarian"; const NAT_BURKINAN = "burkinan"; const NAT_BURMESE = "burmese"; const NAT_BURUNDIAN = "burundian"; const NAT_CAMBODIAN = "cambodian"; const NAT_CAMEROONIAN = "cameroonian"; const NAT_CANADIAN = "canadian"; const NAT_CAPE_VERDEAN = "cape_verdean"; const NAT_CAYMAN_ISLANDER = "cayman_islander"; const NAT_CENTRAL_AFRICAN = "central_african"; const NAT_CHADIAN = "chadian"; const NAT_CHILEAN = "chilean"; const NAT_CHINESE = "chinese"; const NAT_CITIZEN_OF_ANTIGUA_AND_BARBUDA = "citizen_of_antigua_and_barbuda"; const NAT_CITIZEN_OF_BOSNIA_AND_HERZEGOVINA = "citizen_of_bosnia_and_herzegovina"; const NAT_CITIZEN_OF_GUINEABISSAU = "citizen_of_guineabissau"; const NAT_CITIZEN_OF_KIRIBATI = "citizen_of_kiribati"; const NAT_CITIZEN_OF_SEYCHELLES = "citizen_of_seychelles"; const NAT_CITIZEN_OF_THE_DOMINICAN_REPUBLIC = "citizen_of_the_dominican_republic"; const NAT_CITIZEN_OF_VANUATU = "citizen_of_vanuatu"; const NAT_COLOMBIAN = "colombian"; const NAT_COMORAN = "comoran"; const NAT_CONGOLESE_CONGO = "congolese_congo"; const NAT_CONGOLESE_DRC = "congolese_drc"; const NAT_COOK_ISLANDER = "cook_islander"; const NAT_COSTA_RICAN = "costa_rican"; const NAT_CROATIAN = "croatian"; const NAT_CUBAN = "cuban"; const NAT_CYMRAES = "cymraes"; const NAT_CYMRO = "cymro"; const NAT_CYPRIOT = "cypriot"; const NAT_CZECH = "czech"; const NAT_DANISH = "danish"; const NAT_DJIBOUTIAN = "djiboutian"; const NAT_DOMINICAN = "dominican"; const NAT_DUTCH = "dutch"; const NAT_EAST_TIMORESE = "east_timorese"; const NAT_ECUADOREAN = "ecuadorean"; const NAT_EGYPTIAN = "egyptian"; const NAT_EMIRATI = "emirati"; const NAT_ENGLISH = "english"; const NAT_EQUATORIAL_GUINEAN = "equatorial_guinean"; const NAT_ERITREAN = "eritrean"; const NAT_ESTONIAN = "estonian"; const NAT_ETHIOPIAN = "ethiopian"; const NAT_FAROESE = "faroese"; const NAT_FIJIAN = "fijian"; const NAT_FILIPINO = "filipino"; const NAT_FINNISH = "finnish"; const NAT_FRENCH = "french"; const NAT_GABONESE = "gabonese"; const NAT_GAMBIAN = "gambian"; const NAT_GEORGIAN = "georgian"; const NAT_GERMAN = "german"; const NAT_GHANAIAN = "ghanaian"; const NAT_GIBRALTARIAN = "gibraltarian"; const NAT_GREEK = "greek"; const NAT_GREENLANDIC = "greenlandic"; const NAT_GRENADIAN = "grenadian"; const NAT_GUAMANIAN = "guamanian"; const NAT_GUATEMALAN = "guatemalan"; const NAT_GUINEAN = "guinean"; const NAT_GUYANESE = "guyanese"; const NAT_HAITIAN = "haitian"; const NAT_HONDURAN = "honduran"; const NAT_HONG_KONGER = "hong_konger"; const NAT_HUNGARIAN = "hungarian"; const NAT_ICELANDIC = "icelandic"; const NAT_INDIAN = "indian"; const NAT_INDONESIAN = "indonesian"; const NAT_IRANIAN = "iranian"; const NAT_IRAQI = "iraqi"; const NAT_IRISH = "irish"; /* const NAT_ISRAELI = "israeli"; */ const NAT_ITALIAN = "italian"; const NAT_IVORIAN = "ivorian"; const NAT_JAMAICAN = "jamaican"; const NAT_JAPANESE = "japanese"; const NAT_JORDANIAN = "jordanian"; const NAT_KAZAKH = "kazakh"; const NAT_KENYAN = "kenyan"; const NAT_KITTITIAN = "kittitian"; const NAT_KOSOVAN = "kosovan"; const NAT_KUWAITI = "kuwaiti"; const NAT_KYRGYZ = "kyrgyz"; const NAT_LAO = "lao"; const NAT_LATVIAN = "latvian"; const NAT_LEBANESE = "lebanese"; const NAT_LIBERIAN = "liberian"; const NAT_LIBYAN = "libyan"; const NAT_LIECHTENSTEIN_CITIZEN = "liechtenstein_citizen"; const NAT_LITHUANIAN = "lithuanian"; const NAT_LUXEMBOURGER = "luxembourger"; const NAT_MACANESE = "macanese"; const NAT_MACEDONIAN = "macedonian"; const NAT_MALAGASY = "malagasy"; const NAT_MALAWIAN = "malawian"; const NAT_MALAYSIAN = "malaysian"; const NAT_MALDIVIAN = "maldivian"; const NAT_MALIAN = "malian"; const NAT_MALTESE = "maltese"; const NAT_MARSHALLESE = "marshallese"; const NAT_MARTINIQUAIS = "martiniquais"; const NAT_MAURITANIAN = "mauritanian"; const NAT_MAURITIAN = "mauritian"; const NAT_MEXICAN = "mexican"; const NAT_MICRONESIAN = "micronesian"; const NAT_MOLDOVAN = "moldovan"; const NAT_MONEGASQUE = "monegasque"; const NAT_MONGOLIAN = "mongolian"; const NAT_MONTENEGRIN = "montenegrin"; const NAT_MONTSERRATIAN = "montserratian"; const NAT_MOROCCAN = "moroccan"; const NAT_MOSOTHO = "mosotho"; const NAT_MOZAMBICAN = "mozambican"; const NAT_NAMIBIAN = "namibian"; const NAT_NAURUAN = "nauruan"; const NAT_NEPALESE = "nepalese"; const NAT_NEW_ZEALANDER = "new_zealander"; const NAT_NICARAGUAN = "nicaraguan"; const NAT_NIGERIAN = "nigerian"; const NAT_NIGERIEN = "nigerien"; const NAT_NIUEAN = "niuean"; /* const NAT_NORTH_KOREAN = "north_korean"; */ const NAT_NORTHERN_IRISH = "northern_irish"; const NAT_NORWEGIAN = "norwegian"; const NAT_OMANI = "omani"; const NAT_PAKISTANI = "pakistani"; const NAT_PALAUAN = "palauan"; const NAT_PALESTINIAN = "palestinian"; const NAT_PANAMANIAN = "panamanian"; const NAT_PAPUA_NEW_GUINEAN = "papua_new_guinean"; const NAT_PARAGUAYAN = "paraguayan"; const NAT_PERUVIAN = "peruvian"; const NAT_PITCAIRN_ISLANDER = "pitcairn_islander"; const NAT_POLISH = "polish"; const NAT_PORTUGUESE = "portuguese"; const NAT_PRYDEINIG = "prydeinig"; const NAT_PUERTO_RICAN = "puerto_rican"; const NAT_QATARI = "qatari"; const NAT_ROMANIAN = "romanian"; const NAT_RUSSIAN = "russian"; const NAT_RWANDAN = "rwandan"; const NAT_SALVADOREAN = "salvadorean"; const NAT_SAMMARINESE = "sammarinese"; const NAT_SAMOAN = "samoan"; const NAT_SAO_TOMEAN = "sao_tomean"; const NAT_SAUDI_ARABIAN = "saudi_arabian"; const NAT_SCOTTISH = "scottish"; const NAT_SENEGALESE = "senegalese"; const NAT_SERBIAN = "serbian"; const NAT_SIERRA_LEONEAN = "sierra_leonean"; const NAT_SINGAPOREAN = "singaporean"; const NAT_SLOVAK = "slovak"; const NAT_SLOVENIAN = "slovenian"; const NAT_SOLOMON_ISLANDER = "solomon_islander"; const NAT_SOMALI = "somali"; const NAT_SOUTH_AFRICAN = "south_african"; const NAT_SOUTH_KOREAN = "south_korean"; const NAT_SOUTH_SUDANESE = "south_sudanese"; const NAT_SPANISH = "spanish"; const NAT_SRI_LANKAN = "sri_lankan"; const NAT_ST_HELENIAN = "st_helenian"; const NAT_ST_LUCIAN = "st_lucian"; const NAT_STATELESS = "stateless"; const NAT_SUDANESE = "sudanese"; const NAT_SURINAMESE = "surinamese"; const NAT_SWAZI = "swazi"; const NAT_SWEDISH = "swedish"; const NAT_SWISS = "swiss"; const NAT_SYRIAN = "syrian"; const NAT_TAIWANESE = "taiwanese"; const NAT_TAJIK = "tajik"; const NAT_TANZANIAN = "tanzanian"; const NAT_THAI = "thai"; const NAT_TOGOLESE = "togolese"; const NAT_TONGAN = "tongan"; const NAT_TRINIDADIAN = "trinidadian"; const NAT_TRISTANIAN = "tristanian"; const NAT_TUNISIAN = "tunisian"; const NAT_TURKISH = "turkish"; const NAT_TURKMEN = "turkmen"; const NAT_TURKS_AND_CAICOS_ISLANDER = "turks_and_caicos_islander"; const NAT_TUVALUAN = "tuvaluan"; const NAT_UGANDAN = "ugandan"; const NAT_UKRAINIAN = "ukrainian"; const NAT_URUGUAYAN = "uruguayan"; const NAT_UZBEK = "uzbek"; const NAT_VATICAN_CITIZEN = "vatican_citizen"; const NAT_VENEZUELAN = "venezuelan"; const NAT_VIETNAMESE = "vietnamese"; const NAT_VINCENTIAN = "vincentian"; const NAT_WALLISIAN = "wallisian"; const NAT_WELSH = "welsh"; const NAT_YEMENI = "yemeni"; const NAT_ZAMBIAN = "zambian"; const NAT_ZIMBABWEAN = "zimbabwean";
    const NAT_ = [self::NAT_AFGHAN, self::NAT_ALBANIAN, self::NAT_ALGERIAN, self::NAT_AMERICAN, self::NAT_ANDORRAN, self::NAT_ANGOLAN, self::NAT_ANGUILLAN, self::NAT_ARGENTINE, self::NAT_ARMENIAN, self::NAT_AUSTRALIAN, self::NAT_AUSTRIAN, self::NAT_AZERBAIJANI, self::NAT_BAHAMIAN, self::NAT_BAHRAINI, self::NAT_BANGLADESHI, self::NAT_BARBADIAN, self::NAT_BELARUSIAN, self::NAT_BELGIAN, self::NAT_BELIZEAN, self::NAT_BENINESE, self::NAT_BERMUDIAN, self::NAT_BHUTANESE, self::NAT_BOLIVIAN, self::NAT_BOTSWANAN, self::NAT_BRAZILIAN, self::NAT_BRITISH, self::NAT_BRITISH_VIRGIN_ISLANDER, self::NAT_BRUNEIAN, self::NAT_BULGARIAN, self::NAT_BURKINAN, self::NAT_BURMESE, self::NAT_BURUNDIAN, self::NAT_CAMBODIAN, self::NAT_CAMEROONIAN, self::NAT_CANADIAN, self::NAT_CAPE_VERDEAN, self::NAT_CAYMAN_ISLANDER, self::NAT_CENTRAL_AFRICAN, self::NAT_CHADIAN, self::NAT_CHILEAN, self::NAT_CHINESE, self::NAT_CITIZEN_OF_ANTIGUA_AND_BARBUDA, self::NAT_CITIZEN_OF_BOSNIA_AND_HERZEGOVINA, self::NAT_CITIZEN_OF_GUINEABISSAU, self::NAT_CITIZEN_OF_KIRIBATI, self::NAT_CITIZEN_OF_SEYCHELLES, self::NAT_CITIZEN_OF_THE_DOMINICAN_REPUBLIC, self::NAT_CITIZEN_OF_VANUATU, self::NAT_COLOMBIAN, self::NAT_COMORAN, self::NAT_CONGOLESE_CONGO, self::NAT_CONGOLESE_DRC, self::NAT_COOK_ISLANDER, self::NAT_COSTA_RICAN, self::NAT_CROATIAN, self::NAT_CUBAN, self::NAT_CYMRAES, self::NAT_CYMRO, self::NAT_CYPRIOT, self::NAT_CZECH, self::NAT_DANISH, self::NAT_DJIBOUTIAN, self::NAT_DOMINICAN, self::NAT_DUTCH, self::NAT_EAST_TIMORESE, self::NAT_ECUADOREAN, self::NAT_EGYPTIAN, self::NAT_EMIRATI, self::NAT_ENGLISH, self::NAT_EQUATORIAL_GUINEAN, self::NAT_ERITREAN, self::NAT_ESTONIAN, self::NAT_ETHIOPIAN, self::NAT_FAROESE, self::NAT_FIJIAN, self::NAT_FILIPINO, self::NAT_FINNISH, self::NAT_FRENCH, self::NAT_GABONESE, self::NAT_GAMBIAN, self::NAT_GEORGIAN, self::NAT_GERMAN, self::NAT_GHANAIAN, self::NAT_GIBRALTARIAN, self::NAT_GREEK, self::NAT_GREENLANDIC, self::NAT_GRENADIAN, self::NAT_GUAMANIAN, self::NAT_GUATEMALAN, self::NAT_GUINEAN, self::NAT_GUYANESE, self::NAT_HAITIAN, self::NAT_HONDURAN, self::NAT_HONG_KONGER, self::NAT_HUNGARIAN, self::NAT_ICELANDIC, self::NAT_INDIAN, self::NAT_INDONESIAN, self::NAT_IRANIAN, self::NAT_IRAQI, self::NAT_IRISH, /* self::NAT_ISRAELI ,*/ self::NAT_ITALIAN, self::NAT_IVORIAN, self::NAT_JAMAICAN, self::NAT_JAPANESE, self::NAT_JORDANIAN, self::NAT_KAZAKH, self::NAT_KENYAN, self::NAT_KITTITIAN, self::NAT_KOSOVAN, self::NAT_KUWAITI, self::NAT_KYRGYZ, self::NAT_LAO, self::NAT_LATVIAN, self::NAT_LEBANESE, self::NAT_LIBERIAN, self::NAT_LIBYAN, self::NAT_LIECHTENSTEIN_CITIZEN, self::NAT_LITHUANIAN, self::NAT_LUXEMBOURGER, self::NAT_MACANESE, self::NAT_MACEDONIAN, self::NAT_MALAGASY, self::NAT_MALAWIAN, self::NAT_MALAYSIAN, self::NAT_MALDIVIAN, self::NAT_MALIAN, self::NAT_MALTESE, self::NAT_MARSHALLESE, self::NAT_MARTINIQUAIS, self::NAT_MAURITANIAN, self::NAT_MAURITIAN, self::NAT_MEXICAN, self::NAT_MICRONESIAN, self::NAT_MOLDOVAN, self::NAT_MONEGASQUE, self::NAT_MONGOLIAN, self::NAT_MONTENEGRIN, self::NAT_MONTSERRATIAN, self::NAT_MOROCCAN, self::NAT_MOSOTHO, self::NAT_MOZAMBICAN, self::NAT_NAMIBIAN, self::NAT_NAURUAN, self::NAT_NEPALESE, self::NAT_NEW_ZEALANDER, self::NAT_NICARAGUAN, self::NAT_NIGERIAN, self::NAT_NIGERIEN, self::NAT_NIUEAN, /* self::NAT_NORTH_KOREAN, */ self::NAT_NORTHERN_IRISH, self::NAT_NORWEGIAN, self::NAT_OMANI, self::NAT_PAKISTANI, self::NAT_PALAUAN, self::NAT_PALESTINIAN, self::NAT_PANAMANIAN, self::NAT_PAPUA_NEW_GUINEAN, self::NAT_PARAGUAYAN, self::NAT_PERUVIAN, self::NAT_PITCAIRN_ISLANDER, self::NAT_POLISH, self::NAT_PORTUGUESE, self::NAT_PRYDEINIG, self::NAT_PUERTO_RICAN, self::NAT_QATARI, self::NAT_ROMANIAN, self::NAT_RUSSIAN, self::NAT_RWANDAN, self::NAT_SALVADOREAN, self::NAT_SAMMARINESE, self::NAT_SAMOAN, self::NAT_SAO_TOMEAN, self::NAT_SAUDI_ARABIAN, self::NAT_SCOTTISH, self::NAT_SENEGALESE, self::NAT_SERBIAN, self::NAT_SIERRA_LEONEAN, self::NAT_SINGAPOREAN, self::NAT_SLOVAK, self::NAT_SLOVENIAN, self::NAT_SOLOMON_ISLANDER, self::NAT_SOMALI, self::NAT_SOUTH_AFRICAN, self::NAT_SOUTH_KOREAN, self::NAT_SOUTH_SUDANESE, self::NAT_SPANISH, self::NAT_SRI_LANKAN, self::NAT_ST_HELENIAN, self::NAT_ST_LUCIAN, self::NAT_STATELESS, self::NAT_SUDANESE, self::NAT_SURINAMESE, self::NAT_SWAZI, self::NAT_SWEDISH, self::NAT_SWISS, self::NAT_SYRIAN, self::NAT_TAIWANESE, self::NAT_TAJIK, self::NAT_TANZANIAN, self::NAT_THAI, self::NAT_TOGOLESE, self::NAT_TONGAN, self::NAT_TRINIDADIAN, self::NAT_TRISTANIAN, self::NAT_TUNISIAN, self::NAT_TURKISH, self::NAT_TURKMEN, self::NAT_TURKS_AND_CAICOS_ISLANDER, self::NAT_TUVALUAN, self::NAT_UGANDAN, self::NAT_UKRAINIAN, self::NAT_URUGUAYAN, self::NAT_UZBEK, self::NAT_VATICAN_CITIZEN, self::NAT_VENEZUELAN, self::NAT_VIETNAMESE, self::NAT_VINCENTIAN, self::NAT_WALLISIAN, self::NAT_WELSH, self::NAT_YEMENI, self::NAT_ZAMBIAN, self::NAT_ZIMBABWEAN];

    const CAL_GREGORIAN = 'gregorian'; const CAL_JALALI = 'jalali';
    const CAL_ = [self::CAL_GREGORIAN, self::CAL_JALALI];

    const TZ_AFRICA_ABIDJAN = 'Africa/Abidjan'; const TZ_AFRICA_ACCRA = 'Africa/Accra'; const TZ_AFRICA_ADDIS_ABABA = 'Africa/Addis_Ababa'; const TZ_AFRICA_ALGIERS = 'Africa/Algiers'; const TZ_AFRICA_ASMARA = 'Africa/Asmara'; const TZ_AFRICA_ASMERA = 'Africa/Asmera'; const TZ_AFRICA_BAMAKO = 'Africa/Bamako'; const TZ_AFRICA_BANGUI = 'Africa/Bangui'; const TZ_AFRICA_BANJUL = 'Africa/Banjul'; const TZ_AFRICA_BISSAU = 'Africa/Bissau'; const TZ_AFRICA_BLANTYRE = 'Africa/Blantyre'; const TZ_AFRICA_BRAZZAVILLE = 'Africa/Brazzaville'; const TZ_AFRICA_BUJUMBURA = 'Africa/Bujumbura'; const TZ_AFRICA_CAIRO = 'Africa/Cairo'; const TZ_AFRICA_CEUTA = 'Africa/Ceuta'; const TZ_AFRICA_CONAKRY = 'Africa/Conakry'; const TZ_AFRICA_DAKAR = 'Africa/Dakar'; const TZ_AFRICA_DAR_ES_SALAAM = 'Africa/Dar_es_Salaam'; const TZ_AFRICA_DJIBOUTI = 'Africa/Djibouti'; const TZ_AFRICA_DOUALA = 'Africa/Douala'; const TZ_AFRICA_FREETOWN = 'Africa/Freetown'; const TZ_AFRICA_GABORONE = 'Africa/Gaborone'; const TZ_AFRICA_HARARE = 'Africa/Harare'; const TZ_AFRICA_JOHANNESBURG = 'Africa/Johannesburg'; const TZ_AFRICA_JUBA = 'Africa/Juba'; const TZ_AFRICA_KAMPALA = 'Africa/Kampala'; const TZ_AFRICA_KHARTOUM = 'Africa/Khartoum'; const TZ_AFRICA_KIGALI = 'Africa/Kigali'; const TZ_AFRICA_KINSHASA = 'Africa/Kinshasa'; const TZ_AFRICA_LAGOS = 'Africa/Lagos'; const TZ_AFRICA_LIBREVILLE = 'Africa/Libreville'; const TZ_AFRICA_LOME = 'Africa/Lome'; const TZ_AFRICA_LUANDA = 'Africa/Luanda'; const TZ_AFRICA_LUBUMBASHI = 'Africa/Lubumbashi'; const TZ_AFRICA_LUSAKA = 'Africa/Lusaka'; const TZ_AFRICA_MALABO = 'Africa/Malabo'; const TZ_AFRICA_MAPUTO = 'Africa/Maputo'; const TZ_AFRICA_MASERU = 'Africa/Maseru'; const TZ_AFRICA_MBABANE = 'Africa/Mbabane'; const TZ_AFRICA_MOGADISHU = 'Africa/Mogadishu'; const TZ_AFRICA_MONROVIA = 'Africa/Monrovia'; const TZ_AFRICA_NAIROBI = 'Africa/Nairobi'; const TZ_AFRICA_NDJAMENA = 'Africa/Ndjamena'; const TZ_AFRICA_NIAMEY = 'Africa/Niamey'; const TZ_AFRICA_NOUAKCHOTT = 'Africa/Nouakchott'; const TZ_AFRICA_OUAGADOUGOU = 'Africa/Ouagadougou'; const TZ_AFRICA_PORTO_NOVO = 'Africa/Porto-Novo'; const TZ_AFRICA_SAO_TOME = 'Africa/Sao_Tome'; const TZ_AFRICA_TIMBUKTU = 'Africa/Timbuktu'; const TZ_AFRICA_TRIPOLI = 'Africa/Tripoli'; const TZ_AFRICA_TUNIS = 'Africa/Tunis'; const TZ_AFRICA_WINDHOEK = 'Africa/Windhoek'; const TZ_AMERICA_ADAK = 'America/Adak'; const TZ_AMERICA_ANCHORAGE = 'America/Anchorage'; const TZ_AMERICA_ANGUILLA = 'America/Anguilla'; const TZ_AMERICA_ANTIGUA = 'America/Antigua'; const TZ_AMERICA_ARGENTINA_BUENOS_AIRES = 'America/Argentina/Buenos_Aires'; const TZ_AMERICA_ARGENTINA_CATAMARCA = 'America/Argentina/Catamarca'; const TZ_AMERICA_ARGENTINA_COMODRIVADAVIA = 'America/Argentina/ComodRivadavia'; const TZ_AMERICA_ARGENTINA_CORDOBA = 'America/Argentina/Cordoba'; const TZ_AMERICA_ARGENTINA_JUJUY = 'America/Argentina/Jujuy'; const TZ_AMERICA_ARGENTINA_LA_RIOJA = 'America/Argentina/La_Rioja'; const TZ_AMERICA_ARGENTINA_MENDOZA = 'America/Argentina/Mendoza'; const TZ_AMERICA_ARGENTINA_RIO_GALLEGOS = 'America/Argentina/Rio_Gallegos'; const TZ_AMERICA_ARGENTINA_SALTA = 'America/Argentina/Salta'; const TZ_AMERICA_ARGENTINA_SAN_JUAN = 'America/Argentina/San_Juan'; const TZ_AMERICA_ARGENTINA_SAN_LUIS = 'America/Argentina/San_Luis'; const TZ_AMERICA_ARGENTINA_TUCUMAN = 'America/Argentina/Tucuman'; const TZ_AMERICA_ARGENTINA_USHUAIA = 'America/Argentina/Ushuaia'; const TZ_AMERICA_ARUBA = 'America/Aruba'; const TZ_AMERICA_ASUNCION = 'America/Asuncion'; const TZ_AMERICA_ATIKOKAN = 'America/Atikokan'; const TZ_AMERICA_ATKA = 'America/Atka'; const TZ_AMERICA_BAHIA_BANDERAS = 'America/Bahia_Banderas'; const TZ_AMERICA_BARBADOS = 'America/Barbados'; const TZ_AMERICA_BELIZE = 'America/Belize'; const TZ_AMERICA_BLANC_SABLON = 'America/Blanc-Sablon'; const TZ_AMERICA_BOGOTA = 'America/Bogota'; const TZ_AMERICA_BOISE = 'America/Boise'; const TZ_AMERICA_BUENOS_AIRES = 'America/Buenos_Aires'; const TZ_AMERICA_CAMBRIDGE_BAY = 'America/Cambridge_Bay'; const TZ_AMERICA_CANCUN = 'America/Cancun'; const TZ_AMERICA_CARACAS = 'America/Caracas'; const TZ_AMERICA_CATAMARCA = 'America/Catamarca'; const TZ_AMERICA_CAYMAN = 'America/Cayman'; const TZ_AMERICA_CHICAGO = 'America/Chicago'; const TZ_AMERICA_CHIHUAHUA = 'America/Chihuahua'; const TZ_AMERICA_CORAL_HARBOUR = 'America/Coral_Harbour'; const TZ_AMERICA_CORDOBA = 'America/Cordoba'; const TZ_AMERICA_COSTA_RICA = 'America/Costa_Rica'; const TZ_AMERICA_CRESTON = 'America/Creston'; const TZ_AMERICA_CURACAO = 'America/Curacao'; const TZ_AMERICA_DANMARKSHAVN = 'America/Danmarkshavn'; const TZ_AMERICA_DAWSON = 'America/Dawson'; const TZ_AMERICA_DAWSON_CREEK = 'America/Dawson_Creek'; const TZ_AMERICA_DENVER = 'America/Denver'; const TZ_AMERICA_DETROIT = 'America/Detroit'; const TZ_AMERICA_DOMINICA = 'America/Dominica'; const TZ_AMERICA_EDMONTON = 'America/Edmonton'; const TZ_AMERICA_EL_SALVADOR = 'America/El_Salvador'; const TZ_AMERICA_ENSENADA = 'America/Ensenada'; const TZ_AMERICA_FORT_NELSON = 'America/Fort_Nelson'; const TZ_AMERICA_FORT_WAYNE = 'America/Fort_Wayne'; const TZ_AMERICA_GLACE_BAY = 'America/Glace_Bay'; const TZ_AMERICA_GOOSE_BAY = 'America/Goose_Bay'; const TZ_AMERICA_GRAND_TURK = 'America/Grand_Turk'; const TZ_AMERICA_GRENADA = 'America/Grenada'; const TZ_AMERICA_GUADELOUPE = 'America/Guadeloupe'; const TZ_AMERICA_GUATEMALA = 'America/Guatemala'; const TZ_AMERICA_GUAYAQUIL = 'America/Guayaquil'; const TZ_AMERICA_HALIFAX = 'America/Halifax'; const TZ_AMERICA_HAVANA = 'America/Havana'; const TZ_AMERICA_HERMOSILLO = 'America/Hermosillo'; const TZ_AMERICA_INDIANA_INDIANAPOLIS = 'America/Indiana/Indianapolis'; const TZ_AMERICA_INDIANA_KNOX = 'America/Indiana/Knox'; const TZ_AMERICA_INDIANA_MARENGO = 'America/Indiana/Marengo'; const TZ_AMERICA_INDIANA_PETERSBURG = 'America/Indiana/Petersburg'; const TZ_AMERICA_INDIANA_TELL_CITY = 'America/Indiana/Tell_City'; const TZ_AMERICA_INDIANA_VEVAY = 'America/Indiana/Vevay'; const TZ_AMERICA_INDIANA_VINCENNES = 'America/Indiana/Vincennes'; const TZ_AMERICA_INDIANA_WINAMAC = 'America/Indiana/Winamac'; const TZ_AMERICA_INDIANAPOLIS = 'America/Indianapolis'; const TZ_AMERICA_INUVIK = 'America/Inuvik'; const TZ_AMERICA_IQALUIT = 'America/Iqaluit'; const TZ_AMERICA_JAMAICA = 'America/Jamaica'; const TZ_AMERICA_JUJUY = 'America/Jujuy'; const TZ_AMERICA_JUNEAU = 'America/Juneau'; const TZ_AMERICA_KENTUCKY_LOUISVILLE = 'America/Kentucky/Louisville'; const TZ_AMERICA_KENTUCKY_MONTICELLO = 'America/Kentucky/Monticello'; const TZ_AMERICA_KNOX_IN = 'America/Knox_IN'; const TZ_AMERICA_KRALENDIJK = 'America/Kralendijk'; const TZ_AMERICA_LA_PAZ = 'America/La_Paz'; const TZ_AMERICA_LOS_ANGELES = 'America/Los_Angeles'; const TZ_AMERICA_LOUISVILLE = 'America/Louisville'; const TZ_AMERICA_LOWER_PRINCES = 'America/Lower_Princes'; const TZ_AMERICA_MANAGUA = 'America/Managua'; const TZ_AMERICA_MARIGOT = 'America/Marigot'; const TZ_AMERICA_MARTINIQUE = 'America/Martinique'; const TZ_AMERICA_MATAMOROS = 'America/Matamoros'; const TZ_AMERICA_MAZATLAN = 'America/Mazatlan'; const TZ_AMERICA_MENDOZA = 'America/Mendoza'; const TZ_AMERICA_MENOMINEE = 'America/Menominee'; const TZ_AMERICA_MERIDA = 'America/Merida'; const TZ_AMERICA_METLAKATLA = 'America/Metlakatla'; const TZ_AMERICA_MEXICO_CITY = 'America/Mexico_City'; const TZ_AMERICA_MIQUELON = 'America/Miquelon'; const TZ_AMERICA_MONCTON = 'America/Moncton'; const TZ_AMERICA_MONTERREY = 'America/Monterrey'; const TZ_AMERICA_MONTEVIDEO = 'America/Montevideo'; const TZ_AMERICA_MONTREAL = 'America/Montreal'; const TZ_AMERICA_MONTSERRAT = 'America/Montserrat'; const TZ_AMERICA_NASSAU = 'America/Nassau'; const TZ_AMERICA_NEW_YORK = 'America/New_York'; const TZ_AMERICA_NIPIGON = 'America/Nipigon'; const TZ_AMERICA_NOME = 'America/Nome'; const TZ_AMERICA_NORTH_DAKOTA_BEULAH = 'America/North_Dakota/Beulah'; const TZ_AMERICA_NORTH_DAKOTA_CENTER = 'America/North_Dakota/Center'; const TZ_AMERICA_NORTH_DAKOTA_NEW_SALEM = 'America/North_Dakota/New_Salem'; const TZ_AMERICA_OJINAGA = 'America/Ojinaga'; const TZ_AMERICA_PANAMA = 'America/Panama'; const TZ_AMERICA_PANGNIRTUNG = 'America/Pangnirtung'; const TZ_AMERICA_PARAMARIBO = 'America/Paramaribo'; const TZ_AMERICA_PHOENIX = 'America/Phoenix'; const TZ_AMERICA_PORT_AU_PRINCE = 'America/Port-au-Prince'; const TZ_AMERICA_PORT_OF_SPAIN = 'America/Port_of_Spain'; const TZ_AMERICA_PUERTO_RICO = 'America/Puerto_Rico'; const TZ_AMERICA_PUNTA_ARENAS = 'America/Punta_Arenas'; const TZ_AMERICA_RAINY_RIVER = 'America/Rainy_River'; const TZ_AMERICA_RANKIN_INLET = 'America/Rankin_Inlet'; const TZ_AMERICA_REGINA = 'America/Regina'; const TZ_AMERICA_RESOLUTE = 'America/Resolute'; const TZ_AMERICA_ROSARIO = 'America/Rosario'; const TZ_AMERICA_SANTA_ISABEL = 'America/Santa_Isabel'; const TZ_AMERICA_SANTIAGO = 'America/Santiago'; const TZ_AMERICA_SANTO_DOMINGO = 'America/Santo_Domingo'; const TZ_AMERICA_SHIPROCK = 'America/Shiprock'; const TZ_AMERICA_SITKA = 'America/Sitka'; const TZ_AMERICA_ST_BARTHELEMY = 'America/St_Barthelemy'; const TZ_AMERICA_ST_JOHNS = 'America/St_Johns'; const TZ_AMERICA_ST_KITTS = 'America/St_Kitts'; const TZ_AMERICA_ST_LUCIA = 'America/St_Lucia'; const TZ_AMERICA_ST_THOMAS = 'America/St_Thomas'; const TZ_AMERICA_ST_VINCENT = 'America/St_Vincent'; const TZ_AMERICA_SWIFT_CURRENT = 'America/Swift_Current'; const TZ_AMERICA_TEGUCIGALPA = 'America/Tegucigalpa'; const TZ_AMERICA_THULE = 'America/Thule'; const TZ_AMERICA_THUNDER_BAY = 'America/Thunder_Bay'; const TZ_AMERICA_TIJUANA = 'America/Tijuana'; const TZ_AMERICA_TORONTO = 'America/Toronto'; const TZ_AMERICA_TORTOLA = 'America/Tortola'; const TZ_AMERICA_VANCOUVER = 'America/Vancouver'; const TZ_AMERICA_VIRGIN = 'America/Virgin'; const TZ_AMERICA_WHITEHORSE = 'America/Whitehorse'; const TZ_AMERICA_WINNIPEG = 'America/Winnipeg'; const TZ_AMERICA_YAKUTAT = 'America/Yakutat'; const TZ_AMERICA_YELLOWKNIFE = 'America/Yellowknife'; const TZ_ANTARCTICA_MACQUARIE = 'Antarctica/Macquarie'; const TZ_ANTARCTICA_MCMURDO = 'Antarctica/McMurdo'; const TZ_ANTARCTICA_SOUTH_POLE = 'Antarctica/South_Pole'; const TZ_ARCTIC_LONGYEARBYEN = 'Arctic/Longyearbyen'; const TZ_ASIA_AMMAN = 'Asia/Amman'; const TZ_ASIA_BAGHDAD = 'Asia/Baghdad'; const TZ_ASIA_BANGKOK = 'Asia/Bangkok'; const TZ_ASIA_BEIRUT = 'Asia/Beirut'; const TZ_ASIA_CALCUTTA = 'Asia/Calcutta'; const TZ_ASIA_CHONGQING = 'Asia/Chongqing'; const TZ_ASIA_CHUNGKING = 'Asia/Chungking'; const TZ_ASIA_COLOMBO = 'Asia/Colombo'; const TZ_ASIA_DACCA = 'Asia/Dacca'; const TZ_ASIA_DAMASCUS = 'Asia/Damascus'; const TZ_ASIA_DHAKA = 'Asia/Dhaka'; const TZ_ASIA_FAMAGUSTA = 'Asia/Famagusta'; const TZ_ASIA_GAZA = 'Asia/Gaza'; const TZ_ASIA_HARBIN = 'Asia/Harbin'; const TZ_ASIA_HEBRON = 'Asia/Hebron'; const TZ_ASIA_HO_CHI_MINH = 'Asia/Ho_Chi_Minh'; const TZ_ASIA_HONG_KONG = 'Asia/Hong_Kong'; const TZ_ASIA_IRKUTSK = 'Asia/Irkutsk'; const TZ_ASIA_ISTANBUL = 'Asia/Istanbul'; const TZ_ASIA_JAKARTA = 'Asia/Jakarta'; const TZ_ASIA_JAYAPURA = 'Asia/Jayapura'; /* const TZ_ASIA_JERUSALEM = 'Asia/Jerusalem'; */ const TZ_ASIA_KARACHI = 'Asia/Karachi'; const TZ_ASIA_KOLKATA = 'Asia/Kolkata'; const TZ_ASIA_KUALA_LUMPUR = 'Asia/Kuala_Lumpur'; const TZ_ASIA_MACAO = 'Asia/Macao'; const TZ_ASIA_MACAU = 'Asia/Macau'; const TZ_ASIA_MAKASSAR = 'Asia/Makassar'; const TZ_ASIA_MANILA = 'Asia/Manila'; const TZ_ASIA_NICOSIA = 'Asia/Nicosia'; const TZ_ASIA_PHNOM_PENH = 'Asia/Phnom_Penh'; const TZ_ASIA_PONTIANAK = 'Asia/Pontianak'; const TZ_ASIA_PYONGYANG = 'Asia/Pyongyang'; const TZ_ASIA_RANGOON = 'Asia/Rangoon'; const TZ_ASIA_SAIGON = 'Asia/Saigon'; const TZ_ASIA_SEOUL = 'Asia/Seoul'; const TZ_ASIA_SHANGHAI = 'Asia/Shanghai'; const TZ_ASIA_SINGAPORE = 'Asia/Singapore'; const TZ_ASIA_TAIPEI = 'Asia/Taipei'; const TZ_ASIA_TBILISI = 'Asia/Tbilisi'; const TZ_ASIA_TEHRAN = 'Asia/Tehran'; /* const TZ_ASIA_TEL_AVIV = 'Asia/Tel_Aviv'; */ const TZ_ASIA_TOKYO = 'Asia/Tokyo'; const TZ_ASIA_UJUNG_PANDANG = 'Asia/Ujung_Pandang'; const TZ_ASIA_VIENTIANE = 'Asia/Vientiane'; const TZ_ASIA_YANGON = 'Asia/Yangon'; const TZ_ASIA_YEKATERINBURG = 'Asia/Yekaterinburg'; const TZ_ATLANTIC_AZORES = 'Atlantic/Azores'; const TZ_ATLANTIC_BERMUDA = 'Atlantic/Bermuda'; const TZ_ATLANTIC_CANARY = 'Atlantic/Canary'; const TZ_ATLANTIC_FAEROE = 'Atlantic/Faeroe'; const TZ_ATLANTIC_FAROE = 'Atlantic/Faroe'; const TZ_ATLANTIC_JAN_MAYEN = 'Atlantic/Jan_Mayen'; const TZ_ATLANTIC_MADEIRA = 'Atlantic/Madeira'; const TZ_ATLANTIC_REYKJAVIK = 'Atlantic/Reykjavik'; const TZ_ATLANTIC_ST_HELENA = 'Atlantic/St_Helena'; const TZ_ATLANTIC_STANLEY = 'Atlantic/Stanley'; const TZ_AUSTRALIA_ACT = 'Australia/ACT'; const TZ_AUSTRALIA_ADELAIDE = 'Australia/Adelaide'; const TZ_AUSTRALIA_BRISBANE = 'Australia/Brisbane'; const TZ_AUSTRALIA_BROKEN_HILL = 'Australia/Broken_Hill'; const TZ_AUSTRALIA_CANBERRA = 'Australia/Canberra'; const TZ_AUSTRALIA_CURRIE = 'Australia/Currie'; const TZ_AUSTRALIA_DARWIN = 'Australia/Darwin'; const TZ_AUSTRALIA_HOBART = 'Australia/Hobart'; const TZ_AUSTRALIA_LHI = 'Australia/LHI'; const TZ_AUSTRALIA_LINDEMAN = 'Australia/Lindeman'; const TZ_AUSTRALIA_LORD_HOWE = 'Australia/Lord_Howe'; const TZ_AUSTRALIA_MELBOURNE = 'Australia/Melbourne'; const TZ_AUSTRALIA_NSW = 'Australia/NSW'; const TZ_AUSTRALIA_NORTH = 'Australia/North'; const TZ_AUSTRALIA_PERTH = 'Australia/Perth'; const TZ_AUSTRALIA_QUEENSLAND = 'Australia/Queensland'; const TZ_AUSTRALIA_SOUTH = 'Australia/South'; const TZ_AUSTRALIA_SYDNEY = 'Australia/Sydney'; const TZ_AUSTRALIA_TASMANIA = 'Australia/Tasmania'; const TZ_AUSTRALIA_VICTORIA = 'Australia/Victoria'; const TZ_AUSTRALIA_WEST = 'Australia/West'; const TZ_AUSTRALIA_YANCOWINNA = 'Australia/Yancowinna'; const TZ_CANADA_ATLANTIC = 'Canada/Atlantic'; const TZ_CANADA_CENTRAL = 'Canada/Central'; const TZ_CANADA_EASTERN = 'Canada/Eastern'; const TZ_CANADA_MOUNTAIN = 'Canada/Mountain'; const TZ_CANADA_NEWFOUNDLAND = 'Canada/Newfoundland'; const TZ_CANADA_PACIFIC = 'Canada/Pacific'; const TZ_CANADA_SASKATCHEWAN = 'Canada/Saskatchewan'; const TZ_CANADA_YUKON = 'Canada/Yukon'; const TZ_CHILE_CONTINENTAL = 'Chile/Continental'; const TZ_CHILE_EASTERISLAND = 'Chile/EasterIsland'; const TZ_ETC_GMT = 'Etc/GMT'; const TZ_ETC_GREENWICH = 'Etc/Greenwich'; const TZ_ETC_UCT = 'Etc/UCT'; const TZ_ETC_UTC = 'Etc/UTC'; const TZ_ETC_UNIVERSAL = 'Etc/Universal'; const TZ_ETC_ZULU = 'Etc/Zulu'; const TZ_EUROPE_AMSTERDAM = 'Europe/Amsterdam'; const TZ_EUROPE_ANDORRA = 'Europe/Andorra'; const TZ_EUROPE_ATHENS = 'Europe/Athens'; const TZ_EUROPE_BELFAST = 'Europe/Belfast'; const TZ_EUROPE_BELGRADE = 'Europe/Belgrade'; const TZ_EUROPE_BERLIN = 'Europe/Berlin'; const TZ_EUROPE_BRATISLAVA = 'Europe/Bratislava'; const TZ_EUROPE_BRUSSELS = 'Europe/Brussels'; const TZ_EUROPE_BUCHAREST = 'Europe/Bucharest'; const TZ_EUROPE_BUDAPEST = 'Europe/Budapest'; const TZ_EUROPE_BUSINGEN = 'Europe/Busingen'; const TZ_EUROPE_CHISINAU = 'Europe/Chisinau'; const TZ_EUROPE_COPENHAGEN = 'Europe/Copenhagen'; const TZ_EUROPE_DUBLIN = 'Europe/Dublin'; const TZ_EUROPE_GIBRALTAR = 'Europe/Gibraltar'; const TZ_EUROPE_GUERNSEY = 'Europe/Guernsey'; const TZ_EUROPE_HELSINKI = 'Europe/Helsinki'; const TZ_EUROPE_ISLE_OF_MAN = 'Europe/Isle_of_Man'; const TZ_EUROPE_ISTANBUL = 'Europe/Istanbul'; const TZ_EUROPE_JERSEY = 'Europe/Jersey'; const TZ_EUROPE_KALININGRAD = 'Europe/Kaliningrad'; const TZ_EUROPE_KIEV = 'Europe/Kiev'; const TZ_EUROPE_LISBON = 'Europe/Lisbon'; const TZ_EUROPE_LJUBLJANA = 'Europe/Ljubljana'; const TZ_EUROPE_LONDON = 'Europe/London'; const TZ_EUROPE_LUXEMBOURG = 'Europe/Luxembourg'; const TZ_EUROPE_MADRID = 'Europe/Madrid'; const TZ_EUROPE_MALTA = 'Europe/Malta'; const TZ_EUROPE_MARIEHAMN = 'Europe/Mariehamn'; const TZ_EUROPE_MINSK = 'Europe/Minsk'; const TZ_EUROPE_MONACO = 'Europe/Monaco'; const TZ_EUROPE_MOSCOW = 'Europe/Moscow'; const TZ_EUROPE_NICOSIA = 'Europe/Nicosia'; const TZ_EUROPE_OSLO = 'Europe/Oslo'; const TZ_EUROPE_PARIS = 'Europe/Paris'; const TZ_EUROPE_PODGORICA = 'Europe/Podgorica'; const TZ_EUROPE_PRAGUE = 'Europe/Prague'; const TZ_EUROPE_RIGA = 'Europe/Riga'; const TZ_EUROPE_ROME = 'Europe/Rome'; const TZ_EUROPE_SAN_MARINO = 'Europe/San_Marino'; const TZ_EUROPE_SARAJEVO = 'Europe/Sarajevo'; const TZ_EUROPE_SIMFEROPOL = 'Europe/Simferopol'; const TZ_EUROPE_SKOPJE = 'Europe/Skopje'; const TZ_EUROPE_SOFIA = 'Europe/Sofia'; const TZ_EUROPE_STOCKHOLM = 'Europe/Stockholm'; const TZ_EUROPE_TALLINN = 'Europe/Tallinn'; const TZ_EUROPE_TIRANE = 'Europe/Tirane'; const TZ_EUROPE_TIRASPOL = 'Europe/Tiraspol'; const TZ_EUROPE_UZHGOROD = 'Europe/Uzhgorod'; const TZ_EUROPE_VADUZ = 'Europe/Vaduz'; const TZ_EUROPE_VATICAN = 'Europe/Vatican'; const TZ_EUROPE_VIENNA = 'Europe/Vienna'; const TZ_EUROPE_VILNIUS = 'Europe/Vilnius'; const TZ_EUROPE_WARSAW = 'Europe/Warsaw'; const TZ_EUROPE_ZAGREB = 'Europe/Zagreb'; const TZ_EUROPE_ZAPOROZHYE = 'Europe/Zaporozhye'; const TZ_EUROPE_ZURICH = 'Europe/Zurich'; const TZ_GB = 'GB'; const TZ_INDIAN_ANTANANARIVO = 'Indian/Antananarivo'; const TZ_INDIAN_COMORO = 'Indian/Comoro'; const TZ_INDIAN_MALDIVES = 'Indian/Maldives'; const TZ_INDIAN_MAYOTTE = 'Indian/Mayotte'; const TZ_MET = 'MET'; const TZ_MEXICO_BAJANORTE = 'Mexico/BajaNorte'; const TZ_MEXICO_BAJASUR = 'Mexico/BajaSur'; const TZ_MEXICO_GENERAL = 'Mexico/General'; const TZ_NZ = 'NZ'; const TZ_PRC = 'PRC'; const TZ_PACIFIC_AUCKLAND = 'Pacific/Auckland'; const TZ_PACIFIC_EASTER = 'Pacific/Easter'; const TZ_PACIFIC_GUAM = 'Pacific/Guam'; const TZ_PACIFIC_HONOLULU = 'Pacific/Honolulu'; const TZ_PACIFIC_JOHNSTON = 'Pacific/Johnston'; const TZ_PACIFIC_MIDWAY = 'Pacific/Midway'; const TZ_PACIFIC_PAGO_PAGO = 'Pacific/Pago_Pago'; const TZ_PACIFIC_SAIPAN = 'Pacific/Saipan'; const TZ_PACIFIC_SAMOA = 'Pacific/Samoa'; const TZ_ROC = 'ROC'; const TZ_ROK = 'ROK'; const TZ_UTC = 'UTC';
    const TZ_ = [self::TZ_AFRICA_ABIDJAN, self::TZ_AFRICA_ACCRA, self::TZ_AFRICA_ADDIS_ABABA, self::TZ_AFRICA_ALGIERS, self::TZ_AFRICA_ASMARA, self::TZ_AFRICA_ASMERA, self::TZ_AFRICA_BAMAKO, self::TZ_AFRICA_BANGUI, self::TZ_AFRICA_BANJUL, self::TZ_AFRICA_BISSAU, self::TZ_AFRICA_BLANTYRE, self::TZ_AFRICA_BRAZZAVILLE, self::TZ_AFRICA_BUJUMBURA, self::TZ_AFRICA_CAIRO, self::TZ_AFRICA_CEUTA, self::TZ_AFRICA_CONAKRY, self::TZ_AFRICA_DAKAR, self::TZ_AFRICA_DAR_ES_SALAAM, self::TZ_AFRICA_DJIBOUTI, self::TZ_AFRICA_DOUALA, self::TZ_AFRICA_FREETOWN, self::TZ_AFRICA_GABORONE, self::TZ_AFRICA_HARARE, self::TZ_AFRICA_JOHANNESBURG, self::TZ_AFRICA_JUBA, self::TZ_AFRICA_KAMPALA, self::TZ_AFRICA_KHARTOUM, self::TZ_AFRICA_KIGALI, self::TZ_AFRICA_KINSHASA, self::TZ_AFRICA_LAGOS, self::TZ_AFRICA_LIBREVILLE, self::TZ_AFRICA_LOME, self::TZ_AFRICA_LUANDA, self::TZ_AFRICA_LUBUMBASHI, self::TZ_AFRICA_LUSAKA, self::TZ_AFRICA_MALABO, self::TZ_AFRICA_MAPUTO, self::TZ_AFRICA_MASERU, self::TZ_AFRICA_MBABANE, self::TZ_AFRICA_MOGADISHU, self::TZ_AFRICA_MONROVIA, self::TZ_AFRICA_NAIROBI, self::TZ_AFRICA_NDJAMENA, self::TZ_AFRICA_NIAMEY, self::TZ_AFRICA_NOUAKCHOTT, self::TZ_AFRICA_OUAGADOUGOU, self::TZ_AFRICA_PORTO_NOVO, self::TZ_AFRICA_SAO_TOME, self::TZ_AFRICA_TIMBUKTU, self::TZ_AFRICA_TRIPOLI, self::TZ_AFRICA_TUNIS, self::TZ_AFRICA_WINDHOEK, self::TZ_AMERICA_ADAK, self::TZ_AMERICA_ANCHORAGE, self::TZ_AMERICA_ANGUILLA, self::TZ_AMERICA_ANTIGUA, self::TZ_AMERICA_ARGENTINA_BUENOS_AIRES, self::TZ_AMERICA_ARGENTINA_CATAMARCA, self::TZ_AMERICA_ARGENTINA_COMODRIVADAVIA, self::TZ_AMERICA_ARGENTINA_CORDOBA, self::TZ_AMERICA_ARGENTINA_JUJUY, self::TZ_AMERICA_ARGENTINA_LA_RIOJA, self::TZ_AMERICA_ARGENTINA_MENDOZA, self::TZ_AMERICA_ARGENTINA_RIO_GALLEGOS, self::TZ_AMERICA_ARGENTINA_SALTA, self::TZ_AMERICA_ARGENTINA_SAN_JUAN, self::TZ_AMERICA_ARGENTINA_SAN_LUIS, self::TZ_AMERICA_ARGENTINA_TUCUMAN, self::TZ_AMERICA_ARGENTINA_USHUAIA, self::TZ_AMERICA_ARUBA, self::TZ_AMERICA_ASUNCION, self::TZ_AMERICA_ATIKOKAN, self::TZ_AMERICA_ATKA, self::TZ_AMERICA_BAHIA_BANDERAS, self::TZ_AMERICA_BARBADOS, self::TZ_AMERICA_BELIZE, self::TZ_AMERICA_BLANC_SABLON, self::TZ_AMERICA_BOGOTA, self::TZ_AMERICA_BOISE, self::TZ_AMERICA_BUENOS_AIRES, self::TZ_AMERICA_CAMBRIDGE_BAY, self::TZ_AMERICA_CANCUN, self::TZ_AMERICA_CARACAS, self::TZ_AMERICA_CATAMARCA, self::TZ_AMERICA_CAYMAN, self::TZ_AMERICA_CHICAGO, self::TZ_AMERICA_CHIHUAHUA, self::TZ_AMERICA_CORAL_HARBOUR, self::TZ_AMERICA_CORDOBA, self::TZ_AMERICA_COSTA_RICA, self::TZ_AMERICA_CRESTON, self::TZ_AMERICA_CURACAO, self::TZ_AMERICA_DANMARKSHAVN, self::TZ_AMERICA_DAWSON, self::TZ_AMERICA_DAWSON_CREEK, self::TZ_AMERICA_DENVER, self::TZ_AMERICA_DETROIT, self::TZ_AMERICA_DOMINICA, self::TZ_AMERICA_EDMONTON, self::TZ_AMERICA_EL_SALVADOR, self::TZ_AMERICA_ENSENADA, self::TZ_AMERICA_FORT_NELSON, self::TZ_AMERICA_FORT_WAYNE, self::TZ_AMERICA_GLACE_BAY, self::TZ_AMERICA_GOOSE_BAY, self::TZ_AMERICA_GRAND_TURK, self::TZ_AMERICA_GRENADA, self::TZ_AMERICA_GUADELOUPE, self::TZ_AMERICA_GUATEMALA, self::TZ_AMERICA_GUAYAQUIL, self::TZ_AMERICA_HALIFAX, self::TZ_AMERICA_HAVANA, self::TZ_AMERICA_HERMOSILLO, self::TZ_AMERICA_INDIANA_INDIANAPOLIS, self::TZ_AMERICA_INDIANA_KNOX, self::TZ_AMERICA_INDIANA_MARENGO, self::TZ_AMERICA_INDIANA_PETERSBURG, self::TZ_AMERICA_INDIANA_TELL_CITY, self::TZ_AMERICA_INDIANA_VEVAY, self::TZ_AMERICA_INDIANA_VINCENNES, self::TZ_AMERICA_INDIANA_WINAMAC, self::TZ_AMERICA_INDIANAPOLIS, self::TZ_AMERICA_INUVIK, self::TZ_AMERICA_IQALUIT, self::TZ_AMERICA_JAMAICA, self::TZ_AMERICA_JUJUY, self::TZ_AMERICA_JUNEAU, self::TZ_AMERICA_KENTUCKY_LOUISVILLE, self::TZ_AMERICA_KENTUCKY_MONTICELLO, self::TZ_AMERICA_KNOX_IN, self::TZ_AMERICA_KRALENDIJK, self::TZ_AMERICA_LA_PAZ, self::TZ_AMERICA_LOS_ANGELES, self::TZ_AMERICA_LOUISVILLE, self::TZ_AMERICA_LOWER_PRINCES, self::TZ_AMERICA_MANAGUA, self::TZ_AMERICA_MARIGOT, self::TZ_AMERICA_MARTINIQUE, self::TZ_AMERICA_MATAMOROS, self::TZ_AMERICA_MAZATLAN, self::TZ_AMERICA_MENDOZA, self::TZ_AMERICA_MENOMINEE, self::TZ_AMERICA_MERIDA, self::TZ_AMERICA_METLAKATLA, self::TZ_AMERICA_MEXICO_CITY, self::TZ_AMERICA_MIQUELON, self::TZ_AMERICA_MONCTON, self::TZ_AMERICA_MONTERREY, self::TZ_AMERICA_MONTEVIDEO, self::TZ_AMERICA_MONTREAL, self::TZ_AMERICA_MONTSERRAT, self::TZ_AMERICA_NASSAU, self::TZ_AMERICA_NEW_YORK, self::TZ_AMERICA_NIPIGON, self::TZ_AMERICA_NOME, self::TZ_AMERICA_NORTH_DAKOTA_BEULAH, self::TZ_AMERICA_NORTH_DAKOTA_CENTER, self::TZ_AMERICA_NORTH_DAKOTA_NEW_SALEM, self::TZ_AMERICA_OJINAGA, self::TZ_AMERICA_PANAMA, self::TZ_AMERICA_PANGNIRTUNG, self::TZ_AMERICA_PARAMARIBO, self::TZ_AMERICA_PHOENIX, self::TZ_AMERICA_PORT_AU_PRINCE, self::TZ_AMERICA_PORT_OF_SPAIN, self::TZ_AMERICA_PUERTO_RICO, self::TZ_AMERICA_PUNTA_ARENAS, self::TZ_AMERICA_RAINY_RIVER, self::TZ_AMERICA_RANKIN_INLET, self::TZ_AMERICA_REGINA, self::TZ_AMERICA_RESOLUTE, self::TZ_AMERICA_ROSARIO, self::TZ_AMERICA_SANTA_ISABEL, self::TZ_AMERICA_SANTIAGO, self::TZ_AMERICA_SANTO_DOMINGO, self::TZ_AMERICA_SHIPROCK, self::TZ_AMERICA_SITKA, self::TZ_AMERICA_ST_BARTHELEMY, self::TZ_AMERICA_ST_JOHNS, self::TZ_AMERICA_ST_KITTS, self::TZ_AMERICA_ST_LUCIA, self::TZ_AMERICA_ST_THOMAS, self::TZ_AMERICA_ST_VINCENT, self::TZ_AMERICA_SWIFT_CURRENT, self::TZ_AMERICA_TEGUCIGALPA, self::TZ_AMERICA_THULE, self::TZ_AMERICA_THUNDER_BAY, self::TZ_AMERICA_TIJUANA, self::TZ_AMERICA_TORONTO, self::TZ_AMERICA_TORTOLA, self::TZ_AMERICA_VANCOUVER, self::TZ_AMERICA_VIRGIN, self::TZ_AMERICA_WHITEHORSE, self::TZ_AMERICA_WINNIPEG, self::TZ_AMERICA_YAKUTAT, self::TZ_AMERICA_YELLOWKNIFE, self::TZ_ANTARCTICA_MACQUARIE, self::TZ_ANTARCTICA_MCMURDO, self::TZ_ANTARCTICA_SOUTH_POLE, self::TZ_ARCTIC_LONGYEARBYEN, self::TZ_ASIA_AMMAN, self::TZ_ASIA_BAGHDAD, self::TZ_ASIA_BANGKOK, self::TZ_ASIA_BEIRUT, self::TZ_ASIA_CALCUTTA, self::TZ_ASIA_CHONGQING, self::TZ_ASIA_CHUNGKING, self::TZ_ASIA_COLOMBO, self::TZ_ASIA_DACCA, self::TZ_ASIA_DAMASCUS, self::TZ_ASIA_DHAKA, self::TZ_ASIA_FAMAGUSTA, self::TZ_ASIA_GAZA, self::TZ_ASIA_HARBIN, self::TZ_ASIA_HEBRON, self::TZ_ASIA_HO_CHI_MINH, self::TZ_ASIA_HONG_KONG, self::TZ_ASIA_IRKUTSK, self::TZ_ASIA_ISTANBUL, self::TZ_ASIA_JAKARTA, self::TZ_ASIA_JAYAPURA, /* self::TZ_ASIA_JERUSALEM, */ self::TZ_ASIA_KARACHI, self::TZ_ASIA_KOLKATA, self::TZ_ASIA_KUALA_LUMPUR, self::TZ_ASIA_MACAO, self::TZ_ASIA_MACAU, self::TZ_ASIA_MAKASSAR, self::TZ_ASIA_MANILA, self::TZ_ASIA_NICOSIA, self::TZ_ASIA_PHNOM_PENH, self::TZ_ASIA_PONTIANAK, self::TZ_ASIA_PYONGYANG, self::TZ_ASIA_RANGOON, self::TZ_ASIA_SAIGON, self::TZ_ASIA_SEOUL, self::TZ_ASIA_SHANGHAI, self::TZ_ASIA_SINGAPORE, self::TZ_ASIA_TAIPEI, self::TZ_ASIA_TBILISI, self::TZ_ASIA_TEHRAN, /* self::TZ_ASIA_TEL_AVIV, */ self::TZ_ASIA_TOKYO, self::TZ_ASIA_UJUNG_PANDANG, self::TZ_ASIA_VIENTIANE, self::TZ_ASIA_YANGON, self::TZ_ASIA_YEKATERINBURG, self::TZ_ATLANTIC_AZORES, self::TZ_ATLANTIC_BERMUDA, self::TZ_ATLANTIC_CANARY, self::TZ_ATLANTIC_FAEROE, self::TZ_ATLANTIC_FAROE, self::TZ_ATLANTIC_JAN_MAYEN, self::TZ_ATLANTIC_MADEIRA, self::TZ_ATLANTIC_REYKJAVIK, self::TZ_ATLANTIC_ST_HELENA, self::TZ_ATLANTIC_STANLEY, self::TZ_AUSTRALIA_ACT, self::TZ_AUSTRALIA_ADELAIDE, self::TZ_AUSTRALIA_BRISBANE, self::TZ_AUSTRALIA_BROKEN_HILL, self::TZ_AUSTRALIA_CANBERRA, self::TZ_AUSTRALIA_CURRIE, self::TZ_AUSTRALIA_DARWIN, self::TZ_AUSTRALIA_HOBART, self::TZ_AUSTRALIA_LHI, self::TZ_AUSTRALIA_LINDEMAN, self::TZ_AUSTRALIA_LORD_HOWE, self::TZ_AUSTRALIA_MELBOURNE, self::TZ_AUSTRALIA_NSW, self::TZ_AUSTRALIA_NORTH, self::TZ_AUSTRALIA_PERTH, self::TZ_AUSTRALIA_QUEENSLAND, self::TZ_AUSTRALIA_SOUTH, self::TZ_AUSTRALIA_SYDNEY, self::TZ_AUSTRALIA_TASMANIA, self::TZ_AUSTRALIA_VICTORIA, self::TZ_AUSTRALIA_WEST, self::TZ_AUSTRALIA_YANCOWINNA, self::TZ_CANADA_ATLANTIC, self::TZ_CANADA_CENTRAL, self::TZ_CANADA_EASTERN, self::TZ_CANADA_MOUNTAIN, self::TZ_CANADA_NEWFOUNDLAND, self::TZ_CANADA_PACIFIC, self::TZ_CANADA_SASKATCHEWAN, self::TZ_CANADA_YUKON, self::TZ_CHILE_CONTINENTAL, self::TZ_CHILE_EASTERISLAND, self::TZ_ETC_GMT, self::TZ_ETC_GREENWICH, self::TZ_ETC_UCT, self::TZ_ETC_UTC, self::TZ_ETC_UNIVERSAL, self::TZ_ETC_ZULU, self::TZ_EUROPE_AMSTERDAM, self::TZ_EUROPE_ANDORRA, self::TZ_EUROPE_ATHENS, self::TZ_EUROPE_BELFAST, self::TZ_EUROPE_BELGRADE, self::TZ_EUROPE_BERLIN, self::TZ_EUROPE_BRATISLAVA, self::TZ_EUROPE_BRUSSELS, self::TZ_EUROPE_BUCHAREST, self::TZ_EUROPE_BUDAPEST, self::TZ_EUROPE_BUSINGEN, self::TZ_EUROPE_CHISINAU, self::TZ_EUROPE_COPENHAGEN, self::TZ_EUROPE_DUBLIN, self::TZ_EUROPE_GIBRALTAR, self::TZ_EUROPE_GUERNSEY, self::TZ_EUROPE_HELSINKI, self::TZ_EUROPE_ISLE_OF_MAN, self::TZ_EUROPE_ISTANBUL, self::TZ_EUROPE_JERSEY, self::TZ_EUROPE_KALININGRAD, self::TZ_EUROPE_KIEV, self::TZ_EUROPE_LISBON, self::TZ_EUROPE_LJUBLJANA, self::TZ_EUROPE_LONDON, self::TZ_EUROPE_LUXEMBOURG, self::TZ_EUROPE_MADRID, self::TZ_EUROPE_MALTA, self::TZ_EUROPE_MARIEHAMN, self::TZ_EUROPE_MINSK, self::TZ_EUROPE_MONACO, self::TZ_EUROPE_MOSCOW, self::TZ_EUROPE_NICOSIA, self::TZ_EUROPE_OSLO, self::TZ_EUROPE_PARIS, self::TZ_EUROPE_PODGORICA, self::TZ_EUROPE_PRAGUE, self::TZ_EUROPE_RIGA, self::TZ_EUROPE_ROME, self::TZ_EUROPE_SAN_MARINO, self::TZ_EUROPE_SARAJEVO, self::TZ_EUROPE_SIMFEROPOL, self::TZ_EUROPE_SKOPJE, self::TZ_EUROPE_SOFIA, self::TZ_EUROPE_STOCKHOLM, self::TZ_EUROPE_TALLINN, self::TZ_EUROPE_TIRANE, self::TZ_EUROPE_TIRASPOL, self::TZ_EUROPE_UZHGOROD, self::TZ_EUROPE_VADUZ, self::TZ_EUROPE_VATICAN, self::TZ_EUROPE_VIENNA, self::TZ_EUROPE_VILNIUS, self::TZ_EUROPE_WARSAW, self::TZ_EUROPE_ZAGREB, self::TZ_EUROPE_ZAPOROZHYE, self::TZ_EUROPE_ZURICH, self::TZ_GB, self::TZ_INDIAN_ANTANANARIVO, self::TZ_INDIAN_COMORO, self::TZ_INDIAN_MALDIVES, self::TZ_INDIAN_MAYOTTE, self::TZ_MET, self::TZ_MEXICO_BAJANORTE, self::TZ_MEXICO_BAJASUR, self::TZ_MEXICO_GENERAL, self::TZ_NZ, self::TZ_PRC, self::TZ_PACIFIC_AUCKLAND, self::TZ_PACIFIC_EASTER, self::TZ_PACIFIC_GUAM, self::TZ_PACIFIC_HONOLULU, self::TZ_PACIFIC_JOHNSTON, self::TZ_PACIFIC_MIDWAY, self::TZ_PACIFIC_PAGO_PAGO, self::TZ_PACIFIC_SAIPAN, self::TZ_PACIFIC_SAMOA, self::TZ_ROC, self::TZ_ROK, self::TZ_UTC];
}