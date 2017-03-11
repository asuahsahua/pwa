<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WowCharacter;
use AppBundle\Form\CharacterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Traits\RefererTrait;
use Symfony\Component\HttpFoundation\Response;

class CharacterController extends Controller
{
    use RefererTrait;

    /**
     * @Route("/character/new", name="app_character_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $character = new WowCharacter();
        $character->setUser($this->getUser());
        $character->setServer('Maiev');

        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->saveFormData($form)) {
            return $this->redirectToRoute('app_character_index');
        }

        return $this->render('AppBundle:Character:update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
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

        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->saveFormData($form)) {
            return $this->redirectToRoute('app_character_index');
        }

        return $this->render('AppBundle:Character:update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Form $form
     * @return bool
     */
    protected function saveFormData($form)
    {
        /** @var WowCharacter $character */
        $character = $form->getData();
        $isNew = !!$character->getId();

        try {
            $characterInfo = $this->get('wow_api_client')->getCharacter($character->getServer(), $character->getCharacterName());
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $json = json_decode($e->getResponse()->getBody(), true);
            $reason = $json && isset($json['reason']) ? $json['reason'] : "Code {$e->getCode()}";

            $this->addFlash('warning', "Character could not be pulled from Battle.net API: {$reason}");
            return false;
        }
        $character->setFieldsFromBattlnetResponse($characterInfo);

        $em = $this->get('doctrine.orm.default_entity_manager');
        $em->persist($character);
        $em->flush();

        $operation = $isNew ? 'created' : 'updated';
        $this->addFlash('success', "Character {$character->getDisplayName()} was {$operation}!");

        return true;
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
     * @return Response
     */
    public function readAction(Request $request)
    {
        $id = $request->get('id');
        /** @var WowCharacter $character */
        $character = $this->getRepo()->findOneBy(['id' => $id]);

        if (!$character) {
            $this->addFlash('error', 'Could not find that character.');
            return $this->redirectReferrer($request);
        }

        return $this->render('AppBundle:Character:read.html.twig', [
            'character' => $character,
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
