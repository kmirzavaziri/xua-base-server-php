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
}