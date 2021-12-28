<?php

namespace Xua\Core\Services;

use Xua\Core\Eves\Service;

final class LocaleLanguage extends Service
{
    private static string $locale;
    private static string $language;
    private static string $calendar;
    private static string $timezone;

    private function __construct() {}

    protected static function _init(): void
    {
        self::$locale = ConstantService::get('config', 'services.ll.locale');
        self::$language = ConstantService::get('config', 'services.ll.language');
        self::$calendar = ConstantService::get('config', 'services.ll.calendar');
        self::$timezone = ConstantService::get('config', 'services.ll.timezone') ?? date_default_timezone_get();
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
    public static function getCalendar(): string
    {
        return self::$calendar;
    }

    /**
     * @return string
     */
    public static function getTimezone(): string
    {
        return self::$timezone;
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
     * @param string $calendar
     * @return void
     */
    public static function setCalendar(string $calendar): void
    {
        self::$calendar = $calendar;
    }

    /**
     * @param string $calendar
     * @return void
     */
    public static function setTimezone(string $timezone): void
    {
        self::$timezone = $timezone;
    }

    public static function localeToLang(?string $locale): ?string
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
            self::LOC_AL => self::LANG_SQ,
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
            default => null,
        };
    }

    const LOC_AF = 'AF'; const LOC_AX = 'AX'; const LOC_AL = 'AL'; const LOC_DZ = 'DZ'; const LOC_AS = 'AS'; const LOC_AD = 'AD'; const LOC_AO = 'AO'; const LOC_AI = 'AI'; const LOC_AQ = 'AQ'; const LOC_AG = 'AG'; const LOC_AR = 'AR'; const LOC_AM = 'AM'; const LOC_AW = 'AW'; const LOC_AU = 'AU'; const LOC_AT = 'AT'; const LOC_AZ = 'AZ'; const LOC_BS = 'BS'; const LOC_BH = 'BH'; const LOC_BD = 'BD'; const LOC_BB = 'BB'; const LOC_BY = 'BY'; const LOC_BE = 'BE'; const LOC_BZ = 'BZ'; const LOC_BJ = 'BJ'; const LOC_BM = 'BM'; const LOC_BT = 'BT'; const LOC_BO = 'BO'; const LOC_BQ = 'BQ'; const LOC_BA = 'BA'; const LOC_BW = 'BW'; const LOC_BV = 'BV'; const LOC_BR = 'BR'; const LOC_IO = 'IO'; const LOC_BN = 'BN'; const LOC_BG = 'BG'; const LOC_BF = 'BF'; const LOC_BI = 'BI'; const LOC_CV = 'CV'; const LOC_KH = 'KH'; const LOC_CM = 'CM'; const LOC_CA = 'CA'; const LOC_KY = 'KY'; const LOC_CF = 'CF'; const LOC_TD = 'TD'; const LOC_CL = 'CL'; const LOC_CN = 'CN'; const LOC_CX = 'CX'; const LOC_CC = 'CC'; const LOC_CO = 'CO'; const LOC_KM = 'KM'; const LOC_CD = 'CD'; const LOC_CG = 'CG'; const LOC_CK = 'CK'; const LOC_CR = 'CR'; const LOC_CI = 'CI'; const LOC_HR = 'HR'; const LOC_CU = 'CU'; const LOC_CW = 'CW'; const LOC_CY = 'CY'; const LOC_CZ = 'CZ'; const LOC_DK = 'DK'; const LOC_DJ = 'DJ'; const LOC_DM = 'DM'; const LOC_DO = 'DO'; const LOC_EC = 'EC'; const LOC_EG = 'EG'; const LOC_SV = 'SV'; const LOC_GQ = 'GQ'; const LOC_ER = 'ER'; const LOC_EE = 'EE'; const LOC_SZ = 'SZ'; const LOC_ET = 'ET'; const LOC_FK = 'FK'; const LOC_FO = 'FO'; const LOC_FJ = 'FJ'; const LOC_FI = 'FI'; const LOC_FR = 'FR'; const LOC_GF = 'GF'; const LOC_PF = 'PF'; const LOC_TF = 'TF'; const LOC_GA = 'GA'; const LOC_GM = 'GM'; const LOC_GE = 'GE'; const LOC_DE = 'DE'; const LOC_GH = 'GH'; const LOC_GI = 'GI'; const LOC_GR = 'GR'; const LOC_GL = 'GL'; const LOC_GD = 'GD'; const LOC_GP = 'GP'; const LOC_GU = 'GU'; const LOC_GT = 'GT'; const LOC_GG = 'GG'; const LOC_GN = 'GN'; const LOC_GW = 'GW'; const LOC_GY = 'GY'; const LOC_HT = 'HT'; const LOC_HM = 'HM'; const LOC_VA = 'VA'; const LOC_HN = 'HN'; const LOC_HK = 'HK'; const LOC_HU = 'HU'; const LOC_IS = 'IS'; const LOC_IN = 'IN'; const LOC_ID = 'ID'; const LOC_IR = 'IR'; const LOC_IQ = 'IQ'; const LOC_IE = 'IE'; const LOC_IM = 'IM'; /* const LOC_IL = 'IL'; */ const LOC_IT = 'IT'; const LOC_JM = 'JM'; const LOC_JP = 'JP'; const LOC_JE = 'JE'; const LOC_JO = 'JO'; const LOC_KZ = 'KZ'; const LOC_KE = 'KE'; const LOC_KI = 'KI'; /* const LOC_KP = 'KP'; */ const LOC_KR = 'KR'; const LOC_KW = 'KW'; const LOC_KG = 'KG'; const LOC_LA = 'LA'; const LOC_LV = 'LV'; const LOC_LB = 'LB'; const LOC_LS = 'LS'; const LOC_LR = 'LR'; const LOC_LY = 'LY'; const LOC_LI = 'LI'; const LOC_LT = 'LT'; const LOC_LU = 'LU'; const LOC_MO = 'MO'; const LOC_MK = 'MK'; const LOC_MG = 'MG'; const LOC_MW = 'MW'; const LOC_MY = 'MY'; const LOC_MV = 'MV'; const LOC_ML = 'ML'; const LOC_MT = 'MT'; const LOC_MH = 'MH'; const LOC_MQ = 'MQ'; const LOC_MR = 'MR'; const LOC_MU = 'MU'; const LOC_YT = 'YT'; const LOC_MX = 'MX'; const LOC_FM = 'FM'; const LOC_MD = 'MD'; const LOC_MC = 'MC'; const LOC_MN = 'MN'; const LOC_ME = 'ME'; const LOC_MS = 'MS'; const LOC_MA = 'MA'; const LOC_MZ = 'MZ'; const LOC_MM = 'MM'; const LOC_NA = 'NA'; const LOC_NR = 'NR'; const LOC_NP = 'NP'; const LOC_NL = 'NL'; const LOC_NC = 'NC'; const LOC_NZ = 'NZ'; const LOC_NI = 'NI'; const LOC_NE = 'NE'; const LOC_NG = 'NG'; const LOC_NU = 'NU'; const LOC_NF = 'NF'; const LOC_MP = 'MP'; const LOC_NO = 'NO'; const LOC_OM = 'OM'; const LOC_PK = 'PK'; const LOC_PW = 'PW'; const LOC_PS = 'PS'; const LOC_PA = 'PA'; const LOC_PG = 'PG'; const LOC_PY = 'PY'; const LOC_PE = 'PE'; const LOC_PH = 'PH'; const LOC_PN = 'PN'; const LOC_PL = 'PL'; const LOC_PT = 'PT'; const LOC_PR = 'PR'; const LOC_QA = 'QA'; const LOC_RE = 'RE'; const LOC_RO = 'RO'; const LOC_RU = 'RU'; const LOC_RW = 'RW'; const LOC_BL = 'BL'; const LOC_SH = 'SH'; const LOC_KN = 'KN'; const LOC_LC = 'LC'; const LOC_MF = 'MF'; const LOC_PM = 'PM'; const LOC_VC = 'VC'; const LOC_WS = 'WS'; const LOC_SM = 'SM'; const LOC_ST = 'ST'; const LOC_SA = 'SA'; const LOC_SN = 'SN'; const LOC_RS = 'RS'; const LOC_SC = 'SC'; const LOC_SL = 'SL'; const LOC_SG = 'SG'; const LOC_SX = 'SX'; const LOC_SK = 'SK'; const LOC_SI = 'SI'; const LOC_SB = 'SB'; const LOC_SO = 'SO'; const LOC_ZA = 'ZA'; const LOC_GS = 'GS'; const LOC_SS = 'SS'; const LOC_ES = 'ES'; const LOC_LK = 'LK'; const LOC_SD = 'SD'; const LOC_SR = 'SR'; const LOC_SJ = 'SJ'; const LOC_SE = 'SE'; const LOC_CH = 'CH'; const LOC_SY = 'SY'; const LOC_TW = 'TW'; const LOC_TJ = 'TJ'; const LOC_TZ = 'TZ'; const LOC_TH = 'TH'; const LOC_TL = 'TL'; const LOC_TG = 'TG'; const LOC_TK = 'TK'; const LOC_TO = 'TO'; const LOC_TT = 'TT'; const LOC_TN = 'TN'; const LOC_TR = 'TR'; const LOC_TM = 'TM'; const LOC_TC = 'TC'; const LOC_TV = 'TV'; const LOC_UG = 'UG'; const LOC_UA = 'UA'; const LOC_AE = 'AE'; const LOC_GB = 'GB'; const LOC_UM = 'UM'; const LOC_US = 'US'; const LOC_UY = 'UY'; const LOC_UZ = 'UZ'; const LOC_VU = 'VU'; const LOC_VE = 'VE'; const LOC_VN = 'VN'; const LOC_VG = 'VG'; const LOC_VI = 'VI'; const LOC_WF = 'WF'; const LOC_EH = 'EH'; const LOC_YE = 'YE'; const LOC_ZM = 'ZM'; const LOC_ZW = 'ZW';
    const LOC_ = [self::LOC_AF, self::LOC_AX, self::LOC_AL, self::LOC_DZ, self::LOC_AS, self::LOC_AD, self::LOC_AO, self::LOC_AI, self::LOC_AQ, self::LOC_AG, self::LOC_AR, self::LOC_AM, self::LOC_AW, self::LOC_AU, self::LOC_AT, self::LOC_AZ, self::LOC_BS, self::LOC_BH, self::LOC_BD, self::LOC_BB, self::LOC_BY, self::LOC_BE, self::LOC_BZ, self::LOC_BJ, self::LOC_BM, self::LOC_BT, self::LOC_BO, self::LOC_BQ, self::LOC_BA, self::LOC_BW, self::LOC_BV, self::LOC_BR, self::LOC_IO, self::LOC_BN, self::LOC_BG, self::LOC_BF, self::LOC_BI, self::LOC_CV, self::LOC_KH, self::LOC_CM, self::LOC_CA, self::LOC_KY, self::LOC_CF, self::LOC_TD, self::LOC_CL, self::LOC_CN, self::LOC_CX, self::LOC_CC, self::LOC_CO, self::LOC_KM, self::LOC_CD, self::LOC_CG, self::LOC_CK, self::LOC_CR, self::LOC_CI, self::LOC_HR, self::LOC_CU, self::LOC_CW, self::LOC_CY, self::LOC_CZ, self::LOC_DK, self::LOC_DJ, self::LOC_DM, self::LOC_DO, self::LOC_EC, self::LOC_EG, self::LOC_SV, self::LOC_GQ, self::LOC_ER, self::LOC_EE, self::LOC_SZ, self::LOC_ET, self::LOC_FK, self::LOC_FO, self::LOC_FJ, self::LOC_FI, self::LOC_FR, self::LOC_GF, self::LOC_PF, self::LOC_TF, self::LOC_GA, self::LOC_GM, self::LOC_GE, self::LOC_DE, self::LOC_GH, self::LOC_GI, self::LOC_GR, self::LOC_GL, self::LOC_GD, self::LOC_GP, self::LOC_GU, self::LOC_GT, self::LOC_GG, self::LOC_GN, self::LOC_GW, self::LOC_GY, self::LOC_HT, self::LOC_HM, self::LOC_VA, self::LOC_HN, self::LOC_HK, self::LOC_HU, self::LOC_IS, self::LOC_IN, self::LOC_ID, self::LOC_IR, self::LOC_IQ, self::LOC_IE, self::LOC_IM, /* self::LOC_IL, */ self::LOC_IT, self::LOC_JM, self::LOC_JP, self::LOC_JE, self::LOC_JO, self::LOC_KZ, self::LOC_KE, self::LOC_KI, /* self::LOC_KP, */ self::LOC_KR, self::LOC_KW, self::LOC_KG, self::LOC_LA, self::LOC_LV, self::LOC_LB, self::LOC_LS, self::LOC_LR, self::LOC_LY, self::LOC_LI, self::LOC_LT, self::LOC_LU, self::LOC_MO, self::LOC_MK, self::LOC_MG, self::LOC_MW, self::LOC_MY, self::LOC_MV, self::LOC_ML, self::LOC_MT, self::LOC_MH, self::LOC_MQ, self::LOC_MR, self::LOC_MU, self::LOC_YT, self::LOC_MX, self::LOC_FM, self::LOC_MD, self::LOC_MC, self::LOC_MN, self::LOC_ME, self::LOC_MS, self::LOC_MA, self::LOC_MZ, self::LOC_MM, self::LOC_NA, self::LOC_NR, self::LOC_NP, self::LOC_NL, self::LOC_NC, self::LOC_NZ, self::LOC_NI, self::LOC_NE, self::LOC_NG, self::LOC_NU, self::LOC_NF, self::LOC_MP, self::LOC_NO, self::LOC_OM, self::LOC_PK, self::LOC_PW, self::LOC_PS, self::LOC_PA, self::LOC_PG, self::LOC_PY, self::LOC_PE, self::LOC_PH, self::LOC_PN, self::LOC_PL, self::LOC_PT, self::LOC_PR, self::LOC_QA, self::LOC_RE, self::LOC_RO, self::LOC_RU, self::LOC_RW, self::LOC_BL, self::LOC_SH, self::LOC_KN, self::LOC_LC, self::LOC_MF, self::LOC_PM, self::LOC_VC, self::LOC_WS, self::LOC_SM, self::LOC_ST, self::LOC_SA, self::LOC_SN, self::LOC_RS, self::LOC_SC, self::LOC_SL, self::LOC_SG, self::LOC_SX, self::LOC_SK, self::LOC_SI, self::LOC_SB, self::LOC_SO, self::LOC_ZA, self::LOC_GS, self::LOC_SS, self::LOC_ES, self::LOC_LK, self::LOC_SD, self::LOC_SR, self::LOC_SJ, self::LOC_SE, self::LOC_CH, self::LOC_SY, self::LOC_TW, self::LOC_TJ, self::LOC_TZ, self::LOC_TH, self::LOC_TL, self::LOC_TG, self::LOC_TK, self::LOC_TO, self::LOC_TT, self::LOC_TN, self::LOC_TR, self::LOC_TM, self::LOC_TC, self::LOC_TV, self::LOC_UG, self::LOC_UA, self::LOC_AE, self::LOC_GB, self::LOC_UM, self::LOC_US, self::LOC_UY, self::LOC_UZ, self::LOC_VU, self::LOC_VE, self::LOC_VN, self::LOC_VG, self::LOC_VI, self::LOC_WF, self::LOC_EH, self::LOC_YE, self::LOC_ZM, self::LOC_ZW];

