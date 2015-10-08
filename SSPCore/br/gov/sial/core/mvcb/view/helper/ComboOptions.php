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
namespace br\gov\sial\core\mvcb\view\helper;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\mvcb\view\exception\HelperException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.mvcb.view
 * @subpackage helper
 * @name ComboOptions
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class ComboOptions extends SIALAbstract
{
    const HELPER_EXCEPTION = 'Ocorreu um erro ao executar o helper. Verifique os parâmetros informados.';

    /**
     * Cria lista selectOptions.
     *
     * <p>
     *   <b>array <i>$dataSource</i></b> referenciando uma lista de string, $metaInfo deverá conter as chaves
     *   dos dados que serão utilizados.
     *
     *   quando informado uma lista de valores em formato string <b>obrigatoriamente</b>
     *   deverá haver as duas entradas informadas pelo param <i>$metaInfo</i>.
     * </p>
     * <p>
     *   <b>ValueObject <i>$dataSource</i></b> referenciado uma lista de ValueObject, $metaInfo deverá conter os nomes
     *   dos métodos acessores.
     * </p>
     * @code
     * <?php
     *   # define lista de array que sera usado para definir o combo
     *   $arrCombo = array(
     *       array('value' => 1, 'text' => 'primeiro valor'),
     *       array('value' => 2, 'text' => 'segundo valor'),
     *       array('value' => 3, 'text' => 'terceiro valor'),
     *   );
     *
     *   comboOptions::comboOptions(
     *       # define a fonte de dados
     *       $arrCombo,
     *
     *       # define as propriedades do texto
     *       array('value', 'text'),
     *
     *       # texto para exibicao na primeira opcao
     *       'seleciona uma opção',
     *
     *       # define qual options ficar selecionado por padrao
     *       1
     *   );
     *
     * ?>
     * @encode
     *
     * @param string[]|valueObject[] $dataSource
     * @param string[value, value] $metaInfo
     * @param string[value, value] $defaultTitle
     * @param mixed $selectedValue
     * @return string
     * @throws HelperException
     * */
    public function comboOptions ($dataSource, $metaInfo, $defaultTitle = NULL, $selectedValue = NULL)
    {
        try {
            # lista de options
            $options = array();
            # conteudo dos options quando preenchido por um valueObject[]
            $data    = array();

            # verifica se as e
            if(TRUE != is_array($defaultTitle)) {
                $defaultTitle = array(
                    'value' => '',
                    'text'  => $defaultTitle
                );

                $options[] = self::_options($defaultTitle, array('value', 'text'), $selectedValue);
            }

            foreach ($dataSource as $obj) {
                if ($obj instanceof ValueObjectAbstract) {
                    $data['value'] = $obj->$metaInfo[0]();
                    $data['text']  = $obj->$metaInfo[1]();

                    $options[] = self::_options($data, array('value', 'text'), $selectedValue);
                } else {
                    $options[] = self::_options($obj, $metaInfo, $selectedValue);
                }

            }

            return implode("\n", $options);
        } catch (\Exception $excp) {
            throw HelperException::throwsExceptionIfParamIsNull('', self::HELPER_EXCEPTION);
        }
    }

    /**
     * Converte o valor informado em options.
     *
     * @param string[] $dataSource
     * @param string[value, value] $metaInfo
     * @param string $selectedValue
     * @return string
     * */
    private static  function _options ($dataSource, $metaInfo, $selectedValue)
    {
        return sprintf('<option value="%1$s"%3$s>%2$s</option>', $dataSource[$metaInfo[0]]
                                                               , $dataSource[$metaInfo[1]]
                                                               , $dataSource[$metaInfo[0]] == $selectedValue
                                                                 ? ' selected' : NULL);
    }
}