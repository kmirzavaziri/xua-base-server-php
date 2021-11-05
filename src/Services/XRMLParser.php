<?php

namespace Xua\Core\Services;

use JetBrains\PhpStorm\ArrayShape;
use Xua\Core\Eves\Service;
use Xua\Core\Exceptions\XRMLException;

final class XRMLParser extends Service
{
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PATCH = 'PATCH';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_ = [
        self::METHOD_OPTIONS,
        self::METHOD_GET,
        self::METHOD_POST,
        self::METHOD_PATCH,
        self::METHOD_PUT,
        self::METHOD_DELETE,
    ];

    const STR_BACKSLASH = '\\';
    const STR_SPACE = ' ';

    const INDENT_TYPE_FORBIDDEN_CHAR_MAP = [
        "\t" => " ",
        " " => "\t",
    ];

    const LINE_LEVEL = 'level';
    const LINE_SPACE_COUNT = 'spaceCount';
    const LINE_KEY = 'key';
    const LINE_INTERFACES = 'interfaces';
    const LINE_RAW_LINE_NO = 'rawLineNo';

    const KEY_TYPE = 'type';
    const KEY_NAME = 'name';
    const KEY_FLAGS = 'flags';
    const KEY_KEY = 'key';

    const KEY_TYPE_VAR = 'var';
    const KEY_TYPE_LITERAL = 'literal';
    const KEY_KEY_VAR = ' ';

    private string $methodRegx = '';

    private string $xrml;
    private ?string $indentType = null;
    private array $lines = [];
    private array $spaceCount = [0];

    public function __construct(string $xrml)
    {
        $this->xrml = $xrml;
        $this->methodRegx = implode('|', self::METHOD_);
    }

    public function parse(): array
    {
        $this->parseLinear();
        $parents = [];
        $tree = [];
        foreach ($this->lines as $line) {
            $level = $line[self::LINE_LEVEL];
            if ($level == 0) {
                $parent = &$tree;
            } else {
                $parent = &$parents[$level - 1];
            }

            $key = $line[self::LINE_KEY];

            if (isset($parent[$key[self::KEY_KEY]])) {
                throw (new XRMLException())->setError(
                    $line[self::LINE_RAW_LINE_NO] + 1,
                    $key[self::KEY_KEY] == self::KEY_KEY_VAR
                        ? "Cannot have two variables under same route"
                        : "Cannot have two routes of a same key '{$key[self::KEY_KEY]}' under same route"
                );
            }

            if ($key[self::KEY_KEY] == '') {
                $parent[$key[self::KEY_KEY]] = $line;
            } else {
                $parent[$key[self::KEY_KEY]][''] = $line;
            }
            $parents[$level] = &$parent[$key[self::KEY_KEY]];
        }

        return $tree;
    }

    private function parseLinear(): void
    {
        $rawLines = preg_split('/$\R?^/m', $this->xrml);
        $this->lines = [];
        $i = 0;
        $lineNo = 0;
        while ($i < count($rawLines)) {
            $rawLineNo = $i;
            $line = $this->removeComment($rawLines[$i++]);
            while (str_ends_with($line, self::STR_BACKSLASH)) {
                if (!isset($rawLines[$i + 1])) {
                    throw (new XRMLException())->setError($i + 1, "Unexpected EOF after backslash ('\').");
                }
                $line = substr($line, 0, strlen($line) - 1) . self::STR_SPACE . $this->removeComment($rawLines[$i++]);
            }
            if ($line and !ctype_space($line)) {
                try {
                    $this->lines[] = $this->parseLine($lineNo, $line, $rawLineNo);
                } catch (XRMLException $e) {
                    throw (new XRMLException())->setError($rawLineNo + 1, $e->getMessage());
                }
                $lineNo++;
            }
        }
    }

