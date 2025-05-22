<?php
/**
 * PHP Token 2016
 * @author https://gist.github.com/grom358/6722419
 */
class Token {
    public $type;
    public $text;

    public function __construct($type, $text) {
        $this->type = $type;
        $this->text = $text;
    }

    static public function typeName($type) {
        if (is_string($type)) {
            return $type;
        } else {
            return token_name($type);
        }
    }

    public function getTypeName() {
        return self::typeName($this->type);
    }

    public function __toString() {
        return $this->getTypeName() . ':' . $this->text;
    }
}

/**
 * Format PHP code
 */
class Formatter {
    /**
     * Token types that indicate the start of an expression
     */
    private $startExpressionTypes = array(
        T_AND_EQUAL,
        T_ARRAY_CAST,
        T_BOOLEAN_AND,
        T_BOOLEAN_OR,
        T_BOOL_CAST,
        T_BREAK,
        T_CASE,
        T_CLONE,
        T_CONCAT_EQUAL,
        T_CONTINUE,
        T_DEC,
        T_DIV_EQUAL,
        T_DOUBLE_ARROW,
        T_DOUBLE_CAST,
        T_ECHO,
        T_EXIT,
        T_INC,
        T_INT_CAST,
        T_IS_EQUAL,
        T_IS_GREATER_OR_EQUAL,
        T_IS_IDENTICAL,
        T_IS_NOT_EQUAL,
        T_IS_NOT_IDENTICAL,
        T_IS_SMALLER_OR_EQUAL,
        T_LOGICAL_AND,
        T_LOGICAL_OR,
        T_LOGICAL_XOR,
        T_MINUS_EQUAL,
        T_MOD_EQUAL,
        T_MUL_EQUAL,
        T_OBJECT_CAST,
        T_OPEN_TAG,
        T_OPEN_TAG_WITH_ECHO,
        T_OR_EQUAL,
        T_PLUS_EQUAL,
        T_RETURN,
        T_SL,
        T_SL_EQUAL,
        T_SR,
        T_SR_EQUAL,
        T_STRING_CAST,
        T_THROW,
        T_UNSET_CAST,
        T_XOR_EQUAL,
        T_YIELD,
        ';',
        '=',
        '-',
        '+',
        '*',
        '/',
        '%',
        '.',
        '{',
        '&',
        '|',
    );

    /**
     * Token types that are variable/function modifiers
     */
    private $modifierTypes = array(
        T_ABSTRACT,
        T_PRIVATE,
        T_PUBLIC,
        T_PROTECTED,
        T_STATIC,
        T_FINAL,
    );

    /**
     * Token types that start a new scope
     */
    private $scopeTypes = array(
        T_SWITCH,
        T_WHILE,
        T_IF,
        T_FOR,
        T_FOREACH,
        T_DO,
        T_FUNCTION,
        T_CLASS,
        T_TRY,
        T_DECLARE,
    );

    /**
     * Control token types that allow for alternative syntax
     */
    private $startAltTypes = array(
        T_IF,
        T_WHILE,
        T_FOR,
        T_FOREACH,
        T_SWITCH,
        T_DECLARE,
    );

    /**
     * Corresponding token types that end alternative syntax control structure
     */
    private $endAltTypes = array(
        T_ENDIF,
        T_ENDWHILE,
        T_ENDFOR,
        T_ENDFOREACH,
        T_ENDSWITCH,
        T_ENDDECLARE,
    );

    /**
     * Token types with optional expression
     */
    private $optionalExpressionTypes = array(
        T_RETURN,
        T_CONTINUE,
        T_BREAK,
        T_YIELD,
    );

    /**
     * Token types to force as statements
     */
    private $statementTypes = array(
        T_INCLUDE,
        T_INCLUDE_ONCE,
        T_REQUIRE,
        T_REQUIRE_ONCE,
        T_RETURN,
        T_CONTINUE,
        T_BREAK,
        T_YIELD,
        T_ECHO,
    );

