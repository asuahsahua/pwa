<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WowCharacter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Traits\RefererTrait;

class CharacterController extends Controller
{
    use RefererTrait;

    /**
     * @Route("/character/new", name="app_character_new")
     */
    public function newAction(Request $request)
    {
        $character = new WowCharacter();
        $character->setUser($this->getUser());
        $character->setServer('Maiev');

        $form = $this->getForm($character, true);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveFormData($form, true);

            return $this->redirectToRoute('app_character_index');
        }

        return $this->render('AppBundle:Character:update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/character/edit", name="app_character_edit")
     */
    public function editAction(Request $request)
    {
        /** @var WowCharacter $character */
        $character = $this->getRepo()->findOneBy(['id' => $request->get('id')]);
        if (!$character) {
            $this->forward('not_found');
        }

        $form = $this->getForm($character, false);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveFormData($form, false);
            return $this->redirectToRoute('app_character_index');
        }

        return $this->render('AppBundle:Character:update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param WowCharacter $character
     * @param bool $isNew
     * @return Form
     */
    protected function getForm($character, $isNew)
    {
        return $this->createFormBuilder($character)
            ->add('characterName', TextType::class)
            ->add('server', TextType::class)
            ->add('is_tank', CheckboxType::class, ['required' => false, 'label' => 'Tank'])
            ->add('is_heal', CheckboxType::class, ['required' => false, 'label' => 'Healer'])
            ->add('is_dps', CheckboxType::class, ['required' => false, 'label' => 'DPS'])
            ->add('save', SubmitType::class, ['label' => $isNew ? 'Create character' : 'Update character'])
            ->getForm();
    }

    /**
     * @param Form $form
     * @param bool $isNew
     */
    protected function saveFormData($form, $isNew)
    {
        $character = $form->getData();

        $em = $this->get('doctrine.orm.default_entity_manager');
        $em->persist($character);
        $em->flush();

        $operation = $isNew ? 'created' : 'updated';
        $this->addFlash('success', "Character {$character->getDisplayName()} was {$operation}!");
    }

    /**
     * @Route("/character")
     */
    public function indexAction()
    {
        $repo = $this->getRepo();

        /** @var WowCharacter[] $myCharacters */
        $myCharacters = $repo->createQueryBuilder('c')
            ->where('c.user = :user')
            ->setParameter('user', $this->getUser())
            ->getQuery()->getResult();

        /** @var WowCharacter[] $othersCharacters */
        $othersCharacters = $repo->createQueryBuilder('c')
            ->join('c.user', 'u')
            ->getQuery()->getResult();

        return $this->render('AppBundle:Character:index.html.twig', [
            'mine'   => $myCharacters,
            'others' => $othersCharacters,
        ]);
    }

    /**
     * @Route("/character/delete")
     */
    public function deleteAction(Request $request)
    {
        $repo = $this->getRepo();
        /** @var WowCharacter $character */
        $character = $repo->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $request->get('id'))
            ->getQuery()->getOneOrNullResult();

        if (!$character) {
            $this->addFlash('error', 'That character does not exist.');
        } else {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($character);
            $em->flush();
            $this->addFlash('success', "Character {$character->getDisplayName()} was deleted!");
        }

        return $this->redirectReferrer($request);
    }

	/**
	 * @param Request $request
	 * @Route("/character/read", name="character_read")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function readAction(Request $request)
    {
    	$id = $request->get('id');
    	/** @var WowCharacter $character */
    	$character = $this->getRepo()->findOneBy(['id' => $id]);
    	$characterInfo = $this->get('wow_api_client')
		    ->getCharacter($character->getServer(), $character->getCharacterName());
    	if ($characterInfo->getStatusCode() == 200) {
    		$characterInfo = json_decode($characterInfo->getBody());
	    } else {
    		$characterInfo = [];
	    }

    	if (!$character) {
    		$this->addFlash('error', 'Could not find that character.');
    		return $this->redirectReferrer($request);
	    }

	    var_dump($characterInfo);

	    return $this->render('AppBundle:Character:read.html.twig', [
	    	'character' => $character,
		    'info' => $characterInfo,
	    ]);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepo()
    {
        return $this->get('doctrine.orm.default_entity_manager')->getRepository('AppBundle:WowCharacter');
    }
}
