<?php

namespace console\controllers;

use common\components\ConstDoc;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use Yii;

class MessageController extends \yii\console\controllers\MessageController
{
    public $sourcePathMap = [];

    /**
     * @var string
     */
    public $defaultAction = 'extractMessage';

    /**
     * @var array
     */
    public $overwriteOptions = [];

    /**
     * @param null $configFile
     */
    public function actionExtractMessage($configFile = null)
    {
        if (is_array($this->sourcePathMap) && count($this->sourcePathMap)) {
            foreach ($this->sourcePathMap as $sourcePath => $source) {
                $this->overwriteOptions = [
                    'sourcePath' => $sourcePath
                ];

                if (isset($source['except'])) {
                    $this->overwriteOptions['except'] = $source['except'];
                } else {
                    $this->overwriteOptions['except'] = [];
                }

                if (isset($source['messagePath'])) {
                    $this->overwriteOptions['messagePath'] = $source['messagePath'];
                    $messagePath = Yii::getAlias($this->overwriteOptions['messagePath']);

                    if (!is_dir($messagePath)) {
                        @mkdir($messagePath);
                    }
                }

                $this->actionExtract($configFile);
            }
        } else {
            $this->actionExtract($configFile);
        }

        foreach ($this->extractMessages as $fileName => $params) {
            extract($params);

            $this->__saveMessagesCategoryToPHP($messages, $fileName, $overwrite, $removeUnused, $sort, $category, $markUnused);
        }
    }

    public $extractMessages = [];

    protected function saveMessagesCategoryToPHP($messages, $fileName, $overwrite, $removeUnused, $sort, $category, $markUnused)
    {
        if (!isset($this->extractMessages[$fileName])) {
            $this->extractMessages[$fileName] = [
                'messages' => [],
                'overwrite' => $overwrite,
                'removeUnused' => $removeUnused,
                'sort' => $sort,
                'markUnused' => $markUnused,
                'category' => $category,
            ];
        }

        $this->extractMessages[$fileName]['messages'] = array_merge(
            $this->extractMessages[$fileName]['messages'],
            $messages
        );
    }

    /**
     * @param array $messages
     * @param string $fileName
     * @param bool $overwrite
     * @param bool $removeUnused
     * @param bool $sort
     * @param string $category
     * @param bool $markUnused
     * @return int
     */
    protected function __saveMessagesCategoryToPHP($messages, $fileName, $overwrite, $removeUnused, $sort, $category, $markUnused)
    {
        if (is_file($fileName)) {
            $rawExistingMessages = require($fileName);
            $existingMessages = $rawExistingMessages;
            sort($messages);
            ksort($existingMessages);
            if (array_keys($existingMessages) === $messages && (!$sort || array_keys($rawExistingMessages) === $messages)) {
                $this->stdout("Nothing new in \"$category\" category... Nothing to save.\n\n", Console::FG_GREEN);
                return null;
            }
            unset($rawExistingMessages);
            $merged = [];
            $untranslated = [];
            foreach ($messages as $message) {
                if (array_key_exists($message, $existingMessages) && $existingMessages[$message] !== '') {
                    $merged[$message] = $existingMessages[$message];
                } else {
                    $untranslated[] = $message;
                }
            }
            ksort($merged);
            sort($untranslated);
            $todo = [];
            foreach ($untranslated as $message) {
                $todo[$message] = $message;
            }
            ksort($existingMessages);
            foreach ($existingMessages as $message => $translation) {
                if (!$removeUnused && !isset($merged[$message]) && !isset($todo[$message])) {
                    if (!empty($translation) && (!$markUnused || (strncmp($translation, '@@', 2) === 0 && substr_compare($translation, '@@', -2, 2) === 0))) {
                        $todo[$message] = $translation;
                    } else {
                        $todo[$message] = '@@' . $translation . '@@';
                    }
                }
            }
            $merged = array_merge($todo, $merged);
            if ($sort) {
                ksort($merged);
            }
            if (false === $overwrite) {
                $fileName .= '.merged';
            }
            $this->stdout("Translation merged.\n");
        } else {
            $merged = [];
            foreach ($messages as $message) {
                $merged[$message] = $message;
            }
            ksort($merged);
        }

        $array = VarDumper::export($merged);
        $content = <<<EOD
<?php
/**
 * Message translations.
 *
 * This file is automatically generated by 'yii {$this->id}/{$this->action->id}' command.
 * It contains the localizable messages extracted from source code.
 * You may modify this file by translating the extracted messages.
 *
 * Each array element represents the translation (value) of a message (key).
 * If the value is empty, the message is considered as not translated.
 * Messages that no longer need translation will have their translations
 * enclosed between a pair of '@@' marks.
 *
 * Message string can be used with plural forms format. Check i18n section
 * of the guide for details.
 *
 * NOTE: this file must be saved in UTF-8 encoding.
 */
return $array;

EOD;

        if (file_put_contents($fileName, $content) !== false) {
            $this->stdout("Translation saved.\n\n", Console::FG_GREEN);
            return self::EXIT_CODE_NORMAL;
        } else {
            $this->stdout("Translation was NOT saved.\n\n", Console::FG_RED);
            return self::EXIT_CODE_ERROR;
        }
    }