    /**
     * Token types that indicate the possible start of a function
     */
    private $startFunctionTypes = array(
        T_FUNCTION,
        T_ABSTRACT,
        T_PRIVATE,
        T_PUBLIC,
        T_PROTECTED,
        T_STATIC,
        T_FINAL,
    );

    /**
     * Token types to insert a space before
     */
    private $spaceBefore = array('{');

    /**
     * Token types to insert a space after
     */
    private $spaceAfter = array(
        T_ARRAY_CAST,
        T_BOOL_CAST,
        T_CALLABLE,
        T_CASE,
        T_CATCH,
        T_CLONE,
        T_CONCAT_EQUAL,
        T_CONST,
        T_DECLARE,
        T_DIV_EQUAL,
        T_DO,
        T_ECHO,
        T_ELSE,
        T_ELSEIF,
        T_EXTENDS,
        T_FINALLY,
        T_FOR,
        T_FOREACH,
        T_GLOBAL,
        T_GOTO,
        T_IF,
        T_IMPLEMENTS,
        T_INCLUDE,
        T_INCLUDE_ONCE,
        T_INT_CAST,
        T_INTERFACE,
        T_NAMESPACE,
        T_NEW,
        T_OBJECT_CAST,
        T_STRING_CAST,
        T_SWITCH,
        T_THROW,
        T_TRAIT,
        T_TRY,
        T_UNSET_CAST,
        T_USE,
        T_VAR,
        T_WHILE,
        T_INCLUDE,
        T_INCLUDE_ONCE,
        T_REQUIRE,
        T_REQUIRE_ONCE,
        T_FUNCTION,
        T_CLASS,
        T_RETURN,
        T_BREAK,
        T_CONTINUE,
        T_YIELD,
        ',',
        ';',
        '}',
    );

    /**
     * Token types to insert a space before and after
     */
    private $spaceEnclosed = array(
        T_AND_EQUAL,
        T_AS,
        T_BOOLEAN_AND,
        T_BOOLEAN_OR,
        T_CONCAT_EQUAL,
        T_DOUBLE_ARROW,
        T_DIV_EQUAL,
        T_IS_EQUAL,
        T_IS_GREATER_OR_EQUAL,
        T_IS_IDENTICAL,
        T_IS_NOT_EQUAL,
        T_IS_NOT_IDENTICAL,
        T_IS_SMALLER_OR_EQUAL,
        T_LOGICAL_AND,
        T_LOGICAL_OR,
        T_LOGICAL_XOR,
        T_MINUS_EQUAL,
        T_MOD_EQUAL,
        T_MUL_EQUAL,
        T_OR_EQUAL,
        T_PLUS_EQUAL,
        T_SL,
        T_SL_EQUAL,
        T_SR,
        T_SR_EQUAL,
        T_XOR_EQUAL,
        '=',
        '.',
        '+',
        '-',
        '*',
        '/',
        '%',
        '<',
        '>',
        '?',
        ':',
        '&',
        '|',
    );

    /**
     * Token types that require extra indentation when a linebreak is encountered
     */
    private $extraIndentTypes = array(
        T_LOGICAL_AND,
        T_LOGICAL_OR,
        T_LOGICAL_XOR,
        T_BOOLEAN_AND,
        T_BOOLEAN_OR,
        T_OBJECT_OPERATOR,
        '=',
        '.',
        '+',
        '-',
        '*',
        '/',
        '%',
        '&',
        '|',
    );

    /**
     * Variable/function modifier keyword order
     */
    private $modifierOrder = array(
        'abstract',
        'static',
        'final',
        'public',
        'protected',
        'private',
    );

    /**
     * Preserve linebreaks
     */
    private $preserveLinebreaks = TRUE;

    /**
     * Preserve blank lines
     */
    private $preserveBlankLines = TRUE;

    /**
     * Collapse blank lines into single blank line
     */
    private $collapseBlankLines = TRUE;

    /**
     * Indent to use
     */
    private $indent = '    ';

    /**
     * Newline character to insert
     */
    private $newline = "\n";

    /**
     * Token types to start on a newline
     */
    private $newlineBefore = array();

