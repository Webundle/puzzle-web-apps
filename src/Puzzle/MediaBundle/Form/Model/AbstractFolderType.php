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
                'label' => 'media.folder.name'
            ])
            ->add('tag', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'media.folder.tag',
                'required' => false
            ])
            ->add('allowedExtensions', ChoiceType::class, array(
                'translation_domain' => 'messages',
                'label' => 'media.folder.allowedExtensions',
                'choices' => array(
                    "media.file.filter_all" => "*",
                    "media.picture.filter" => MediaUtil::supportedPictureExtensions(),
                    "media.audio.filter" => MediaUtil::supportedAudioExtensions(),
                    "media.video.filter" => MediaUtil::supportedVideoExtensions(),
                    "media.document.filter" => MediaUtil::supportedDocumentExtensions()
                )
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