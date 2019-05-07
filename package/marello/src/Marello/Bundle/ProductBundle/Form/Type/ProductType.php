<?php

namespace Marello\Bundle\ProductBundle\Form\Type;

use Marello\Bundle\CatalogBundle\Entity\Category;
use Marello\Bundle\PricingBundle\Form\EventListener\ChannelPricingSubscriber;
use Marello\Bundle\PricingBundle\Form\EventListener\PricingSubscriber;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Form\EventListener\DefaultAttributeFamilySubscriber;
use Marello\Bundle\SalesBundle\Form\EventListener\DefaultSalesChannelSubscriber;
use Marello\Bundle\TaxBundle\Form\Type\TaxCodeSelectType;
use Oro\Bundle\AttachmentBundle\Form\Type\ImageType;
use Oro\Bundle\FormBundle\Form\Type\EntityIdentifierType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class ProductType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_product_form';

    /**
     * @var DefaultSalesChannelSubscriber
     */
    protected $defaultSalesChannelSubscriber;

    /**
     * @var PricingSubscriber
     */
    protected $pricingSubscriber;

    /**
     * @var ChannelPricingSubscriber
     */
    protected $channelPricingSubscriber;

    /**
     * @var DefaultAttributeFamilySubscriber $defaultAttributeFamilySubscriber
     */
    protected $defaultAttributeFamilySubscriber;

    /**
     * @param DefaultSalesChannelSubscriber $defaultSalesChannelSubscriber
     * @param PricingSubscriber $pricingSubscriber
     * @param ChannelPricingSubscriber $channelPricingSubscriber
     */
    public function __construct(
        DefaultSalesChannelSubscriber $defaultSalesChannelSubscriber,
        PricingSubscriber $pricingSubscriber,
        ChannelPricingSubscriber $channelPricingSubscriber
    ) {
        $this->defaultSalesChannelSubscriber = $defaultSalesChannelSubscriber;
        $this->pricingSubscriber = $pricingSubscriber;
        $this->channelPricingSubscriber = $channelPricingSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                    'label'    => 'marello.product.name.label',
                ]
            )
            ->add(
                'sku',
                TextType::class,
                [
                    'required' => true,
                    'label'    => 'marello.product.sku.label',
                ]
            )
            ->add(
                'manufacturingCode',
                TextType::class,
                [
                    'required' => false,
                    'label'    => 'marello.product.manufacturing_code.label',
                ]
            )
            ->add(
                'weight',
                NumberType::class,
                [
                    'required'  => false,
                    'scale'     => 2,
                    'label'     => 'marello.product.weight.label'
                ]
            )
            ->add(
                'warranty',
                NumberType::class,
                [
                    'required'  => false,
                    'label'     => 'marello.product.warranty.label',
                    'tooltip'   => 'marello.product.form.tooltip.warranty'
                ]
            )
            ->add(
                'status',
                EntityType::class,
                [
                    'label'         => 'marello.product.status.label',
                    'class'         => 'MarelloProductBundle:ProductStatus',
                    'required'      => true,
                ]
            )
            ->add(
                'addSalesChannels',
                EntityIdentifierType::class,
                [
                    'class'    => 'MarelloSalesBundle:SalesChannel',
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            )
            ->add(
                'removeSalesChannels',
                EntityIdentifierType::class,
                [
                    'class'    => 'MarelloSalesBundle:SalesChannel',
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            )
            ->add(
                'suppliers',
                ProductSupplierRelationCollectionType::class,
                [
                    'label' => 'marello.supplier.entity_label',
                ]
            )
            ->add('taxCode', TaxCodeSelectType::class)
            ->add(
                'salesChannelTaxCodes',
                ProductChannelTaxRelationCollectionType::class,
                [
                    'label' => 'marello.tax.taxcode.entity_label',
                ]
            )
            ->add(
                'image',
                ImageType::class,
                [
                    'label' => 'marello.product.image.label',
                    'required' => false
                ]
            )
            ->add(
                'appendCategories',
                EntityIdentifierType::class,
                [
                    'class'    => Category::class,
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            )
            ->add(
                'removeCategories',
                EntityIdentifierType::class,
                [
                    'class'    => Category::class,
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            );

        $builder->addEventSubscriber($this->defaultSalesChannelSubscriber);
        $builder->addEventSubscriber($this->pricingSubscriber);
        $builder->addEventSubscriber($this->channelPricingSubscriber);
        $builder->addEventSubscriber($this->defaultAttributeFamilySubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Product::class,
            'intention'          => 'product',
            'single_form'        => true,
            'enable_attributes'  => true,
            'constraints'        => [new Valid()],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }

    /**
     * Add default attribute family subscriber and keeping BC
     * probably will be removed once going to next major version of Marello 3.0
     * @param DefaultAttributeFamilySubscriber $subscriber
     */
    public function setDefaultAttributeFamilySubscriber(DefaultAttributeFamilySubscriber $subscriber)
    {
        $this->defaultAttributeFamilySubscriber = $subscriber;
    }
}