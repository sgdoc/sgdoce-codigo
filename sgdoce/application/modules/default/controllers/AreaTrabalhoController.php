<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
class Default_AreaTrabalhoController extends Core_Controller_Action_Base
{
    protected $_service = 'AreaTrabalho';

    public function indexAction ()
    {}

    public function caixaAction ()
    {
        $this->_forward('index');
    }

    public function qtdItensMinhaCaixaJsonAction ()
    {
        $this->_jsonResponse(function () {
            return $this->getService()->getQtdItensMinhaCaixa(
                \Core_Integration_Sica_User::getPersonId(),
                \Core_Integration_Sica_User::getUserUnit(),
                $this->_getParam('artefactType', 0)
            );
        });
    }

    public function gridJsonAction ()
    {
        $this->_jsonResponse(function () {

            $data = array(
                array(
                    'id' => 1,
                    'processNumber' => '11111.111111/2015-99',
                    'documentDigital' => '00000001',
                    'handlingInformation' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. A dignissimos maxime aspernatur deleniti veritatis commodi, numquam consequuntur quos et tenetur ex vero assumenda voluptatibus magnam amet, adipisci alias dolores itaque.',
                ),
                array(
                    'id' => 2,
                    'processNumber' => '22222.222221/2015-99',
                    'documentDigital' => '00000002',
                    'handlingInformation' => 'Incidunt odio at magnam provident doloremque aliquid officiis tempora maiores, neque ratione omnis itaque sed tenetur dicta totam quidem amet id nisi aperiam perferendis asperiores porro. Inventore nemo unde tempora.',
                ),
                array(
                    'id' => 3,
                    'processNumber' => '33333.333331/2015-99',
                    'documentDigital' => '00000003',
                    'handlingInformation' => 'Ullam veniam culpa blanditiis atque itaque sit nisi in ea natus. Iste, labore consectetur tempora quaerat cupiditate nostrum ab, esse quo optio vel doloribus obcaecati, minus facilis aut similique itaque.',
                ),
                array(
                    'id' => 4,
                    'processNumber' => '44444.444441/2015-99',
                    'documentDigital' => '00000004',
                    'handlingInformation' => 'Similique et earum quibusdam dolore. Blanditiis itaque iusto architecto, harum nihil nulla cumque, nam delectus error officia maxime, dolore nostrum recusandae. Nemo, aspernatur earum maxime dolorum, molestias fuga rem consequatur.',
                ),
                array(
                    'id' => 5,
                    'processNumber' => '55555.555551/2015-99',
                    'documentDigital' => '00000005',
                    'handlingInformation' => 'Commodi quis ut provident adipisci quisquam magnam, unde, distinctio obcaecati reiciendis molestiae excepturi sequi aliquam ipsa praesentium rem, fugit quos, facere deleniti consectetur? Sit eaque minus illo voluptates quisquam provident!',
                ),
            );

            return array(
                'total'    => 55,
                'dataGrid' => $data,
            );
        });
    }

    private function _jsonResponse($callbackToFetchData)
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $result = array("error" => false, 'data' => null);

        try {
            $result['data'] = $callbackToFetchData();
        } catch (\Exception $exp) {
            $result['error'] = $exp->getMessage();
        }

        $this->_helper->json($result);
    }
}
