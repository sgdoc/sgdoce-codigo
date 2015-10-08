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
namespace br\gov\sial\core\valueObject;
use \RecursiveArrayIterator,
    \RecursiveIteratorIterator,
    br\gov\sial\core\lang\TFile,
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\Location,
    br\gov\sial\core\persist\util\Annotation,
    br\gov\sial\core\valueObject\exception\ValueObjectException;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage valueObject
 * @author J. Augusto <augustowebd@gmail.com>
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
abstract class ValueObjectAbstract extends SIALAbstract
{
    /**
     * @var strin
     * */
    const T_VALUEOBJECT_OPERATION_REQUIRE_SOME_OBJECT = 'A operação requer algum objeto';

    /**
     * @var
     * */
    const T_VALUEOBJECT_IMPOSSIBLE_DEFINE_NAMESPACE = 'Impossível definir o namespace do objeto informado, certifique-se que o mesmo seja uma class MVCB';

    /**
     * @var string
     * */
    const T_VALUEOBJECT_UNAVALIABLE = 'ValueObject %s indisponível, certifique-se que a mesma já tenha sido criada ou seu namespace esteja correto';

    /**
     * @var cache do ValueObject
     * */
    private $_annon;

    /**
     * @var boolean
     * */
    private $_isEmpty = NULL;

    /**
     * construtor
     * */
    public function __construct ()
    {
        $this->annotation()->cache();
    }

    /**
     * @param string $name
     * @param mixed[] $arguments
     * @return mixed
     * */
    public function __call($name, $arguments)
    {
        $attr = substr($name, 3);
        $attr[0] = strtolower($attr[0]);

        switch (substr($name, 0, 3)) {
            case 'get':
                return $this->$attr;
                break;

            case 'set':
                $this->$attr = $arguments;
                break;

            default:
                parent::__call($name, $arguments);
                break;
        }
    }

    /**
     * verifica a existencia do attr na anotacao, tambem, fica reponsavel por
     * verificar se o attr possui referencia para outro ValueObject, foreignKey.
     * caso o attr solicitado nao exista, também, na anotacao, uma exception
     * sera lancada.
     *
     * @param string $attr
     * @throws ValueObjectException
     * */
    public function __get ($attr)
    {
        $value  = NULL;
        $getter = $this->getter($attr);

        # prioriza o uso do getter
        if ($getter) {
            $value = $this->$getter();
        } else {
            $translatendAttr = self::attrTranslateName($attr);

            # note que se o attr nao existe, a superclasse lancara um
            # exception, tal qual ja eh feito
            if (!isset($this->$translatendAttr)) {
                parent::__get($attr);
            }

            $value = $this->$translatendAttr;

            # verifica se o attr em questao trata-se de uma chave-estrangeira
            $attrAnnon = $this->annotation()->load()->attrs;
            $foreignKey = isset($attrAnnon->$attr->foreignKey) ? $attrAnnon->$attr->foreignKey : NULL;

            if ($foreignKey) {

                # converte namespace de valueObject para o namespace capaz
                # de recuperar os dados da chave-estrangeira
                $namespace = explode('valueObject', $foreignKey->refer);
                $namespace[1]  = str_replace('ValueObject', 'Business', $namespace[1]);
                $namespace[0] .=  'mvcb' . self::NAMESPACE_SEPARATOR;
                $namespace = implode('business', $namespace);
                $value = $namespace::factory()->find($value);
            }
        }

        return $value;
    }

    /**
     * atribui o valor para o attr que nao existe explicitamente no codigo,
     * mas que existe na anotacao, bem como na exitade a qual o VO representa
     *
     * @param string $attr
     * @param mixed $value
     * @throws ValueObjectException
     * */
    public function __set ($attr, $value)
    {
        $setter = $this->setter($attr);

        # prioriza o uso do setter
        if ($setter) {
            $this->$setter($value);
        } else {
            $attr = self::attrTranslateName($attr);
            $this->$attr = $value;
        }
    }

    /**
     * verifica se o atributo informado existe
     * sera ver
     *
     * */

