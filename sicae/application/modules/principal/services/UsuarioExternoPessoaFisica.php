<?php

/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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

namespace Principal\Service;

class UsuarioExternoPessoaFisica extends \Principal\Service\UsuarioExterno
{

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:UsuarioExterno';

    /**
     *
     * @var type
     */
    protected $_entityOption = array(
        'entity' => '\Sica\Model\Entity\UsuarioPessoaFisica',
        'mapping' => array(
            'sqUsuarioExterno' => '\Sica\Model\Entity\UsuarioExterno',
            'sqTipoEscolaridade' => '\Sica\Model\Entity\TipoEscolaridade',
            'sqPaisOrigem' => '\Sica\Model\Entity\Pais'
            ));

    public function postSave($entity, $dto = NULL)
    {
        $data = $dto->getMap();
        unset($data['sqUsuarioExterno']);

        $entityPF = $this->_getRepository('app:UsuarioPessoaFisica')->find($entity->getSqUsuarioExterno());

        $data['sqTipoEscolaridade'] = NULL;
        $data['sqPaisOrigem'] = NULL;

        if ($dto->getSqTipoEscolaridade()) {
            $data['sqTipoEscolaridade'] = $this->_getRepository('app:TipoEscolaridade')
                    ->find($dto->getSqTipoEscolaridade());
        }

        if ($dto->getSqPaisOrigem()) {
            $data['sqPaisOrigem'] = $this->_getRepository('app:Pais')->find($dto->getSqPaisOrigem());
        }

        if ($entityPF) {
            $entityPF->fromArray($data);
        } else {
            $entityPF = \Core_Dto::factoryFromData($dto->getMap(), 'entity', $this->_entityOption)->getEntity();
            $entityPF->setSqTipoEscolaridade($data['sqTipoEscolaridade']);
            $entityPF->setSqPaisOrigem($data['sqPaisOrigem']);
        }

        $entityPF->setNuCpf(\Zend_Filter::filterStatic($entityPF->getNuCpf(), 'Digits'));
        $entityPF->setSqUsuarioExterno($entity);

        $this->getEntityManager()->persist($entityPF);
        $this->getEntityManager()->flush($entityPF);

        parent::postSave($entity, $dto);
    }

}
