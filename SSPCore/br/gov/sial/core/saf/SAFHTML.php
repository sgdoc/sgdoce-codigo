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
namespace br\gov\sial\core\saf;
use br\gov\sial\core\Renderizable,
    br\gov\sial\core\exception\IOException,
    br\gov\sial\core\output\screen\html\H1,
    br\gov\sial\core\output\screen\html\H2,
    br\gov\sial\core\output\screen\html\H3,
    br\gov\sial\core\output\screen\html\H4,
    br\gov\sial\core\output\screen\html\H5,
    br\gov\sial\core\output\screen\html\H6,
    br\gov\sial\core\output\screen\html\Br,
    br\gov\sial\core\output\screen\html\HR,
    br\gov\sial\core\output\screen\html\UL,
    br\gov\sial\core\output\screen\html\LI,
    br\gov\sial\core\output\screen\html\Img,
    br\gov\sial\core\output\screen\html\Div,
    br\gov\sial\core\output\screen\html\Base,
    br\gov\sial\core\output\screen\html\Span,
    br\gov\sial\core\output\screen\html\Meta,
    br\gov\sial\core\output\screen\html\Form,
    br\gov\sial\core\output\screen\html\Text,
    br\gov\sial\core\output\screen\html\Link,
    br\gov\sial\core\output\screen\html\Label,
    br\gov\sial\core\output\screen\html\Table,
    br\gov\sial\core\output\screen\html\Input,
    br\gov\sial\core\output\screen\html\Title,
    br\gov\sial\core\output\screen\html\Legend,
    br\gov\sial\core\output\screen\html\Select,
    br\gov\sial\core\output\screen\html\Strong,
    br\gov\sial\core\output\screen\html\Iframe,
    br\gov\sial\core\output\screen\html\Anchor,
    br\gov\sial\core\output\screen\html\Button,
    br\gov\sial\core\output\screen\html\Comment,
    br\gov\sial\core\output\screen\html\Fieldset,
    br\gov\sial\core\output\screen\html\TableRow,
    br\gov\sial\core\output\screen\html\TextArea,
    br\gov\sial\core\output\screen\html\Paragraph,
    br\gov\sial\core\output\screen\html\TableData,
    br\gov\sial\core\output\screen\html\TableBody,
    br\gov\sial\core\output\screen\html\TableHead,
    br\gov\sial\core\output\screen\html\Javascript,
    br\gov\sial\core\output\screen\ElementAbstract,
    br\gov\sial\core\output\screen\DocumentAbstract,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\output\screen\html\TableHeaderCell,
    br\gov\sial\core\output\screen\component\JsonAbstract,
    br\gov\sial\core\output\screen\component\WellAbstract,
    br\gov\sial\core\output\screen\component\GridAbstract,
    br\gov\sial\core\output\screen\component\MenuAbstract,
    br\gov\sial\core\output\screen\component\ModalAbstract,
    br\gov\sial\core\output\screen\component\ComboAbstract,
    br\gov\sial\core\output\screen\component\AlertAbstract,
    br\gov\sial\core\output\screen\ElementContainerAbstract,
    br\gov\sial\core\output\screen\component\ImageMapAbstract,
    br\gov\sial\core\output\screen\component\BrandbarAbstract,
    br\gov\sial\core\output\screen\component\ButtonBarAbstract,
    br\gov\sial\core\output\screen\component\ScreenFormAbstract,
    br\gov\sial\core\output\screen\component\PaginationAbstract,
    br\gov\sial\core\output\screen\component\BreadcrumbAbstract,
    br\gov\sial\core\output\screen\component\NavigationAbstract,
    br\gov\sial\core\output\screen\component\ProgressBarAbstract,
    br\gov\sial\core\output\screen\component\InputLabelAbstract,
    br\gov\sial\core\output\screen\component\GridDataSourceArray,
    br\gov\sial\core\output\screen\component\AutoCompleteAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage saf
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class SAFHTML extends SAFAbstract implements Renderizable
{
    /**
     * @var string
     * */
    const T_SAF_TYPE = 'html';

    /**
     * @var string
     * */
    const T_SAF_FORM_METHOD = 'post';

    /**
     * @var float
     * */
    const T_WIDTH_DEFAULT = 2.3;

    /**
     * @var integer
     * */
    public static $_seed = 0;

    /**
     * @var DocumentAbstract
     * */
    protected $_document;

    /**
     * Construtor
     * */
    public function __construct ()
    {
        $this->_document = DocumentAbstract::factory(self::T_SAF_TYPE);
    }

    /**
     * @inheritdoc
     * */
    public function add ($element, $param = NULL, $place = 'body')
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull('head' == $place || 'body' == $place, self::T_SAF_INVALID_AREA);

        if (is_string($element)) {

            # verifica se o elemento informado é válido
            IllegalArgumentException::throwsExceptionIfParamIsNull(method_exists($this, $element), self::T_SAF_INVALID_ELEMENT);

            $element = $this->$element((object) $param);
        }

        # adiciona na area informada, por '$place', do documento
        $this->_document->$place->add($element);

        return $this;
    }

    /**
     * Cria um elemento e retorna sua instância
     *
     * @param type $element
     * @param type $param
     * @return \br\gov\sial\core\output\screen\ElementAbstract
     * */
    public function create ($element, \stdClass $param = NULL)
    {
        # verifica se o elemento informado é válido
        IllegalArgumentException::throwsExceptionIfParamIsNull(method_exists($this, $element), self::T_SAF_INVALID_ELEMENT);
        $element = $this->$element((object) $param);

        return $element;
    }

    /**
     * @inheritdoc
     *
     * A soma dos algarismos de width deve totalizar em 12, exemplo, 3.9, 2.10, 1.11, etc
     *
     * @return Div
     *
     * @code
     * <?php
     *     $param = new stdClass;
     *     $param->label = 'Label';
     *     $param->value = 'content';
     *     $param->width = 3.9;
     *     $isaf->display($param);
     * ?>
     * @endcode
     * @return ElementContainerAbstract
     * */
    public function display (\stdClass $param)
    {
        list($wLbl, $wVal) = explode('.', (double) $this->safeToggle($param, 'width', self::T_WIDTH_DEFAULT), 2);

        $container = Div::factory()->addClass(array('control-group', 'span' . (($wLbl + $wVal) + 1)));

        if (isset($param->label)) {
            $container->add(Span::factory()->addClass('span' . $wLbl)->add(Strong::factory()->add(new Text($param->label))));
        }

        if (isset($param->value)) {
            $container->add(
                Span::factory()->addClass('span' . $wVal)->add(new Text($param->value))
            );
        }

        return $container;
    }

    /**
     * Compõe o elemento target. Se target e element são strings, sua instâncias são criadas
     *
     * @param \br\gov\sial\core\output\screen\ElementAbstract | string $target
     * @param \br\gov\sial\core\output\screen\ElementAbstract | string $element
     * @param mixed[] $targetParams
     * @param mixed[] $elementParams
     * @return \br\gov\sial\core\output\screen\ElementAbstract
     */
    public function appendTo ($target, $element, $targetParams = NULL, $elementParams = NULL)
    {
        if (is_string($target)){
            # verifica se o elemento informado é válido
            IllegalArgumentException::throwsExceptionIfParamIsNull(method_exists($this, $target), self::T_SAF_INVALID_ELEMENT);
            $target = $this->$target((object) $targetParams);
        }

        if (is_string($element)){
            # verifica se o elemento informado é válido
            IllegalArgumentException::throwsExceptionIfParamIsNull(method_exists($this, $element), self::T_SAF_INVALID_ELEMENT);
            $element = $this->$element((object) $elementParams);
        }

        # compõe o target
        $target->setContent($element);

        return $target;
    }

    /**
     * @return string
     * */
    public function render ()
    {
        return $this->_document->render();
    }

    /**
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     */
    public function  menu (\stdClass $param)
    {
        return MenuAbstract::factory($param)->build();
    }

    /**
     * cria barra de navegacao
     *
     * @example
     * @code
     * <?php
     *   $options[] = array('href' => 'href_#1.1', 'text' => 'OPCAO #1.1');
     *   $options[] = array('href' => 'href_#1.2', 'text' => 'OPCAO #1.2');
     *   $options[] = array('href' => 'href_#3.1', 'text' => 'OPCAO #3.1');
     *   ...
     *   $options[] = array('href' => 'href_#N.m', 'text' => 'OPCAO #N.M');
     *
     *   $safObject->brandbar((object) $param);
     * ?>
     * @endcode
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function brandbar (\stdClass $param)
    {
        return BrandbarAbstract::factory($param)->build();
    }

    /**
     * Breadcrumbs
     */
    public function breadcrumb (\stdClass $param)
    {
        return BreadcrumbAbstract::factory($param)->build();
    }

    /**
     * cria barra de botoes
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function buttonbar (\stdClass $param)
    {
        return ButtonBarAbstract::factory($param, self::T_SAF_TYPE);
    }

    /**
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function grid (\stdClass $param)
    {
        $param->title = $this->safeToggle($param, 'title', ElementAbstract::genId($param));

        # cria instancia da grid
        $grid = GridAbstract::factory(
            $this->safeToggle($param, 'title', NULL),
            $this->safeToggle($param, 'columns', array()),
            new GridDataSourceArray($this->safeToggle($param, 'data', array())),
            self::T_SAF_TYPE,
            $this->safeToggle($param, 'extraParam'),
            $this->safeToggle($param, 'cdn', NULL)
        );

        if (isset($param->executeBuild) && FALSE === $param->executeBuild) {
            return $grid;
        }

        return $grid->build();
    }

    /**
     * {@inheritdoc}
     * @param stdClass $param{
     *    string filterTitle = NULL,
     *    string comboName,
     *    string[] comboData,
     *    string comboValue,
     *    string comboText
     * }
     * @return Div
     * */
    public function smartFilter (\stdClass $param)
    {
        # este componente eh formado de 3 partes:
        # 1 - entrada dos filtros
        # 2 - exibicao do resultado, grid
        # 3 - detalhamento de resultado, exibe todos os campos do registro selecionado
        #     na grid de resultado (parte #2)

        $seed  = $this->safeToggle($param, 'id', rand(1, 999));
        $elmId = 'sf' . $seed;

        # container geral, que agrupa todo o component (filter, grid)
        $divSmartyFilter = Div::factory()->attr('id', 'containerSmartyFilter' . $seed);

        $divAlert = Div::factory()->attr('id','gridDivAlert');

        $divSmartyFilter->add($divAlert);

        # cria linha contendo
        $container = Div::factory()
                       ->addClass('input-prepend')
                       ->attr('id', $elmId);

        # criar wraper dos elementos de selecao
        $divGroup  = Div::factory()->addClass('btn-group');

        # botao de seleciona de opcao de filtro
        $btnAction = new Button('opção', 'smartFilterBtnAction');
        $btnAction->addClass(array('btn', 'btnAction'))
                  ->attr('tabindex', -1);

        # botao dropdown
        $btnDropdown = Button::factory()
                             ->addClass(array('btn', 'dropdown-toggle'))
                             ->attr('tabindex', -1)
                             ->attr('data-toggle', 'dropdown')
                             ->add(Span::factory()->addClass('caret'));

        #opcao do menu dropdown
        $dropDownMenu = UL::factory()
                          ->addClass('dropdown-menu');

        # gera lista de opcao do menu
        foreach ($this->safeToggle($param, 'comboOptions', new \stdClass) as $option) {
            $option = (object) $option;
            $text  = $param->comboText;
            $value = $param->comboValue;

            $anchor = new Anchor($option->$text, '#' . $option->$value, $target = NULL);

            $elm    = LI::factory()->add($anchor);

            $dropDownMenu->add($elm);
        }

        # div modal para manipulacao dos valores da grid
        $divModal = Div::factory()->addClass(array('SFModal', 'modal', 'hide'))
                          ->attr('tabindex', -1)
                          ->attr('role', 'dialog')
                          ->attr('aria-hidden', 'true');

        $divModalHeader     = Div::factory()->addClass(array('modal-header'));
        $divModalHeaderTlt  = H3::factory();
        $divModalHeaderBtn  = Button::factory('x')
                                    ->addClass('close')
                                    ->attr('aria-hidden', 'true')
                                    ->attr('data-toggle', 'modal');

        $divModalHeader->add($divModalHeaderBtn)
                       ->add($divModalHeaderTlt);

        $divModalBody   = Div::factory()->addClass(array('modal-body', 'modal-body-margin'));

        $divModalFooter = Div::factory()->addClass(array('modal-footer'));

        $modalFooterBtnClose = Anchor::factory('Fechar')
                                      ->addClass('btn')
                                      ->attr('id', 'modalBtnClose');

        $modalFooterBtnSaveChange = Anchor::factory('Salvar Alterações')
                                          ->addClass(array('btn', 'btn-primary'))
                                          ->attr('id', 'modalBtnSaveChange');

        $divModalFooter->add($modalFooterBtnClose)
                       ->add($modalFooterBtnSaveChange);

        $divModal->add($divModalHeader)
                 ->add($divModalBody)
                 ->add($divModalFooter);

        # @todo substituir pelo componente modal
        $divSmartyFilter->add($divModal);

        # armazena o valor da ultima opcao selecionada este campo vai suprir a necessidade de efetuar
        # uma varredura no lado do cliente para saber qual das N options foi selecionada, tendo em vista
        # que o campo do filter nao eh um HTMLSelect real.
        $inputSelected     = $this->input((object) array('name' => 'selectedIndex', 'type' => 'hidden'));
        $inputSelected->id = 'selectedIndex';
        $divSmartyFilter->add($inputSelected);

        # armazena os nomes das colunas
        $columns = $this->input((object) array(
            'name' => 'columns',
            'type' => 'hidden',
            'value' => str_replace('"', "'", json_encode($param->grid->columns)))
        );
        $divSmartyFilter->add($columns);

        # armazena a rowkey
        $divSmartyFilter->add(
            $this->input((object) array(
                'name'  => 'rowKey',
                'type'  => 'hidden',
                'value' => $this->safeToggle($param->grid, 'rowKey')
            ))
        );

        # armazena eventos agendados para cada linha da grid
        $divSmartyFilter->add(
            $this->input((object) array(
                'name'  => 'event',
                'type'  => 'hidden',
                'value' => str_replace('"', "'", json_encode($this->safeToggle($param->grid, 'event', 0)))
            ))
        );

        # input que vai receber o param de pesquisa
        $param->comboName = 'smartFilterInput';
        $input = new Input($param->comboName);

        # monta o componente
        $divGroup->add($btnAction)
                 ->add($btnDropdown)
                 ->add($dropDownMenu)
                 ->add($input);

        # barra de botoes
        $toobar = $this->buttonbar((object) array('options' => array('search')));

        /* @return string */
        $arrPHPtoObjectJs = function (\stdClass $param) {
            $object = '{';

            foreach ($param as $key => $val) {

                switch(gettype($val)) {
                    case 'string' : $val = sprintf('"%s"', $val); break;
                    case 'boolean': $val = sprintf('%s', $val ? 'true' : 'false' ); break;
                }

                $object .= sprintf('%s:%s,', $key, $val);
            }

            $object = substr($object, 0, -1) . '}';

            return $object;
        };

        # injeta ID da grid para que a parte JS possa localiza-la
        $param->httpRequest->gridID = sprintf('#table-%1$s_wrapper', $elmId);

        $scriptDocReady = new Text(
            sprintf('<script>$(document).ready(function () {$("#%1$s").smartFilter(%2$s); $("%3$s").hide();});</script>'
                    , $elmId
                    , $arrPHPtoObjectJs($param->httpRequest)
                    , $param->httpRequest->gridID
            )
        );

        # primeira parte do componente (filter)
        $container->add($divGroup)
                  ->add($toobar);

        # segunda parte do component (grid)
        $gridConf = new \stdClass;
        $gridConf->title        = $elmId;
        $gridConf->columns      = $param->grid->columns;
        $gridConf->data         = array();
        $gridConf->httpRequest  = $param->httpRequest;
        $gridConf->name         = $gridConf->title;
        $grid                   = $this->grid($gridConf)
                                       ->setUrlJS($param->cdn)
                                       ->registerHttpInfo(json_encode($param->httpRequest))
                                       ->loadDataOnReady()
                                       ;

        $divSmartyFilter->add($container)
                        ->add($grid)
                        ->add($scriptDocReady);

        return $divSmartyFilter;
    }

    /**
     * cria autocomplete
     *
     * @param stdClass $param
     * @return Div
     * */
    public function autoComplete (\stdClass $param)
    {

        IllegalArgumentException::throwsExceptionIfParamIsNull(isset($param->id), self::T_SAF_INVALID_ELEMENT);

        $autoComplete = AutoCompleteAbstract::factory($param, self::T_SAF_TYPE)->build();

        $scriptDocReady = new Text(sprintf('<script>$(document).ready(function () {$("#%1$s").SAFAutoComplete();});</script>', $autoComplete->id));

        $autoComplete->add($scriptDocReady);

        return $autoComplete;
    }

    /**
     * cria grupo de radio button
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function GRadio (\stdClass $param)
    {
        return $this->GRadioOrCheckbox('radio', $param->name, (object) $param->options);
    }

    /**
     * cria grupo de check button
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function GCheck (\stdClass $param)
    {
        return $this->GRadioOrCheckbox('checkbox', $param->name, (object) $param->options);
    }

    /**
     * cria combo
     *
     * @param stdClass $param
     * @return Select
     * */
    public function combo (\stdClass $param)
    {
        return ComboAbstract::factory($param)->build();
    }

    /**
     * @param stdClass $param
     * @return Button
     * */
    public function button (\stdClass $param)
    {
        $button = new Button($this->safeToggle($param, 'label'), $this->safeToggle($param, 'name'));
        $button->id = $this->genId($param);
        $button->addClass('btn');

        return $button;
    }

    /**
     * cria formulario
     *
     * @param stdClass $param
     * @return Form
     * */
    public function form (\stdClass $param)
    {
        return new Form($param->name, isset($param->action) ? $param->action : NULL);
    }

    /**
     * cria tela
     *
     * @param stdClass
     * @return ScreenForm
     * */
    public function screenForm (\stdClass $param)
    {
        return ScreenFormAbstract::factory($param, self::T_SAF_TYPE);
    }

    /**
     * @param stdClass $param
     * @return Title
     * */
    public function title (\stdClass $param)
    {
        return new Title($param->content);
    }

    /**
     * @param stdClass $param
     * @return Meta
     * */
    public function meta (\stdClass $param)
    {
        return Meta::factory()->setProperties($param);
    }

    /**
     * cria definicao de base de link para o documento
     *
     * @param stdClass $param
     * @return Base
     * */
    public function base (\stdClass $param)
    {
        return new Base($param->href);
    }

    /**
     * @param stdClass $config
     * @return Comment
     * */
    public function comment (\stdClass $param)
    {
        return new Comment($param->content);
    }

    /**
     * cria referencia externo
     *
     * @param stdClass $param
     * @return Link
     * */
    public function link (\stdClass $param)
    {
        return new Link($param->href);
    }

    /**
     * cria referencia para favicon
     *
     * @param stdClass $param
     * @return Link
     * */
    public function favicon (\stdClass $param)
    {
        $favicon = $this->link($param);
        $favicon->setProperties((object) array('rel' => 'shortcut icon'));
        return $favicon;
    }

    /**
     * cria referencia para documento css
     *
     * @param stdClass $param
     * @return Link
     * */
    public function stylesheet (\stdClass $param)
    {
        $CParam = clone $param;
        $SSheet = $this->link($CParam);
        unset($CParam->href);
        $SSheet->setProperties($CParam);
        return $SSheet;
    }

    /**
     * cria referencia para documento javascript
     *
     * @param stdClass $param
     * @return Javascript
     * */
    public function javascript (\stdClass $param)
    {
        return new Javascript($param->src);
    }

    /**
     * Cria a referência para Tag HTML span
     */
    public function span (\stdClass $param)
    {
        return new Span();
    }

    /**
     * Cria a referência para a Tag HTML h1
     */
    public function h1 (\stdClass $param)
    {
        return new H1($this->safeToggle($param, 'title', NULL));
    }

    /**
     * cria quebra de linha
     *
     * @return Br
     * */
    public function br ()
    {
        return new Br;
    }

    /**
     * cria linha horizontal
     *
     * @return HR
     * */
    public function hr ()
    {
        return new HR;
    }

    /**
     * @return Div
     * */
    public function div ()
    {
        return new Div;
    }

    /**
     * @return Iframe
     */
    public function iframe (\stdClass $param)
    {
        return new Iframe($param->src);
    }

    /**
     * Cria tag HTML de imagem
     *
     * @param stdClass $param
     * @return Img
     * */
    public function img (\stdClass $param)
    {
        return new Img($param->src, $param->alt);
    }

    /**
     * painel de exibicao de componente
     *
     * @param stdClass $param
     * @return Fieldset
     * */
    public function panel (\stdClass $param)
    {
        $panel   = new Fieldset( $this->safeToggle($param, 'title') );
        $content = $this->safeToggle($param, 'content');

        if ($content) {
            foreach ((array) $content as $elm) {

                # @todo verificar se cada um dos elementos informado eh do tipo ElementAbstract
                $panel->add($elm);
            }
        }

        return $panel;
    }

    /**
     * cria paragrafo
     *
     * @param stdClass $param
     * @return Paragraph
     * */
    public function paragraph (\stdClass $param)
    {
        $paragraph = new Paragraph($this->safeToggle($param, 'content'));
        $paragraph->addClass($this->safeToggle($param, 'type'));
        return $paragraph;
    }

    /**
     * Cria objeto HTML input
     *
     * @code
     * <?php
     *   # Adiciona a tela um objeto HTML input
     *   $app->add('input', (object) array('name' => 'txNome', 'placeholder' => 'Digite...'));
     *
     *   # Adiciona a tela um objeto HTML input já preenchido
     *   $app->add('input', (object) array('name' => 'txNome', 'value' => 'Fulano'));
     * @encode
     *
     * @param stdClass $param
     * @return Input
     * */
    public function input (\stdClass $param)
    {
        $input = new Input($this->safeToggle($param, 'name', NULL), $this->safeToggle($param, 'type', 'text'));

        if (isset($param->attrs)) {
            $input->setProperties($param->attrs);
        }

        if (isset($param->class)) {
            $input->addClass($param->class);
        }

        $input->value = $this->safeToggle($param, 'value');
        $input->placeholder = $input->safeToggle($param, 'placeholder');
        return $input;
    }

    /**
     * Cria um elemento HTML input agregado a um label
     *
     * @param stdClass $param
     * @return Div
     * @example SAFHTML::inputLabal
     * @code
     * <?php
     *   # cria param
     *   $param = new stdClass;
     *   $param->label    = 'titulo do campo'  ; // tipo do label
     *   $param->required = TRUE               ; // define se campo sera requerido
     *   $param->name     = 'nome_input'       ; // nome do campo
     *   $param->type     = 'text'             ; // tipo do input que sera criado
     *   $param->value    = 'init value'       ; // valor inicial do campo
     *
     *   $safhtmlObject->inputlabel($param);
     * ?>
     * @endcode
     * */
    public function inputlabel (\stdClass $param)
    {
        return InputLabelAbstract::factory($param, self::T_SAF_TYPE)->build();
    }

    /**
     * cria textarea
     *
     * @param stdClass $param
     * @return TextArea
     * */
    public function textarea (\stdClass $param)
    {
        $textarea = new TextArea($this->safeToggle($param, 'name'), $this->safeToggle($param, 'value'));
        $textarea->placeholder = $this->safeToggle($param, 'placeholder');

        if (isset($param->rows)) {
            $textarea->rows = $param->rows;
        }

        if (isset($param->cols)) {
            $textarea->cols = $param->cols;
        }

        return $textarea;
    }

    /**
     * progressbar
     *
     * @param stdClass $config
     * @return ElementContainerAbstract
     * */
    public function progressbar (\stdClass $config)
    {
        return ProgressBarAbstract::factory($config, 'html')->build();
    }

    /**
     * @param string $type
     * @param string $name
     * @param stdClass $elements
     * @param string $IDElem
     * @return Fieldset
     * */
    public function GRadioOrCheckbox ($type, $name, \stdClass $elements)
    {
        $fiedset = new Fieldset;
        $content = NULL;

        foreach ($elements as $elm) {

            $elm = (object) $elm;

            $label = new Label($elm->text);

            $input = new Input($name, $type);
            $input->value = $elm->value;

            if (isset($elm->checked)) {
                $input->checked = 'checked';
            }

            $label->add($input);
            $fiedset->add($label);
        }

        return $fiedset;
    }

    /**
     * formulario de login
     *
     * @param stdClass $param
     * @return Form
     * */
    public function login (\stdClass $param)
    {
        $param->name  = $this->safeToggle ($param, 'name', 'frmLogin');
        $form = $this->form($param);
        $form->method = self::T_SAF_FORM_METHOD;

        $fiedset = Fieldset::factory()->add(new Legend($param->legend));

        foreach($param->input as $input) {
            $fiedset->add($this->_createControlGroupInput($input));
        }

        $options = new \stdClass;
        $options->options = $param->toolbar;

        $form->add($fiedset)
             ->add($this->buttonbar($options));
        return $form;
    }

    /**
     * Alertas do sistema
     * @param stdClass $param
     * @return alert
     */
    public function alert (\stdClass $param)
    {
       return AlertAbstract::factory($param)->build();
    }

    /**
     * Well
     * @param stdClass $config
     * @return well
     */
    public function well (\stdClass $config)
    {
        return WellAbstract::factory($config)->build();
    }

    /**
     * Modal
     * @param stdClass $config
     * @return modal
     */
    public function modal (\stdClass $config)
    {
        return ModalAbstract::factory($config)->build();
    }

    /**
     * ImagemMap
     * @param stdClass $config
     * @return imageMap
     */
    public function imageMap (\stdClass $config)
    {
        return ImageMapAbstract::factory($config)->build();
    }

    /**
     * Json
     * @param stdClass $config
     * @return json
     */
    public function json (\stdClass $config)
    {
        return JsonAbstract::factory($config)->build();
    }

    /**
     * Tab
     * @param stdClass $config
     * @return Navigation
     */
    public function navigation (\stdClass $config)
    {
        return NavigationAbstract::factory($config)->build();
    }

    /**
     * Pagination
     * @param stdClass $config
     * @return Pagination
     */
    public function pagination (\stdClass $config)
    {
        return PaginationAbstract::factory($config, self::T_SAF_TYPE)->build();
    }

    /**
     * cria campo dentro de um controlgroup
     *
     * @param stdClass
     * @return Div
     * */
    private function _createControlGroupInput (\stdClass $param)
    {
        $group = new Div;
        $group->addClass('control-group');

        $label = new Label($param->label);
        $label->addClass('control-label');

        $input = new Input($param->name, $param->type);

        if (isset($param->required) && TRUE == $param->required) {
            $span = new Span();
            $span->addClass(Input::T_INPUT_REQUIRED_CLASS)
                 ->add(new Text(Input::T_INPUT_REQUIRED_MASK))
                 ->title = $this->safeToggle($param, 'requiredTitle', Input::T_INPUT_REQUIRED_TITLE);

            $label->add($span);
            $input->addClass(Input::T_INPUT_REQUIRED_CLASS);
        }

        $divInput = new Div;
        $divInput->addClass('controls')
                 ->add($input);


        // $label->add($divInput);
        $group->add($label)
              ->add($divInput);

        return $group;
    }

    /**
     * @return string
     * */
    public function __toString ()
    {
        return $this->render();
    }

    /**
     * Procura recursivamente um elemento com o id informado no documento principal
     * @param string $id
     * @return ElementAbstract
     */
    public function getElementById ($id)
    {
        return $this->_document->getElementById($id);
    }

    /**
     * Procura recursivamente todos os elementos que possuem a classe informada
     * @param string $class
     * @return ElementAbstract[]
     */
    public function getElementsByClass ($class)
    {
        return $this->_document->getElementsByClass($class);
    }

    /**
     * Procura recursivamente todos os elementos que possuem o atributo name informado
     * @param string $class
     * @return ElementAbstract[]
     */
    public function getElementsByName ($name)
    {
        return $this->_document->getElementsByName($name);
    }

    /**
     * Componente Table
     * @return Table
     */
    public function table ()
    {
        return new Table();
    }

    /**
     * Componente TableRow
     * @return TableRow
     */
    public function tableRow ()
    {
        return new TableRow();
    }

    /**
     * Componente TableBody
     * @return TableBody
     */
    public function tableBody ()
    {
        return new TableBody();
    }

    /**
     * Componente TableHead
     * @return TableHead
     */
    public function tableHead ()
    {
        return new TableHead();
    }

    /**
     * Componente TableHeaderCell
     * @return TableHeaderCell
     */
    public function tableHeaderCell (\stdClass $param = NULL)
    {
        return new TableHeaderCell($this->safeToggle ($param, 'content'));
    }

    /**
     * Componente TableData
     * @return TableData
     */
    public function tableData (\stdClass $param = NULL)
    {
        return new TableData($this->safeToggle ($param, 'content'));
    }

    /**
     * Componente UL
     * @return UL
     */
    public function ul ()
    {
        return new UL();
    }

    /**
     * Componente Li
     * @return LI
     */
    public function li ()
    {
        return new LI();
    }

    /**
     * Componente Anchor
     * @return Anchor
     */
    public function anchor (\stdClass $param = NULL)
    {
        return new Anchor($this->safeToggle ($param, 'text'), $this->safeToggle ($param, 'href'), $this->safeToggle ($param, 'target'));
    }

    /**
     * Componente Text
     * @return Text
     */
    public function text (\stdClass $param = NULL)
    {
        return new Text($this->safeToggle ($param, 'content'));
    }

    /**
     * Componente Label
     * @return Label
     */
    public function label (\stdClass $param = NULL)
    {
        return new Label($this->safeToggle ($param, 'content'));
    }

    /**
     * @param string[] $config
     * @return string
     */
    public function select (\stdClass $param = NULl)
    {
        return new Select($this->safeToggle($param, 'name'),
                          $this->safeToggle($param, 'data'),
                          $this->safeToggle($param, 'value'),
                          $this->safeToggle($param, 'text'),
                          $this->safeToggle($param, 'selectedIndex')
                         );
    }

    /**
     * @param string[] $config
     * @return string
     */
    public function genId (\stdClass $config = NULl)
    {
        return ElementAbstract::genId($config);
    }

    /**
     * cria espaçador
     *
     * @return ElementAbstract
     */
    public function spacer (\stdClass $param = NULL)
    {
        $qtd = isset($param->qtd) ? $param->qtd : 1;
        return new Text(str_repeat('&nbsp;', (int) $qtd));
    }

    /**
     * cria icone
     * a lista
     * @return ElementAbstract
     */
    public function icon (\stdClass $param = NULL)
    {
        return new Text(sprintf('<i class="icon-%s"></i>', $param->type));
    }
}