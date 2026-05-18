<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Http\UploadedFile;
use Smalot\PdfParser\Parser as PdfParser;
use ZipArchive;

class DocumentTextExtractor
{
    public function extractFromUpload(UploadedFile $file): array
    {
        $ext = strtolower((string) $file->getClientOriginalExtension());

        if (in_array($ext, ['ppt', 'xls'], true)) {
            throw new \RuntimeException('This file type needs to be saved as .pptx or .xlsx first, then upload again.');
        }

        if (! in_array($ext, ['pdf', 'pptx', 'xlsx', 'txt'], true)) {
            throw new \RuntimeException('Unsupported file type. Please upload a PDF, PPTX, XLSX, or TXT file.');
        }

        $text = $ext === 'txt'
            ? (string) $file->get()
            : $this->extractTextFromDocument($file, $ext);

        return [
            'extension' => $ext,
            'text' => $this->normalizeText($text),
        ];
    }

    public function normalizeText(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = trim((string) preg_replace('/[ \t]+/', ' ', $text));
        $text = str_replace("\0", '', $text);

        $maxChars = 200000;
        if (mb_strlen($text) > $maxChars) {
            $text = rtrim(mb_substr($text, 0, $maxChars))."\n\n...[truncated]";
        }

        return trim($text);
    }

    private function extractTextFromDocument(UploadedFile $file, string $ext): string
    {
        $path = $file->getPathname();

        if ($ext === 'pdf') {
            $text = '';

            try {
                $parser = new PdfParser;
                $pdf = $parser->parseFile($path);

                $text = (string) $pdf->getText();
                $normalized = trim((string) preg_replace('/[ \t]+/', ' ', str_replace(["\r\n", "\r"], "\n", $text)));

                if ($normalized !== '') {
                    return $text;
                }
            } catch (\Throwable $e) {
                // Some valid PDFs cannot be decoded by the text parser; OCR may still read them.
            }

            try {
                return $this->extractTextFromPdfOcr($path);
            } catch (\RuntimeException $e) {
                if (trim($text) !== '') {
                    return $text;
                }

                throw $e;
            }
        }

        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('Unable to read the uploaded file.');
        }