    const LANG_AF = 'af'; const LANG_AR = 'ar'; const LANG_AZ = 'az'; const LANG_BE = 'be'; const LANG_BG = 'bg'; const LANG_BS = 'bs'; const LANG_CA = 'ca'; const LANG_CS = 'cs'; const LANG_CY = 'cy'; const LANG_DA = 'da'; const LANG_DE = 'de'; const LANG_DV = 'dv'; const LANG_EL = 'el'; const LANG_EN = 'en'; const LANG_EO = 'eo'; const LANG_ES = 'es'; const LANG_ET = 'et'; const LANG_EU = 'eu'; const LANG_FA = 'fa'; const LANG_FI = 'fi'; const LANG_FO = 'fo'; const LANG_FR = 'fr'; const LANG_GL = 'gl'; const LANG_GU = 'gu'; /* const LANG_HE = 'he'; */ const LANG_HI = 'hi'; const LANG_HR = 'hr'; const LANG_HU = 'hu'; const LANG_HY = 'hy'; const LANG_ID = 'id'; const LANG_IS = 'is'; const LANG_IT = 'it'; const LANG_JA = 'ja'; const LANG_KA = 'ka'; const LANG_KK = 'kk'; const LANG_KN = 'kn'; const LANG_KO = 'ko'; const LANG_KOK = 'kok'; const LANG_KY = 'ky'; const LANG_LT = 'lt'; const LANG_LV = 'lv'; const LANG_MI = 'mi'; const LANG_MK = 'mk'; const LANG_MN = 'mn'; const LANG_MR = 'mr'; const LANG_MS = 'ms'; const LANG_MT = 'mt'; const LANG_NB = 'nb'; const LANG_NL = 'nl'; const LANG_NN = 'nn'; const LANG_NS = 'ns'; const LANG_PA = 'pa'; const LANG_PL = 'pl'; const LANG_PS = 'ps'; const LANG_PT = 'pt'; const LANG_QU = 'qu'; const LANG_RO = 'ro'; const LANG_RU = 'ru'; const LANG_SA = 'sa'; const LANG_SE = 'se'; const LANG_SK = 'sk'; const LANG_SL = 'sl'; const LANG_SQ = 'sq'; const LANG_SR = 'sr'; const LANG_SV = 'sv'; const LANG_SW = 'sw'; const LANG_SYR = 'syr'; const LANG_TA = 'ta'; const LANG_TE = 'te'; const LANG_TH = 'th'; const LANG_TL = 'tl'; const LANG_TN = 'tn'; const LANG_TR = 'tr'; const LANG_TT = 'tt'; const LANG_TS = 'ts'; const LANG_UK = 'uk'; const LANG_UR = 'ur'; const LANG_UZ = 'uz'; const LANG_VI = 'vi'; const LANG_XH = 'xh'; const LANG_ZH = 'zh'; const LANG_ZU = 'zu';
    const LANG_ = [self::LANG_AF, self::LANG_AR, self::LANG_AZ, self::LANG_BE, self::LANG_BG, self::LANG_BS, self::LANG_CA, self::LANG_CS, self::LANG_CY, self::LANG_DA, self::LANG_DE, self::LANG_DV, self::LANG_EL, self::LANG_EN, self::LANG_EO, self::LANG_ES, self::LANG_ET, self::LANG_EU, self::LANG_FA, self::LANG_FI, self::LANG_FO, self::LANG_FR, self::LANG_GL, self::LANG_GU, /* self::LANG_HE, */ self::LANG_HI, self::LANG_HR, self::LANG_HU, self::LANG_HY, self::LANG_ID, self::LANG_IS, self::LANG_IT, self::LANG_JA, self::LANG_KA, self::LANG_KK, self::LANG_KN, self::LANG_KO, self::LANG_KOK, self::LANG_KY, self::LANG_LT, self::LANG_LV, self::LANG_MI, self::LANG_MK, self::LANG_MN, self::LANG_MR, self::LANG_MS, self::LANG_MT, self::LANG_NB, self::LANG_NL, self::LANG_NN, self::LANG_NS, self::LANG_PA, self::LANG_PL, self::LANG_PS, self::LANG_PT, self::LANG_QU, self::LANG_RO, self::LANG_RU, self::LANG_SA, self::LANG_SE, self::LANG_SK, self::LANG_SL, self::LANG_SQ, self::LANG_SR, self::LANG_SV, self::LANG_SW, self::LANG_SYR, self::LANG_TA, self::LANG_TE, self::LANG_TH, self::LANG_TL, self::LANG_TN, self::LANG_TR, self::LANG_TT, self::LANG_TS, self::LANG_UK, self::LANG_UR, self::LANG_UZ, self::LANG_VI, self::LANG_XH, self::LANG_ZH, self::LANG_ZU];

