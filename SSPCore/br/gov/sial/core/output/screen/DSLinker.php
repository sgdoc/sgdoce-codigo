<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */
namespace br\gov\sial\core\output\screen;
use br\gov\sial\core\util\Config,
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\persist\Persist,
    br\gov\sial\core\persist\query\Entity,
    br\gov\sial\core\persist\PersistConfig,
    br\gov\sial\core\persist\query\QueryAbstract,
    br\gov\sial\core\persist\DSLinkerReferenceable;

/**
 * SIAL
 *
 * este componente tem por finalidade ligar uma fonte de dados
 * a um componente (DataSourceElement)
 *
 * @package br.gov.sial.core.output
 * @subpackage screen
 * @name DSLinker
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class DSLinker extends SIALAbstract implements \Iterator
{
    /**
     * @var integer
     * */
    private $_key = 0;

    /**
     * status inicial do cursor
     * NOTE: Note que o resultado inicializado pelo construtor
     *
     * @var mixed
     * */
    private $_cursor = FALSE;

    /**
     * armazena o resultado da pesquisa
     *
     * @var mixed
     * */
    private $_result;

    /**
     * o primeiro param pode ser uma Query ou um objeto Entity. Se uma Entity for informado, então Config
     * torna-se-á obrigatório devido a necessidade de estabelecer a conexao com o respositorio de dados.
     * a fonte de dados poderá ser informado por meio de terceiro para ou omitido para ser utilizado
     * a fonte de dados padrao.
     *
     * @param DSLinkerReferenceable $target
     * @param Config $config
     * @param string $dsName
     * */
    public function __construct (DSLinkerReferenceable $target, Config $config = NULL, $dsName  = 'default')
    {
        # se o target for uma referencia para Entity ou Query entao sera
        # necessario um objeto executor de consulta Persist
        if ($target instanceof Entity || $target instanceof QueryAbstract) {
            $info   = $config->get($config->get($dsName));
            $target = QueryAbstract::factory($info->get('driver'), $target);

            PersistConfig::registerConfigs($config->toArray());
            $pConfig = $configPersist = PersistConfig::factory($config->get($dsName));

            $namespace = sprintf('br\gov\sial\core\persist\%s\Persist', $pConfig->get('adapter'));
            $executor = Persist::factory($namespace, $pConfig);
            $target = $executor->execute($target);
        }

        $this->_result = $target;

        /* necessario para tornar o */
        $this->next();
    }

    /**
     * @return stdClass
     * */
    public function row ()
    {
        $content = $this->current();
        $this->next();
        return $content;
    }

    /**
     * @return stdClass
     * */
    public function current ()
    {
        return $this->_cursor;
    }

    /**
     * @return stdClass
     * */
    public function next()
    {
        $this->_key++;
        $this->_cursor = $this->_result->fetch();
    }

    /**
     * @return integer
     * */
    public function key ()
    {
        return $this->_key;
    }

    /**
     * @return boolean
     * */
    public function valid ()
    {
        return (boolean) $this->_cursor;
    }

    /**
     * @return NULL
     * */
    public function rewind ()
    {
        /* implementado apenas por imposicao da interface::Iterator */
    }
}