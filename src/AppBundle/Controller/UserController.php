<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
	/**
	 * @Route("/user/settings", name="user-settings")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function settingsAction(Request $request)
	{
		$user = $this->getUser();

		/** @var Form $form */
		$form = $this->createFormBuilder($user)
			->add('timezone', TimezoneType::class, [
				'preferred_choices' => [
					'America/New_York',
					'America/Chicago',
					'America/Denver',
					'America/Phoenix',
					'America/Los_Angeles',
					'America/Anchorage',
					'America/Adak',
					'Pacific/Honolulu',
				],
			])
			->add('save', SubmitType::class, ['label' => 'Save Settings'])
			->getForm();

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$user = $form->getData();

			$em = $this->get('doctrine.orm.default_entity_manager');
			$em->persist($user);
			$em->flush();

			return $this->redirectToRoute('homepage');
		}

		return $this->render('AppBundle:User:settings.html.twig', array(
			'form' => $form->createView(),
		));
	}
}