    const NAT_AFGHAN = "afghan"; const NAT_ALBANIAN = "albanian"; const NAT_ALGERIAN = "algerian"; const NAT_AMERICAN = "american"; const NAT_ANDORRAN = "andorran"; const NAT_ANGOLAN = "angolan"; const NAT_ANGUILLAN = "anguillan"; const NAT_ARGENTINE = "argentine"; const NAT_ARMENIAN = "armenian"; const NAT_AUSTRALIAN = "australian"; const NAT_AUSTRIAN = "austrian"; const NAT_AZERBAIJANI = "azerbaijani"; const NAT_BAHAMIAN = "bahamian"; const NAT_BAHRAINI = "bahraini"; const NAT_BANGLADESHI = "bangladeshi"; const NAT_BARBADIAN = "barbadian"; const NAT_BELARUSIAN = "belarusian"; const NAT_BELGIAN = "belgian"; const NAT_BELIZEAN = "belizean"; const NAT_BENINESE = "beninese"; const NAT_BERMUDIAN = "bermudian"; const NAT_BHUTANESE = "bhutanese"; const NAT_BOLIVIAN = "bolivian"; const NAT_BOTSWANAN = "botswanan"; const NAT_BRAZILIAN = "brazilian"; const NAT_BRITISH = "british"; const NAT_BRITISH_VIRGIN_ISLANDER = "british_virgin_islander"; const NAT_BRUNEIAN = "bruneian"; const NAT_BULGARIAN = "bulgarian"; const NAT_BURKINAN = "burkinan"; const NAT_BURMESE = "burmese"; const NAT_BURUNDIAN = "burundian"; const NAT_CAMBODIAN = "cambodian"; const NAT_CAMEROONIAN = "cameroonian"; const NAT_CANADIAN = "canadian"; const NAT_CAPE_VERDEAN = "cape_verdean"; const NAT_CAYMAN_ISLANDER = "cayman_islander"; const NAT_CENTRAL_AFRICAN = "central_african"; const NAT_CHADIAN = "chadian"; const NAT_CHILEAN = "chilean"; const NAT_CHINESE = "chinese"; const NAT_CITIZEN_OF_ANTIGUA_AND_BARBUDA = "citizen_of_antigua_and_barbuda"; const NAT_CITIZEN_OF_BOSNIA_AND_HERZEGOVINA = "citizen_of_bosnia_and_herzegovina"; const NAT_CITIZEN_OF_GUINEABISSAU = "citizen_of_guineabissau"; const NAT_CITIZEN_OF_KIRIBATI = "citizen_of_kiribati"; const NAT_CITIZEN_OF_SEYCHELLES = "citizen_of_seychelles"; const NAT_CITIZEN_OF_THE_DOMINICAN_REPUBLIC = "citizen_of_the_dominican_republic"; const NAT_CITIZEN_OF_VANUATU = "citizen_of_vanuatu"; const NAT_COLOMBIAN = "colombian"; const NAT_COMORAN = "comoran"; const NAT_CONGOLESE_CONGO = "congolese_congo"; const NAT_CONGOLESE_DRC = "congolese_drc"; const NAT_COOK_ISLANDER = "cook_islander"; const NAT_COSTA_RICAN = "costa_rican"; const NAT_CROATIAN = "croatian"; const NAT_CUBAN = "cuban"; const NAT_CYMRAES = "cymraes"; const NAT_CYMRO = "cymro"; const NAT_CYPRIOT = "cypriot"; const NAT_CZECH = "czech"; const NAT_DANISH = "danish"; const NAT_DJIBOUTIAN = "djiboutian"; const NAT_DOMINICAN = "dominican"; const NAT_DUTCH = "dutch"; const NAT_EAST_TIMORESE = "east_timorese"; const NAT_ECUADOREAN = "ecuadorean"; const NAT_EGYPTIAN = "egyptian"; const NAT_EMIRATI = "emirati"; const NAT_ENGLISH = "english"; const NAT_EQUATORIAL_GUINEAN = "equatorial_guinean"; const NAT_ERITREAN = "eritrean"; const NAT_ESTONIAN = "estonian"; const NAT_ETHIOPIAN = "ethiopian"; const NAT_FAROESE = "faroese"; const NAT_FIJIAN = "fijian"; const NAT_FILIPINO = "filipino"; const NAT_FINNISH = "finnish"; const NAT_FRENCH = "french"; const NAT_GABONESE = "gabonese"; const NAT_GAMBIAN = "gambian"; const NAT_GEORGIAN = "georgian"; const NAT_GERMAN = "german"; const NAT_GHANAIAN = "ghanaian"; const NAT_GIBRALTARIAN = "gibraltarian"; const NAT_GREEK = "greek"; const NAT_GREENLANDIC = "greenlandic"; const NAT_GRENADIAN = "grenadian"; const NAT_GUAMANIAN = "guamanian"; const NAT_GUATEMALAN = "guatemalan"; const NAT_GUINEAN = "guinean"; const NAT_GUYANESE = "guyanese"; const NAT_HAITIAN = "haitian"; const NAT_HONDURAN = "honduran"; const NAT_HONG_KONGER = "hong_konger"; const NAT_HUNGARIAN = "hungarian"; const NAT_ICELANDIC = "icelandic"; const NAT_INDIAN = "indian"; const NAT_INDONESIAN = "indonesian"; const NAT_IRANIAN = "iranian"; const NAT_IRAQI = "iraqi"; const NAT_IRISH = "irish"; /* const NAT_ISRAELI = "israeli"; */ const NAT_ITALIAN = "italian"; const NAT_IVORIAN = "ivorian"; const NAT_JAMAICAN = "jamaican"; const NAT_JAPANESE = "japanese"; const NAT_JORDANIAN = "jordanian"; const NAT_KAZAKH = "kazakh"; const NAT_KENYAN = "kenyan"; const NAT_KITTITIAN = "kittitian"; const NAT_KOSOVAN = "kosovan"; const NAT_KUWAITI = "kuwaiti"; const NAT_KYRGYZ = "kyrgyz"; const NAT_LAO = "lao"; const NAT_LATVIAN = "latvian"; const NAT_LEBANESE = "lebanese"; const NAT_LIBERIAN = "liberian"; const NAT_LIBYAN = "libyan"; const NAT_LIECHTENSTEIN_CITIZEN = "liechtenstein_citizen"; const NAT_LITHUANIAN = "lithuanian"; const NAT_LUXEMBOURGER = "luxembourger"; const NAT_MACANESE = "macanese"; const NAT_MACEDONIAN = "macedonian"; const NAT_MALAGASY = "malagasy"; const NAT_MALAWIAN = "malawian"; const NAT_MALAYSIAN = "malaysian"; const NAT_MALDIVIAN = "maldivian"; const NAT_MALIAN = "malian"; const NAT_MALTESE = "maltese"; const NAT_MARSHALLESE = "marshallese"; const NAT_MARTINIQUAIS = "martiniquais"; const NAT_MAURITANIAN = "mauritanian"; const NAT_MAURITIAN = "mauritian"; const NAT_MEXICAN = "mexican"; const NAT_MICRONESIAN = "micronesian"; const NAT_MOLDOVAN = "moldovan"; const NAT_MONEGASQUE = "monegasque"; const NAT_MONGOLIAN = "mongolian"; const NAT_MONTENEGRIN = "montenegrin"; const NAT_MONTSERRATIAN = "montserratian"; const NAT_MOROCCAN = "moroccan"; const NAT_MOSOTHO = "mosotho"; const NAT_MOZAMBICAN = "mozambican"; const NAT_NAMIBIAN = "namibian"; const NAT_NAURUAN = "nauruan"; const NAT_NEPALESE = "nepalese"; const NAT_NEW_ZEALANDER = "new_zealander"; const NAT_NICARAGUAN = "nicaraguan"; const NAT_NIGERIAN = "nigerian"; const NAT_NIGERIEN = "nigerien"; const NAT_NIUEAN = "niuean"; /* const NAT_NORTH_KOREAN = "north_korean"; */ const NAT_NORTHERN_IRISH = "northern_irish"; const NAT_NORWEGIAN = "norwegian"; const NAT_OMANI = "omani"; const NAT_PAKISTANI = "pakistani"; const NAT_PALAUAN = "palauan"; const NAT_PALESTINIAN = "palestinian"; const NAT_PANAMANIAN = "panamanian"; const NAT_PAPUA_NEW_GUINEAN = "papua_new_guinean"; const NAT_PARAGUAYAN = "paraguayan"; const NAT_PERUVIAN = "peruvian"; const NAT_PITCAIRN_ISLANDER = "pitcairn_islander"; const NAT_POLISH = "polish"; const NAT_PORTUGUESE = "portuguese"; const NAT_PRYDEINIG = "prydeinig"; const NAT_PUERTO_RICAN = "puerto_rican"; const NAT_QATARI = "qatari"; const NAT_ROMANIAN = "romanian"; const NAT_RUSSIAN = "russian"; const NAT_RWANDAN = "rwandan"; const NAT_SALVADOREAN = "salvadorean"; const NAT_SAMMARINESE = "sammarinese"; const NAT_SAMOAN = "samoan"; const NAT_SAO_TOMEAN = "sao_tomean"; const NAT_SAUDI_ARABIAN = "saudi_arabian"; const NAT_SCOTTISH = "scottish"; const NAT_SENEGALESE = "senegalese"; const NAT_SERBIAN = "serbian"; const NAT_SIERRA_LEONEAN = "sierra_leonean"; const NAT_SINGAPOREAN = "singaporean"; const NAT_SLOVAK = "slovak"; const NAT_SLOVENIAN = "slovenian"; const NAT_SOLOMON_ISLANDER = "solomon_islander"; const NAT_SOMALI = "somali"; const NAT_SOUTH_AFRICAN = "south_african"; const NAT_SOUTH_KOREAN = "south_korean"; const NAT_SOUTH_SUDANESE = "south_sudanese"; const NAT_SPANISH = "spanish"; const NAT_SRI_LANKAN = "sri_lankan"; const NAT_ST_HELENIAN = "st_helenian"; const NAT_ST_LUCIAN = "st_lucian"; const NAT_STATELESS = "stateless"; const NAT_SUDANESE = "sudanese"; const NAT_SURINAMESE = "surinamese"; const NAT_SWAZI = "swazi"; const NAT_SWEDISH = "swedish"; const NAT_SWISS = "swiss"; const NAT_SYRIAN = "syrian"; const NAT_TAIWANESE = "taiwanese"; const NAT_TAJIK = "tajik"; const NAT_TANZANIAN = "tanzanian"; const NAT_THAI = "thai"; const NAT_TOGOLESE = "togolese"; const NAT_TONGAN = "tongan"; const NAT_TRINIDADIAN = "trinidadian"; const NAT_TRISTANIAN = "tristanian"; const NAT_TUNISIAN = "tunisian"; const NAT_TURKISH = "turkish"; const NAT_TURKMEN = "turkmen"; const NAT_TURKS_AND_CAICOS_ISLANDER = "turks_and_caicos_islander"; const NAT_TUVALUAN = "tuvaluan"; const NAT_UGANDAN = "ugandan"; const NAT_UKRAINIAN = "ukrainian"; const NAT_URUGUAYAN = "uruguayan"; const NAT_UZBEK = "uzbek"; const NAT_VATICAN_CITIZEN = "vatican_citizen"; const NAT_VENEZUELAN = "venezuelan"; const NAT_VIETNAMESE = "vietnamese"; const NAT_VINCENTIAN = "vincentian"; const NAT_WALLISIAN = "wallisian"; const NAT_WELSH = "welsh"; const NAT_YEMENI = "yemeni"; const NAT_ZAMBIAN = "zambian"; const NAT_ZIMBABWEAN = "zimbabwean";
    const NAT_ = [self::NAT_AFGHAN, self::NAT_ALBANIAN, self::NAT_ALGERIAN, self::NAT_AMERICAN, self::NAT_ANDORRAN, self::NAT_ANGOLAN, self::NAT_ANGUILLAN, self::NAT_ARGENTINE, self::NAT_ARMENIAN, self::NAT_AUSTRALIAN, self::NAT_AUSTRIAN, self::NAT_AZERBAIJANI, self::NAT_BAHAMIAN, self::NAT_BAHRAINI, self::NAT_BANGLADESHI, self::NAT_BARBADIAN, self::NAT_BELARUSIAN, self::NAT_BELGIAN, self::NAT_BELIZEAN, self::NAT_BENINESE, self::NAT_BERMUDIAN, self::NAT_BHUTANESE, self::NAT_BOLIVIAN, self::NAT_BOTSWANAN, self::NAT_BRAZILIAN, self::NAT_BRITISH, self::NAT_BRITISH_VIRGIN_ISLANDER, self::NAT_BRUNEIAN, self::NAT_BULGARIAN, self::NAT_BURKINAN, self::NAT_BURMESE, self::NAT_BURUNDIAN, self::NAT_CAMBODIAN, self::NAT_CAMEROONIAN, self::NAT_CANADIAN, self::NAT_CAPE_VERDEAN, self::NAT_CAYMAN_ISLANDER, self::NAT_CENTRAL_AFRICAN, self::NAT_CHADIAN, self::NAT_CHILEAN, self::NAT_CHINESE, self::NAT_CITIZEN_OF_ANTIGUA_AND_BARBUDA, self::NAT_CITIZEN_OF_BOSNIA_AND_HERZEGOVINA, self::NAT_CITIZEN_OF_GUINEABISSAU, self::NAT_CITIZEN_OF_KIRIBATI, self::NAT_CITIZEN_OF_SEYCHELLES, self::NAT_CITIZEN_OF_THE_DOMINICAN_REPUBLIC, self::NAT_CITIZEN_OF_VANUATU, self::NAT_COLOMBIAN, self::NAT_COMORAN, self::NAT_CONGOLESE_CONGO, self::NAT_CONGOLESE_DRC, self::NAT_COOK_ISLANDER, self::NAT_COSTA_RICAN, self::NAT_CROATIAN, self::NAT_CUBAN, self::NAT_CYMRAES, self::NAT_CYMRO, self::NAT_CYPRIOT, self::NAT_CZECH, self::NAT_DANISH, self::NAT_DJIBOUTIAN, self::NAT_DOMINICAN, self::NAT_DUTCH, self::NAT_EAST_TIMORESE, self::NAT_ECUADOREAN, self::NAT_EGYPTIAN, self::NAT_EMIRATI, self::NAT_ENGLISH, self::NAT_EQUATORIAL_GUINEAN, self::NAT_ERITREAN, self::NAT_ESTONIAN, self::NAT_ETHIOPIAN, self::NAT_FAROESE, self::NAT_FIJIAN, self::NAT_FILIPINO, self::NAT_FINNISH, self::NAT_FRENCH, self::NAT_GABONESE, self::NAT_GAMBIAN, self::NAT_GEORGIAN, self::NAT_GERMAN, self::NAT_GHANAIAN, self::NAT_GIBRALTARIAN, self::NAT_GREEK, self::NAT_GREENLANDIC, self::NAT_GRENADIAN, self::NAT_GUAMANIAN, self::NAT_GUATEMALAN, self::NAT_GUINEAN, self::NAT_GUYANESE, self::NAT_HAITIAN, self::NAT_HONDURAN, self::NAT_HONG_KONGER, self::NAT_HUNGARIAN, self::NAT_ICELANDIC, self::NAT_INDIAN, self::NAT_INDONESIAN, self::NAT_IRANIAN, self::NAT_IRAQI, self::NAT_IRISH, /* self::NAT_ISRAELI ,*/ self::NAT_ITALIAN, self::NAT_IVORIAN, self::NAT_JAMAICAN, self::NAT_JAPANESE, self::NAT_JORDANIAN, self::NAT_KAZAKH, self::NAT_KENYAN, self::NAT_KITTITIAN, self::NAT_KOSOVAN, self::NAT_KUWAITI, self::NAT_KYRGYZ, self::NAT_LAO, self::NAT_LATVIAN, self::NAT_LEBANESE, self::NAT_LIBERIAN, self::NAT_LIBYAN, self::NAT_LIECHTENSTEIN_CITIZEN, self::NAT_LITHUANIAN, self::NAT_LUXEMBOURGER, self::NAT_MACANESE, self::NAT_MACEDONIAN, self::NAT_MALAGASY, self::NAT_MALAWIAN, self::NAT_MALAYSIAN, self::NAT_MALDIVIAN, self::NAT_MALIAN, self::NAT_MALTESE, self::NAT_MARSHALLESE, self::NAT_MARTINIQUAIS, self::NAT_MAURITANIAN, self::NAT_MAURITIAN, self::NAT_MEXICAN, self::NAT_MICRONESIAN, self::NAT_MOLDOVAN, self::NAT_MONEGASQUE, self::NAT_MONGOLIAN, self::NAT_MONTENEGRIN, self::NAT_MONTSERRATIAN, self::NAT_MOROCCAN, self::NAT_MOSOTHO, self::NAT_MOZAMBICAN, self::NAT_NAMIBIAN, self::NAT_NAURUAN, self::NAT_NEPALESE, self::NAT_NEW_ZEALANDER, self::NAT_NICARAGUAN, self::NAT_NIGERIAN, self::NAT_NIGERIEN, self::NAT_NIUEAN, /* self::NAT_NORTH_KOREAN, */ self::NAT_NORTHERN_IRISH, self::NAT_NORWEGIAN, self::NAT_OMANI, self::NAT_PAKISTANI, self::NAT_PALAUAN, self::NAT_PALESTINIAN, self::NAT_PANAMANIAN, self::NAT_PAPUA_NEW_GUINEAN, self::NAT_PARAGUAYAN, self::NAT_PERUVIAN, self::NAT_PITCAIRN_ISLANDER, self::NAT_POLISH, self::NAT_PORTUGUESE, self::NAT_PRYDEINIG, self::NAT_PUERTO_RICAN, self::NAT_QATARI, self::NAT_ROMANIAN, self::NAT_RUSSIAN, self::NAT_RWANDAN, self::NAT_SALVADOREAN, self::NAT_SAMMARINESE, self::NAT_SAMOAN, self::NAT_SAO_TOMEAN, self::NAT_SAUDI_ARABIAN, self::NAT_SCOTTISH, self::NAT_SENEGALESE, self::NAT_SERBIAN, self::NAT_SIERRA_LEONEAN, self::NAT_SINGAPOREAN, self::NAT_SLOVAK, self::NAT_SLOVENIAN, self::NAT_SOLOMON_ISLANDER, self::NAT_SOMALI, self::NAT_SOUTH_AFRICAN, self::NAT_SOUTH_KOREAN, self::NAT_SOUTH_SUDANESE, self::NAT_SPANISH, self::NAT_SRI_LANKAN, self::NAT_ST_HELENIAN, self::NAT_ST_LUCIAN, self::NAT_STATELESS, self::NAT_SUDANESE, self::NAT_SURINAMESE, self::NAT_SWAZI, self::NAT_SWEDISH, self::NAT_SWISS, self::NAT_SYRIAN, self::NAT_TAIWANESE, self::NAT_TAJIK, self::NAT_TANZANIAN, self::NAT_THAI, self::NAT_TOGOLESE, self::NAT_TONGAN, self::NAT_TRINIDADIAN, self::NAT_TRISTANIAN, self::NAT_TUNISIAN, self::NAT_TURKISH, self::NAT_TURKMEN, self::NAT_TURKS_AND_CAICOS_ISLANDER, self::NAT_TUVALUAN, self::NAT_UGANDAN, self::NAT_UKRAINIAN, self::NAT_URUGUAYAN, self::NAT_UZBEK, self::NAT_VATICAN_CITIZEN, self::NAT_VENEZUELAN, self::NAT_VIETNAMESE, self::NAT_VINCENTIAN, self::NAT_WALLISIAN, self::NAT_WELSH, self::NAT_YEMENI, self::NAT_ZAMBIAN, self::NAT_ZIMBABWEAN];

    const CAL_GREGORIAN = 'gregorian'; const CAL_JALALI = 'jalali';
    const CAL_ = [self::CAL_GREGORIAN, self::CAL_JALALI];
}