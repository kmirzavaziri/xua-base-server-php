<?php

namespace Services;

use XUA\Service;
use function imagecreatefrombmp;
use function imagecreatefromgif;
use function imagecreatefromjpeg;
use function imagecreatefrompng;

abstract class Mime extends Service
{
    const MIME_TEXT_PLAIN = 'text/plain';
    const MIME_TEXT_HTML = 'text/html';
    const MIME_TEXT_CSS = 'text/css';

    const MIME_IMAGE_PNG = 'image/png';
    const MIME_IMAGE_JPEG = 'image/jpeg';
    const MIME_IMAGE_GIF = 'image/gif';
    const MIME_IMAGE_BMP = 'image/bmp';

    const MIME_VIDEO_X_FLV = 'video/x-flv';
    const MIME_VIDEO_QUICKTIME = 'video/quicktime';
    const MIME_VIDEO_MP4 = 'video/mp4';

    const MIME_AUDIO_MPEG = 'audio/mpeg';

    const MIME_APPLICATION_JAVASCRIPT = 'application/javascript';
    const MIME_APPLICATION_JSON = 'application/json';
    const MIME_APPLICATION_XML = 'application/xml';
    const MIME_APPLICATION_X_SHOCKWAVE_FLASH = 'application/x-shockwave-flash';
    const MIME_APPLICATION_ZIP = 'application/zip';
    const MIME_APPLICATION_X_RAR_COMPRESSED = 'application/x-rar-compressed';
    const MIME_APPLICATION_X_MSDOWNLOAD = 'application/x-msdownload';
    const MIME_APPLICATION_VND_MS_CAB_COMPRESSED = 'application/vnd.ms-cab-compressed';
    const MIME_APPLICATION_POSTSCRIPT = 'application/postscript';
    const MIME_APPLICATION_MSWORD = 'application/msword';
    const MIME_APPLICATION_RTF = 'application/rtf';
    const MIME_APPLICATION_VND_MS_EXCEL = 'application/vnd.ms-excel';
    const MIME_APPLICATION_VND_MS_POWERPOINT = 'application/vnd.ms-powerpoint';
    const MIME_APPLICATION_VND_OASIS_OPENDOCUMENT_TEXT = 'application/vnd.oasis.opendocument.text';
    const MIME_APPLICATION_VND_OASIS_OPENDOCUMENT_SPREADSHEET = 'application/vnd.oasis.opendocument.spreadsheet';
    const MIME_APPLICATION_PDF = 'application/pdf';

    const MIME_TEXT = [
        self::MIME_TEXT_PLAIN,
        self::MIME_TEXT_HTML,
        self::MIME_TEXT_CSS,
    ];

    const MIME_IMAGE = [
        self::MIME_IMAGE_PNG,
        self::MIME_IMAGE_JPEG,
        self::MIME_IMAGE_GIF,
        self::MIME_IMAGE_BMP,
    ];

    const MIME_VIDEO = [
        self::MIME_VIDEO_X_FLV,
        self::MIME_VIDEO_QUICKTIME,
        self::MIME_VIDEO_MP4,
    ];

    const MIME_AUDIO = [
        self::MIME_AUDIO_MPEG
    ];

    const MIME_APPLICATION = [
        self::MIME_APPLICATION_JAVASCRIPT,
        self::MIME_APPLICATION_JSON,
        self::MIME_APPLICATION_XML,
        self::MIME_APPLICATION_X_SHOCKWAVE_FLASH,
        self::MIME_APPLICATION_ZIP,
        self::MIME_APPLICATION_X_RAR_COMPRESSED,
        self::MIME_APPLICATION_X_MSDOWNLOAD,
        self::MIME_APPLICATION_VND_MS_CAB_COMPRESSED,
        self::MIME_APPLICATION_POSTSCRIPT,
        self::MIME_APPLICATION_MSWORD,
        self::MIME_APPLICATION_RTF,
        self::MIME_APPLICATION_VND_MS_EXCEL,
        self::MIME_APPLICATION_VND_MS_POWERPOINT,
        self::MIME_APPLICATION_VND_OASIS_OPENDOCUMENT_TEXT,
        self::MIME_APPLICATION_VND_OASIS_OPENDOCUMENT_SPREADSHEET,
        self::MIME_APPLICATION_PDF,
    ];
}