        try {
            if ($ext === 'pptx') {
                return $this->extractTextFromPptxZip($zip);
            }

            if ($ext === 'xlsx') {
                return $this->extractTextFromXlsxZip($zip);
            }

            throw new \RuntimeException('Unsupported file type.');
        } finally {
            $zip->close();
        }
    }

    private function extractTextFromPptxZip(ZipArchive $zip): string
    {
        $slideFiles = [];
        $notesFiles = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = (string) $zip->getNameIndex($i);
            if (preg_match('#^ppt/slides/slide[0-9]+\.xml$#i', $name)) {
                $slideFiles[] = $name;
            } elseif (preg_match('#^ppt/notesSlides/notesSlide[0-9]+\.xml$#i', $name)) {
                $notesFiles[] = $name;
            }
        }

        usort($slideFiles, 'strnatcmp');
        usort($notesFiles, 'strnatcmp');

        $chunks = [];
        foreach (array_merge($slideFiles, $notesFiles) as $fileName) {
            $xml = $zip->getFromName($fileName);
            if (! is_string($xml) || $xml === '') {
                continue;
            }

            $lines = $this->extractTextNodesFromXml($xml);
            if ($lines !== []) {
                $chunks[] = implode(' ', $lines);
            }
        }

        $text = implode("\n\n", $chunks);
        $ocr = trim($this->tryOcrImagesFromZip($zip, 'ppt/media/'));

        return $ocr !== '' && trim($text) !== '' ? trim($text)."\n\n".$ocr : ($ocr !== '' ? $ocr : $text);
    }

    private function extractTextFromXlsxZip(ZipArchive $zip): string
    {
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');

        if (is_string($sharedStringsXml) && $sharedStringsXml !== '') {
            $doc = new DOMDocument;
            if (@$doc->loadXML($sharedStringsXml, LIBXML_NOERROR | LIBXML_NOWARNING) !== false) {
                $xp = new DOMXPath($doc);
                $siNodes = $xp->query('//*[local-name()="si"]');
                if ($siNodes) {
                    foreach ($siNodes as $si) {
                        $tNodes = $xp->query('.//*[local-name()="t"]', $si);
                        $text = '';
                        if ($tNodes) {
                            foreach ($tNodes as $t) {
                                $text .= (string) $t->textContent;
                            }
                        }
                        $sharedStrings[] = $text;
                    }
                }
            }
        }

        $sheetFiles = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = (string) $zip->getNameIndex($i);
            if (preg_match('#^xl/worksheets/sheet[0-9]+\.xml$#i', $name)) {
                $sheetFiles[] = $name;
            }
        }
        usort($sheetFiles, 'strnatcmp');

        $out = [];
        foreach ($sheetFiles as $fileName) {
            $xml = $zip->getFromName($fileName);
            if (! is_string($xml) || $xml === '') {
                continue;
            }

            $doc = new DOMDocument;
            if (@$doc->loadXML($xml, LIBXML_NOERROR | LIBXML_NOWARNING) === false) {
                continue;
            }

            $xp = new DOMXPath($doc);
            $rowNodes = $xp->query('//*[local-name()="row"]');
            if (! $rowNodes) {
                continue;
            }

            $lines = [];
            foreach ($rowNodes as $row) {
                $cellNodes = $xp->query('.//*[local-name()="c"]', $row);
                if (! $cellNodes) {
                    continue;
                }

                $rowValues = [];
                foreach ($cellNodes as $cell) {
                    $type = (string) $cell->attributes?->getNamedItem('t')?->nodeValue;
                    $vNode = $xp->query('./*[local-name()="v"]', $cell)?->item(0);
                    $isNode = $xp->query('./*[local-name()="is"]', $cell)?->item(0);

                    if ($type === 's' && $vNode) {
                        $idx = (int) $vNode->textContent;
                        $rowValues[] = $sharedStrings[$idx] ?? '';
                    } elseif ($type === 'inlineStr' && $isNode) {
                        $tNodes = $xp->query('.//*[local-name()="t"]', $isNode);
                        $text = '';
                        if ($tNodes) {
                            foreach ($tNodes as $t) {
                                $text .= (string) $t->textContent;
                            }
                        }
                        $rowValues[] = $text;
                    } elseif ($vNode) {
                        $rowValues[] = (string) $vNode->textContent;
                    } else {
                        $rowValues[] = '';
                    }
                }

                $line = trim(implode("\t", array_map(static fn ($value) => trim((string) $value), $rowValues)));
                if ($line !== '') {
                    $lines[] = $line;
                }
            }

            if ($lines !== []) {
                $out[] = implode("\n", $lines);
            }
        }

        $text = implode("\n\n", $out);
        $ocr = trim($this->tryOcrImagesFromZip($zip, 'xl/media/'));

        return $ocr !== '' && trim($text) !== '' ? trim($text)."\n\n".$ocr : ($ocr !== '' ? $ocr : $text);
    }

    private function extractTextNodesFromXml(string $xml): array
    {
        $doc = new DOMDocument;
        if (@$doc->loadXML($xml, LIBXML_NOERROR | LIBXML_NOWARNING) === false) {
            return [];
        }

        $xp = new DOMXPath($doc);
        $nodes = $xp->query('//*[local-name()="t"]');
        if (! $nodes) {
            return [];
        }

        $out = [];
        foreach ($nodes as $node) {
            $value = trim((string) $node->textContent);
            if ($value !== '') {
                $out[] = $value;
            }
        }

        return $out;
    }

    private function extractTextFromPdfOcr(string $pdfPath): string
    {
        $tesseract = $this->resolveExecutable(trim((string) env('TESSERACT_BIN', 'tesseract')), $this->defaultTesseractCandidates());
        $pdftoppm = $this->resolveExecutable(trim((string) env('PDFTOPPM_BIN', 'pdftoppm')), $this->defaultPdftoppmCandidates());
        $lang = trim((string) env('OCR_LANG', 'eng'));
        $dpi = (int) env('OCR_DPI', 200);
        $maxPages = (int) env('OCR_MAX_PAGES', 12);

        if (! $this->canRunExecutable($tesseract, ['--version'])) {
            throw new \RuntimeException('OCR requires Tesseract to be installed. Set TESSERACT_BIN to the full path of tesseract.exe.');
        }

        $tmpDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'dilg_ocr_'.bin2hex(random_bytes(8));
        if (! @mkdir($tmpDir, 0700, true) && ! is_dir($tmpDir)) {
            throw new \RuntimeException('Unable to create temp directory.');
        }

        $prefix = $tmpDir.DIRECTORY_SEPARATOR.'page';

        try {
            $images = [];

            if (extension_loaded('imagick')) {
                try {
                    $images = $this->renderPdfToImagesUsingImagick($pdfPath, $prefix, $dpi, $maxPages);
                } catch (\Throwable $e) {
                    $images = [];
                }
            }

            if ($images === []) {
                if (! $this->canRunExecutable($pdftoppm, ['-v'])) {
                    throw new \RuntimeException('OCR requires Poppler (pdftoppm) to be installed. Set PDFTOPPM_BIN to the full path of pdftoppm.exe.');
                }

                $this->runCommand([$pdftoppm, '-png', '-r', (string) $dpi, $pdfPath, $prefix], 300);

                $images = glob($prefix.'-*.png') ?: [];
                usort($images, 'strnatcmp');
                if ($maxPages > 0) {
                    $images = array_slice($images, 0, $maxPages);
                }
            }

            $chunks = [];
            foreach ($images as $img) {
                $out = $this->runCommand([$tesseract, $img, 'stdout', '-l', $lang], 300);
                $txt = trim((string) $out);
                if ($txt !== '') {
                    $chunks[] = $txt;
                }
            }

            return implode("\n\n", $chunks);
        } finally {
            $this->deleteDirectory($tmpDir);
        }
    }

    private function tryOcrImagesFromZip(ZipArchive $zip, string $prefix): string
    {
        $tesseract = $this->resolveExecutable(trim((string) env('TESSERACT_BIN', 'tesseract')), $this->defaultTesseractCandidates());
        if ($tesseract === '' || ! $this->canRunExecutable($tesseract, ['--version'])) {
            return '';
        }

        $lang = trim((string) env('OCR_LANG', 'eng'));
        $maxImages = (int) env('OCR_MAX_IMAGES', 25);

        $imageFiles = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = (string) $zip->getNameIndex($i);
            if (! str_starts_with(strtolower($name), strtolower($prefix))) {
                continue;
            }
            if (preg_match('/\.(png|jpe?g|bmp|tiff?)$/i', $name)) {
                $imageFiles[] = $name;
            }
        }

        if ($imageFiles === []) {
            return '';
        }

        usort($imageFiles, 'strnatcmp');
        if ($maxImages > 0) {
            $imageFiles = array_slice($imageFiles, 0, $maxImages);
        }

        $tmpDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'dilg_ocr_img_'.bin2hex(random_bytes(8));
        if (! @mkdir($tmpDir, 0700, true) && ! is_dir($tmpDir)) {
            return '';
        }

        try {
            $chunks = [];
            foreach ($imageFiles as $idx => $zipName) {
                $bytes = $zip->getFromName($zipName);
                if (! is_string($bytes) || $bytes === '') {
                    continue;
                }

                $ext = strtolower(pathinfo($zipName, PATHINFO_EXTENSION));
                $tmpPath = $tmpDir.DIRECTORY_SEPARATOR.'img_'.($idx + 1).'.'.$ext;
                if (@file_put_contents($tmpPath, $bytes) === false) {
                    continue;
                }

                try {
                    $out = $this->runCommand([$tesseract, $tmpPath, 'stdout', '-l', $lang], 300);
                    $txt = trim((string) $out);
                    if ($txt !== '') {
                        $chunks[] = $txt;
                    }
                } catch (\Throwable $e) {
                }
            }

            return implode("\n\n", $chunks);
        } finally {
            $this->deleteDirectory($tmpDir);
        }
    }

    private function runCommand(array $command, int $timeoutSeconds): string
    {
        $disableFunctions = strtolower((string) ini_get('disable_functions'));
        if (! function_exists('proc_open') || str_contains($disableFunctions, 'proc_open')) {
            throw new \RuntimeException('proc_open is disabled in PHP. Enable proc_open (and related proc_* functions) in php.ini disable_functions.');
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes);
        if (! is_resource($process)) {
            throw new \RuntimeException('Unable to start process.');
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $stdout = '';
        $stderr = '';
        $start = microtime(true);

        while (true) {
            $status = proc_get_status($process);
            $stdout .= stream_get_contents($pipes[1]);
            $stderr .= stream_get_contents($pipes[2]);

            if (! $status['running']) {
                break;
            }

            if ((microtime(true) - $start) > $timeoutSeconds) {
                proc_terminate($process);
                throw new \RuntimeException('Process timed out.');
            }

            usleep(25000);
        }

        $stdout .= stream_get_contents($pipes[1]);
        $stderr .= stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);
        if ($exitCode !== 0) {
            throw new \RuntimeException(trim($stderr) === '' ? 'Process failed.' : trim($stderr));
        }

        return $stdout;
    }

    private function canRunExecutable(string $executable, array $args): bool
    {
        if ($executable === '') {
            return false;
        }

        if (is_file($executable)) {
            return true;
        }

        try {
            $this->runCommand(array_merge([$executable], $args), 5);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function resolveExecutable(string $configuredValue, array $candidates): string
    {
        $configuredValue = trim($configuredValue);
        if ($configuredValue !== '') {
            if (is_file($configuredValue)) {
                return $configuredValue;
            }
            if ($this->canRunExecutable($configuredValue, ['--version']) || $this->canRunExecutable($configuredValue, ['-v'])) {
                return $configuredValue;
            }
        }

        foreach ($candidates as $candidate) {
            $candidate = trim((string) $candidate);
            if ($candidate !== '' && is_file($candidate)) {
                return $candidate;
            }
        }

        return $configuredValue;
    }

    private function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir($item->getPathname());
            } else {
                @unlink($item->getPathname());
            }
        }

        @rmdir($dir);
    }

    private function defaultTesseractCandidates(): array
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return [];
        }

        $programFiles = (string) getenv('ProgramFiles');
        $programFilesX86 = (string) getenv('ProgramFiles(x86)');
        $localAppData = (string) getenv('LOCALAPPDATA');
        $userProfile = (string) getenv('USERPROFILE');
        $globs = glob('C:\\Users\\*\\AppData\\Local\\Microsoft\\WinGet\\Links\\tesseract.exe') ?: [];
        $globs = array_merge($globs, glob('C:\\Users\\*\\AppData\\Local\\Programs\\Tesseract-OCR\\tesseract.exe') ?: []);

        return array_values(array_filter(array_merge([
            $programFiles ? $programFiles.'\\Tesseract-OCR\\tesseract.exe' : null,
            $programFilesX86 ? $programFilesX86.'\\Tesseract-OCR\\tesseract.exe' : null,
            $localAppData ? $localAppData.'\\Programs\\Tesseract-OCR\\tesseract.exe' : null,
            $localAppData ? $localAppData.'\\Microsoft\\WinGet\\Links\\tesseract.exe' : null,
            'C:\\ProgramData\\chocolatey\\bin\\tesseract.exe',
            $userProfile ? $userProfile.'\\scoop\\apps\\tesseract\\current\\tesseract.exe' : null,
        ], $globs), static fn ($value) => is_string($value) && $value !== ''));
    }

    private function defaultPdftoppmCandidates(): array
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return [];
        }

        $programFiles = (string) getenv('ProgramFiles');
        $programFilesX86 = (string) getenv('ProgramFiles(x86)');
        $localAppData = (string) getenv('LOCALAPPDATA');
        $userProfile = (string) getenv('USERPROFILE');

        $globs = [];
        if ($programFiles) {
            $globs = array_merge($globs, glob($programFiles.'\\poppler*\\Library\\bin\\pdftoppm.exe') ?: []);
        }
        if ($programFilesX86) {
            $globs = array_merge($globs, glob($programFilesX86.'\\poppler*\\Library\\bin\\pdftoppm.exe') ?: []);
        }
        if ($localAppData) {
            $globs = array_merge($globs, glob($localAppData.'\\Microsoft\\WinGet\\Packages\\oschwartz10612.Poppler_*\\Release-*\\Library\\bin\\pdftoppm.exe') ?: []);
            $globs = array_merge($globs, glob($localAppData.'\\Microsoft\\WinGet\\Packages\\oschwartz10612.Poppler_*\\*\\Library\\bin\\pdftoppm.exe') ?: []);
        }
        $globs = array_merge($globs, glob('C:\\Users\\*\\AppData\\Local\\Microsoft\\WinGet\\Links\\pdftoppm.exe') ?: []);

        return array_values(array_filter(array_merge($globs, [
            $localAppData ? $localAppData.'\\Microsoft\\WinGet\\Links\\pdftoppm.exe' : null,
            'C:\\ProgramData\\chocolatey\\bin\\pdftoppm.exe',
            $userProfile ? $userProfile.'\\scoop\\apps\\poppler\\current\\Library\\bin\\pdftoppm.exe' : null,
        ]), static fn ($value) => is_string($value) && $value !== ''));
    }

    private function renderPdfToImagesUsingImagick(string $pdfPath, string $prefix, int $dpi, int $maxPages): array
    {
        $count = $maxPages > 0 ? $maxPages : 12;
        $imagick = new \Imagick;
        $imagick->setResolution($dpi, $dpi);
        $imagick->readImage($pdfPath.'[0-'.($count - 1).']');

        $out = [];
        $i = 0;
        foreach ($imagick as $page) {
            $i++;
            $page->setImageFormat('png');
            $path = $prefix.'-'.$i.'.png';
            $page->writeImage($path);
            $out[] = $path;
        }

        $imagick->clear();
        $imagick->destroy();

        return $out;
    }
}
