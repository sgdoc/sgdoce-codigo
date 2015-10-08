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

class UsuarioExternoPessoaJuridica extends \Principal\Service\UsuarioExterno
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
        'entity' => '\Sica\Model\Entity\UsuarioPessoaJuridica',
        'mapping' => array(
            'sqUsuarioExterno' => '\Sica\Model\Entity\UsuarioExterno'
            ));

    public function postSave($entity, $dto = NULL)
    {
        $data = $dto->getMap();
        unset($data['sqUsuarioExterno']);

        $entityPJ = $this->_getRepository('app:UsuarioPessoaJuridica')->find($entity->getSqUsuarioExterno());

        if ($entityPJ) {
            $entityPJ->fromArray($data);
        } else {
            $entityPJ = \Core_Dto::factoryFromData($dto->getMap(), 'entity', $this->_entityOption)->getEntity();
        }

        $entityPJ->setNuCnpj(\Zend_Filter::filterStatic($entityPJ->getNuCnpj(), 'Digits'));
        $entityPJ->setSqUsuarioExterno($entity);

        $this->getEntityManager()->persist($entityPJ);
        $this->getEntityManager()->flush($entityPJ);

        parent::postSave($entity, $dto);
    }

}
