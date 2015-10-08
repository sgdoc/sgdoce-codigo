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
namespace br\gov\sial\core\output\screen\component\html;
use br\gov\sial\core\persist\Persist,
    br\gov\sial\core\persist\query\Entity,
    br\gov\sial\core\output\screen\IBuild,
    br\gov\sial\core\output\screen\html\Form,
    br\gov\sial\core\output\screen\html\Input,
    br\gov\sial\core\output\screen\html\Select,
    br\gov\sial\core\persist\query\QueryAbstract,
    br\gov\sial\core\valueObject\ValueObjectAbstract;

/**
 * SIAL
 *
 * este componente gera um formulario com todos os elemenetos do VO informado, montando inclusive suas
 * dependencia de chave estrageira. por padrao todas as chaves estrangeiras serao tratadas como campos do
 * tipo 'select'. Se a entidade informada estiver preenchida os campos do formulario trara seus respectivos
 * valores preenchidos.
 *
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * @name DSForm
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class DSForm extends Form implements IBuild
{
    /**
     * @var ValueObjectAbstract
     * */
    protected $_source;

    /**
     * @var Persist
     * */
    protected $_executor = NULL;

    /**
     * @var stdClass
     * */
    protected $_FKConfig = NULL;

    /**
     * o primeiro param define a entidade base, ou seja, a enteidade que sera usada para criacao dos campos no
     * formulario, o segundo param define o nome do formulario, o terceiro param define o destino do formulario,
     * o quarto param define informacoes extra dos entendidades de ligacao (foreign key) como por exemplo quais
     * atributos serao utilizados
     *
     * @param ValueObjectAbstract $source
     * @param string $name
     * @param string $action
     * @param stdClass $FKConfig
     * */
    public function __construct (ValueObjectAbstract $source, $name, $action = NULL, DSFormFKConfig $FKConfig = NULL)
    {
        parent::__construct($name, $action);
        $this->_source = $source;
        $this->_FKConfig = $FKConfig;
    }

    /**
     * cria os campos do formulario baseando-se na entidade informada
     *
     * @return DSForm
     * */
    public function factoryField ()
    {
        $fields  = array();
        $columns = $this->_source->annotation()->load();
        $columns = $columns->attrs;

        foreach ($columns as $key => $column) {
            $getter = $column->get;
            $fields[$column->name] = $this->field($column, $this->_source->$getter());
        }
        $this->add($fields);
        return $this;
    }

    /**
     * cria o campo que sera adicionado ao form
     *
     * @return ElementAbstract
     * */
    public function field (\stdClass $info, $value = NULL)
    {
        $field  = NULL;
        $hidden = $this->_FKConfig->isHidden($info->name);

        if (isset($info->foreignKey) && is_string($info->foreignKey) && FALSE == $hidden) {
            # cria campo de referencia externa
            $field = $this->fieldForeign($info->name, Entity::factory($info->foreignKey), $value);
        } else {

            $field = new Input($info->name, $hidden ? 'hidden' : 'text');
            $field->value = $value;
        }

        return $field;
    }

    /**
     * @param string $attrName
     * @param Entity $entity
     * @param string $defaultValue
     * @return ElementAbstract
     * */
    public function fieldForeign ($attrName, Entity $entity, $defaultValue = NULL)
    {
        $info = $this->_FKConfig->get($attrName);
        return new Select(
            $attrName,
            $this->_executor->execute(QueryAbstract::factory($this->_executor->getConfig()->get('driver'), $entity)),
            $info->value,
            $info->label,
            $defaultValue
        );
    }

    /**
     * @param Persist
     * @return DSForm
     * */
    public function setExecutor (Persist $executor)
    {
        $this->_executor = $executor;
        return $this;
    }

    /**
     * @return DSForm
     * */
    public function build ()
    {
        return $this;
    }
}