    /**
     * Token types to track brace for
     */
    private $braceTypes = array(
        T_CLASS,
        T_FUNCTION,
        T_TRY,
        T_CATCH,
        T_FINALLY,
        T_IF,
        T_ELSE,
        T_ELSEIF,
        T_FOR,
        T_FOREACH,
        T_WHILE,
        T_SWITCH,
        T_DO,
    );

    /**
     * Token types from $braceTypes to start beginning '{' on newline
     */
    private $braceNewline = array();

    /**
     * Tokens from tokenizer
     */
    private $tokens;

    /**
     * Number of tokens
     */
    private $tokenCount;

    /**
     * Output buffer
     */
    private $output;

    /**
     * Last brace token type encountered
     */
    private $braceType;

    /**
     * Whether a blank line was preserved
     */
    private $blankLinePreserved;

    /**
     * True if the whitespace token only contains linear whitespace
     */
    private $linearWhitespace;

    /**
     * Used to flag that a space needs to be inserted
     */
    private $insertSpace;

    /**
     * Used to flag that a newline needs to be inserted
     */
    private $insertNewline;

    /**
     * True if output position at start of line
     */
    private $insertIndent;

    /**
     * Type of previous non whitespace token
     */
    private $previousType;

    /**
     * Type of next non whitespace token
     */
    private $nextType;

    /**
     * Variable/function modifiers. Eg. public, final, static etc
     */
    private $keywords;

    /**
     * True if at start of an expression
     */
    private $startExpression;

    /**
     * True if outside of PHP code block
     */
    private $escaped;

    /**
     * Token type to end escape mode with
     */
    private $escapeEndOnType;

    /**
     * True if the open tag was an open with echo. Eg <?=
     */
    private $echoTag;

    /**
     * Current indentation level
     */
    private $indentLevel;

    /**
     * Open parenthesis '(' count
     */
    private $parenCount;

    /**
     * Open bracket '[' count
     */
    private $bracketCount;

    /**
     * The scope that was last removed from the stack
     */
    private $previousScope;

    /**
     * The current scope
     */
    private $currentScope;

    /**
     * Stack of current scopes
     */
    private $scopeStack;

    /**
     * The paren count when statement token was encountered
     */
    private $statementParenCount;

    /**
     * Last alternative token type encountered
     */
    private $altType;

    /**
     * True if processing a case/default statement
     */
    private $isCase;

    /**
     * True if an extra indent is required when indenting the line
     */
    private $extraIndent;

    /**
     * Used to track the start and end of array()
     */
    private $arrayParenStack;

    /**
     * Used to track the start and end of [] arrays
     */
    private $arrayBracketStack;

    /**
     * Used to track if optional expression was given
     */
    private $isOptionalExpression;