    /**
     * @throws XRMLException
     */
    #[ArrayShape([self::LINE_LEVEL => 'nt', self::LINE_SPACE_COUNT => 'int', self::LINE_KEY => 'string', self::LINE_INTERFACES => 'string', self::LINE_RAW_LINE_NO => 'int'])]
    private function parseLine(int $lineNo, string $line, int $rawLineNo): array
    {
        preg_match('/^([ \t]*)/', $line, $indent);
        $indent = $indent[0];
        if ($indent) {
            if (!$this->indentType) {
                $this->indentType = $indent[0];
            }
            if (str_contains($indent, self::INDENT_TYPE_FORBIDDEN_CHAR_MAP[$this->indentType])) {
                throw new XRMLException('Inconsistent use of tabs and spaces in indentation.');
            }
        }
        $spaceCount = strlen($indent);
        $line = substr($line, strlen($indent), strlen($line) - strlen($indent));
        $line = explode(' ', $line, 2);
        $key = $this->parseKey($line[0]);
        $interfaces = $this->parseInterfaces($line[1] ?? '');
        $level = $this->calculateLevel($lineNo, $spaceCount);

        if ($key[self::KEY_KEY] == ''  and $level != 0) {
            throw new XRMLException("The empty route indicator ('/') is only allowed in root level.");
        }

        return [
            self::LINE_LEVEL => $level,
            self::LINE_SPACE_COUNT => $spaceCount,
            self::LINE_KEY => $key,
            self::LINE_INTERFACES => $interfaces,
            self::LINE_RAW_LINE_NO => $rawLineNo,
        ];
    }

    /**
     * @throws XRMLException
     */
    private function calculateLevel(int $lineNo, int $spaceCount): int
    {
        if ($lineNo == 0) {
            if ($spaceCount != 0) {
                throw new XRMLException('Starting line cannot be indented.');
            }
            return 0;
        } else {
            $lastLine = $this->lines[$lineNo - 1];

            if ($spaceCount < $lastLine[self::LINE_SPACE_COUNT]) {
                for ($i = 0; $i < $lastLine[self::LINE_LEVEL]; $i++) {
                    if ($this->spaceCount[$i] == $spaceCount) {
                        return $i;
                    }
                }
                throw new XRMLException('Inconsistent indent.');
            }

            if ($spaceCount > $lastLine[self::LINE_SPACE_COUNT]) {
                $level = $lastLine[self::LINE_LEVEL] + 1;
                $this->spaceCount[$level] = $spaceCount;
                return $level;
            }

            return $lastLine[self::LINE_LEVEL];
        }
    }

    private function removeComment(string $line): string
    {
        return strstr($line, '#', true) ?: $line;
    }

    private function parseKey(string $keyText): array
    {
        $keyText = trim($keyText, '/');

        if (strlen($keyText) > 2 and $keyText[0] == '{' and str_ends_with($keyText, '}')) {
            $keyData = explode('|', substr($keyText, 1, strlen($keyText) - 2));
            $name = array_shift($keyData);
            $flags = [];
            foreach ($keyData as $flagName) {
                $flags[$flagName] = true;
            }
            return [
                self::KEY_TYPE => self::KEY_TYPE_VAR,
                self::KEY_KEY => self::KEY_KEY_VAR,
                self::KEY_NAME => $name,
                self::KEY_FLAGS => $flags,
            ];
        }

        return [
            self::KEY_TYPE => self::KEY_TYPE_LITERAL,
            self::KEY_KEY => $keyText,
            self::KEY_NAME => '',
            self::KEY_FLAGS => [],
        ];
    }

    private function parseInterfaces(string $interfacesText) : array
    {
        if (!$interfacesText) {
            return [];
        }

        $result = [];
        $interfaces = preg_split('/\s+/', ltrim($interfacesText));
        foreach ($interfaces as $interface) {
            $pattern = '/((' . $this->methodRegx . ')\(([^)(]*)\))|([^)(]*)/';
            preg_match($pattern, $interface,$matches);
            $count = count($matches);
            if ($matches[$count - 2]) {
                $result[$matches[$count - 2]] = $matches[$count - 1];
            } else {
                foreach (self::METHOD_ as $method) {
                    $result[$method] =  $matches[$count - 1];
                }
            }
        }
        return $result;
    }
}