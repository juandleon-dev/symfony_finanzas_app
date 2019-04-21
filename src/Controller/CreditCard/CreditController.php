<?php

namespace App\Controller\CreditCard;

use App\Entity\CreditCard\CreditCardConsume;
use App\Entity\CreditCard\CreditCardUser;
use App\Extractor\CreditCard\CreditCardConsumeExtractor;
use App\Extractor\CreditCard\CreditCardExtractor;
use App\Form\Credit\CreditConsumeType;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/credits")
 * */
class CreditController extends Controller
{
    /**
     * @Security("is_granted('ROLE_USER')")
     * @Route("/list", name="credit_list")
     * @param CreditCardConsumeExtractor $creditCardConsumeExtractor
     * @param CreditCardExtractor $cardExtractor
     * @return Response
     */
    public function index(CreditCardConsumeExtractor $creditCardConsumeExtractor, CreditCardExtractor $cardExtractor)
    {
        $creditCardConsumes = $creditCardConsumeExtractor->extractByOwner( $this->getUser() );
        $creditCards = $cardExtractor->extractByOwner( $this->getUser() );

        $repo = $this->getDoctrine()->getRepository(CreditCardUser::class);
        $cardUsers = $repo->getByOwner( $this->getUser() );
        dump($cardUsers);

        return $this->render('credit/index.html.twig', [
            'credit_cards' => $creditCards,
            'consumes' => $creditCardConsumes,
            'card_users' => $cardUsers
        ]);
    }

    /**
     * @Route("/new", name="credit_new")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function createCreditConsume(Request $request)
    {
        $creditConsume = new CreditCardConsume();

        $form = $this->createForm(CreditConsumeType::class, $creditConsume, [
            'credit_card_user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() )
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($creditConsume);
            $em->flush();

            $this->addFlash('success', 'Crédito agregado');
            $this->redirectToRoute('credit');
        }

        return $this->render('credit/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/credit-card-debt/{creditCard}", name="credit_card_debt")
     * @param CreditCardConsumeExtractor $creditCardConsumeExtractor
     * @param $creditCard
     */
    public function creditCardDetail(CreditCardConsumeExtractor $creditCardConsumeExtractor, $creditCard)
    {
        $repo = $this->getDoctrine()->getRepository(CreditCardConsume::class);
        $creditCardDebts = $repo->findByCreditCard( $creditCard );

        $debtsByUser = $creditCardConsumeExtractor->extractActualDebt( $creditCardDebts );

        dump($debtsByUser); die;

    }

//    public function userCreditCardResume()
//    {
//
//    }

}
