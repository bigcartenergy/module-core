<?php
/**
 * Copyright (c) Big Cart Energy. All rights reserved.
 * SPDX-License-Identifier: OSL-3.0
 */
declare(strict_types=1);

namespace BigCartEnergy\Core\Block\Adminhtml\System\Config;

use BigCartEnergy\Core\Model\Modules;
use BigCartEnergy\Core\ViewModel\Brand;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;

/**
 * The "About Big Cart Energy" group: installed Big Cart Energy modules and their versions, the links, and the
 * installation-service line. Rendered from a template inside one table row, because the fieldset's header
 * opens the fields table and anything else there would be moved out of it by the browser.
 */
class About extends Fieldset
{
    /**
     * @var Brand
     */
    private $brand;

    /**
     * @var Modules
     */
    private $modules;

    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        Brand $brand,
        Modules $modules,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
        $this->brand = $brand;
        $this->modules = $modules;
    }

    /**
     * @inheritDoc
     */
    public function render(AbstractElement $element)
    {
        $html = $this->_getHeaderHtml($element);
        $html .= '<tr><td colspan="4" class="bce-brand-about-cell">';
        $html .= $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Template::class)
            ->setTemplate('BigCartEnergy_Core::system/config/about.phtml')
            ->setData('brand', $this->brand)
            ->setData('modules', $this->modules->getInstalled())
            ->toHtml();
        $html .= '</td></tr>';
        $html .= $this->_getFooterHtml($element);
        return $html;
    }
}