    /**
     * @return array
     */
    public function getPassedOptionValues()
    {
        return ArrayHelper::merge(
            parent::getPassedOptionValues(),
            $this->overwriteOptions
        );
    }

    /**
     * Extracts messages from a file
     *
     * @param string $fileName name of the file to extract messages from
     * @param string $translator name of the function used to translate messages
     * @param array $ignoreCategories message categories to ignore.
     * This parameter is available since version 2.0.4.
     * @return array
     */
    protected function extractMessages($fileName, $translator, $ignoreCategories = [])
    {
        $coloredFileName = Console::ansiFormat($fileName, [Console::FG_CYAN]);
        $this->stdout("Extracting messages from $coloredFileName...\n");

        $subject = file_get_contents($fileName);
        $messages = [];
        $tokens = token_get_all($subject);
        foreach ((array) $translator as $currentTranslator) {
            $translatorTokens = token_get_all('<?php ' . $currentTranslator);
            array_shift($translatorTokens);
            $messages = array_merge_recursive(
                $messages,
                $this->extractMessagesFromTokens($tokens, $translatorTokens, $ignoreCategories)
            );
            // extra
            $messages = array_merge_recursive(
                $messages,
                $this->extractMessagesFromTokensDocBlock($tokens)
            );
        }

        $this->stdout("\n");

        return $messages;
    }

    /**
     * Extracts messages from a parsed PHP tokens list.
     * @param array $tokens tokens to be processed.
     * @param array $translatorTokens translator tokens.
     * @param array $ignoreCategories message categories to ignore.
     * @return array messages.
     */
    protected function extractMessagesFromTokens(array $tokens, array $translatorTokens, array $ignoreCategories)
    {
        $messages = [];
        $translatorTokensCount = count($translatorTokens);
        $matchedTokensCount = 0;
        $buffer = [];
        $pendingParenthesisCount = 0;

        foreach ($tokens as $token) {
            // finding out translator call
            if ($matchedTokensCount < $translatorTokensCount) {
                if ($this->tokensEqual($token, $translatorTokens[$matchedTokensCount])) {
                    $matchedTokensCount++;
                } else {
                    $matchedTokensCount = 0;
                }
            } elseif ($matchedTokensCount === $translatorTokensCount) {
                // translator found

                // end of function call
                if ($this->tokensEqual(')', $token)) {
                    $pendingParenthesisCount--;

                    if ($pendingParenthesisCount === 0) {
                        // end of translator call or end of something that we can't extract
                        if (isset($buffer[0][0], $buffer[1], $buffer[2][0]) && $buffer[0][0] === T_CONSTANT_ENCAPSED_STRING && $buffer[1] === ',' && $buffer[2][0] === T_CONSTANT_ENCAPSED_STRING) {
                            // is valid call we can extract
                            $category = stripcslashes($buffer[0][1]);
                            $category = mb_substr($category, 1, mb_strlen($category) - 2);

                            if (!$this->isCategoryIgnored($category, $ignoreCategories)) {
                                $message = stripcslashes($buffer[2][1]);
                                $message = mb_substr($message, 1, mb_strlen($message) - 2);

                                $messages[$category][] = $message;
                            }

                            $nestedTokens = array_slice($buffer, 3);
                            if (count($nestedTokens) > $translatorTokensCount) {
                                // search for possible nested translator calls
                                $messages = array_merge_recursive($messages, $this->extractMessagesFromTokens($nestedTokens, $translatorTokens, $ignoreCategories));
                            }
                        } else {
                            // invalid call or dynamic call we can't extract
                            $line = Console::ansiFormat($this->getLine($buffer), [Console::FG_CYAN]);
                            $skipping = Console::ansiFormat('Skipping line', [Console::FG_YELLOW]);
                            $this->stdout("$skipping $line. Make sure both category and message are static strings.\n");
                        }

                        // prepare for the next match
                        $matchedTokensCount = 0;
                        $pendingParenthesisCount = 0;
                        $buffer = [];
                    } else {
                        $buffer[] = $token;
                    }
                } elseif ($this->tokensEqual('(', $token)) {
                    // count beginning of function call, skipping translator beginning
                    if ($pendingParenthesisCount > 0) {
                        $buffer[] = $token;
                    }
                    $pendingParenthesisCount++;
                } elseif (isset($token[0]) && !in_array($token[0], [T_WHITESPACE, T_COMMENT])) {
                    // ignore comments and whitespaces
                    $buffer[] = $token;
                }
            }
        }

        return $messages;
    }

    /**
     * @param $tokens
     * @return array
     */
    private function extractMessagesFromTokensDocBlock($tokens)
    {
        $messages = [];
        $docComments = ConstDoc::parse($tokens);
        foreach ($docComments as $docComment) {
            if (!empty($docComment['comment']) && isset($docComment['params']['message'])) {
                $category = $docComment['params']['message'] ?: 'message';
                $messages[$category][] = $docComment['comment'];
            }
        }
        return $messages;
    }
}