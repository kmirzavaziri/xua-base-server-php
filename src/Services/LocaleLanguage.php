<?php


namespace XUA\Services;


use XUA\Exceptions\InstantiationException;
use XUA\Eves\Service;

final class LocaleLanguage extends Service
{
    const DEFAULT_LOCALE = self::LOC_IR;
    const DEFAULT_LANGUAGE = self::LANG_FA;

    private static string $locale;
    private static string $language;

    /**
     * @throws InstantiationException
     */
    function __construct()
    {
        throw new InstantiationException('Cannot instantiate class `LocaleLanguage`.');
    }

    protected static function _init(): void
    {
        self::$locale = self::DEFAULT_LOCALE;
        self::$language = self::DEFAULT_LANGUAGE;
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

    const LOC_ZA = 'ZA'; const LOC_AE = 'AE'; const LOC_BH = 'BH'; const LOC_DZ = 'DZ'; const LOC_EG = 'EG'; const LOC_IQ = 'IQ'; const LOC_JO = 'JO'; const LOC_KW = 'KW'; const LOC_LB = 'LB'; const LOC_LY = 'LY'; const LOC_MA = 'MA'; const LOC_OM = 'OM'; const LOC_QA = 'QA'; const LOC_SA = 'SA'; const LOC_SY = 'SY'; const LOC_TN = 'TN'; const LOC_YE = 'YE'; const LOC_AZ = 'AZ'; const LOC_BY = 'BY'; const LOC_BG = 'BG'; const LOC_BA = 'BA'; const LOC_ES = 'ES'; const LOC_CZ = 'CZ'; const LOC_GB = 'GB'; const LOC_DK = 'DK'; const LOC_AT = 'AT'; const LOC_CH = 'CH'; const LOC_DE = 'DE'; const LOC_LI = 'LI'; const LOC_LU = 'LU'; const LOC_MV = 'MV'; const LOC_GR = 'GR'; const LOC_AU = 'AU'; const LOC_BZ = 'BZ'; const LOC_CA = 'CA'; const LOC_CB = 'CB'; const LOC_IE = 'IE'; const LOC_JM = 'JM'; const LOC_NZ = 'NZ'; const LOC_PH = 'PH'; const LOC_TT = 'TT'; const LOC_US = 'US'; const LOC_ZW = 'ZW'; const LOC_AR = 'AR'; const LOC_BO = 'BO'; const LOC_CL = 'CL'; const LOC_CO = 'CO'; const LOC_CR = 'CR'; const LOC_DO = 'DO'; const LOC_EC = 'EC'; const LOC_GT = 'GT'; const LOC_HN = 'HN'; const LOC_MX = 'MX'; const LOC_NI = 'NI'; const LOC_PA = 'PA'; const LOC_PE = 'PE'; const LOC_PR = 'PR'; const LOC_PY = 'PY'; const LOC_SV = 'SV'; const LOC_UY = 'UY'; const LOC_VE = 'VE'; const LOC_EE = 'EE'; const LOC_IR = 'IR'; const LOC_FI = 'FI'; const LOC_FO = 'FO'; const LOC_BE = 'BE'; const LOC_FR = 'FR'; const LOC_MC = 'MC'; const LOC_IN = 'IN'; const LOC_IL = 'IL'; const LOC_HR = 'HR'; const LOC_HU = 'HU'; const LOC_AM = 'AM'; const LOC_ID = 'ID'; const LOC_IS = 'IS'; const LOC_IT = 'IT'; const LOC_JP = 'JP'; const LOC_GE = 'GE'; const LOC_KZ = 'KZ'; const LOC_KR = 'KR'; const LOC_KG = 'KG'; const LOC_LT = 'LT'; const LOC_LV = 'LV'; const LOC_MK = 'MK'; const LOC_MN = 'MN'; const LOC_BN = 'BN'; const LOC_MY = 'MY'; const LOC_MT = 'MT'; const LOC_NO = 'NO'; const LOC_NL = 'NL'; const LOC_PL = 'PL'; const LOC_BR = 'BR'; const LOC_PT = 'PT'; const LOC_RO = 'RO'; const LOC_RU = 'RU'; const LOC_SE = 'SE'; const LOC_SK = 'SK'; const LOC_SI = 'SI'; const LOC_AL = 'AL'; const LOC_SP = 'SP'; const LOC_KE = 'KE'; const LOC_TH = 'TH'; const LOC_TR = 'TR'; const LOC_UA = 'UA'; const LOC_PK = 'PK'; const LOC_UZ = 'UZ'; const LOC_VN = 'VN'; const LOC_CN = 'CN'; const LOC_HK = 'HK'; const LOC_MO = 'MO'; const LOC_SG = 'SG'; const LOC_TW = 'TW';
    const LANG_AF = 'af'; const LANG_AR = 'ar'; const LANG_AZ = 'az'; const LANG_BE = 'be'; const LANG_BG = 'bg'; const LANG_BS = 'bs'; const LANG_CA = 'ca'; const LANG_CS = 'cs'; const LANG_CY = 'cy'; const LANG_DA = 'da'; const LANG_DE = 'de'; const LANG_DV = 'dv'; const LANG_EL = 'el'; const LANG_EN = 'en'; const LANG_EO = 'eo'; const LANG_ES = 'es'; const LANG_ET = 'et'; const LANG_EU = 'eu'; const LANG_FA = 'fa'; const LANG_FI = 'fi'; const LANG_FO = 'fo'; const LANG_FR = 'fr'; const LANG_GL = 'gl'; const LANG_GU = 'gu'; const LANG_HE = 'he'; const LANG_HI = 'hi'; const LANG_HR = 'hr'; const LANG_HU = 'hu'; const LANG_HY = 'hy'; const LANG_ID = 'id'; const LANG_IS = 'is'; const LANG_IT = 'it'; const LANG_JA = 'ja'; const LANG_KA = 'ka'; const LANG_KK = 'kk'; const LANG_KN = 'kn'; const LANG_KO = 'ko'; const LANG_KOK = 'kok'; const LANG_KY = 'ky'; const LANG_LT = 'lt'; const LANG_LV = 'lv'; const LANG_MI = 'mi'; const LANG_MK = 'mk'; const LANG_MN = 'mn'; const LANG_MR = 'mr'; const LANG_MS = 'ms'; const LANG_MT = 'mt'; const LANG_NB = 'nb'; const LANG_NL = 'nl'; const LANG_NN = 'nn'; const LANG_NS = 'ns'; const LANG_PA = 'pa'; const LANG_PL = 'pl'; const LANG_PS = 'ps'; const LANG_PT = 'pt'; const LANG_QU = 'qu'; const LANG_RO = 'ro'; const LANG_RU = 'ru'; const LANG_SA = 'sa'; const LANG_SE = 'se'; const LANG_SK = 'sk'; const LANG_SL = 'sl'; const LANG_SQ = 'sq'; const LANG_SR = 'sr'; const LANG_SV = 'sv'; const LANG_SW = 'sw'; const LANG_SYR = 'syr'; const LANG_TA = 'ta'; const LANG_TE = 'te'; const LANG_TH = 'th'; const LANG_TL = 'tl'; const LANG_TN = 'tn'; const LANG_TR = 'tr'; const LANG_TT = 'tt'; const LANG_TS = 'ts'; const LANG_UK = 'uk'; const LANG_UR = 'ur'; const LANG_UZ = 'uz'; const LANG_VI = 'vi'; const LANG_XH = 'xh'; const LANG_ZH = 'zh'; const LANG_ZU = 'zu';
}