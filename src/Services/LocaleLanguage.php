<?php

namespace Xua\Core\Services;

use Xua\Core\Eves\Service;

final class LocaleLanguage extends Service
{
    private static string $locale;
    private static string $language;

    private function __construct() {}

    protected static function _init(): void
    {
        self::$locale = ConstantService::get('config', 'services.ll.locale');
        self::$language = ConstantService::get('config', 'services.ll.language');
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
     * @return bool
     */
    public static function isRtl(): bool
    {
        return in_array(self::$language, [
            self::LANG_AR,
//            self::LANG_ARC,
//            self::LANG_CKB,
            self::LANG_DV,
            self::LANG_FA,
//            self::LANG_HA,
            self::LANG_HE,
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

    public static function localeToLang(?string $locale): ?string
    {
        return match ($locale) {
            /* self::LOC_DJ, self::LOC_ER, self::LOC_ET => self::LANG_AA, */
            /* self::LOC_SD, */ self::LOC_AE, self::LOC_BH, self::LOC_DZ, self::LOC_EG, self::LOC_IQ, self::LOC_JO, self::LOC_KW, self::LOC_LB, self::LOC_LY, self::LOC_MA, self::LOC_OM, self::LOC_QA, self::LOC_SA, self::LOC_SY, self::LOC_TN, self::LOC_YE => self::LANG_AR,
            self::LOC_AZ => self::LANG_AZ,
            self::LOC_BY => self::LANG_BE,
            self::LOC_BG => self::LANG_BG,
            /* self::LOC_BD => self::LANG_BN, */
            self::LOC_BA => self::LANG_BS,
            self::LOC_CZ => self::LANG_CS,
            self::LOC_DK => self::LANG_DA,
            self::LOC_AT, self::LOC_CH, self::LOC_DE, self::LOC_LU => self::LANG_DE,
            self::LOC_MV => self::LANG_DV,
            /* self::LOC_BT => self::LANG_DZ, */
            self::LOC_GR => self::LANG_EL,
            /* self::LOC_AG, self::LOC_AI, self::LOC_AQ, self::LOC_AS, self::LOC_BB, self::LOC_BW, self::LOC_NG, self::LOC_ZM, */ self::LOC_AU, self::LOC_CA, self::LOC_GB, self::LOC_IE, self::LOC_KE, self::LOC_NZ, self::LOC_PH, self::LOC_SG, self::LOC_US, self::LOC_ZA, self::LOC_ZW => self::LANG_EN,
            /* self::LOC_AD, self::LOC_CU, */ self::LOC_AR, self::LOC_BO, self::LOC_CL, self::LOC_CO, self::LOC_CR, self::LOC_DO, self::LOC_EC, self::LOC_ES, self::LOC_GT, self::LOC_HN, self::LOC_MX, self::LOC_NI, self::LOC_PA, self::LOC_PE, self::LOC_PR, self::LOC_PY, self::LOC_SV, self::LOC_UY, self::LOC_VE => self::LANG_ES,
            self::LOC_EE => self::LANG_ET,
            self::LOC_IR => self::LANG_FA,
            self::LOC_FI => self::LANG_FI,
            self::LOC_FO => self::LANG_FO,
            /* self::LOC_SN, */ self::LOC_BE, self::LOC_FR => self::LANG_FR,
            self::LOC_IL => self::LANG_HE,
            self::LOC_IN => self::LANG_HI,
            self::LOC_HR => self::LANG_HR,
            /* self::LOC_HT => self::LANG_HT, */
            self::LOC_HU => self::LANG_HU,
            self::LOC_AM => self::LANG_HY,
            self::LOC_ID => self::LANG_ID,
            self::LOC_IS => self::LANG_IS,
            self::LOC_IT => self::LANG_IT,
            self::LOC_JP => self::LANG_JA,
            self::LOC_GE => self::LANG_KA,
            self::LOC_KZ => self::LANG_KK,
            /* self::LOC_GL => self::LANG_KL, */
            /* self::LOC_KH => self::LANG_KM, */
            self::LOC_KR => self::LANG_KO,
            self::LOC_KG => self::LANG_KY,
            /* self::LOC_UG => self::LANG_LG, */
            /* self::LOC_LA => self::LANG_LO, */
            self::LOC_LT => self::LANG_LT,
            self::LOC_LV => self::LANG_LV,
            /* self::LOC_MG => self::LANG_MG, */
            self::LOC_MK => self::LANG_MK,
            self::LOC_MN => self::LANG_MN,
            self::LOC_MY => self::LANG_MS,
            self::LOC_MT => self::LANG_MT,
            /* self::LOC_MM => self::LANG_MY, */
            /* self::LOC_NP => self::LANG_NE, */
            /* self::LOC_AW, */ self::LOC_NL => self::LANG_NL,
            /* self::LOC_NO => self::LANG_NO, */
            self::LOC_PL => self::LANG_PL,
            /* self::LOC_AF => self::LANG_PS, */
            /* self::LOC_AO, */ self::LOC_BR, self::LOC_PT => self::LANG_PT,
            self::LOC_RO => self::LANG_RO,
            self::LOC_RU, self::LOC_UA => self::LANG_RU,
            /* self::LOC_RW => self::LANG_RW, */
            /* self::LOC_AX => self::LANG_SE, */
            self::LOC_SK => self::LANG_SK,
            self::LOC_SI => self::LANG_SL,
            /* self::LOC_SO => self::LANG_SO, */
            self::LOC_AL => self::LANG_SQ,
            /* self::LOC_ME, self::LOC_RS => self::LANG_SR, */
            self::LOC_SE => self::LANG_SV,
            /* self::LOC_TZ => self::LANG_SW, */
            /* self::LOC_LK => self::LANG_TA, */
            /* self::LOC_TJ => self::LANG_TG, */
            self::LOC_TH => self::LANG_TH,
            /* self::LOC_TM => self::LANG_TK, */
            /* self::LOC_CY, */ self::LOC_TR => self::LANG_TR,
            self::LOC_PK => self::LANG_UR,
            self::LOC_UZ => self::LANG_UZ,
            self::LOC_VN => self::LANG_VI,
            self::LOC_CN, self::LOC_HK, self::LOC_TW => self::LANG_ZH,
            default => null,
        };
    }

    const LOC_ZA = 'ZA'; const LOC_AE = 'AE'; const LOC_BH = 'BH'; const LOC_DZ = 'DZ'; const LOC_EG = 'EG'; const LOC_IQ = 'IQ'; const LOC_JO = 'JO'; const LOC_KW = 'KW'; const LOC_LB = 'LB'; const LOC_LY = 'LY'; const LOC_MA = 'MA'; const LOC_OM = 'OM'; const LOC_QA = 'QA'; const LOC_SA = 'SA'; const LOC_SY = 'SY'; const LOC_TN = 'TN'; const LOC_YE = 'YE'; const LOC_AZ = 'AZ'; const LOC_BY = 'BY'; const LOC_BG = 'BG'; const LOC_BA = 'BA'; const LOC_ES = 'ES'; const LOC_CZ = 'CZ'; const LOC_GB = 'GB'; const LOC_DK = 'DK'; const LOC_AT = 'AT'; const LOC_CH = 'CH'; const LOC_DE = 'DE'; const LOC_LI = 'LI'; const LOC_LU = 'LU'; const LOC_MV = 'MV'; const LOC_GR = 'GR'; const LOC_AU = 'AU'; const LOC_BZ = 'BZ'; const LOC_CA = 'CA'; const LOC_CB = 'CB'; const LOC_IE = 'IE'; const LOC_JM = 'JM'; const LOC_NZ = 'NZ'; const LOC_PH = 'PH'; const LOC_TT = 'TT'; const LOC_US = 'US'; const LOC_ZW = 'ZW'; const LOC_AR = 'AR'; const LOC_BO = 'BO'; const LOC_CL = 'CL'; const LOC_CO = 'CO'; const LOC_CR = 'CR'; const LOC_DO = 'DO'; const LOC_EC = 'EC'; const LOC_GT = 'GT'; const LOC_HN = 'HN'; const LOC_MX = 'MX'; const LOC_NI = 'NI'; const LOC_PA = 'PA'; const LOC_PE = 'PE'; const LOC_PR = 'PR'; const LOC_PY = 'PY'; const LOC_SV = 'SV'; const LOC_UY = 'UY'; const LOC_VE = 'VE'; const LOC_EE = 'EE'; const LOC_IR = 'IR'; const LOC_FI = 'FI'; const LOC_FO = 'FO'; const LOC_BE = 'BE'; const LOC_FR = 'FR'; const LOC_MC = 'MC'; const LOC_IN = 'IN'; const LOC_IL = 'IL'; const LOC_HR = 'HR'; const LOC_HU = 'HU'; const LOC_AM = 'AM'; const LOC_ID = 'ID'; const LOC_IS = 'IS'; const LOC_IT = 'IT'; const LOC_JP = 'JP'; const LOC_GE = 'GE'; const LOC_KZ = 'KZ'; const LOC_KR = 'KR'; const LOC_KG = 'KG'; const LOC_LT = 'LT'; const LOC_LV = 'LV'; const LOC_MK = 'MK'; const LOC_MN = 'MN'; const LOC_BN = 'BN'; const LOC_MY = 'MY'; const LOC_MT = 'MT'; const LOC_NO = 'NO'; const LOC_NL = 'NL'; const LOC_PL = 'PL'; const LOC_BR = 'BR'; const LOC_PT = 'PT'; const LOC_RO = 'RO'; const LOC_RU = 'RU'; const LOC_SE = 'SE'; const LOC_SK = 'SK'; const LOC_SI = 'SI'; const LOC_AL = 'AL'; const LOC_SP = 'SP'; const LOC_KE = 'KE'; const LOC_TH = 'TH'; const LOC_TR = 'TR'; const LOC_UA = 'UA'; const LOC_PK = 'PK'; const LOC_UZ = 'UZ'; const LOC_VN = 'VN'; const LOC_CN = 'CN'; const LOC_HK = 'HK'; const LOC_MO = 'MO'; const LOC_SG = 'SG'; const LOC_TW = 'TW';
    const LOC_ = [self::LOC_ZA, self::LOC_AE, self::LOC_BH, self::LOC_DZ, self::LOC_EG, self::LOC_IQ, self::LOC_JO, self::LOC_KW, self::LOC_LB, self::LOC_LY, self::LOC_MA, self::LOC_OM, self::LOC_QA, self::LOC_SA, self::LOC_SY, self::LOC_TN, self::LOC_YE, self::LOC_AZ, self::LOC_BY, self::LOC_BG, self::LOC_BA, self::LOC_ES, self::LOC_CZ, self::LOC_GB, self::LOC_DK, self::LOC_AT, self::LOC_CH, self::LOC_DE, self::LOC_LI, self::LOC_LU, self::LOC_MV, self::LOC_GR, self::LOC_AU, self::LOC_BZ, self::LOC_CA, self::LOC_CB, self::LOC_IE, self::LOC_JM, self::LOC_NZ, self::LOC_PH, self::LOC_TT, self::LOC_US, self::LOC_ZW, self::LOC_AR, self::LOC_BO, self::LOC_CL, self::LOC_CO, self::LOC_CR, self::LOC_DO, self::LOC_EC, self::LOC_GT, self::LOC_HN, self::LOC_MX, self::LOC_NI, self::LOC_PA, self::LOC_PE, self::LOC_PR, self::LOC_PY, self::LOC_SV, self::LOC_UY, self::LOC_VE, self::LOC_EE, self::LOC_IR, self::LOC_FI, self::LOC_FO, self::LOC_BE, self::LOC_FR, self::LOC_MC, self::LOC_IN, self::LOC_IL, self::LOC_HR, self::LOC_HU, self::LOC_AM, self::LOC_ID, self::LOC_IS, self::LOC_IT, self::LOC_JP, self::LOC_GE, self::LOC_KZ, self::LOC_KR, self::LOC_KG, self::LOC_LT, self::LOC_LV, self::LOC_MK, self::LOC_MN, self::LOC_BN, self::LOC_MY, self::LOC_MT, self::LOC_NO, self::LOC_NL, self::LOC_PL, self::LOC_BR, self::LOC_PT, self::LOC_RO, self::LOC_RU, self::LOC_SE, self::LOC_SK, self::LOC_SI, self::LOC_AL, self::LOC_SP, self::LOC_KE, self::LOC_TH, self::LOC_TR, self::LOC_UA, self::LOC_PK, self::LOC_UZ, self::LOC_VN, self::LOC_CN, self::LOC_HK, self::LOC_MO, self::LOC_SG, self::LOC_TW];
    const LANG_AF = 'af'; const LANG_AR = 'ar'; const LANG_AZ = 'az'; const LANG_BE = 'be'; const LANG_BG = 'bg'; const LANG_BS = 'bs'; const LANG_CA = 'ca'; const LANG_CS = 'cs'; const LANG_CY = 'cy'; const LANG_DA = 'da'; const LANG_DE = 'de'; const LANG_DV = 'dv'; const LANG_EL = 'el'; const LANG_EN = 'en'; const LANG_EO = 'eo'; const LANG_ES = 'es'; const LANG_ET = 'et'; const LANG_EU = 'eu'; const LANG_FA = 'fa'; const LANG_FI = 'fi'; const LANG_FO = 'fo'; const LANG_FR = 'fr'; const LANG_GL = 'gl'; const LANG_GU = 'gu'; const LANG_HE = 'he'; const LANG_HI = 'hi'; const LANG_HR = 'hr'; const LANG_HU = 'hu'; const LANG_HY = 'hy'; const LANG_ID = 'id'; const LANG_IS = 'is'; const LANG_IT = 'it'; const LANG_JA = 'ja'; const LANG_KA = 'ka'; const LANG_KK = 'kk'; const LANG_KN = 'kn'; const LANG_KO = 'ko'; const LANG_KOK = 'kok'; const LANG_KY = 'ky'; const LANG_LT = 'lt'; const LANG_LV = 'lv'; const LANG_MI = 'mi'; const LANG_MK = 'mk'; const LANG_MN = 'mn'; const LANG_MR = 'mr'; const LANG_MS = 'ms'; const LANG_MT = 'mt'; const LANG_NB = 'nb'; const LANG_NL = 'nl'; const LANG_NN = 'nn'; const LANG_NS = 'ns'; const LANG_PA = 'pa'; const LANG_PL = 'pl'; const LANG_PS = 'ps'; const LANG_PT = 'pt'; const LANG_QU = 'qu'; const LANG_RO = 'ro'; const LANG_RU = 'ru'; const LANG_SA = 'sa'; const LANG_SE = 'se'; const LANG_SK = 'sk'; const LANG_SL = 'sl'; const LANG_SQ = 'sq'; const LANG_SR = 'sr'; const LANG_SV = 'sv'; const LANG_SW = 'sw'; const LANG_SYR = 'syr'; const LANG_TA = 'ta'; const LANG_TE = 'te'; const LANG_TH = 'th'; const LANG_TL = 'tl'; const LANG_TN = 'tn'; const LANG_TR = 'tr'; const LANG_TT = 'tt'; const LANG_TS = 'ts'; const LANG_UK = 'uk'; const LANG_UR = 'ur'; const LANG_UZ = 'uz'; const LANG_VI = 'vi'; const LANG_XH = 'xh'; const LANG_ZH = 'zh'; const LANG_ZU = 'zu';
    const LANG_ = [self::LANG_AF, self::LANG_AR, self::LANG_AZ, self::LANG_BE, self::LANG_BG, self::LANG_BS, self::LANG_CA, self::LANG_CS, self::LANG_CY, self::LANG_DA, self::LANG_DE, self::LANG_DV, self::LANG_EL, self::LANG_EN, self::LANG_EO, self::LANG_ES, self::LANG_ET, self::LANG_EU, self::LANG_FA, self::LANG_FI, self::LANG_FO, self::LANG_FR, self::LANG_GL, self::LANG_GU, self::LANG_HE, self::LANG_HI, self::LANG_HR, self::LANG_HU, self::LANG_HY, self::LANG_ID, self::LANG_IS, self::LANG_IT, self::LANG_JA, self::LANG_KA, self::LANG_KK, self::LANG_KN, self::LANG_KO, self::LANG_KOK, self::LANG_KY, self::LANG_LT, self::LANG_LV, self::LANG_MI, self::LANG_MK, self::LANG_MN, self::LANG_MR, self::LANG_MS, self::LANG_MT, self::LANG_NB, self::LANG_NL, self::LANG_NN, self::LANG_NS, self::LANG_PA, self::LANG_PL, self::LANG_PS, self::LANG_PT, self::LANG_QU, self::LANG_RO, self::LANG_RU, self::LANG_SA, self::LANG_SE, self::LANG_SK, self::LANG_SL, self::LANG_SQ, self::LANG_SR, self::LANG_SV, self::LANG_SW, self::LANG_SYR, self::LANG_TA, self::LANG_TE, self::LANG_TH, self::LANG_TL, self::LANG_TN, self::LANG_TR, self::LANG_TT, self::LANG_TS, self::LANG_UK, self::LANG_UR, self::LANG_UZ, self::LANG_VI, self::LANG_XH, self::LANG_ZH, self::LANG_ZU];
    const NAT_AFGHAN = "Afghan"; const NAT_ALBANIAN = "Albanian"; const NAT_ALGERIAN = "Algerian"; const NAT_AMERICAN = "American"; const NAT_ANDORRAN = "Andorran"; const NAT_ANGOLAN = "Angolan"; const NAT_ANGUILLAN = "Anguillan"; const NAT_ARGENTINE = "Argentine"; const NAT_ARMENIAN = "Armenian"; const NAT_AUSTRALIAN = "Australian"; const NAT_AUSTRIAN = "Austrian"; const NAT_AZERBAIJANI = "Azerbaijani"; const NAT_BAHAMIAN = "Bahamian"; const NAT_BAHRAINI = "Bahraini"; const NAT_BANGLADESHI = "Bangladeshi"; const NAT_BARBADIAN = "Barbadian"; const NAT_BELARUSIAN = "Belarusian"; const NAT_BELGIAN = "Belgian"; const NAT_BELIZEAN = "Belizean"; const NAT_BENINESE = "Beninese"; const NAT_BERMUDIAN = "Bermudian"; const NAT_BHUTANESE = "Bhutanese"; const NAT_BOLIVIAN = "Bolivian"; const NAT_BOTSWANAN = "Botswanan"; const NAT_BRAZILIAN = "Brazilian"; const NAT_BRITISH = "British"; const NAT_BRITISH_VIRGIN_ISLANDER = "British Virgin Islander"; const NAT_BRUNEIAN = "Bruneian"; const NAT_BULGARIAN = "Bulgarian"; const NAT_BURKINAN = "Burkinan"; const NAT_BURMESE = "Burmese"; const NAT_BURUNDIAN = "Burundian"; const NAT_CAMBODIAN = "Cambodian"; const NAT_CAMEROONIAN = "Cameroonian"; const NAT_CANADIAN = "Canadian"; const NAT_CAPE_VERDEAN = "Cape Verdean"; const NAT_CAYMAN_ISLANDER = "Cayman Islander"; const NAT_CENTRAL_AFRICAN = "Central African"; const NAT_CHADIAN = "Chadian"; const NAT_CHILEAN = "Chilean"; const NAT_CHINESE = "Chinese"; const NAT_CITIZEN_OF_ANTIGUA_AND_BARBUDA = "Citizen of Antigua and Barbuda"; const NAT_CITIZEN_OF_BOSNIA_AND_HERZEGOVINA = "Citizen of Bosnia and Herzegovina"; const NAT_CITIZEN_OF_GUINEABISSAU = "Citizen of Guinea-Bissau"; const NAT_CITIZEN_OF_KIRIBATI = "Citizen of Kiribati"; const NAT_CITIZEN_OF_SEYCHELLES = "Citizen of Seychelles"; const NAT_CITIZEN_OF_THE_DOMINICAN_REPUBLIC = "Citizen of the Dominican Republic"; const NAT_CITIZEN_OF_VANUATU = "Citizen of Vanuatu"; const NAT_COLOMBIAN = "Colombian"; const NAT_COMORAN = "Comoran"; const NAT_CONGOLESE_CONGO = "Congolese (Congo)"; const NAT_CONGOLESE_DRC = "Congolese (DRC)"; const NAT_COOK_ISLANDER = "Cook Islander"; const NAT_COSTA_RICAN = "Costa Rican"; const NAT_CROATIAN = "Croatian"; const NAT_CUBAN = "Cuban"; const NAT_CYMRAES = "Cymraes"; const NAT_CYMRO = "Cymro"; const NAT_CYPRIOT = "Cypriot"; const NAT_CZECH = "Czech"; const NAT_DANISH = "Danish"; const NAT_DJIBOUTIAN = "Djiboutian"; const NAT_DOMINICAN = "Dominican"; const NAT_DUTCH = "Dutch"; const NAT_EAST_TIMORESE = "East Timorese"; const NAT_ECUADOREAN = "Ecuadorean"; const NAT_EGYPTIAN = "Egyptian"; const NAT_EMIRATI = "Emirati"; const NAT_ENGLISH = "English"; const NAT_EQUATORIAL_GUINEAN = "Equatorial Guinean"; const NAT_ERITREAN = "Eritrean"; const NAT_ESTONIAN = "Estonian"; const NAT_ETHIOPIAN = "Ethiopian"; const NAT_FAROESE = "Faroese"; const NAT_FIJIAN = "Fijian"; const NAT_FILIPINO = "Filipino"; const NAT_FINNISH = "Finnish"; const NAT_FRENCH = "French"; const NAT_GABONESE = "Gabonese"; const NAT_GAMBIAN = "Gambian"; const NAT_GEORGIAN = "Georgian"; const NAT_GERMAN = "German"; const NAT_GHANAIAN = "Ghanaian"; const NAT_GIBRALTARIAN = "Gibraltarian"; const NAT_GREEK = "Greek"; const NAT_GREENLANDIC = "Greenlandic"; const NAT_GRENADIAN = "Grenadian"; const NAT_GUAMANIAN = "Guamanian"; const NAT_GUATEMALAN = "Guatemalan"; const NAT_GUINEAN = "Guinean"; const NAT_GUYANESE = "Guyanese"; const NAT_HAITIAN = "Haitian"; const NAT_HONDURAN = "Honduran"; const NAT_HONG_KONGER = "Hong Konger"; const NAT_HUNGARIAN = "Hungarian"; const NAT_ICELANDIC = "Icelandic"; const NAT_INDIAN = "Indian"; const NAT_INDONESIAN = "Indonesian"; const NAT_IRANIAN = "Iranian"; const NAT_IRAQI = "Iraqi"; const NAT_IRISH = "Irish"; const NAT_ISRAELI = "Israeli"; const NAT_ITALIAN = "Italian"; const NAT_IVORIAN = "Ivorian"; const NAT_JAMAICAN = "Jamaican"; const NAT_JAPANESE = "Japanese"; const NAT_JORDANIAN = "Jordanian"; const NAT_KAZAKH = "Kazakh"; const NAT_KENYAN = "Kenyan"; const NAT_KITTITIAN = "Kittitian"; const NAT_KOSOVAN = "Kosovan"; const NAT_KUWAITI = "Kuwaiti"; const NAT_KYRGYZ = "Kyrgyz"; const NAT_LAO = "Lao"; const NAT_LATVIAN = "Latvian"; const NAT_LEBANESE = "Lebanese"; const NAT_LIBERIAN = "Liberian"; const NAT_LIBYAN = "Libyan"; const NAT_LIECHTENSTEIN_CITIZEN = "Liechtenstein citizen"; const NAT_LITHUANIAN = "Lithuanian"; const NAT_LUXEMBOURGER = "Luxembourger"; const NAT_MACANESE = "Macanese"; const NAT_MACEDONIAN = "Macedonian"; const NAT_MALAGASY = "Malagasy"; const NAT_MALAWIAN = "Malawian"; const NAT_MALAYSIAN = "Malaysian"; const NAT_MALDIVIAN = "Maldivian"; const NAT_MALIAN = "Malian"; const NAT_MALTESE = "Maltese"; const NAT_MARSHALLESE = "Marshallese"; const NAT_MARTINIQUAIS = "Martiniquais"; const NAT_MAURITANIAN = "Mauritanian"; const NAT_MAURITIAN = "Mauritian"; const NAT_MEXICAN = "Mexican"; const NAT_MICRONESIAN = "Micronesian"; const NAT_MOLDOVAN = "Moldovan"; const NAT_MONEGASQUE = "Monegasque"; const NAT_MONGOLIAN = "Mongolian"; const NAT_MONTENEGRIN = "Montenegrin"; const NAT_MONTSERRATIAN = "Montserratian"; const NAT_MOROCCAN = "Moroccan"; const NAT_MOSOTHO = "Mosotho"; const NAT_MOZAMBICAN = "Mozambican"; const NAT_NAMIBIAN = "Namibian"; const NAT_NAURUAN = "Nauruan"; const NAT_NEPALESE = "Nepalese"; const NAT_NEW_ZEALANDER = "New Zealander"; const NAT_NICARAGUAN = "Nicaraguan"; const NAT_NIGERIAN = "Nigerian"; const NAT_NIGERIEN = "Nigerien"; const NAT_NIUEAN = "Niuean"; const NAT_NORTH_KOREAN = "North Korean"; const NAT_NORTHERN_IRISH = "Northern Irish"; const NAT_NORWEGIAN = "Norwegian"; const NAT_OMANI = "Omani"; const NAT_PAKISTANI = "Pakistani"; const NAT_PALAUAN = "Palauan"; const NAT_PALESTINIAN = "Palestinian"; const NAT_PANAMANIAN = "Panamanian"; const NAT_PAPUA_NEW_GUINEAN = "Papua New Guinean"; const NAT_PARAGUAYAN = "Paraguayan"; const NAT_PERUVIAN = "Peruvian"; const NAT_PITCAIRN_ISLANDER = "Pitcairn Islander"; const NAT_POLISH = "Polish"; const NAT_PORTUGUESE = "Portuguese"; const NAT_PRYDEINIG = "Prydeinig"; const NAT_PUERTO_RICAN = "Puerto Rican"; const NAT_QATARI = "Qatari"; const NAT_ROMANIAN = "Romanian"; const NAT_RUSSIAN = "Russian"; const NAT_RWANDAN = "Rwandan"; const NAT_SALVADOREAN = "Salvadorean"; const NAT_SAMMARINESE = "Sammarinese"; const NAT_SAMOAN = "Samoan"; const NAT_SAO_TOMEAN = "Sao Tomean"; const NAT_SAUDI_ARABIAN = "Saudi Arabian"; const NAT_SCOTTISH = "Scottish"; const NAT_SENEGALESE = "Senegalese"; const NAT_SERBIAN = "Serbian"; const NAT_SIERRA_LEONEAN = "Sierra Leonean"; const NAT_SINGAPOREAN = "Singaporean"; const NAT_SLOVAK = "Slovak"; const NAT_SLOVENIAN = "Slovenian"; const NAT_SOLOMON_ISLANDER = "Solomon Islander"; const NAT_SOMALI = "Somali"; const NAT_SOUTH_AFRICAN = "South African"; const NAT_SOUTH_KOREAN = "South Korean"; const NAT_SOUTH_SUDANESE = "South Sudanese"; const NAT_SPANISH = "Spanish"; const NAT_SRI_LANKAN = "Sri Lankan"; const NAT_ST_HELENIAN = "St Helenian"; const NAT_ST_LUCIAN = "St Lucian"; const NAT_STATELESS = "Stateless"; const NAT_SUDANESE = "Sudanese"; const NAT_SURINAMESE = "Surinamese"; const NAT_SWAZI = "Swazi"; const NAT_SWEDISH = "Swedish"; const NAT_SWISS = "Swiss"; const NAT_SYRIAN = "Syrian"; const NAT_TAIWANESE = "Taiwanese"; const NAT_TAJIK = "Tajik"; const NAT_TANZANIAN = "Tanzanian"; const NAT_THAI = "Thai"; const NAT_TOGOLESE = "Togolese"; const NAT_TONGAN = "Tongan"; const NAT_TRINIDADIAN = "Trinidadian"; const NAT_TRISTANIAN = "Tristanian"; const NAT_TUNISIAN = "Tunisian"; const NAT_TURKISH = "Turkish"; const NAT_TURKMEN = "Turkmen"; const NAT_TURKS_AND_CAICOS_ISLANDER = "Turks and Caicos Islander"; const NAT_TUVALUAN = "Tuvaluan"; const NAT_UGANDAN = "Ugandan"; const NAT_UKRAINIAN = "Ukrainian"; const NAT_URUGUAYAN = "Uruguayan"; const NAT_UZBEK = "Uzbek"; const NAT_VATICAN_CITIZEN = "Vatican citizen"; const NAT_VENEZUELAN = "Venezuelan"; const NAT_VIETNAMESE = "Vietnamese"; const NAT_VINCENTIAN = "Vincentian"; const NAT_WALLISIAN = "Wallisian"; const NAT_WELSH = "Welsh"; const NAT_YEMENI = "Yemeni"; const NAT_ZAMBIAN = "Zambian"; const NAT_ZIMBABWEAN = "Zimbabwean";
    const NAT_ = [self::NAT_AFGHAN, self::NAT_ALBANIAN, self::NAT_ALGERIAN, self::NAT_AMERICAN, self::NAT_ANDORRAN, self::NAT_ANGOLAN, self::NAT_ANGUILLAN, self::NAT_ARGENTINE, self::NAT_ARMENIAN, self::NAT_AUSTRALIAN, self::NAT_AUSTRIAN, self::NAT_AZERBAIJANI, self::NAT_BAHAMIAN, self::NAT_BAHRAINI, self::NAT_BANGLADESHI, self::NAT_BARBADIAN, self::NAT_BELARUSIAN, self::NAT_BELGIAN, self::NAT_BELIZEAN, self::NAT_BENINESE, self::NAT_BERMUDIAN, self::NAT_BHUTANESE, self::NAT_BOLIVIAN, self::NAT_BOTSWANAN, self::NAT_BRAZILIAN, self::NAT_BRITISH, self::NAT_BRITISH_VIRGIN_ISLANDER, self::NAT_BRUNEIAN, self::NAT_BULGARIAN, self::NAT_BURKINAN, self::NAT_BURMESE, self::NAT_BURUNDIAN, self::NAT_CAMBODIAN, self::NAT_CAMEROONIAN, self::NAT_CANADIAN, self::NAT_CAPE_VERDEAN, self::NAT_CAYMAN_ISLANDER, self::NAT_CENTRAL_AFRICAN, self::NAT_CHADIAN, self::NAT_CHILEAN, self::NAT_CHINESE, self::NAT_CITIZEN_OF_ANTIGUA_AND_BARBUDA, self::NAT_CITIZEN_OF_BOSNIA_AND_HERZEGOVINA, self::NAT_CITIZEN_OF_GUINEABISSAU, self::NAT_CITIZEN_OF_KIRIBATI, self::NAT_CITIZEN_OF_SEYCHELLES, self::NAT_CITIZEN_OF_THE_DOMINICAN_REPUBLIC, self::NAT_CITIZEN_OF_VANUATU, self::NAT_COLOMBIAN, self::NAT_COMORAN, self::NAT_CONGOLESE_CONGO, self::NAT_CONGOLESE_DRC, self::NAT_COOK_ISLANDER, self::NAT_COSTA_RICAN, self::NAT_CROATIAN, self::NAT_CUBAN, self::NAT_CYMRAES, self::NAT_CYMRO, self::NAT_CYPRIOT, self::NAT_CZECH, self::NAT_DANISH, self::NAT_DJIBOUTIAN, self::NAT_DOMINICAN, self::NAT_DUTCH, self::NAT_EAST_TIMORESE, self::NAT_ECUADOREAN, self::NAT_EGYPTIAN, self::NAT_EMIRATI, self::NAT_ENGLISH, self::NAT_EQUATORIAL_GUINEAN, self::NAT_ERITREAN, self::NAT_ESTONIAN, self::NAT_ETHIOPIAN, self::NAT_FAROESE, self::NAT_FIJIAN, self::NAT_FILIPINO, self::NAT_FINNISH, self::NAT_FRENCH, self::NAT_GABONESE, self::NAT_GAMBIAN, self::NAT_GEORGIAN, self::NAT_GERMAN, self::NAT_GHANAIAN, self::NAT_GIBRALTARIAN, self::NAT_GREEK, self::NAT_GREENLANDIC, self::NAT_GRENADIAN, self::NAT_GUAMANIAN, self::NAT_GUATEMALAN, self::NAT_GUINEAN, self::NAT_GUYANESE, self::NAT_HAITIAN, self::NAT_HONDURAN, self::NAT_HONG_KONGER, self::NAT_HUNGARIAN, self::NAT_ICELANDIC, self::NAT_INDIAN, self::NAT_INDONESIAN, self::NAT_IRANIAN, self::NAT_IRAQI, self::NAT_IRISH, self::NAT_ISRAELI, self::NAT_ITALIAN, self::NAT_IVORIAN, self::NAT_JAMAICAN, self::NAT_JAPANESE, self::NAT_JORDANIAN, self::NAT_KAZAKH, self::NAT_KENYAN, self::NAT_KITTITIAN, self::NAT_KOSOVAN, self::NAT_KUWAITI, self::NAT_KYRGYZ, self::NAT_LAO, self::NAT_LATVIAN, self::NAT_LEBANESE, self::NAT_LIBERIAN, self::NAT_LIBYAN, self::NAT_LIECHTENSTEIN_CITIZEN, self::NAT_LITHUANIAN, self::NAT_LUXEMBOURGER, self::NAT_MACANESE, self::NAT_MACEDONIAN, self::NAT_MALAGASY, self::NAT_MALAWIAN, self::NAT_MALAYSIAN, self::NAT_MALDIVIAN, self::NAT_MALIAN, self::NAT_MALTESE, self::NAT_MARSHALLESE, self::NAT_MARTINIQUAIS, self::NAT_MAURITANIAN, self::NAT_MAURITIAN, self::NAT_MEXICAN, self::NAT_MICRONESIAN, self::NAT_MOLDOVAN, self::NAT_MONEGASQUE, self::NAT_MONGOLIAN, self::NAT_MONTENEGRIN, self::NAT_MONTSERRATIAN, self::NAT_MOROCCAN, self::NAT_MOSOTHO, self::NAT_MOZAMBICAN, self::NAT_NAMIBIAN, self::NAT_NAURUAN, self::NAT_NEPALESE, self::NAT_NEW_ZEALANDER, self::NAT_NICARAGUAN, self::NAT_NIGERIAN, self::NAT_NIGERIEN, self::NAT_NIUEAN, self::NAT_NORTH_KOREAN, self::NAT_NORTHERN_IRISH, self::NAT_NORWEGIAN, self::NAT_OMANI, self::NAT_PAKISTANI, self::NAT_PALAUAN, self::NAT_PALESTINIAN, self::NAT_PANAMANIAN, self::NAT_PAPUA_NEW_GUINEAN, self::NAT_PARAGUAYAN, self::NAT_PERUVIAN, self::NAT_PITCAIRN_ISLANDER, self::NAT_POLISH, self::NAT_PORTUGUESE, self::NAT_PRYDEINIG, self::NAT_PUERTO_RICAN, self::NAT_QATARI, self::NAT_ROMANIAN, self::NAT_RUSSIAN, self::NAT_RWANDAN, self::NAT_SALVADOREAN, self::NAT_SAMMARINESE, self::NAT_SAMOAN, self::NAT_SAO_TOMEAN, self::NAT_SAUDI_ARABIAN, self::NAT_SCOTTISH, self::NAT_SENEGALESE, self::NAT_SERBIAN, self::NAT_SIERRA_LEONEAN, self::NAT_SINGAPOREAN, self::NAT_SLOVAK, self::NAT_SLOVENIAN, self::NAT_SOLOMON_ISLANDER, self::NAT_SOMALI, self::NAT_SOUTH_AFRICAN, self::NAT_SOUTH_KOREAN, self::NAT_SOUTH_SUDANESE, self::NAT_SPANISH, self::NAT_SRI_LANKAN, self::NAT_ST_HELENIAN, self::NAT_ST_LUCIAN, self::NAT_STATELESS, self::NAT_SUDANESE, self::NAT_SURINAMESE, self::NAT_SWAZI, self::NAT_SWEDISH, self::NAT_SWISS, self::NAT_SYRIAN, self::NAT_TAIWANESE, self::NAT_TAJIK, self::NAT_TANZANIAN, self::NAT_THAI, self::NAT_TOGOLESE, self::NAT_TONGAN, self::NAT_TRINIDADIAN, self::NAT_TRISTANIAN, self::NAT_TUNISIAN, self::NAT_TURKISH, self::NAT_TURKMEN, self::NAT_TURKS_AND_CAICOS_ISLANDER, self::NAT_TUVALUAN, self::NAT_UGANDAN, self::NAT_UKRAINIAN, self::NAT_URUGUAYAN, self::NAT_UZBEK, self::NAT_VATICAN_CITIZEN, self::NAT_VENEZUELAN, self::NAT_VIETNAMESE, self::NAT_VINCENTIAN, self::NAT_WALLISIAN, self::NAT_WELSH, self::NAT_YEMENI, self::NAT_ZAMBIAN, self::NAT_ZIMBABWEAN];
}