    /**
     * retorna o nome do acessor getter
     *
     * @param string $atter
     * */
    public function getter ($attr)
    {
        $getter = NULL;

        $attrs     = $this->annotation()->load()->attrs;
        $attr      = '_' == $attr[0] ? substr($attr, 1) : $attr;
        $attrAnnon = isset($attrs->$attr) ? $attrs->$attr : NULL;

        # verifica se existe um metodo setter para o attr em questao
        if ($attrAnnon && isset($attrAnnon->get)) {
            # verifica se foi definido um getter pela anotacao
            $getter = $attrAnnon->get;
        } else {

            # verifica se existe um getter, mesmo sem anotacao
            $getter = 'get' . ucfirst($attr);

            # conforma a existencia do getter, se este nao existe
            # anula o nome do getter assumino que o mesmo nao existe
            if (!$this->hasMethod($getter)) {
                $getter = NULL;
            }
        }
    }

    /**
     * retorna o nome do acessor setter
     *
     * @param string $atter
     * */
    public function setter ($attr)
    {
        $setter    = NULL;
        $attrs     = $this->annotation()->load()->attrs;
        $attr      = '_' == $attr[0] ? substr($attr, 1) : $attr;
        $attrAnnon = isset($attrs->$attr) ? $attrs->$attr : NULL;

        # verifica se existe um metodo setter para o attr em questao
        if ($attrAnnon && isset($attrAnnon->set)) {
            # verifica se foi definido um getter pela anotacao
            $setter = $attrAnnon->set;
        } else {

            # verifica se existe um getter, mesmo sem anotacao
            $setter = 'set' . ucfirst($attr);

            # conforma a existencia do getter, se este nao existe
            # anula o nome do getter assumino que o mesmo nao existe
            if (!$this->hasMethod($setter)) {
                $setter = NULL;
            }
        }

        return $setter;
    }

    /**
     * limita o conteudo do attr no limite informado
     * se o param informado for NULL nada sera feito e o NULL sera retornado
     *
     * @param string $content
     * @param integer $limite
     * @param integer $start
     * */
    public function substr ($content, $limit, $start = 0)
    {
        if (!$content) {
            return NULL;
        }

        return substr(trim($content), $start, $limit);
    }

    /**
     * @return Annotation
     * */
    public final function annotation ()
    {
        if (NULL == $this->_annon) {
            $this->_annon = new Annotation($this);
        }

        return $this->_annon;
    }

   /**
     * copia o conteudo, dos atributos avaliados como vazio do objeto atual, do valueObjet informado
     *
     * Nota: Este metodo só funciona com objetos de mesmo tipo
     *
     * @param ValueObjectAbstract $source
     * @return ValueObjectAbstract
     * */
    public function copySaveObjectData (self $source)
    {
        $ttype = $this->annotation();
        $tdata = $this->toArray();
        $sdata = $source->toArray();

        ValueObjectException::throwsExceptionIfParamIsNull(
            $source->annotation()->getClassName() == $ttype->getClassName(),
            self::T_VALUEOBJECT_OPERATION_REQUIRE_SOME_OBJECT
        );

        # copia os dados da origem para destino
        foreach ($ttype->getAttrsDoc() as $attr) {
            $name   = $attr['name'];
            $setter = $attr['set'];

            # quando o valor for um array isso indica uma chave estrangeira, assim, pega-se a
            # mesma chave e busca no array.
            if (is_array($sdata[$name])) {
                $sdata[$name] = isset($attr['foreingKeyAlias']) ? $sdata[$name][$attr['foreingKeyAlias']]
                                                                : $sdata[$name][$name];
            }

            # se attr local estiver vazio entao preenche-o com o
            # dado do valueObject informado
            if (is_array($tdata[$name])) {
                $tdata[$name] = isset($attr['foreingKeyAlias']) ? $tdata[$name][$attr['foreingKeyAlias']]
                                                                : $tdata[$name] = $tdata[$name][$name];
            }

            # preenche o attr deste VO se o attr estiver vazio
            if (('boolean' != gettype($tdata[$name])) && '' == $tdata[$name] || ('boolean' != gettype($tdata[$name])) && FALSE === $tdata[$name]) {
                $this->$setter($sdata[$name]);
            }
        }

        return $this;
    }

    /**
     * calcula o hash do valueObject
     *
     * @return string
     * */
    public function hash ()
    {
        return md5(json_encode($this->toArray()));
    }

