<?php
/*
    detectobr v1.0
    Utility to scan php files searching declarations where optional
    parameters are before required ones, which is considered as DEPRECATED
    by php 8 and above.

    Author: Santiago Santos Cortizo
 */

echo 'Searching declarations with optional parameters before required parameters...' . "\n";
echo 'Processed ' . scan('.') . ' files';

function scan(string $path): int
{
    $cuantos = 0;
    $dir = scandir($path);
    foreach ($dir as $entry) {
        if ($entry != '.' && $entry != '..') {
            $file = $path . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($file)) {
                $cuantos += scan($file);
            } else if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) == 'php') {
                $cuantos++;
                $content = file_get_contents($file);
                $lines = explode("\n", $content);
                unset($content);
                scanFile($file, $lines);
            }
        }
    }
    return $cuantos;
}

function scanFile(string $file, array $lines): void
{
    $presented = false;
    $currentLine = 0;
    foreach ($lines as $content) {
        $currentLine++;
        $matches = [];
        preg_match_all('/function\s+\w+\(([^\)]+)\)/i', $content, $matches);
        if ($matches && count($matches) > 1 && !empty($matches[1])) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $params = [];
                preg_match_all('/\s*\w*\s*\.{0,3}[$](\w+?\s*=?\s*)*/i', $matches[1][$i], $params);
                if (!empty($params)) {
                    $optionalDetected = false;
                    foreach ($params[0] as $param) {
                        if (!empty($param) && !empty($param)) {
                            if ($optionalDetected && isRequired($param, true)) {
                                if (!$presented) echo $file . "\n";
                                $presented = true;
                                echo "\t" . '=> ' . $matches[0][$i] . ' (line: ' . $currentLine . ')' . "\n";
                                break;
                            } else if (!isRequired($param)) {
                                $optionalDetected = true;
                            }
                        }
                    }
                }
            }
        }
    }
}

function isRequired(string $param, $excludeNullables = false): bool
{
    $result = false;
    if (strpos($param, '=') === false && strpos($param, '...') === false) {
        $result = true;
    } else if (strpos($param, '...') === false) {
        if (!$excludeNullables) {
            $parts = explode('=', $param);
            $hasType = strpos(trim($parts[0]), ' ') !== false;
            $defaultIsNull = trim(strtolower($parts[1])) == 'null';
            $result = $hasType && $defaultIsNull;
        }
    }
    return $result;
}

function test1(string $data = null, $required): void
{
}    //  Not detected as OBR
function test2($data = null, $required): void
{
}    //  Detected as OBR
function test3($data, $required, int ...$args): void
{
}    //  Not detected as OBR