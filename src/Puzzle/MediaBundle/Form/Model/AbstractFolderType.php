<?php

namespace Puzzle\MediaBundle\Form\Model;

use Puzzle\MediaBundle\Entity\Folder;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractFolderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'media.folder.property.name'
            ])
            ->add('tag', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'media.folder.property.tag',
                'required' => false
            ])
            ->add('filter', ChoiceType::class, array(
                'translation_domain' => 'messages',
                'choices' => array(
                    "media.filter.all" => "*",
                    "media.filter.picture" => MediaUtil::supportedPictureExtensions(),
                    "media.filter.audio" => MediaUtil::supportedAudioExtensions(),
                    "media.filter.video" => MediaUtil::supportedVideoExtensions(),
                    "media.filter.document" => MediaUtil::supportedDocumentExtensions(),
                    "media.filter.customize" => "customize",
                ),
                'mapped' => false
            ))
            ->add('allowedExtensions', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'media.folder.property.allowed_extensions',
                'required' => false
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Folder::class,
            'validation_groups' => array(
                Folder::class,
                'determineValidationGroups',
            ),
        ));
    }
}