    /**
     * carrega os dados do VO com base no array informado
     * o criterio para definicao do carregamento de dados eh o nome de cada
     * indice do array informado seja igual a seu respectivo metodo acesso(set) sem o prefixo 'set'.
     * ex: nome  --(casa com)--> setNome
     *     ativo --(casa com)--> setAtivo
     * @example ValueObjectAbstract::loadData
     * @code
     *     $arr = array('nome' => 'Nome', 'ativo' => 't');
     *     $valueObject->loadData($arr);
     *     # ira retornar o valueObject preenchido quando os parametros do VO coincidirem com as chaves do array
     *     # ...
     *     # [_nome:br\gov\icmbio\yyy\zzz\system\module\valueObject\ModuleValueObject:private] => Nome
     *     # [_ativo:br\gov\icmbio\yyy\zzz\system\module\valueObject\ModuleValueObject:private] => t
     *     # ...
     * @endcode
     * @param string[]
     * @return ValueObjectAbstract
     * */
    public final function loadData (array $data = array())
    {
        $attrs = $this->annotation()->load()->attrs;

        foreach ($data as $attr => $value) {
            $attr = self::attrTranslateName($attr);
            $this->$attr = $value;
        }

        return $this;
    }

    /**
     * ajusta o nome do attr informado para ser compativel com o padrao SIAL
     *
     * @param string $name
     * @return string
     * */
    public function attrTranslateName ($name)
    {
        $name = implode(array_map(function ($frag) {
            return ucfirst($frag);
        }, explode('_', $name)));

        $name[0] = strtolower($name[0]);

        return "_{$name}";
    }

    /**
     * @return boolean
     * */
    public function isEmpty ()
    {
        if (NULL === $this->_isEmpty) {
            $data = $this->toArray();
            $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($this->toArray()));

            foreach ($iterator as $element => $value) {
                if (!empty($value)) {
                    $this->_isEmpty = FALSE;
                }
            }
        }

        return (NULL === $this->_isEmpty) ? TRUE : $this->_isEmpty;
    }

    /**
     * @return string[]
     * */
    public function toArray ()
    {
        $values = array();
        $stack  = array();
        $attrs  = (array) $this->annotation()->load()->attrs;

        foreach ($attrs as $attr) {
            $get    = $attr->get;
            $result = $this->$get();

            if ($result instanceof self) {
                $result = $result->toArray();
            }

            $values[$attr->name] = $result;
        }

        return $values;
    }

    /**
     * @return string
     * */
    public function toJson ()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return string
     * */
    public function toXml (array $data = NULL, $inner = FALSE)
    {
        if (!$inner) {
            $data = $this->toArray();
        }

        $output = null;
        foreach ((array) $data as $node => $value) {
            $output .= sprintf('<%1$s>%2$s</%1$s>', $node, is_array($value) ? $this->toXml($value, TRUE) : $value);
        }

        if (!$inner) {
            $output = sprintf('<%1$s>%2$s</%1$s>', $this->annotation()->load()->entity, $output);
        }

        return $output;
    }

    /**
     * @return string
     * */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * @param string|ModelAbstract|null $namespace
     * @param stdClass $data
     * @return ValueObjectAbstract
     * */
    public static function factory ($namespace = NULL, $data = NULL)
    {
        $data = $data ?: new \stdClass();

        if (NULL === $namespace) {
            $namespace = get_called_class();
        }

        if ($namespace instanceof parent) {
            $namespace = $namespace->getClassName();
        }

       if (FALSE === stripos($namespace, sprintf('%1$svalueObject%1$s', self::NAMESPACE_SEPARATOR)) && $namespace != 'br\gov\sial\core\lang\TFile') {
            $sep = sprintf('%1$smvcb%1$s', self::NAMESPACE_SEPARATOR);
            ValueObjectException::throwsExceptionIfParamIsNull(!(FALSE === stripos($namespace, $sep)), self::T_VALUEOBJECT_IMPOSSIBLE_DEFINE_NAMESPACE);
            $namespace  = current(explode($sep, $namespace));
            $namespace  = sprintf('%1$s%2$svalueObject%2$s%3$s', $namespace, self::NAMESPACE_SEPARATOR , ucfirst(end(explode(self::NAMESPACE_SEPARATOR, $namespace))) . 'ValueObject');
            ValueObjectException::throwsExceptionIfParamIsNull(Location::hasClassInNamespace($namespace), sprintf(self::T_VALUEOBJECT_UNAVALIABLE, $namespace));
        }

        $valueObject = new $namespace;
        $valueObject->loadData((array) $data);

        return $valueObject;
    }
}