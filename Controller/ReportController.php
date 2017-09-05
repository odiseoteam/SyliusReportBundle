<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Report\DataFetcher\DataFetcherInterface;
use Sylius\Component\Report\DataFetcher\DelegatingDataFetcherInterface;
use Sylius\Component\Report\Model\ReportInterface;
use Sylius\Component\Report\Renderer\DelegatingRendererInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Fernando Caraballo Ortiz <caraballo.ortiz@gmail.com>
 */
class ReportController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function renderAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::SHOW);

        /** @var ReportInterface $report */
        $report = $this->findOr404($configuration);

        /** @var ServiceRegistryInterface $serviceRegistry */
        $serviceRegistry = $this->get('sylius.registry.report.data_fetcher');
        /** @var DataFetcherInterface $dataFetcher */
        $dataFetcher = $serviceRegistry->get($report->getDataFetcher());
        /** @var FormInterface $configurationForm */
        $configurationForm = $this->container->get('form.factory')->createNamed(
            'configuration',
            $dataFetcher->getType(),
            $report->getDataFetcherConfiguration()
        );

        if ($request->query->has('configuration')) {
            $configurationForm->submit($request);
        }

        $this->eventDispatcher->dispatch(ResourceActions::SHOW, $configuration, $report);

        $view = View::create($report);

        if ($configuration->isHtmlRequest()) {
            $view
                ->setTemplate($configuration->getTemplate(ResourceActions::SHOW . '.html'))
                ->setTemplateVar($this->metadata->getName())
                ->setData([
                    'configuration' => $configuration,
                    'metadata' => $this->metadata,
                    'resource' => $report,
                    'form' => $configurationForm->createView(),
                    'configurationForm' => $configurationForm->getData(),
                    $this->metadata->getName() => $report,
                ])
            ;
        }

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     * @param string  $report
     * @param array   $configuration
     *
     * @return Response
     */
    public function embedAction(Request $request, $report, array $configuration = [])
    {
        /** @var CurrencyContextInterface $currencyContext */
        $currencyContext = $this->get('sylius.context.currency');

        if (!$report instanceof ReportInterface) {
            $report = $this->getReportRepository()->findOneBy(['code' => $report]);
        }

        if (null === $report) {
            return $this->container->get('templating')->renderResponse('SyliusReportBundle::noDataTemplate.html.twig');
        }

        $configuration = ($request->query->has('configuration')) ? $request->query->get('configuration', $configuration) : $report->getDataFetcherConfiguration();
        $configuration['baseCurrency'] = $currencyContext->getCurrencyCode();

        $data = $this->getReportDataFetcher()->fetch($report, $configuration);

        return new Response($this->getReportRenderer()->render($report, $data));
    }

    /**
     * @return DelegatingRendererInterface
     */
    private function getReportRenderer()
    {
        return $this->container->get('sylius.report.renderer');
    }

    /**
     * @return DelegatingDataFetcherInterface
     */
    private function getReportDataFetcher()
    {
        return $this->container->get('sylius.report.data_fetcher');
    }

    /**
     * @return RepositoryInterface
     */
    private function getReportRepository()
    {
        return $this->container->get('sylius.repository.report');
    }
}
