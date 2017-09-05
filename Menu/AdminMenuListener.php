<?php

namespace Sylius\Bundle\ReportBundle\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminMenuItems(MenuBuilderEvent $event)
    {
        $menu = $event->getMenu();

        $menu->getChild('marketing')
            ->addChild('reports', ['route' => 'sylius_admin_report_index'])
            ->setLabel('Reports')
            ->setLabelAttribute('icon', 'bar chart')
        ;
    }
}