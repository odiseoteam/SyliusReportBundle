<?php

namespace Sylius\Bundle\ReportBundle\Form\Type\DataFetcher;

use Opos\Bundle\ReportBundle\DataFetcher\TimePeriod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Odiseo Team <team@odiseo.com.ar>
 */
class TimePeriodType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateType::class, [
                'label' => 'sylius.form.report.user_registration.start',
                'years' => range(date('Y') - 100, date('Y')),
                'data' => new \DateTime(),
            ])
            ->add('end', DateType::class, [
                'label' => 'sylius.form.report.user_registration.end',
                'years' => range(date('Y') - 100, date('Y')),
                'data' => new \DateTime(),
            ])
            ->add('period', ChoiceType::class, [
                'choices' => TimePeriod::getPeriodChoices(),
                'multiple' => false,
                'label' => 'sylius.form.report.user_registration.period',
            ])
            ->add('empty_records', CheckboxType::class, [
                'label' => 'sylius.form.report.user_registration.empty_records',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_data_fetcher_time_period';
    }
}
