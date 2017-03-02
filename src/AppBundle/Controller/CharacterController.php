<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WowCharacter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class CharacterController extends Controller
{
    /**
     * @Route("/character/new")
     */
    public function newAction(Request $request)
    {
        $character = new WowCharacter();
        $character->setUser($this->getUser());
        $character->setServer('Maiev-US');

        /** @var Form $form */
        $form = $this->createFormBuilder($character)
            ->add('characterName', TextType::class)
            ->add('server', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Add Character'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $character = $form->getData();

            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($character);
            $em->flush();

            return $this->redirectToRoute('app_character_index');
        }

        return $this->render('AppBundle:Character:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/character")
     */
    public function indexAction()
    {
        $repo = $this->get('doctrine.orm.default_entity_manager')->getRepository('AppBundle:WowCharacter');

        /** @var WowCharacter[] $myCharacters */
        $myCharacters = $repo->createQueryBuilder('c')
            ->where('c.user = :user')
            ->setParameter('user', $this->getUser())
            ->getQuery()->getResult();

        /** @var WowCharacter[] $othersCharacters */
        $othersCharacters = $repo->createQueryBuilder('c')
            ->join('c.user', 'u')
            ->getQuery()->getResult();

        return $this->render('AppBundle:Character:index.html.twig', array(
            'mine' => $myCharacters,
            'others' => $othersCharacters,
        ));
    }

    /**
     * @Route("/character/delete")
     */
    public function deleteAction()
    {
        return $this->render('AppBundle:Character:delete.html.twig', array(
            // ...
        ));
    }

}
