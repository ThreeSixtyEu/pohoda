<?php
namespace Rshop\Synchronization\Pohoda;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Category extends Agenda
{
    /**
     * Root for import
     *
     * @var string
     */
    public static $importRoot = 'ctg:category';

    /**
     * All elements
     *
     * @var array
     */
    protected $_elements = ['name', 'description', 'sequence', 'displayed', 'picture', 'note'];

    /**
     * Configure options for options resolver
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver
     */
    protected function _configureOptions(OptionsResolver $resolver)
    {
        // available options
        $resolver->setDefined($this->_elements);

        // validate / format options
        $resolver->setRequired('name');
        $resolver->setNormalizer('name', $resolver->string48Normalizer);
        $resolver->setNormalizer('sequence', $resolver->intNormalizer);
        $resolver->setNormalizer('displayed', $resolver->boolNormalizer);
    }

    /**
     * Add subcategory
     *
     * @param Category subcategory
     * @return void
     */
    public function addSubcategory(Category $category)
    {
        if (!isset($this->_data['subCategories'])) {
            $this->_data['subCategories'] = [];
        }

        $this->_data['subCategories'][] = $category;
    }

    /**
     * Get XML
     *
     * @return \SimpleXMLElement
     */
    public function getXML()
    {
        $xml = $this->_createXML()->addChild('ctg:categoryDetail', null, $this->_namespace('ctg'));
        $xml->addAttribute('version', '2.0');

        $this->categoryXML($xml);

        return $xml;
    }

    /**
     * Attach category to XML element
     *
     * @param \SimpleXMLElement
     * @return void
     */
    public function categoryXML(\SimpleXMLElement $xml)
    {
        $category = $xml->addChild('ctg:category', null, $this->_namespace('ctg'));

        $this->_addElements($category, $this->_elements, 'ctg');

        if (isset($this->_data['subCategories'])) {
            $subCategories = $category->addChild('ctg:subCategories', null, $this->_namespace('ctg'));

            foreach ($this->_data['subCategories'] as $subCategory) {
                $subCategory->categoryXML($subCategories);
            }
        }
    }
}
