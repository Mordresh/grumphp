<?php

namespace GrumPHP\Formatter;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

/**
 * Class PhpCsFixerFormatter
 *
 * @package GrumPHP\Formatter
 */
class PhpCsFixerFormatter implements ProcessFormatterInterface
{
    /**
     * @var int
     */
    private $counter = 0;

    /**
     * Resets the internal counter.
     */
    public function resetCounter()
    {
        $this->counter = 0;
    }

    /**
     * @param Process $process
     *
     * @return string
     */
    public function format(Process $process)
    {
        $output = $process->getOutput();
        if (!$output) {
            return $process->getErrorOutput();
        }

        if (!$json = json_decode($output, true)) {
            return $output;
        }

        return $this->formatJsonResponse($json);
    }

    /**
     * @param Process $process
     *
     * @return string
     */
    public function formatSuggestion(Process $process)
    {
        $pattern = '%s ';

        $dryrun = sprintf($pattern, ProcessUtils::escapeArgument('--dry-run'));
        $formatJson = sprintf($pattern, ProcessUtils::escapeArgument('--format=json'));

        return str_replace([$dryrun, $formatJson], '', $process->getCommandLine());
    }

    /**
     * @param array $messages
     * @param array $suggestions
     *
     * @return string
     */
    public function formatErrorMessage(array $messages, array $suggestions)
    {
        return sprintf(
            '%sYou can fix all errors by running following commands:%s',
            implode(PHP_EOL, $messages) . PHP_EOL . PHP_EOL,
            PHP_EOL . implode(PHP_EOL, $suggestions)
        );
    }

    /**
     * @param array $json
     *
     * @return string
     */
    private function formatJsonResponse(array $json)
    {
        $formatted = [];
        foreach ($json['files'] as $file) {
            if (!is_array($file) || !isset($file['name'])) {
                $formatted[] = 'Invalid file: ' . print_r($file, true);
                continue;
            }

            $formatted[] = $this->formatFile($file);
        }

        return implode(PHP_EOL, $formatted);
    }

    /**
     * @param array $file
     *
     * @return string
     */
    private function formatFile(array $file)
    {
        if (!isset($file['name'])) {
            return 'Invalid file: ' . print_r($file, true);
        }

        $hasFixers = isset($file['appliedFixers']);

        return sprintf(
            '%s) %s%s',
            ++$this->counter,
            $file['name'],
            $hasFixers ? ' (' . implode(',', $file['appliedFixers']) . ')' : ''
        );
    }
}