    public function __construct($config = array()) {
        foreach ($config as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * Get the current indent
     */
    private function getIndent() {
        if ($this->indentLevel < 0) {
            echo $this->output;
            die('error indent');
        }
        $indentLevel = $this->indentLevel;
        if ($this->extraIndent) {
            $indentLevel++;
        }
        return str_repeat($this->indent, $indentLevel);
    }

    /**
     * Get the member/method keywords in the correct order
     */
    private function getKeywords() {
        $modifiers = '';
        foreach ($this->modifierOrder as $keyword) {
            if (in_array($keyword, $this->keywords)) {
                $modifiers .= $keyword . ' ';
            }
        }
        $this->keywords = array();
        return $modifiers;
    }

    /**
     * Standardize format of token from token_get_all
     */
    static private function parseToken($token) {
        if (is_array($token)) {
            $type = $token[0];
            $text = $token[1];
        } else {
            $type = $token;
            $text = $token;
        }
        return new Token($type, $text);
    }

    /**
     * Get tokens from source code
     */
    private function getTokens($source) {
        $tokens = array();
        foreach (token_get_all($source) as $rawToken) {
            $tokens[] = self::parseToken($rawToken);
        }

        // Fold 'else if' into 'elseif'
        $n = count($tokens) - 2;
        for ($i = 0; $i < $n; $i++) {
            if ($tokens[$i]->type === T_ELSE && $tokens[$i + 1]->type === T_WHITESPACE && $tokens[$i + 2]->type === T_IF) {
                array_splice($tokens, $i, 3, array(new Token(T_ELSEIF, 'elseif')));
            }
        }

        $this->tokens = $tokens;
        $this->tokenCount = count($tokens);
    }

    /**
     * Pop scope from stack
     */
    private function popScope() {
        $this->previousScope = array_pop($this->scopeStack);
        $len = count($this->scopeStack);
        $this->currentScope = &$this->scopeStack[$len - 1];
    }

    /**
     * Push a new scope onto the stack and make it the current scope
     */
    private function pushScope($scope) {
        $this->scopeStack[] = $scope;
        $len = count($this->scopeStack);
        $this->currentScope = &$this->scopeStack[$len - 1];
    }

    /**
     * Initalize formatter for formatting source code
     */
    private function init($source) {
        $this->getTokens($source);
        $this->output = '';
        $this->indentLevel = 0;
        $this->parenCount = 0;
        $this->bracketCount = 0;
        $this->escaped = TRUE;
        $this->blankLinePreserved = FALSE;
        $this->linearWhitespace = FALSE;
        $this->insertSpace = FALSE;
        $this->insertNewline = FALSE;
        $this->insertIndent = TRUE;
        $this->extraIndent = FALSE;
        $this->previousType = NULL;
        $this->keywords = array();
        $this->startExpression = FALSE;
        $this->escaped = TRUE;
        $this->escapeEndOnType = NULL;
        $this->echoTag = FALSE;
        $globalScope = array('type' => T_GLOBAL, 'indentLevel' => -1);
        $this->previousScope = $globalScope;
        $this->scopeStack = array($globalScope);
        $this->currentScope = &$this->scopeStack[0];
        $this->statementParenCount = -1;
        $this->altType = NULL;
        $this->braceType = NULL;
        $this->isCase = FALSE;
        $this->arrayParenStack = array();
        $this->arrayBracketStack = array();
        $this->isOptionalExpression = FALSE;
    }

    private function write($str) {
        $this->output .= $str;
    }

    private function writeNewline() {
        $this->output .= $this->newline;
        $this->insertIndent = TRUE;
        $this->insertSpace = FALSE;
        $this->insertNewline = FALSE;
    }

    /**
     * Decrease indent and check if end of current scope
     */
    private function decreaseIndent() {
        $this->indentLevel--;
        if ($this->currentScope['type'] === T_SWITCH) {
            $this->indentLevel--;
        }
        if ($this->indentLevel === $this->currentScope['indentLevel']) {
            $this->popScope();
        }
    }

    /**
     * Process a token from the stream
     */
    private function processToken($token) {
        // Cache token properties
        $type = $token->type;
        $text = $token->text;

        // PHP open tag ends escaped mode
        if ($type === T_OPEN_TAG || $type === T_OPEN_TAG_WITH_ECHO) {
            $this->escaped = FALSE;
            $this->echoTag = ($type === T_OPEN_TAG_WITH_ECHO);
            $this->write(trim($text));
            if ($this->echoTag) {
                $this->insertNewline = FALSE;
            } else {
                $this->writeNewline();
            }
            return;
        }

        // PHP close tag begins escaped mode
        if ($type === T_CLOSE_TAG) {
            $this->escaped = TRUE;
            if (!$this->echoTag) {
                // Insert explicit semicolon if required
                if ($this->previousType !== NULL && $this->previousType !== '}' && $this->previousType !== '{' && $this->previousType !== ';') {
                    $this->write(';');
                }
                $this->write(' ');
            }
            $this->write($text);
            return;
        }

        // Don't format anything while escaped
        if ($this->escaped) {
            if ($type === $this->escapeEndOnType) {
                $this->escaped = FALSE;
                $this->escapeEndOnType = NULL;
            }
            $this->write($text);
            return;
        }

        // Escape mode on heredoc
        if ($type === T_START_HEREDOC) {
            $this->escaped = TRUE;
            $this->escapeEndOnType = T_END_HEREDOC;
        }

        // Escape mode on "
        if ($type === '"') {
            $this->escaped = TRUE;
            $this->escapeEndOnType = '"';
        }

        // Handle whitespace
        if ($type === T_WHITESPACE) {
            $nlCount = substr_count($text, "\n");
            $this->blankLinePreserved = FALSE;
            $this->linearWhitespace = FALSE;
            if ($nlCount > 0) {
                $this->insertNewline = TRUE; // Mark to insert a newline
                // Preserve blank lines
                if ($nlCount > 1) {
                    if ($this->collapseBlankLines) {
                        $nlCount = 2;
                    }
                    $this->write(str_repeat($this->newline, $nlCount - 1));
                    $this->blankLinePreserved = TRUE;
                }
            } else {
                $this->linearWhitespace = TRUE;
            }
            return;
        }

        // Count parens
        if ($type === '(') {
            $this->parenCount++;
            $this->indentLevel++;
        } elseif ($type === ')') {
            $this->parenCount--;
            $this->indentLevel--;
        }

        // Count brackets
        if ($type === '[') {
            $this->bracketCount++;
            $this->indentLevel++;
        } elseif ($type === ']') {
            $this->bracketCount--;
            $this->indentLevel--;
        }

        // Start of array()
        if ($type === T_ARRAY) {
            $this->arrayParenStack[] = $this->parenCount;
        }
        // End of array()
        if ($type === ')' && !empty($this->arrayParenStack) && $this->parenCount === $this->arrayParenStack[count($this->arrayParenStack) - 1]) {
            // Insert , on last element of array
            if ($this->previousType !== ',' && $this->insertNewline) {
                $this->write(',');
            }
            array_pop($this->arrayParenStack);
        }

        // Start of [] array
        if ($this->startExpression && $type === '[') {
            $this->arrayBracketStack[] = $this->bracketCount - 1;
        }
        if ($type === ']' && !empty($this->arrayBracketStack) && $this->bracketCount === $this->arrayBracketStack[count($this->arrayBracketStack) - 1]) {
            // Insert , on last element of array
            if ($this->previousType !== ',' && $this->insertNewline) {
                $this->write(',');
            }
            array_pop($this->arrayBracketStack);
        }

        if ($type === T_ELSE || $type === T_ELSEIF) {
            // Continue if scope on else/elseif
            if ($this->previousScope['type'] === T_IF) {
                $scope = $this->previousScope;
                unset($scope['close']);
                $this->pushScope($scope);
            }

            // Handle newlines for else/elseif
            if (array_key_exists('alt', $this->currentScope)) {
                $this->insertNewline = !$this->insertIndent;
            } else {
                $this->insertNewline = in_array($type, $this->braceNewline);
            }
        }

        if ($type === T_CATCH || $type === T_FINALLY) {
            $this->insertNewline = in_array($type, $this->braceNewline);
        }

        // Handle alternative syntax for control structures
        if ($type === T_ELSE || $type === T_ELSEIF) {
            if (array_key_exists('alt', $this->currentScope)) {
                $this->indentLevel--;
            }
            $this->altType = $type;
        } elseif ($this->altType === NULL && in_array($type, $this->startAltTypes)) {
            if ($this->previousType === '}' && $type === T_WHILE && $this->previousScope['type'] === T_DO) {
                // Ignore while that is part of do/while
                $this->insertNewline = FALSE;
            } else {
                $this->altType = $type;
            }
        } elseif ($this->altType === T_ELSE || ($this->altType !== NULL && $this->parenCount === 0 && $this->previousType === ')' && $type !== ')')) {
            // Extra indent on T_SWITCH
            if ($this->altType === T_SWITCH) {
                $this->indentLevel++;
            }
            $this->altType = NULL;
            if ($type === ':') {
                $this->indentLevel++;
                $this->insertNewline = TRUE;
                $this->currentScope['alt'] = TRUE;
                $this->write(':');
                return;
            } elseif ($type !== '{') {
                // Ignore any preserved linebreaks as inserting { triggers
                // insertion of newlines
                $this->insertNewline = FALSE;
                //$this->write("@{$this->parenCount}@");
                $this->processToken(new Token('{', '{')); // Insert {
                $this->currentScope['close'] = TRUE; // Mark scope only contains a single statement
            }
        } elseif (in_array($type, $this->endAltTypes)) {
            $this->decreaseIndent();
        }

        // New scope
        if (in_array($type, $this->scopeTypes)) {
            $this->pushScope(array('type' => $type, 'indentLevel' => $this->indentLevel));
        }

        // Handle case/default
        if ($type === T_CASE || $type === T_DEFAULT) {
            $this->isCase = TRUE;
            $this->indentLevel--;
        } elseif ($this->isCase && $type === ':') {
            $this->isCase = FALSE;
            $this->indentLevel++;
            $this->insertNewline = TRUE;
            $this->write(':');
            return;
        }

        // Insert newline for the required brace types
        if (in_array($type, $this->braceTypes)) {
            $this->braceType = $type;
        }
        if ($type === '{' && in_array($this->braceType, $this->braceNewline)) {
            $this->insertNewline = TRUE;
        }

        // Flag to insert space before
        if (in_array($type, $this->spaceBefore) || in_array($type, $this->spaceEnclosed)) {
            if (!($this->startExpression && ($type === '+' || $type === '-'))) {
                $this->insertSpace = TRUE;
            }
        } elseif ($type === ';') {
            if ($this->isOptionalExpression) {
                $this->insertSpace = FALSE;
            }
        }

        if ($type === T_COMMENT && $this->linearWhitespace && $this->previousType !== T_COMMENT) {
            $this->insertNewline = FALSE;
            $this->insertSpace = TRUE;
        }

        // Handle insertion of newline
        if ($this->insertNewline) {
            $this->writeNewline();
        }

        // Seperate functions with blank lines
        if (!$this->blankLinePreserved) {
            $isComment = ($type === T_COMMENT || $type === T_DOC_COMMENT);
            $followsFunction = ($this->previousScope['type'] === T_FUNCTION && $this->previousType === '}');
            if ($isComment && $followsFunction) {
                $this->writeNewline();
            }
            $isFunction = in_array($type, $this->startFunctionTypes);
            if ($isFunction && $followsFunction) {
                $this->writeNewline();
            }
        }

        // Save variable/method modifiers
        if (in_array($type, $this->modifierTypes)) {
            $this->keywords[] = $text;
            return;
        }

        // } decreases indent
        if ($type === '}') {
            $this->decreaseIndent();
        }

        // Indent start of line
        if ($this->insertIndent) {
            $this->extraIndent = in_array($this->previousType, $this->extraIndentTypes) || in_array($type, $this->extraIndentTypes);
            $this->write($this->getIndent());
            $this->insertIndent = FALSE;
        }

        // { increases indent
        if ($type === '{') {
            $this->indentLevel++;
        }

        // Track if optional expression was supplied
        if (in_array($type, $this->optionalExpressionTypes)) {
            $this->isOptionalExpression = TRUE;
        } elseif ($this->isOptionalExpression && $type !== ';') {
            $this->isOptionalExpression = FALSE;
        }

        // Convert statement functions into statements
        if ($type === '(' && in_array($this->previousType, $this->statementTypes)) {
            $this->statementParenCount = $this->parenCount - 1;
            return '';
        }
        if ($type === ')' && $this->statementParenCount === $this->parenCount) {
            $this->statementParenCount = -1;
            return '';
        }

        // Insert space
        if ($this->insertSpace) {
            $this->write(' ');
            $this->insertSpace = FALSE;
        }

        // Flag to insert space after
        if (in_array($type, $this->spaceAfter) || in_array($type, $this->spaceEnclosed)) {
            if (!($this->startExpression && ($type === '+' || $type === '-'))) {
                $this->insertSpace = TRUE;
            }
        } elseif ($type === ';') {
            if ($this->isOptionalExpression) {
                $this->insertSpace = FALSE;
            }
        }

        // Track if at start of expression
        if (!$this->startExpression) {
            if ($type === T_INC || $type === T_DEC) {
                $this->startExpression = FALSE;
            } elseif (in_array($type, $this->startExpressionTypes)) {
                $this->startExpression = TRUE;
            }
        } else {
            if (!in_array($type, $this->startExpressionTypes)) {
                $this->startExpression = FALSE;
            }
        }

        // ; inside for have spaces after them inside of newline
        if ($type === ';' && $this->currentScope['type'] === T_FOR && $this->parenCount > 0) {
            $this->insertNewline = FALSE;
            $this->insertSpace = TRUE;
        } elseif ($type === ';' || $type === '{' || $type === '}') {
            $this->insertNewline = TRUE;
        }

        // Restore variable/method modifiers
        if ($type === T_VARIABLE || $type === T_FUNCTION) {
            $this->write($this->getKeywords());
        }

        // Reformat comments
        if ($type === T_COMMENT || $type === T_DOC_COMMENT) {
            if ($text[0] === '#') {
                $comment = rtrim(substr($text, 1));
                $text = '//' . $comment;
                $this->insertNewline = TRUE;
            } elseif (substr($text, 0, 2) === '//') {
                $comment = rtrim(substr($text, 2));
                $text = '//' . $comment;
                $this->insertNewline = TRUE;
            } else {
                $comment = '';
                $lines = explode("\n", $text);
                foreach ($lines as $i => $line) {
                    $line = trim($line);
                    if ($i > 0) {
                        if (!empty($line[0]) && $line[0] === '*') {
                            $line = $this->newline . $this->getIndent() . ' ' . $line;
                        } else {
                            $line = $this->newline . $line;
                        }
                    }
                    $comment .= $line;
                }
                $text = $comment;
            }
        }

        if ($type === T_STRING) {
            $keyword = strtolower($text);
            if ($keyword === 'true' || $keyword === 'false' || $keyword === 'null') {
                // Uppercase true/false/null
                $text = strtoupper($text);
            } elseif ($this->previousType === '[' && strtoupper($text) !== $text && $this->nextType === ']') {
                // Non-constant array key should be quoted. Assume constants are in uppercase
                $text = "'" . $text . "'";
            } elseif ($this->parenCount === 1 && ($this->previousType === '(' || $this->previousType === ',') && ($this->nextType === T_VARIABLE || $this->nextType === '&')) {
                // Is parameter class type
                $this->insertSpace = TRUE;
            }
        }

        // Write token text
        $this->write($text);

        if ($type !== T_WHITESPACE) {
            $this->blankLinePreserved = FALSE;
            $this->linearWhitespace = FALSE;
        }

        // Insert } for single statement control structures
        if ($this->parenCount === 0 && ($type === ';' || $type === '}') && array_key_exists('close', $this->currentScope)) {
            $this->processToken(new Token('}', '}'));
        }
    }

    /**
     * Format source code
     */
    public function format($source) {
        $this->init($source);
        for ($i = 0; $i < $this->tokenCount; $i++) {
            $token = $this->tokens[$i];
            // Ignore whitespace at end of file
            if ($token->type === T_WHITESPACE && $i === $this->tokenCount - 1) {
                break;
            }
            $nextToken = NULL;
            if ($i + 1 < $this->tokenCount) {
                $nextToken = $this->tokens[$i + 1];
                if ($nextToken->type === T_WHITESPACE) {
                    if ($i + 2 < $this->tokenCount) {
                        $nextToken = $this->tokens[$i + 2];
                    } else {
                        $nextToken = NULL;
                    }
                }
            }
            if ($nextToken !== NULL) {
                $this->nextType = $nextToken->type;
            }
            $this->processToken($token);
            if ($token->type !== T_WHITESPACE) {
                $this->previousType = $token->type;
            }
        }
        $this->output .= $this->newline;
        return $this->output;
    }
}
