<?php
/*
 * (c) 2017: 975L <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Knp\Component\Pager\PaginatorInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Form\GiftVoucherAvailableType;
use c975L\GiftVoucherBundle\Service\GiftVoucherService;

class AvailableController extends Controller
{
//DASHBOARD
    /**
     * @Route("/gift-voucher/dashboard",
     *      name="giftvoucher_dashboard")
     * @Method({"GET", "HEAD"})
     */
    public function dashboard(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('dashboard', null);

        //Gets the manager
        $em = $this->getDoctrine()->getManager();

        //Gets GiftVouchers Purchased
        if ($request->query->get('v') === null || $request->query->get('v') == '') {
            $pagination = $paginator->paginate(
                $em->getRepository('c975LGiftVoucherBundle:GiftVoucherPurchased')->findPurchased(),
                $request->query->getInt('p', 1),
                15
            );
        //Gets GiftVouchers Available
        } elseif ($request->query->get('v') == 'available') {
            $pagination = $paginator->paginate(
                $em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')->findAllAvailable(),
                $request->query->getInt('p', 1),
                15
            );
        //Not found
        } else {
            throw $this->createNotFoundException();
        }

        //Renders the dashboard
        return $this->render('@c975LGiftVoucher/pages/dashboard.html.twig', array(
            'giftVouchers' => $pagination,
        ));
    }

//DISPLAY
    /**
     * @Route("/gift-voucher/display/{id}",
     *      name="giftvoucher_display",
     *      requirements={
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD"})
     */
    public function display(GiftVoucherAvailable $giftVoucherAvailable)
    {
        $this->denyAccessUnlessGranted('display', $giftVoucherAvailable);

        //Renders the GiftVoucher
        return $this->render('@c975LGiftVoucher/pages/displayAvailable.html.twig', array(
            'giftVoucher' => $giftVoucherAvailable,
        ));
    }

//CREATE
    /**
     * @Route("/gift-voucher/create",
     *      name="giftvoucher_create")
     * @Method({"GET", "HEAD", "POST"})
     */
    public function create(Request $request, GiftVoucherService $giftVoucherService)
    {
        $giftVoucherAvailable = new GiftVoucherAvailable();
        $this->denyAccessUnlessGranted('create', $giftVoucherAvailable);

        //Defines form
        $giftVoucherConfig = array('action' => 'create');
        $form = $this->createForm(GiftVoucherAvailableType::class, $giftVoucherAvailable, array('giftVoucherConfig' => $giftVoucherConfig));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Adjust slug in case of not accepted signs
            $giftVoucherAvailable->setSlug($giftVoucherService->slugify($form->getData()->getSlug()));

            //Persists data in DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($giftVoucherAvailable);
            $em->flush();

            //Redirects to the GiftVoucher created
            return $this->redirectToRoute('giftvoucher_display', array(
                'id' => $giftVoucherAvailable->getId(),
            ));
        }

        //Renders the create form
        return $this->render('@c975LGiftVoucher/forms/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

//MODIFY
    /**
     * @Route("/gift-voucher/modify/{id}",
     *      name="giftvoucher_modify",
     *      requirements={
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function modify(Request $request, GiftVoucherService $giftVoucherService, GiftVoucherAvailable $giftVoucherAvailable)
    {
        $this->denyAccessUnlessGranted('modify', $giftVoucherAvailable);

        //Defines form
        $giftVoucherConfig = array('action' => 'modify');
        $form = $this->createForm(GiftVoucherAvailableType::class, $giftVoucherAvailable, array('giftVoucherConfig' => $giftVoucherConfig));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Adjust slug in case of not accepted signs
            $giftVoucherAvailable->setSlug($giftVoucherService->slugify($form->getData()->getSlug()));

            //Persists data in DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($giftVoucherAvailable);
            $em->flush();

            //Redirects to the GiftVoucher
            return $this->redirectToRoute('giftvoucher_display', array(
                'id' => $giftVoucherAvailable->getId(),
            ));
        }

        //Renders the modify form
        return $this->render('@c975LGiftVoucher/forms/modify.html.twig', array(
            'giftVoucher' => $giftVoucherAvailable,
            'form' => $form->createView(),
        ));
    }

//DUPLICATE
    /**
     * @Route("/gift-voucher/duplicate/{id}",
     *      name="giftvoucher_duplicate",
     *      requirements={
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function duplicate(Request $request, GiftVoucherService $giftVoucherService, GiftVoucherAvailable $giftVoucherAvailable)
    {
        $this->denyAccessUnlessGranted('duplicate', $giftVoucherAvailable);

        //Defines form
        $giftVoucherAvailableClone = clone $giftVoucherAvailable;
        $giftVoucherAvailableClone
            ->setObject(null)
            ->setSlug(null)
        ;
        $giftVoucherConfig = array('action' => 'duplicate');
        $form = $this->createForm(GiftVoucherAvailableType::class, $giftVoucherAvailableClone, array('giftVoucherConfig' => $giftVoucherConfig));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Adjust slug in case of not accepted signs
            $giftVoucherAvailableClone->setSlug($giftVoucherService->slugify($form->getData()->getSlug()));

            //Persists data in DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($giftVoucherAvailableClone);
            $em->flush();

            //Redirects to the GiftVoucher
            return $this->redirectToRoute('giftvoucher_display', array(
                'id' => $giftVoucherAvailableClone->getId(),
            ));
        }

        //Returns the form to duplicate content
        return $this->render('@c975LGiftVoucher/forms/duplicate.html.twig', array(
            'form' => $form->createView(),
            'giftVoucher' => $giftVoucherAvailableClone,
            'object' => $giftVoucherAvailable->getObject(),
        ));
    }

//DELETE
    /**
     * @Route("/gift-voucher/delete/{id}",
     *      name="giftvoucher_delete",
     *      requirements={
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function delete(Request $request, GiftVoucherAvailable $giftVoucherAvailable)
    {
        $this->denyAccessUnlessGranted('delete', $giftVoucherAvailable);

        //Defines form
        $giftVoucherConfig = array('action' => 'delete');
        $form = $this->createForm(GiftVoucherAvailableType::class, $giftVoucherAvailable, array('giftVoucherConfig' => $giftVoucherConfig));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Persists data in DB
            $giftVoucherAvailable->setSuppressed(true);

            //Persists data in DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($giftVoucherAvailable);
            $em->flush();

            //Redirects to the dashboard
            return $this->redirectToRoute('giftvoucher_dashboard');
        }

        //Renders the delete form
        return $this->render('@c975LGiftVoucher/forms/delete.html.twig', array(
            'form' => $form->createView(),
            'giftVoucher' => $giftVoucherAvailable,
        ));
    }

//SLUG
    /**
     * @Route("/gift-voucher/slug/{text}",
     *      name="giftvoucher_slug")
     * @Method({"POST"})
     */
    public function slug(GiftVoucherService $giftVoucherService, $text)
    {
        return $this->json(array('a' => $giftVoucherService->slugify($text)));
    }

//HELP
    /**
     * @Route("/gift-voucher/help",
     *      name="giftvoucher_help")
     * @Method({"GET", "HEAD"})
     */
    public function help()
    {
        $this->denyAccessUnlessGranted('help', null);

        //Renders the help
        return $this->render('@c975LGiftVoucher/pages/help.html.twig');
    }
}
