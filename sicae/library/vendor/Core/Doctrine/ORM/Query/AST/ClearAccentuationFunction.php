<?php
use Doctrine\ORM\Query\Lexer,
    Doctrine\ORM\Query\AST\Functions\FunctionNode;

class Core_Doctrine_ORM_Query_AST_ClearAccentuationFunction extends FunctionNode
{
    /**
     * @override
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        $platform = $sqlWalker->getConnection()->getDatabasePlatform();

        if (!method_exists($platform, 'getClearAccetuationExpression')) {
            return $sqlWalker->walkStringPrimary($this->firstStringPrimary);
        }

        return $platform->getClearAccetuationExpression(
                $sqlWalker->walkStringPrimary($this->firstStringPrimary)
        );
    }

    /**
     * @override
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->firstStringPrimary = $parser->StringPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
